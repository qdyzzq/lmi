<?php

namespace App\Http\Controllers\Sprs;

use App\Http\Controllers\Controller;
use App\Models\Sprs\SprsSubmission;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Saves one tab of the SPRS form.
 *
 * One POST per tab rather than one for the whole form: with eight tabs
 * and unreliable connections, a failed save should cost one section, not
 * everything the user typed.
 *
 * Field names come from config/sprs_sections.php so the controller and
 * the JS work off the same list.
 */
class SectionController extends Controller
{
    /** Days into a new month that the previous month stays editable. */
    private const GRACE_DAYS = 0;

    /**
     * POST /sprs/submissions/{year}/{month}/sections/{section}
     */
    public function store(Request $request, int $year, int $month, string $section): JsonResponse
    {
        $config = config("sprs_sections.{$section}");

        if ($config === null || ($config['dynamic'] ?? false)) {
            return response()->json(['message' => "Unknown section: {$section}"], 404);
        }

        $user = $request->user();

        if ($user->office_id === null) {
            return response()->json(['message' => 'No office assigned.'], 403);
        }

        $submission = $this->submissionFor($user->office_id, $year, $month);

        if (! $this->canEdit($user, $submission)) {
            return response()->json(['message' => 'You cannot edit this submission.'], 403);
        }

        // Only columns the config declares are accepted, so a crafted
        // request can't write to something the form doesn't expose.
        $fields = $this->fields($config);
        $rules  = [];

        foreach ($fields as $field => $kind) {
            $rules[$field] = $kind === 'boolean'
                ? ['nullable', 'boolean']
                : ['nullable', 'integer', 'min:0', 'max:4294967295'];
        }

        $rules['expected_updated_at'] = ['nullable', 'string'];

        $data = $request->validate($rules);
        $seen = $data['expected_updated_at'] ?? null;
        unset($data['expected_updated_at']);

        // Only write what was actually sent — saving the visible tab must
        // never blank a field the client didn't include.
        $data = array_intersect_key($data, $fields);

        $model = $config['model'];
        $row   = $model::where('sprs_submission_id', $submission->id)->first();

        // Two encoders can share an office, so a blind save lets the
        // second overwrite the first. Compare against what the client
        // last saw.
        if ($seen !== null && $row !== null && $row->updated_at?->toIso8601String() !== $seen) {
            return response()->json([
                'message'    => 'Someone else changed this section. Reload before saving.',
                'updated_at' => $row->updated_at?->toIso8601String(),
                'current'    => $row->only(array_keys($fields)),
            ], 409);
        }

        $saved = $model::updateOrCreate(['sprs_submission_id' => $submission->id], $data);

        return response()->json([
            'saved'         => true,
            'submission_id' => $submission->id,
            'section'       => $section,
            'updated_at'    => $saved->updated_at?->toIso8601String(),
            'values'        => $saved->only(array_keys($fields)),
        ]);
    }

    /**
     * The envelope for this office and period. Created on first save, not
     * on page open — an office that only looked at the form shouldn't
     * count as having started.
     *
     * The unique index on (office_id, period_year, period_month) makes
     * this race-safe: if two tabs save at once, the loser re-reads.
     */
    private function submissionFor(int $officeId, int $year, int $month): SprsSubmission
    {
        $key = ['office_id' => $officeId, 'period_year' => $year, 'period_month' => $month];

        try {
            return SprsSubmission::firstOrCreate($key, ['status' => 'draft', 'revision' => 1]);
        } catch (UniqueConstraintViolationException) {
            return SprsSubmission::where($key)->firstOrFail();
        }
    }

    /**
     * Levels 1-3 edit their own office and must beat the month deadline.
     * FO (4) edits anything below it with no deadline — it's verifying,
     * not filing, and a reviewer getting to it late is normal.
     * RO (5) doesn't edit at all; it approves or rejects.
     */
    private function canEdit($user, SprsSubmission $submission): bool
    {
        $level = $user->office?->officeType?->hierarchy_level;

        if ($level === 4) {
            return in_array($submission->status->value, ['submitted', 'rejected'], true)
                && in_array($submission->office_id, $user->office->descendantIds(false), true);
        }

        if (! in_array($level, [1, 2, 3], true)) {
            return false;
        }

        if ($submission->office_id !== $user->office_id) {
            return false;
        }

        // Anything sent back is always editable. Without this exception a
        // submission returned after the month closed could never be fixed.
        if ($submission->status->value === 'returned') {
            return true;
        }

        if ($submission->status->value !== 'draft') {
            return false;
        }

        return $this->isOpenPeriod($submission->period_year, $submission->period_month);
    }

    private function isOpenPeriod(int $year, int $month): bool
    {
        $now = now();

        if ($year === $now->year && $month === $now->month) {
            return true;
        }

        if (self::GRACE_DAYS > 0 && $now->day <= self::GRACE_DAYS) {
            $prev = $now->copy()->subMonthNoOverflow();

            return $year === $prev->year && $month === $prev->month;
        }

        return false;
    }

    /**
     * Flatten the config into field => kind, across grouped and ungrouped
     * sections alike.
     *
     * @return array<string, string>
     */
    private function fields(array $config): array
    {
        $rows = isset($config['groups'])
            ? collect($config['groups'])->flatMap(fn ($g) => $g['rows'])
            : collect($config['rows'] ?? []);

        return $rows->mapWithKeys(fn ($r) => [$r['field'] => $r['kind'] ?? 'number'])->all();
    }
}