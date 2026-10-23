<?php

namespace App\Http\Controllers\Sprs;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Sprs\Office;
use App\Models\Sprs\SprsSubmission;
use App\Models\Sprs\SprsSubmissionAction;
use App\Services\Sprs\SubmissionScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Moving reports up the chain.
 *
 * The chain cascades — JPO to PESO, PESO to province, province to FO, FO
 * to RO — so forwarding isn't an FO-only action any more. Levels 2, 3 and
 * 4 all do the same thing: take what's sitting with them and pass the
 * batch up.
 *
 * `current_office_id` is what makes that one method instead of three. It
 * says who holds a report right now; forwarding moves it to the holder's
 * parent. No status per tier, no branching per level.
 */
class ReviewController extends Controller
{
    public function __construct(private SubmissionScope $scope)
    {
    }

    /**
     * POST /sprs/submissions/forward
     *
     * Pass everything currently held by this office up to its parent.
     *
     * The whole batch at once, not one report at a time — a PESO with six
     * JPOs sends its compilation as one act, and so does the province,
     * and so does FO.
     *
     * The audit trail stays per submission. One row each, so a question
     * about any single office still has an answer. It's the decision
     * that's collective, not the record.
     */
    public function forward(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'year'  => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $office = $user->office;
        $level  = $office?->officeType?->hierarchy_level;

        // Levels 2-4 forward. Level 1 has nobody below it, so nothing
        // ever sits with a JPO. RO is the end of the chain — it approves.
        if (! in_array($level, [2, 3, 4], true)) {
            return back()->withErrors(['review' => 'Your office does not forward submissions.']);
        }

        if ($office->parent_office_id === null) {
            return back()->withErrors(['review' => 'Your office has nowhere to forward to.']);
        }

        if (! $this->scope->withinDeadline($user)) {
            return back()->withErrors([
                'review' => 'Your deadline for this month was '
                    . $this->scope->deadlineDate($user)->format('j F') . '.',
            ]);
        }

        $pending = SprsSubmission::query()
            ->where('current_office_id', $office->id)
            ->where('period_year', $data['year'])
            ->where('period_month', $data['month'])
            // Returned reports go back up the same way. Without this a
            // rejection would be a dead end — the office could correct
            // the figures and then have no way to send them on.
            ->whereIn('status', [
                SubmissionStatus::Submitted->value,
                SubmissionStatus::Returned->value,
            ])
            ->get();

        if ($pending->isEmpty()) {
            return back()->withErrors([
                'review' => 'Nothing to forward — no reports are sitting with you for this month.',
            ]);
        }

        $parent = Office::findOrFail($office->parent_office_id);

        // Reaching RO is the one transition that changes the status.
        // Everywhere else the report is still in transit and `submitted`
        // stays accurate; current_office_id already says where it is.
        $reachesRo = $parent->officeType?->hierarchy_level === 5;

        DB::transaction(function () use ($pending, $user, $parent, $reachesRo) {
            foreach ($pending as $submission) {
                $from = $submission->status;

                $submission->current_office_id = $parent->id;

                if ($reachesRo) {
                    $submission->status      = SubmissionStatus::EndorsedToRo;
                    $submission->endorsed_by = $user->id;
                    $submission->endorsed_at = now();
                } else {
                    // A corrected report is in transit again, not still
                    // returned.
                    $submission->status = SubmissionStatus::Submitted;
                }

                $submission->save();

                SprsSubmissionAction::create([
                    'sprs_submission_id' => $submission->id,
                    'actor_id'           => $user->id,
                    'action'             => $reachesRo
                        ? SprsSubmissionAction::ENDORSED
                        : SprsSubmissionAction::SUBMITTED,
                    'from_status'        => $from->value,
                    'to_status'          => $submission->status->value,
                    'revision'           => $submission->revision,
                    'note'               => 'Forwarded to ' . $parent->name
                        . ' with ' . $pending->count() . ' report'
                        . ($pending->count() === 1 ? '' : 's') . '.',
                ]);
            }
        });

        $count = $pending->count();

        return back()->with(
            'success',
            $count . ' report' . ($count === 1 ? '' : 's') . ' forwarded to ' . $parent->name . '.'
        );
    }

    /**
     * POST /sprs/submissions/reject
     *
     * Send the batch back down, with a reason.
     *
     * Whole batch, not one report — the chain moves in batches at every
     * other step, and rejecting one office while fifteen others sit with
     * the rejector would leave a month half-here and half-there.
     *
     * Lands with whoever forwarded it, which is one tier down. RO rejects
     * to FO; FO returns to the province. That office fixes whatever the
     * note describes and forwards again.
     *
     * The reason is free text. RO names the office in it — a structured
     * field would be tidier, but the note is what gets read either way.
     */
    public function reject(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'year'   => ['required', 'integer', 'min:2000', 'max:2100'],
            'month'  => ['required', 'integer', 'min:1', 'max:12'],
            // Required on purpose. A rejection with no reason gives the
            // office below nothing to act on, and they'd have to come
            // back and ask — which is the phone call this is meant to
            // replace.
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $office = $user->office;
        $level  = $office?->officeType?->hierarchy_level;

        // Levels 4 and 5 review what comes up. Nobody below them has
        // anything sent to them to send back.
        if (! in_array($level, [4, 5], true)) {
            return back()->withErrors(['review' => 'Your office does not review submissions.']);
        }

        $held = SprsSubmission::query()
            ->where('current_office_id', $office->id)
            ->where('period_year', $data['year'])
            ->where('period_month', $data['month'])
            ->whereIn('status', [
                SubmissionStatus::Submitted->value,
                SubmissionStatus::EndorsedToRo->value,
            ])
            ->get();

        if ($held->isEmpty()) {
            return back()->withErrors([
                'review' => 'Nothing to send back — no reports are sitting with you for this month.',
            ]);
        }

        // Back to whoever last handed it over. Each submission carries its
        // own trail, so this is read per report rather than assumed: FO
        // filing on an office's behalf skipped tiers on the way up, and
        // the way down has to match.
        $senders = $this->sendersOf($held);

        DB::transaction(function () use ($held, $user, $senders, $data, $level) {
            foreach ($held as $submission) {
                $from = $submission->status;

                $submission->status            = SubmissionStatus::Returned;
                $submission->current_office_id = $senders[$submission->id] ?? $submission->office_id;
                // A new round. Notes stay pinned to the revision they
                // belong to, so a second rejection doesn't overwrite the
                // reason for the first.
                $submission->revision          = $submission->revision + 1;
                $submission->save();

                SprsSubmissionAction::create([
                    'sprs_submission_id' => $submission->id,
                    'actor_id'           => $user->id,
                    'action'             => $level === 5
                        ? SprsSubmissionAction::REJECTED
                        : SprsSubmissionAction::RETURNED,
                    'from_status'        => $from->value,
                    'to_status'          => SubmissionStatus::Returned->value,
                    // The revision it was rejected at, not the new one.
                    'revision'           => $submission->revision - 1,
                    'note'               => $data['reason'],
                ]);
            }
        });

        $count = $held->count();

        return back()->with(
            'success',
            $count . ' report' . ($count === 1 ? '' : 's') . ' sent back for correction.'
        );
    }

    /**
     * Who handed each submission over, from its own history.
     *
     * Not simply "one tier down" — FO filing for an office that missed
     * its deadline skipped the tiers between, so sending that one back
     * down the normal path would drop it on a PESO that never had it.
     *
     * @param  \Illuminate\Support\Collection<int, SprsSubmission>  $submissions
     * @return array<int, int> submission id => office id
     */
    private function sendersOf($submissions): array
    {
        $ids = $submissions->pluck('id')->all();

        if ($ids === []) {
            return [];
        }

        // The last office to move each one upward.
        $rows = DB::table('sprs_submission_actions as a')
            ->join('users as u', 'u.id', '=', 'a.actor_id')
            ->whereIn('a.sprs_submission_id', $ids)
            ->whereIn('a.action', [
                SprsSubmissionAction::SUBMITTED,
                SprsSubmissionAction::ENDORSED,
            ])
            ->orderByDesc('a.id')
            ->select(['a.sprs_submission_id', 'u.office_id'])
            ->get();

        $senders = [];

        foreach ($rows as $row) {
            // orderByDesc means the first seen per submission is the most
            // recent.
            $senders[$row->sprs_submission_id] ??= (int) $row->office_id;
        }

        return $senders;
    }

    /**
     * POST /sprs/submissions/approve
     *
     * RO approves one field office's reports for the period.
     *
     * Per field office, not per period. The six won't all reach RO on the
     * same day, so waiting for the last would hold up the five that
     * arrived on time.
     */
    public function approve(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'field_office_id' => ['required', 'integer'],
            'year'            => ['required', 'integer', 'min:2000', 'max:2100'],
            'month'           => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $level = $user->office?->officeType?->hierarchy_level;

        if ($level !== 5) {
            return back()->withErrors(['review' => 'Only the regional office can approve submissions.']);
        }

        if (! in_array($data['field_office_id'], $user->office->descendantIds(includeSelf: false), true)) {
            return back()->withErrors(['review' => 'That field office is not in your scope.']);
        }

        if (! $this->scope->withinDeadline($user)) {
            return back()->withErrors([
                'review' => 'Your deadline for this month was '
                    . $this->scope->deadlineDate($user)->format('j F') . '.',
            ]);
        }

        $fieldOffice = Office::findOrFail($data['field_office_id']);

        // Everything from that field office's tree now sitting with RO.
        $pending = SprsSubmission::query()
            ->where('current_office_id', $user->office_id)
            ->whereIn('office_id', $fieldOffice->descendantIds(includeSelf: false))
            ->where('period_year', $data['year'])
            ->where('period_month', $data['month'])
            ->where('status', SubmissionStatus::EndorsedToRo->value)
            ->get();

        if ($pending->isEmpty()) {
            return back()->withErrors([
                'review' => $fieldOffice->name . ' has nothing awaiting your approval for this month.',
            ]);
        }

        DB::transaction(function () use ($pending, $user, $fieldOffice) {
            foreach ($pending as $submission) {
                $from = $submission->status;

                $submission->status      = SubmissionStatus::Approved;
                $submission->approved_by = $user->id;
                $submission->approved_at = now();
                // Nobody holds an approved report — it's finished.
                $submission->current_office_id = null;
                $submission->save();

                SprsSubmissionAction::create([
                    'sprs_submission_id' => $submission->id,
                    'actor_id'           => $user->id,
                    'action'             => SprsSubmissionAction::APPROVED,
                    'from_status'        => $from->value,
                    'to_status'          => SubmissionStatus::Approved->value,
                    'revision'           => $submission->revision,
                    'note'               => 'Approved with ' . $pending->count() . ' report'
                        . ($pending->count() === 1 ? '' : 's') . ' from ' . $fieldOffice->name . '.',
                ]);
            }
        });

        $count = $pending->count();

        return back()->with(
            'success',
            $count . ' report' . ($count === 1 ? '' : 's') . ' from ' . $fieldOffice->name . ' approved.'
        );
    }
}