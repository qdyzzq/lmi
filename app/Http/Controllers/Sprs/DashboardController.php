<?php

namespace App\Http\Controllers\Sprs;

use App\Http\Controllers\Controller;
use App\Services\Sprs\SubmissionScope;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private SubmissionScope $scope)
    {
    }

    /**
     * Superadmin dashboard — system-wide data, not scoped to one user.
     * Route: GET /sprs/superadmin/dashboard
     */
    public function superadmin(): View
    {
        return view('sprs.superadmin.dashboard', [
            // TODO: system-wide stats — user counts, submission totals, etc.
        ]);
    }

    /**
     * Shared dashboard for every non-superadmin role.
     *
     * A summary, not a report. Three counts and the list of who hasn't
     * filed — the wide figure table lives on the Submissions page, where
     * there's a period picker to go with it. Nobody opens a dashboard to
     * read fifteen rows by sixteen columns.
     *
     * Branches on hierarchy_level, NOT on offices.office_type.
     *
     * office_type is a foreign key to role_types.id, and the two don't
     * line up: the seeder creates six role types (ids 1-6) but only five
     * levels, because Peso_City and Peso_Municipality share level 2. So
     * FO is id 5 / level 4, and RO is id 6 / level 5. Matching on the id
     * sent FO down the wrong branch and served PESO Province the FO
     * layout.
     *
     *   level 1-2  JPO, PESO City/Municipality — own report only
     *   level 3    PESO Province — files its own AND oversees its offices
     *   level 4-5  FO, RO — oversight only
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if($user->office_id === null, 403, 'No office assigned.');

        $level = $user->office->officeType?->hierarchy_level;

        // No match arm for an unmapped level — a null or unexpected value
        // used to throw UnhandledMatchError, which reads as a crash rather
        // than a configuration problem.
        abort_if($level === null, 500, 'This office has no role type assigned.');

        $now   = now();
        $year  = (int) $request->query('year', $now->year);
        $month = (int) $request->query('month', $now->month);

        // Guard the range rather than trusting the query string — an
        // out-of-range month reaches Carbon::create() further down and
        // produces a date nobody asked for instead of an error.
        $month = max(1, min(12, $month));
        $year  = max($now->year - 5, min($now->year, $year));

        $isCurrentPeriod = $year === $now->year && $month === $now->month;

        $data = [
            'level'       => $level,
            'officeName'  => $user->office->name,
            'year'        => $year,
            'month'       => $month,
            'showOwnData' => $level <= 3,

            // Levels 2, 3 and 4 oversee offices. Level 2 was excluded
            // when a PESO had nothing beneath it — the municipalities
            // reported to the province then. They report to the city now,
            // so PESO Digos has fourteen offices and no way to see them
            // from here.
            //
            // Level 1 has nothing below it. RO is excluded because it
            // works in field offices rather than the twenty-one PESOs
            // under them; its cards carry the same information at the
            // right unit.
            'showTeam'    => in_array($level, [2, 3, 4], true),
        ];

        // Only a JPO has nothing beneath it. RO must not take this exit
        // either — showTeam is false for it, but it still needs its field
        // office cards.
        if ($level <= 1) {
            return view('sprs.dashboard', $data + [
                'isRegional'   => false,
                'fieldOffices' => collect(),
                'canApprove'   => false,
                'canEndorse'   => false,
                'canOpenForm'  => false,
            ]);
        }

        $root = $this->scope->resolveRoot($user, $request->integer('office') ?: null);

        $deadline = $this->scope->deadlineDate($user);

        return view('sprs.dashboard', $data + [
            'root'        => $root,
            'completion'  => $this->scope->completion($root, $year, $month),
            'outstanding' => $this->scope->outstanding($root, $year, $month),
            // FO and RO can open another office's form; province is
            // view-only over its children.
            'canOpenForm'   => $level >= 4,
            'canEndorse'    => $level === 4,
            // RO sees field offices, not the twenty-one PESOs beneath
            // them, and approves one field office at a time.
            'isRegional'    => $level === 5,
            'canApprove'    => $level === 5,
            'fieldOffices'  => $level === 5
                ? $this->scope->fieldOfficeSummaries($root, $year, $month)
                : collect(),
            'deadlineLabel' => $deadline?->format('j F'),
            'isCurrentPeriod' => $isCurrentPeriod,
            'years'           => range($now->year, $now->year - 2),
            // Negative once it's passed. Null for anyone outside the
            // hierarchy, who has no deadline at all.
            // Only meaningful while looking at the current month. A
            // countdown against August's deadline while browsing March
            // would be noise at best and misleading at worst.
            'daysLeft'      => ($deadline && $isCurrentPeriod)
                ? (int) now()->startOfDay()->diffInDays($deadline->copy()->startOfDay(), absolute: false)
                : null,
        ]);
    }
}