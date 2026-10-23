<?php

namespace App\Http\Controllers\Sprs;

use App\Http\Controllers\Controller;
use App\Enums\SubmissionStatus;
use App\Models\Sprs\SprsSubmission;
use App\Models\Sprs\SprsSubmissionAction;
use App\Models\Sprs\OtherActivity;
use App\Services\Sprs\SubmissionScope;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AccomplishmentController extends Controller
{
    /** Days into a new month that the previous month stays editable. */
    private const GRACE_DAYS = 0;


    /** How many past years the year dropdown offers. */
    private const YEARS_BACK = 2;

    /**
     * Statuses FO can still edit — everything up to the moment it's
     * handed to RO. Not endorsed_to_ro (RO is reading it), not approved
     * (final).
     */
    private const FO_EDITABLE = ['draft', 'submitted', 'returned', 'rejected'];

    public function __construct(private SubmissionScope $scope)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if($user->office_id === null, 403, 'No office assigned.');

        // FO and RO reach other offices' forms from the breakdown table.
        // resolveRoot() rejects anything outside the viewer's subtree, so
        // a crafted ?office= can't reach sideways across the hierarchy.
        $office = $this->scope->resolveRoot($user, $request->integer('office') ?: null);

        [$openYear, $openMonth] = $this->openPeriod();

        $year = (int) $request->query('year', $openYear);

        // Every month of the selected year, per section, per field. Drives
        // three things at once: the open month's inputs, the read-only
        // past columns, and the year-to-date Total.
        [$monthly, $meta] = $this->monthlyValues($office->id, $year, $openMonth);

        $submission = SprsSubmission::where('office_id', $office->id)
            ->where('period_year', $openYear)
            ->where('period_month', $openMonth)
            ->first();

        $canEdit = $year === $openYear && (
            $submission === null
                ? $this->canStart($user, $office)
                : $this->canEdit($user, $submission)
        );

        $years = range($openYear, $openYear - self::YEARS_BACK);

        // Everything the browser needs, assembled here rather than in the
        // view. Blade's @json directive can't parse a multi-line array
        // argument, so the payload is built as one variable.
        $bootstrap = [
            'sections'  => config('sprs_sections'),
            'monthly'   => $monthly,
            'meta'      => $meta,
            'year'      => $year,
            'openYear'  => $openYear,
            'openMonth' => $openMonth,           // 1-12
            'years'     => $years,
            'canEdit'   => $canEdit,
            // Submitting is the owning office's action only — FO editing
            // someone's figures doesn't let it submit on their behalf.
            'deadline'      => $this->deadlineDate($user)?->toIso8601String(),
            'deadlineLabel' => $this->deadlineDate($user)?->format('j F Y'),
            'deadlineDay'   => $this->deadlineDayFor($user),
            'pastDeadline'  => ! $this->withinDeadline($user),
            'recipientName' => ($office->officeType?->hierarchy_level === 1)
                ? $office->parent?->name
                : null,
            'forwardsTo' => $office->parent?->name,
            'canSubmit' => $submission !== null
                && $year === $openYear
                && $this->canSubmit($user, $submission),
            'status'    => $submission?->status?->value ?? 'draft',
            'officeId'  => $office->id,
        ];

        return view('sprs.accomplishments.form', [
            'bootstrap'     => $bootstrap,
            'year'          => $year,
            'years'         => $years,
            'office'        => $office,
            'officeName'    => $office->name,
            'status'        => $submission?->status?->value ?? 'draft',
            // True when FO or RO has opened someone else's form from the
            // breakdown. The view needs to say whose, and offer a way back.
            'isOtherOffice' => $office->id !== $user->office_id,
            // Where this report has got to. "Submitted" alone stops being
            // useful the moment it's been forwarded twice — the office
            // wants to know it reached RO, not just that it left here.
            'holderName'    => $submission?->current_office_id
                ? \App\Models\Sprs\Office::find($submission->current_office_id)?->name
                : null,
            // Who this report goes to on submit.
            //
            // An office that compiles keeps its own report until it
            // forwards, so there's no recipient to name — null, and the
            // view says "your compilation" instead. Only a JPO hands its
            // report straight over.
            'recipientName' => ($office->officeType?->hierarchy_level === 1)
                ? $office->parent?->name
                : null,
            'forwardsTo'    => $office->parent?->name,
            'parentOffice'  => $office->parent,
        ]);
    }

    /**
     * POST /sprs/accomplishment/{year}/{month}/sections/{section}
     *
     * One POST per tab, not one for the whole form: with eight tabs and
     * unreliable connections a failed save should cost one section, not
     * everything the user typed.
     */
    public function saveSection(Request $request, int $year, int $month, string $section): JsonResponse
    {
        $config = config("sprs_sections.{$section}");

        if ($config === null || ($config['dynamic'] ?? false)) {
            return response()->json(['message' => "Unknown section: {$section}"], 404);
        }

        $user = $request->user();

        if ($user->office_id === null) {
            return response()->json(['message' => 'No office assigned.'], 403);
        }

        $office     = $this->scope->resolveRoot($user, $request->integer('office') ?: null);
        $submission = $this->submissionFor($office->id, $year, $month);

        if (! $this->canEdit($user, $submission)) {
            return response()->json(['message' => 'You cannot edit this submission.'], 403);
        }

        // Only columns the config declares are accepted, so a crafted
        // request can't write to a column the form doesn't expose.
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

        // Only write what was sent — saving the visible tab must never
        // blank a field the client didn't include.
        $data = array_intersect_key($data, $fields);

        $model = $config['model'];
        $row   = $model::where('sprs_submission_id', $submission->id)->first();

        // Two encoders can share an office, so a blind save lets the
        // second overwrite the first. Compare against what the client saw.
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
     * POST /sprs/accomplishment/{year}/{month}/submit
     *
     * Hands the whole period up to FO in one action. The eight tabs save
     * independently as drafts; this is the single point where the office
     * says "these are our numbers".
     *
     * The status change and its audit row go in one transaction. If the
     * log write fails, the status change rolls back with it — a status
     * with no record of who set it is worse than a failed submit.
     */
    public function submit(Request $request, int $year, int $month): JsonResponse
    {
        $user = $request->user();

        $office = $this->scope->resolveRoot($user, $request->integer('office') ?: null);

        $submission = SprsSubmission::where('office_id', $office->id)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->first();

        if ($submission === null) {
            return response()->json([
                'message' => 'Nothing to submit — no data has been entered for this month.',
            ], 422);
        }

        // Submitting is the owning office's action. FO can edit an
        // office's figures but can't submit on their behalf, or FO could
        // push a report through and then endorse its own work.
        if (! $this->withinDeadline($user) && $submission->status->value !== 'returned') {
            return response()->json([
                'message' => 'Your deadline for this month was '
                    . $this->deadlineDate($user)->format('j F') . '. Contact your field office.',
            ], 403);
        }

        if (! $this->canSubmit($user, $submission)) {
            return response()->json([
                'message' => 'You cannot submit this report.',
            ], 403);
        }

        $empty = $this->emptySections($submission);

        // A completely blank report is almost always a misclick. Partly
        // filled is allowed — some offices genuinely have no activity in
        // a section, and there's no reliable way to tell that from an
        // oversight without DOLE telling us which fields are mandatory.
        if (count($empty) === count($this->persistedSections())) {
            return response()->json([
                'message' => 'This report is empty. Enter some figures before submitting.',
            ], 422);
        }

        $from = $submission->status;

        // Whether this is the office filing its own report or FO filing
        // for them. The distinction matters later: a per-office
        // contribution ranking built on figures FO typed reads as though
        // the office reported them.
        $onBehalf = $submission->office_id !== $user->office_id;

        // Where it goes next.
        //
        // Into the compilation of whoever will forward it — which for an
        // office that compiles is its own.
        //
        // A PESO submitting its own report is saying "mine is done", not
        // "send it up". It belongs alongside its JPOs' reports until the
        // PESO forwards the batch. Sending it straight to the province
        // skipped the PESO's own compilation, so its figures left before
        // it had seen them next to its offices'.
        //
        // A JPO has no compilation of its own, so its report goes to its
        // PESO's.
        //
        // When FO files for an office that couldn't, it goes straight to
        // FO. Routing it through the tiers in between would drop it on a
        // PESO whose own deadline has passed and who can no longer
        // forward anything — it would simply sit there.
        $ownLevel = $submission->office->officeType?->hierarchy_level;

        $destination = match (true) {
            $onBehalf         => $user->office_id,
            $ownLevel === 1   => $submission->office->parent_office_id,
            default           => $submission->office_id,
        };

        DB::transaction(function () use ($submission, $user, $from, $onBehalf, $destination) {
            // Assigned directly, not through update(). submitted_by,
            // endorsed_by and approved_by are deliberately kept out of
            // $fillable so a crafted request can't set them — but that
            // also means update() silently drops them, which left status
            // moving while the actor columns stayed null.
            $submission->status            = SubmissionStatus::Submitted;
            $submission->current_office_id = $destination;
            $submission->submitted_by      = $user->id;
            $submission->submitted_at      = now();
            $submission->save();

            SprsSubmissionAction::create([
                'sprs_submission_id' => $submission->id,
                'actor_id'           => $user->id,
                'action'             => SprsSubmissionAction::SUBMITTED,
                'from_status'        => $from->value,
                'to_status'          => SubmissionStatus::Submitted->value,
                'revision'           => $submission->revision,
                'note'               => $onBehalf
                    ? 'Filed by ' . $user->office->name . ' on behalf of the office.'
                    : null,
            ]);
        });

        return response()->json([
            'submitted'      => true,
            'status'         => SubmissionStatus::Submitted->value,
            'empty_sections' => $empty,
            'on_behalf'      => $onBehalf,
            'message'        => match (true) {
                $onBehalf       => 'Filed on behalf of ' . $submission->office->name . '.',
                $ownLevel === 1 => 'Submitted to ' . ($submission->office->parent?->name ?? 'the next office up') . '.',
                default         => 'Submitted. It now sits in your compilation.',
            },
        ]);
    }

    /**
     * Who can push a report up to the next tier.
     *
     * The owning office normally. But FO can also file for an office
     * below it, because an unsubmitted draft otherwise sits stuck
     * forever: the office's own deadline has passed, FO can edit the
     * numbers but couldn't move them, and the month simply went missing.
     *
     * FO doing this is recorded distinctly — see submit(). The office is
     * still the subject of the report; FO is only the person who filed
     * it, and the two shouldn't be conflated when someone later asks who
     * reported what.
     */
    private function canSubmit($user, SprsSubmission $submission): bool
    {
        $level = $this->level($user);

        // FO filing on behalf of an office below it.
        if ($level === 4) {
            return $this->withinDeadline($user)
                && in_array($submission->status->value, ['draft', 'returned'], true)
                && in_array($submission->office_id, $user->office->descendantIds(false), true);
        }

        if (! in_array($level, [1, 2, 3], true)) {
            return false;
        }

        if ($submission->office_id !== $user->office_id) {
            return false;
        }

        // A returned submission can always be resubmitted — otherwise
        // sending something back after the 21st would strand it.
        if ($submission->status->value === 'returned') {
            return true;
        }

        return $submission->status->value === 'draft'
            && $this->withinDeadline($user);
    }

    /**
     * Sections with no row at all, or a row where every field is null.
     *
     * Returned to the caller rather than blocking on it, so the browser
     * can warn "CGEC and LMI are empty — submit anyway?" and let the
     * person decide.
     *
     * @return array<int, string>
     */
    private function emptySections(SprsSubmission $submission): array
    {
        $empty = [];

        foreach ($this->persistedSections() as $key => $config) {
            $fields = array_keys($this->fields($config));
            $row    = $config['model']::where('sprs_submission_id', $submission->id)->first();

            if ($row === null) {
                $empty[] = $key;
                continue;
            }

            $hasAny = collect($fields)->contains(fn ($f) => $row->{$f} !== null);

            if (! $hasAny) {
                $empty[] = $key;
            }
        }

        return $empty;
    }

    /** @return array<string, array> sections backed by a fixed field set */
    private function persistedSections(): array
    {
        return collect(config('sprs_sections'))
            ->reject(fn ($s) => $s['dynamic'] ?? false)
            ->all();
    }

    /**
     * POST /sprs/accomplishment/{year}/{month}/activities
     *
     * The whole list in one request, rather than add/edit/delete per row.
     *
     * Rows here are user-created and get reordered, renamed and removed
     * while someone works. Three endpoints would mean three chances for a
     * dropped connection to leave the list half-applied; one payload
     * either lands or it doesn't.
     *
     * Anything absent from the payload is deleted — the client always
     * sends the complete list.
     */
    public function saveActivities(Request $request, int $year, int $month): JsonResponse
    {
        $user = $request->user();

        if ($user->office_id === null) {
            return response()->json(['message' => 'No office assigned.'], 403);
        }

        $office     = $this->scope->resolveRoot($user, $request->integer('office') ?: null);
        $submission = $this->submissionFor($office->id, $year, $month);

        if (! $this->canEdit($user, $submission)) {
            return response()->json(['message' => 'You cannot edit this submission.'], 403);
        }

        $data = $request->validate([
            'activities'                 => ['present', 'array', 'max:100'],
            'activities.*.id'            => ['nullable', 'integer'],
            'activities.*.activity_name' => ['required', 'string', 'max:255'],
            'activities.*.count'         => ['nullable', 'integer', 'min:0', 'max:4294967295'],
        ]);

        $saved = DB::transaction(function () use ($data, $submission) {
            $keep = [];

            foreach ($data['activities'] as $activity) {
                // Match on the id *and* the submission, so a crafted id
                // can't reassign another office's row to this submission.
                $row = isset($activity['id'])
                    ? OtherActivity::where('sprs_submission_id', $submission->id)
                        ->find($activity['id'])
                    : null;

                $row ??= new OtherActivity(['sprs_submission_id' => $submission->id]);

                $row->fill([
                    'sprs_submission_id' => $submission->id,
                    'activity_name'      => trim($activity['activity_name']),
                    'count'              => $activity['count'] ?? null,
                ]);

                $row->save();
                $keep[] = $row->id;
            }

            OtherActivity::where('sprs_submission_id', $submission->id)
                ->whereNotIn('id', $keep ?: [0])
                ->delete();

            return OtherActivity::where('sprs_submission_id', $submission->id)
                ->orderBy('id')
                ->get(['id', 'activity_name', 'count']);
        });

        return response()->json([
            'saved'         => true,
            'submission_id' => $submission->id,
            'activities'    => $saved,
        ]);
    }

    // ================= data =================

    /**
     * All twelve months of one year for one office.
     *
     * Returns [monthly, meta]:
     *   monthly[section][field][month] = value
     *   meta[section] = ['updated_at' => ..., 'status' => ...]
     *
     * One query per section rather than one per row — a 36-field tab is
     * still a single query. The browser sums across months itself for the
     * Total column, so typing never triggers a round trip.
     */
    private function monthlyValues(int $officeId, int $year, int $openMonth): array
    {
        $monthly = [];
        $meta    = [];

        foreach (config('sprs_sections') as $key => $config) {
            // The repeating tab. Rows are user-created and belong to one
            // month, so they're sent as a list rather than a field grid.
            if ($config['dynamic'] ?? false) {
                $submissionId = SprsSubmission::where('office_id', $officeId)
                    ->where('period_year', $year)
                    ->where('period_month', $openMonth)
                    ->value('id');

                $monthly[$key] = $submissionId
                    ? OtherActivity::where('sprs_submission_id', $submissionId)
                        ->orderBy('id')
                        ->get(['id', 'activity_name', 'count'])
                        ->toArray()
                    : [];

                $meta[$key] = ['updated_at' => null];
                continue;
            }

            $fields = array_keys($this->fields($config));
            $table  = (new $config['model'])->getTable();

            $select = array_map(fn ($f) => "sec.{$f}", $fields);
            $select[] = 'sub.period_month';
            $select[] = 'sec.updated_at';

            $rows = DB::table("{$table} as sec")
                ->join('sprs_submissions as sub', 'sub.id', '=', 'sec.sprs_submission_id')
                ->where('sub.office_id', $officeId)
                ->where('sub.period_year', $year)
                ->select($select)
                ->get();

            $monthly[$key] = [];
            $meta[$key]    = ['updated_at' => null];

            foreach ($rows as $row) {
                $month = (int) $row->period_month;

                foreach ($fields as $field) {
                    // null stays null — "not answered" must not become 0.
                    $monthly[$key][$field][$month] = $row->{$field} === null
                        ? null
                        : (int) $row->{$field};
                }

                if ($month === $openMonth) {
                    $meta[$key]['updated_at'] = $row->updated_at
                        ? \Carbon\Carbon::parse($row->updated_at)->toIso8601String()
                        : null;
                }
            }
        }

        return [$monthly, $meta];
    }

    // ================= helpers =================

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
     * not filing, so getting to it late is normal.
     * RO (5) doesn't edit; it approves or rejects.
     */
    private function canEdit($user, SprsSubmission $submission): bool
    {
        $level = $this->level($user);

        // FO can edit anything below it while the submission is still in
        // FO's hands, with no month deadline. That's what makes "PESO
        // forgot to file, FO enters it for them" possible — the office's
        // own form is locked by then.
        //
        // Once FO passes it up, it stops being editable. endorsed_to_ro
        // means RO is looking at it and FO changing figures underneath
        // would mean RO approves something different from what it read.
        // approved is final. Either way the route back is a rejection,
        // which returns it to FO and reopens editing.
        if ($level === 4) {
            return $this->withinDeadline($user)
                && in_array($submission->status->value, self::FO_EDITABLE, true)
                && in_array($submission->office_id, $user->office->descendantIds(false), true);
        }

        if (! in_array($level, [1, 2, 3], true)) {
            return false;
        }

        if ($submission->office_id !== $user->office_id) {
            return false;
        }

        // Anything sent back is always editable, deadline included.
        // Without this exception a submission returned on the 22nd could
        // never be corrected, and would sit stuck with no way out.
        if ($submission->status->value === 'returned') {
            return true;
        }

        if ($submission->status->value !== 'draft') {
            return false;
        }

        return $this->isOpenPeriod($submission->period_year, $submission->period_month)
            && $this->withinDeadline($user);
    }

    /**
     * Whether this user may create a submission for an office that has
     * none yet. Own office if they file reports, or anything below them
     * if they're FO.
     */
    private function canStart($user, $office): bool
    {
        $level = $this->level($user);

        if ($level === 4) {
            return in_array($office->id, $user->office->descendantIds(false), true);
        }

        return $office->id === $user->office_id
            && in_array($level, [1, 2, 3], true);
    }

    private function level($user): ?int
    {
        return $user->office?->officeType?->hierarchy_level;
    }

    /** @return array{0:int, 1:int} */
    private function openPeriod(): array
    {
        $now = now();

        if (self::GRACE_DAYS > 0 && $now->day <= self::GRACE_DAYS) {
            $prev = $now->copy()->subMonthNoOverflow();

            return [(int) $prev->year, (int) $prev->month];
        }

        return [(int) $now->year, (int) $now->month];
    }

    private function isOpenPeriod(int $year, int $month): bool
    {
        [$y, $m] = $this->openPeriod();

        return $year === $y && $month === $m;
    }

    /**
     * Whether this user is still inside the monthly cut-off.
     *
     * Separate from isOpenPeriod() on purpose: the month can be open while
     * the deadline has passed, and the two produce different messages.
     * "This month is closed" and "The deadline was 21 August" send people
     * to different places.
     */
    /**
     * Whether this user is still inside their own cut-off.
     *
     * Separate from isOpenPeriod() on purpose: the month can be open while
     * this person's deadline has passed, and the two need different
     * messages. "This month is closed" and "your deadline was 21 August"
     * send people to different places — one is a wait, the other is a call
     * to the office above them.
     */
    private function withinDeadline($user): bool
    {
        $day = $this->deadlineDayFor($user);

        return $day === null || now()->day <= $day;
    }

    /** Null for anyone outside the hierarchy — super admin, statistician. */
    private function deadlineDayFor($user): ?int
    {
        // One source of truth. This controller used to hold its own copy,
        // which drifted the moment the service was changed — the topbar
        // read 31 from the service while the banner read 21 from here,
        // and the same page disagreed with itself.
        return $this->scope->deadlineDayFor($user);
    }

    private function deadlineDate($user): ?\Carbon\Carbon
    {
        $day = $this->deadlineDayFor($user);

        if ($day === null) {
            return null;
        }

        [$year, $month] = $this->openPeriod();

        return \Carbon\Carbon::create($year, $month, $day)->endOfDay();
    }

    /** @return array<string, string> field => kind */
    private function fields(array $config): array
    {
        $rows = isset($config['groups'])
            ? collect($config['groups'])->flatMap(fn ($g) => $g['rows'])
            : collect($config['rows'] ?? []);

        // Header rows have no field — they're labels, or values computed
        // from the rows beneath them. Nothing to validate or persist.
        return $rows
            ->reject(fn ($r) => ($r['header'] ?? false) || ($r['computed'] ?? false))
            ->mapWithKeys(fn ($r) => [$r['field'] => $r['kind'] ?? 'number'])
            ->all();
    }
}