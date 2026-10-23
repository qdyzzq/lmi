<?php

namespace App\Http\Controllers\Sprs;

use App\Http\Controllers\Controller;
use App\Services\Sprs\SubmissionScope;
use App\Models\Sprs\SprsSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * The compilation: an office's own report plus everything gathered from
 * the offices beneath it, and the button that passes the batch upward.
 *
 * Levels 2, 3 and 4 compile. A JPO has nothing below it, so there is
 * nothing to gather. RO sees the same page under the label
 * "Submissions" — it reviews a region rather than compiling one.
 *
 * The same page at every level. RO lands on its field offices; clicking
 * one re-renders with that field office as the root, which is exactly
 * what the FO sees. No separate "team status" screen.
 */
class CompilationController extends Controller
{
    public function __construct(private SubmissionScope $scope)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        // Throws 403 if the requested office isn't in the viewer's subtree,
        // so a crafted ?office= can't reach sideways across the hierarchy.
        $root = $this->scope->resolveRoot($user, $request->integer('office') ?: null);

        $level = $user->office?->officeType?->hierarchy_level;

        // Level 2 compiles now. A PESO gathers its JPOs the same way the
        // province gathers its PESOs.
        abort_if($level === null || $level < 2, 403, 'This page is for offices that compile submissions.');

        $sections   = collect(config('sprs_sections'));
        $sectionKey = $request->query('section', $sections->keys()->first());

        abort_unless($sections->has($sectionKey), 404, 'Unknown section.');

        $now   = now();
        $year  = (int) $request->query('year', $now->year);
        $month = (int) $request->query('month', $now->month);


        $cards = $this->scope->submissionCards($root, $year, $month);

        $childOffices = $cards->isEmpty()
            ? $this->scope->fieldOfficeSummaries($root, $year, $month)
            : collect();

        return view('sprs.compilation', [
            'root'         => $root,
            'cards'        => $cards,
            // When the tier below doesn't file — RO looking at its field
            // offices — the list is those offices, not the submissions
            // beneath them. Different unit, different partial.
            'childOffices' => $childOffices,
            'listsOffices' => $childOffices->isNotEmpty(),
            'sections'     => $sections,
            'sectionKey'   => $sectionKey,
            // What leaves when the batch is forwarded. Shown above the
            // forward button, because that's the moment it matters.
            'combined'     => $this->scope->combinedRows($user->office, $sectionKey, $year, $month),
            'years'        => $this->scope->availableYears($user->office),
            // The header shows the viewer's own deadline, not the root's.
            // RO drilling into a field office is still working to RO's
            // date, whatever office is on screen.
            'deadlineLabel' => $this->scope->deadlineDate($user)?->format('j F'),
            'daysLeft'      => ($d = $this->scope->deadlineDate($user))
                ? (int) now()->startOfDay()->diffInDays($d->copy()->startOfDay(), absolute: false)
                : null,
            // How many of the area's reports the person still holds.
            // Drives whether the panel reads "what you're forwarding" or
            // "what you sent up" — the figures are the same either way.
            'reportedCount' => $cards->filter(
                fn ($c) => ! in_array($c->status, [null, 'draft'], true)
            )->count(),
            'completion'   => $this->scope->completion($root, $year, $month),
            'year'        => $year,
            'month'       => $month,
            // FO and RO can open someone else's form; province is view-only.
            'canOpenForm' => $level >= 4,
            // Levels 2-4 forward what's sitting with them. Level 1 has
            // nobody below it; RO approves rather than forwards.
            'canForward'  => in_array($level, [2, 3, 4], true)
                && $root->id === $user->office_id,
            'held'        => $this->scope->heldBy($user->office, $year, $month),
            'parentName'  => $user->office->parent?->name,
            // Whether this office has filed its own report yet.
            //
            // Warns rather than blocks — an office with genuinely nothing
            // to report shouldn't be stopped from forwarding its JPOs, but
            // it should have to notice it's leaving itself out. FO and RO
            // don't file, so there's nothing to leave out.
            // A batch sent back, and the reason. Sits above everything
            // else on the page — a rejection is overdue work, not a
            // status.
            // Where the month currently sits. Follows the batch up the
            // chain, so the label keeps up without this office doing
            // anything.
            'batch'         => $this->scope->batchStatus($user->office, $year, $month),
            'rejection'     => $this->scope->rejectionNotes($user->office, $year, $month),
            'returnedCount' => $cards->where('status', 'returned')->count(),

            // Reviewers send back; filers don't. Levels 4 and 5 are the
            // only ones with something handed to them to hand back.
            // Scoped the same way as approve. RO reviews one field
            // office at a time, so sending back has to be one field
            // office at a time too — otherwise the regional page offers
            // a bulk rejection of everything from a screen that can't
            // show the figures or approve them.
            'canReject'     => match ($level) {
                4       => $root->id === $user->office_id,
                5       => $root->officeType?->hierarchy_level === 4,
                default => false,
            },
            'senderName'    => $this->senderNameFor($user, $year, $month),

            'ownSubmitted' => $level >= 4 || $cards->contains(
                fn ($c) => (int) $c->office_id === (int) $user->office_id
                    && ! in_array($c->status, [null, 'draft'], true)
            ),
            'office'      => $user->office,
            // RO approves a field office's reports, and only while
            // looking at that field office. Approving from the regional
            // view would mean approving six field offices at once
            // without opening any of them.
            'canApprove'  => $level === 5
                && $root->officeType?->hierarchy_level === 4,
            'isRegional'  => $level === 5,
            'breadcrumb'  => $this->breadcrumb($user, $root),
        ]);
    }

    /**
     * GET /sprs/submissions/{submission}/section/{section}
     *
     * One report's figures for one section, as JSON.
     *
     * Fetched when a card is expanded rather than rendered upfront —
     * fifteen offices across eight tabs would be a heavy page for
     * something where only one panel is ever open at a time.
     */
    public function section(Request $request, SprsSubmission $submission, string $section): JsonResponse
    {
        $user = $request->user();

        // Same subtree rule as everywhere else. A crafted submission id
        // must not reach outside the viewer's scope.
        abort_unless(
            in_array($submission->office_id, $user->office?->descendantIds() ?? [], true),
            403,
            'That submission is not in your scope.'
        );

        $office = $submission->office;

        // A card shows what the list around it doesn't.
        //
        // Looking at your own office, the offices beneath you are already
        // rows further down the page — folding them into your own card
        // would count them twice and bury your own figures. So your card
        // is your own report, full stop.
        //
        // Looking at a child, its own children aren't on the page at all.
        // PESO Bansalan forwarded a compilation, so from Digos that card
        // has to be the compilation, with the parts behind the toggle.
        $isOwn = (int) $office->id === (int) $user->office_id;

        $rows = $isOwn
            ? $this->scope->sectionRows($submission, $section)
            : $this->scope->combinedRows(
                $office,
                $section,
                $submission->period_year,
                $submission->period_month
            );

        return response()->json([
            'section' => $section,
            'label'   => config("sprs_sections.{$section}.label"),
            'own'     => $isOwn,
            'rows'    => $rows,
            // The offices this one compiled, if any. Loaded alongside so
            // opening a card and asking "what's behind this" doesn't cost
            // a second round trip.
            // No breakdown on your own card either — the same offices
            // are listed beneath it.
            'breakdown' => $isOwn ? [] : $this->scope->subOfficeRows(
                $office,
                $section,
                $submission->period_year,
                $submission->period_month
            ),
        ]);
    }

    /**
     * Where a rejection would land, for the confirmation wording.
     *
     * Read from the trail rather than assumed to be one tier down: FO
     * filing for an office that missed its deadline skipped the tiers
     * between on the way up, so the way back has to match.
     */
    private function senderNameFor($user, int $year, int $month): ?string
    {
        $id = DB::table('sprs_submission_actions as a')
            ->join('sprs_submissions as s', 's.id', '=', 'a.sprs_submission_id')
            ->join('users as u', 'u.id', '=', 'a.actor_id')
            ->where('s.current_office_id', $user->office_id)
            ->where('s.period_year', $year)
            ->where('s.period_month', $month)
            ->whereIn('a.action', ['submitted', 'endorsed'])
            ->orderByDesc('a.id')
            ->value('u.office_id');

        return $id
            ? \App\Models\Sprs\Office::find($id)?->name
            : null;
    }

    /**
     * Trail from the viewer's own office down to the one being viewed, so
     * an RO three levels deep can get back up.
     *
     * @return array<int, \App\Models\Sprs\Office>
     */
    private function breadcrumb($user, $root): array
    {
        $trail = [];
        $node  = $root;
        $stop  = $user->office_id;

        while ($node !== null) {
            array_unshift($trail, $node);

            if ($node->id === $stop) {
                break;
            }

            $node = $node->parent;
        }

        return $trail;
    }
}