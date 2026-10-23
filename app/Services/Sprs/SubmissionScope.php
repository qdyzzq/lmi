<?php

namespace App\Services\Sprs;

use App\Models\Sprs\Office;
use App\Models\Sprs\SprsSubmission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The queries behind the breakdown view.
 *
 * One class rather than one per role: PESO Province, FO and RO all ask
 * the same question — "the offices directly under this one, what did
 * they report, and who hasn't" — just from different starting points.
 * RO drilling into a field office lands on exactly the view that field
 * office sees.
 */
class SubmissionScope
{
    /**
     * Statuses that count toward a total.
     *
     * Drafts are excluded on purpose — a draft isn't a claim, and a
     * figure that moves while people type can't be forwarded
     * meaningfully.
     *
     * `returned` counts. A report sent back for correction was still
     * submitted; it's being fixed, not unmade. Leaving it out emptied
     * the combined panel and the reported count the instant anything was
     * rejected, which read as the data having vanished.
     */
    public const COUNTED = ['submitted', 'endorsed_to_ro', 'returned', 'rejected', 'approved'];

    /**
     * Last day of the month each level can still act, by hierarchy_level.
     *
     *   1  JPO                ) file their own report
     *   2  PESO City / Muni   )
     *   3  PESO Province      )
     *   4  FO                 reviews and endorses upward
     *   5  RO                 approves or rejects
     *
     * Staggered so each tier has a day after the one below it. A single
     * date for everyone left FO reviewing on the same day the offices
     * submitted.
     *
     * Lives here rather than on a controller because both the form and
     * the review actions need it, and two copies would drift.
     */
    public const DEADLINES = [
        1 => 31,
        2 => 31,
        3 => 31,
        4 => 31,
        5 => 31,
    ];

    /*
     * Production values — put these back before this handles real data:
     *
     *   1 => 21, 2 => 21, 3 => 21, 4 => 22, 5 => 23
     */

    /**
     * Resolve which office the viewer is looking at, rejecting anything
     * outside their subtree.
     */
    public function resolveRoot(User $viewer, ?int $requested): Office
    {
        $own = $viewer->office;

        abort_if($own === null, 403, 'No office assigned.');

        if ($requested === null || $requested === $own->id) {
            return $own;
        }

        abort_unless(
            in_array($requested, $own->descendantIds(), true),
            403,
            'That office is not in your scope.'
        );

        return Office::findOrFail($requested);
    }

    /** Null for anyone outside the hierarchy — super admin, statistician. */
    public function deadlineDayFor(User $user): ?int
    {
        $level = $user->office?->officeType?->hierarchy_level;

        return self::DEADLINES[$level] ?? null;
    }

    public function withinDeadline(User $user): bool
    {
        $day = $this->deadlineDayFor($user);

        return $day === null || now()->day <= $day;
    }

    public function deadlineDate(User $user): ?\Carbon\Carbon
    {
        $day = $this->deadlineDayFor($user);

        return $day === null
            ? null
            : \Carbon\Carbon::create(now()->year, now()->month, $day)->endOfDay();
    }

    /**
     * Offices directly under $root, each with this period's submission
     * status.
     *
     * Starts from offices and joins outward, so an office that filed
     * nothing still comes back — with a null status. Query the
     * submissions table instead and those offices simply don't exist,
     * which is the list people most want to see.
     *
     * child_count tells the view whether the row can be drilled into.
     */
    public function children(Office $root, int $year, int $month): Collection
    {
        return DB::table('offices as o')
            ->leftJoin('sprs_submissions as s', function ($join) use ($year, $month) {
                $join->on('s.office_id', '=', 'o.id')
                    ->where('s.period_year', '=', $year)
                    ->where('s.period_month', '=', $month);
            })
            ->leftJoin('role_types as r', 'r.id', '=', 'o.office_type')
            ->leftJoin('offices as c', 'c.parent_office_id', '=', 'o.id')
            ->where('o.parent_office_id', $root->id)
            ->groupBy('o.id', 'o.name', 'r.name', 'r.hierarchy_level', 's.id', 's.status', 's.submitted_at')
            ->orderBy('r.hierarchy_level', 'desc')
            ->orderBy('o.name')
            ->select([
                'o.id',
                'o.name',
                'r.name as role',
                'r.hierarchy_level as level',
                's.id as submission_id',
                's.status',
                's.current_office_id',
                's.submitted_at',
                DB::raw('COUNT(DISTINCT c.id) as child_count'),
            ])
            ->get();
    }

    /**
     * One section's figures for every office in $root's subtree, plus the
     * column totals.
     *
     * Flat, and deliberately the whole subtree rather than direct children
     * only. FO looking at Davao del Sur wants all 15 PESO and JPO offices
     * in one table, not one row saying "14 below" that links to a page
     * showing what's already on screen.
     *
     * The root's own row is included only when the root actually files a
     * report — levels 1-3. FO and RO review; they have no submission of
     * their own, so a row for them would always be empty.
     *
     * `level` and `depth` come back with each row so the view can indent
     * and sort, which is what makes a 22-row flat list readable.
     *
     * @return array{fields: array<int,array>, rows: Collection, totals: array<string,int>}
     */
    public function breakdown(Office $root, string $sectionKey, int $year, int $month): array
    {
        $config = config("sprs_sections.{$sectionKey}");

        abort_if($config === null || ($config['dynamic'] ?? false), 404, 'Unknown section.');

        $fields = $this->fieldList($config);
        $names  = array_column($fields, 'field');
        $table  = (new $config['model'])->getTable();

        $rootLevel = $root->officeType?->hierarchy_level;
        $rootFiles = $rootLevel !== null && $rootLevel <= 3;

        // Filing levels only. RO's table was listing its six field
        // offices as rows that could never have figures in them.
        $officeIds = $this->filingDescendantsOf($root);

        if ($rootFiles) {
            $officeIds[] = $root->id;
        }

        $select = array_map(fn ($f) => "sec.{$f}", $names);
        $select[] = 'o.id as office_id';
        $select[] = 'o.name as office_name';
        $select[] = 'r.name as role_name';
        $select[] = 'r.hierarchy_level as level';
        $select[] = 'sub.status';
        $select[] = 'sub.id as submission_id';

        $rows = DB::table('offices as o')
            ->leftJoin('sprs_submissions as sub', function ($join) use ($year, $month) {
                $join->on('sub.office_id', '=', 'o.id')
                    ->where('sub.period_year', '=', $year)
                    ->where('sub.period_month', '=', $month);
            })
            ->leftJoin("{$table} as sec", 'sec.sprs_submission_id', '=', 'sub.id')
            ->leftJoin('role_types as r', 'r.id', '=', 'o.office_type')
            ->whereIn('o.id', $officeIds ?: [0])
            // Highest tier first, then alphabetical. Mirrors the tree
            // closely enough to read without drawing it.
            ->orderByDesc('r.hierarchy_level')
            ->orderBy('o.name')
            ->select($select)
            ->get();

        // Indent depth relative to the root, so the same partial reads
        // correctly whether RO is looking at six field offices or a
        // province is looking at its fourteen.
        $depths = $this->depthsUnder($root);

        $rows = $rows->map(function ($row) use ($depths) {
            $row->depth = $depths[$row->office_id] ?? 0;

            return $row;
        });

        // Drafts show in the table but don't count. A draft isn't a claim
        // — the office hasn't stood behind those numbers yet, and a total
        // that shifts while people type can't be endorsed meaningfully.
        $totals = array_fill_keys($names, 0);

        foreach ($rows as $row) {
            if (! in_array($row->status, self::COUNTED, true)) {
                continue;
            }

            foreach ($names as $field) {
                $totals[$field] += (int) ($row->{$field} ?? 0);
            }
        }

        return ['fields' => $fields, 'rows' => $rows, 'totals' => $totals];
    }

    /**
     * Other Activities across the subtree.
     *
     * A list rather than a grid, because the rows are user-created: "Blood
     * Donation Drive" exists for Digos and not for Bansalan, so there's no
     * fixed column set to build a table from.
     *
     * Every office in the subtree comes back regardless, in the same order
     * and with the same indent as the main table — an office reporting
     * nothing is as much a fact as one reporting three activities, and FO
     * scanning the list shouldn't have to work out who's missing.
     *
     * @return Collection<int, object>
     */
    public function activities(Office $root, int $year, int $month): Collection
    {
        $rootLevel = $root->officeType?->hierarchy_level;
        $rootFiles = $rootLevel !== null && $rootLevel <= 3;

        // Filing levels only. RO's table was listing its six field
        // offices as rows that could never have figures in them.
        $officeIds = $this->filingDescendantsOf($root);

        if ($rootFiles) {
            $officeIds[] = $root->id;
        }

        $rows = DB::table('offices as o')
            ->leftJoin('sprs_submissions as sub', function ($join) use ($year, $month) {
                $join->on('sub.office_id', '=', 'o.id')
                    ->where('sub.period_year', '=', $year)
                    ->where('sub.period_month', '=', $month);
            })
            ->leftJoin('other_activities as act', 'act.sprs_submission_id', '=', 'sub.id')
            ->leftJoin('role_types as r', 'r.id', '=', 'o.office_type')
            ->whereIn('o.id', $officeIds ?: [0])
            ->orderByDesc('r.hierarchy_level')
            ->orderBy('o.name')
            ->orderBy('act.id')
            ->select([
                'o.id as office_id',
                'o.name as office_name',
                'r.name as role_name',
                'r.hierarchy_level as level',
                'sub.status',
                'act.id as activity_id',
                'act.activity_name',
                'act.count',
            ])
            ->get();

        // One row per office, activities nested. The join produces a row
        // per activity, and offices with none come back with nulls.
        $depths = $this->depthsUnder($root);

        return $rows
            ->groupBy('office_id')
            ->map(function ($group) use ($depths) {
                $first = $group->first();

                $activities = $group
                    ->filter(fn ($r) => $r->activity_id !== null)
                    ->map(fn ($r) => [
                        'name'  => $r->activity_name,
                        'count' => $r->count === null ? null : (int) $r->count,
                    ])
                    ->values();

                return (object) [
                    'office_id'   => $first->office_id,
                    'office_name' => $first->office_name,
                    'role_name'   => $first->role_name,
                    'level'       => $first->level,
                    'depth'       => $depths[$first->office_id] ?? 0,
                    'status'      => $first->status,
                    'activities'  => $activities,
                ];
            })
            ->sortBy([
                fn ($a, $b) => ($b->level ?? 0) <=> ($a->level ?? 0),
                fn ($a, $b) => $a->office_name <=> $b->office_name,
            ])
            ->values();
    }

    /**
     * One card per office for the submissions list.
     *
     * No figures. The card carries who filed it, when, and how many tabs
     * they left untouched — enough to decide whether a report is worth
     * opening. Anyone who wants the numbers opens the report, where all
     * eight tabs are laid out properly rather than squeezed into a row.
     *
     * @return Collection<int, object>
     */
    public function submissionCards(Office $root, int $year, int $month): Collection
    {
        $rootLevel = $root->officeType?->hierarchy_level;
        $rootFiles = $rootLevel !== null && $rootLevel <= 3;

        $officeIds = $this->listedOfficesFor($root);

        if ($officeIds === []) {
            return collect();
        }

        $rows = DB::table('offices as o')
            ->leftJoin('sprs_submissions as s', function ($join) use ($year, $month) {
                $join->on('s.office_id', '=', 'o.id')
                    ->where('s.period_year', '=', $year)
                    ->where('s.period_month', '=', $month);
            })
            ->leftJoin('role_types as r', 'r.id', '=', 'o.office_type')
            ->leftJoin('users as u', 'u.id', '=', 's.submitted_by')
            ->leftJoin('offices as parent', 'parent.id', '=', 'o.parent_office_id')
            ->whereIn('o.id', $officeIds)
            ->orderByDesc('r.hierarchy_level')
            ->orderBy('o.name')
            ->select([
                'o.id as office_id',
                'o.name as office_name',
                'r.name as role_name',
                'r.hierarchy_level as level',
                'parent.name as parent_name',
                's.id as submission_id',
                's.status',
                's.current_office_id',
                's.submitted_at',
                's.endorsed_at',
                's.approved_at',
                'u.name as submitted_by_name',
                'u.office_id as submitted_by_office',
            ])
            ->get();

        $filled = $this->filledTabCounts($rows->pluck('submission_id')->filter()->all());
        $total  = count($this->persistedSectionKeys());
        $depths = $this->depthsUnder($root);

        // Offices beneath each one on the list. A card with any gets a
        // breakdown option — PESO Bansalan's report is its own figures,
        // and the offices behind it are one click further in.
        $beneath = DB::table('offices as o')
            ->join('role_types as r', 'r.id', '=', 'o.office_type')
            ->whereIn('o.parent_office_id', $rows->pluck('office_id')->all() ?: [0])
            ->whereIn('r.hierarchy_level', [1, 2, 3])
            ->selectRaw('o.parent_office_id, COUNT(*) as n')
            ->groupBy('o.parent_office_id')
            ->pluck('n', 'parent_office_id')
            ->all();

        return $rows->map(function ($row) use ($filled, $total, $depths, $beneath) {
            $row->filled_tabs = $filled[$row->submission_id] ?? 0;
            $row->total_tabs  = $total;
            $row->empty_tabs  = $total - $row->filled_tabs;

            // Filed by someone from another office — FO entering figures
            // for an office that couldn't. Worth surfacing: the report is
            // the office's, but they didn't type it.
            $row->filed_on_behalf = $row->submitted_by_office !== null
                && (int) $row->submitted_by_office !== (int) $row->office_id;

            $row->depth = $depths[$row->office_id] ?? 0;
            $row->sub_office_count = (int) ($beneath[$row->office_id] ?? 0);

            return $row;
        });
    }

    /**
     * One section's figures for a single submission, in the same row
     * order the entry form uses.
     *
     * Titles and calculated rows come back too, so the expanded card can
     * mirror the form's layout — someone who fills the form should
     * recognise what they're reading without translating between two
     * shapes.
     *
     * @return array<int, array{label:string, value:?int, header:bool, computed:bool, indent:bool}>
     */
    public function sectionRows(SprsSubmission $submission, string $sectionKey): array
    {
        $config = config("sprs_sections.{$sectionKey}");

        abort_if($config === null, 404, 'Unknown section.');

        if ($config['dynamic'] ?? false) {
            return $submission->otherActivities
                ->map(fn ($a) => [
                    'label'    => $a->activity_name,
                    'value'    => $a->count,
                    'header'   => false,
                    'computed' => false,
                    'indent'   => true,
                ])
                ->all();
        }

        $row = $config['model']::where('sprs_submission_id', $submission->id)->first();

        $definitions = isset($config['groups'])
            ? collect($config['groups'])->flatMap(function ($group) {
                return collect([['label' => $group['header'], 'header' => true]])
                    ->concat($group['rows']);
            })
            : collect($config['rows'] ?? []);

        return $definitions->map(function ($definition) use ($row) {
            $isHeader   = ($definition['header'] ?? false) === true;
            $hasField   = isset($definition['field']);
            $isComputed = ! $hasField && isset($definition['sum_of']);

            $value = null;

            if ($hasField && $row) {
                $value = $row->{$definition['field']};
            }

            // Calculated rows aren't stored — "Total Female Reached with
            // LMI" is the sum of two other fields, and a saved copy could
            // disagree with them.
            if ($isComputed && $row) {
                $parts = collect($definition['sum_of'])
                    ->map(fn ($f) => $row->{$f})
                    ->filter(fn ($v) => $v !== null);

                $value = $parts->isEmpty() ? null : (int) $parts->sum();
            }

            return [
                'label'    => is_string($definition['label'] ?? null) ? $definition['label'] : '',
                'value'    => $value === null ? null : (int) $value,
                'header'   => $isHeader || ($definition['header'] ?? false),
                'computed' => $isComputed,
                'indent'   => ! $isHeader && ! ($definition['header'] ?? false),
            ];
        })->all();
    }

    /**
     * The offices beneath one office, each with its own figures for a
     * section.
     *
     * What Digos gets when it opens PESO Bansalan's card and asks what's
     * behind that number. Bansalan's own report is the card; this is
     * everything Bansalan compiled to produce it.
     *
     * Goes the whole way down rather than one tier, because someone
     * drilling into a card wants the offices, not another level of
     * drilling. Bansalan has one JPO; a city with municipalities that
     * each have JPOs would return all of them, flat and indented.
     *
     * @return array<int, array{office:string, depth:int, status:?string, rows:array}>
     */
    public function subOfficeRows(Office $office, string $sectionKey, int $year, int $month): array
    {
        $ids = $this->filingDescendantsOf($office);

        // Nothing beneath it means nothing to break down — the card's
        // figures already are that office's report, and a one-row
        // breakdown repeating them would be noise.
        if ($ids === []) {
            return [];
        }

        // The office's own report leads the list. It's part of what it
        // compiled, and leaving it out made the parts fail to add up to
        // the total on the card above.
        $ids[] = $office->id;

        $depths = $this->depthsUnder($office);

        $submissions = SprsSubmission::query()
            ->with('office.officeType')
            ->whereIn('office_id', $ids)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->get()
            ->keyBy('office_id');

        return DB::table('offices as o')
            ->leftJoin('role_types as r', 'r.id', '=', 'o.office_type')
            ->whereIn('o.id', $ids)
            ->orderByRaw('o.id = ? DESC', [$office->id])
            ->orderByDesc('r.hierarchy_level')
            ->orderBy('o.name')
            ->select(['o.id', 'o.name', 'r.name as role_name'])
            ->get()
            ->map(function ($row) use ($submissions, $depths, $sectionKey, $office) {
                $submission = $submissions->get($row->id);

                $isOwn = (int) $row->id === (int) $office->id;

                return [
                    'office' => $row->name,
                    'role'   => $isOwn ? 'own report' : $row->role_name,
                    'own'    => $isOwn,
                    'depth'  => $isOwn ? 0 : max(0, ($depths[$row->id] ?? 1) - 1),
                    'status' => $submission?->status?->value,
                    // An office that filed nothing has no rows to show.
                    // Sending back an all-null table would read as a
                    // report of blanks rather than an absent report.
                    'rows'   => $submission && $submission->status->value !== 'draft'
                        ? $this->sectionRows($submission, $sectionKey)
                        : [],
                ];
            })
            ->all();
    }

    /**
     * The combined figure for one section across every report currently
     * sitting with this office.
     *
     * This is what leaves when the batch is forwarded, so it counts only
     * what's actually in hand. An office that hasn't filed contributes
     * nothing — which is arithmetically the same as contributing zero,
     * and the header says how many are missing.
     *
     * @return array<int, array{label:string, value:?int, header:bool, computed:bool, indent:bool}>
     */
    public function combinedRows(Office $office, string $sectionKey, int $year, int $month): array
    {
        $config = config("sprs_sections.{$sectionKey}");

        abort_if($config === null, 404, 'Unknown section.');

        // Everything this area reported for the period, wherever it sits
        // now — not only what's still in hand.
        //
        // Scoping it to current_office_id meant the panel emptied the
        // moment you forwarded, which is exactly when someone wants to
        // look back at what they sent. The figure is the area's total for
        // the month; forwarding moves reports, it doesn't unmake them.
        $officeIds = $this->filingDescendantsOf($office);
        $officeIds[] = $office->id;

        $submissionIds = DB::table('sprs_submissions')
            ->whereIn('office_id', $officeIds)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->whereIn('status', self::COUNTED)
            ->pluck('id')
            ->all();

        if ($config['dynamic'] ?? false) {
            // Activities are named per office, so there's nothing to add
            // up — the same drive run by two offices is two entries, not
            // one total. Listed rather than summed.
            return DB::table('other_activities')
                ->whereIn('sprs_submission_id', $submissionIds ?: [0])
                ->orderBy('activity_name')
                ->get(['activity_name', 'count'])
                ->map(fn ($a) => [
                    'label'    => $a->activity_name,
                    'value'    => $a->count === null ? null : (int) $a->count,
                    'header'   => false,
                    'computed' => false,
                    'indent'   => true,
                ])
                ->all();
        }

        $fields = array_column($this->fieldList($config), 'field');
        $table  = (new $config['model'])->getTable();
        $totals = array_fill_keys($fields, null);

        if ($submissionIds !== [] && $fields !== []) {
            $selects = array_map(
                static fn (string $f) => "SUM(`{$f}`) AS `{$f}`",
                $fields
            );

            $summed = DB::table($table)
                ->whereIn('sprs_submission_id', $submissionIds)
                ->selectRaw(implode(', ', $selects))
                ->first();

            foreach ($fields as $field) {
                // SUM over all-null columns returns null, not 0 — which is
                // what we want. Nobody reported anything, so there is no
                // figure, not a figure of zero.
                $totals[$field] = $summed?->{$field} === null
                    ? null
                    : (int) $summed->{$field};
            }
        }

        $definitions = isset($config['groups'])
            ? collect($config['groups'])->flatMap(function ($group) {
                return collect([['label' => $group['header'], 'header' => true]])
                    ->concat($group['rows']);
            })
            : collect($config['rows'] ?? []);

        return $definitions->map(function ($definition) use ($totals) {
            $isHeader   = ($definition['header'] ?? false) === true;
            $hasField   = isset($definition['field']);
            $isComputed = ! $hasField && isset($definition['sum_of']);

            $value = $hasField ? ($totals[$definition['field']] ?? null) : null;

            if ($isComputed) {
                $parts = collect($definition['sum_of'])
                    ->map(fn ($f) => $totals[$f] ?? null)
                    ->filter(fn ($v) => $v !== null);

                $value = $parts->isEmpty() ? null : (int) $parts->sum();
            }

            return [
                'label'    => is_string($definition['label'] ?? null) ? $definition['label'] : '',
                'value'    => $value,
                'header'   => $isHeader || ($definition['header'] ?? false),
                'computed' => $isComputed,
                'indent'   => ! $isHeader && ! ($definition['header'] ?? false),
            ];
        })->all();
    }

    /**
     * The reason a batch was last sent back, and every earlier one.
     *
     * Rejections apply to the whole month, so one note covers all of
     * them — but it's stored per submission, which means reading any one
     * of them gives the same answer. The trail is kept so a second
     * rejection doesn't hide the reason for the first.
     *
     * @return array{latest: ?object, earlier: Collection<int, object>}
     */
    public function rejectionNotes(Office $office, int $year, int $month): array
    {
        $ids = DB::table('sprs_submissions')
            ->where('current_office_id', $office->id)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->where('status', 'returned')
            ->pluck('id')
            ->all();

        if ($ids === []) {
            return ['latest' => null, 'earlier' => collect()];
        }

        $notes = DB::table('sprs_submission_actions as a')
            ->join('users as u', 'u.id', '=', 'a.actor_id')
            ->leftJoin('offices as o', 'o.id', '=', 'u.office_id')
            ->whereIn('a.sprs_submission_id', $ids)
            ->whereIn('a.action', ['rejected', 'returned'])
            ->whereNotNull('a.note')
            // One note covers the batch, so collapse to one row per
            // round rather than repeating it fifteen times.
            ->groupBy('a.revision', 'a.note', 'u.name', 'o.name', 'a.action')
            ->orderByDesc('a.revision')
            ->select([
                'a.revision',
                'a.note',
                'a.action',
                'u.name as actor_name',
                'o.name as actor_office',
                DB::raw('MAX(a.created_at) as created_at'),
                DB::raw('COUNT(*) as report_count'),
            ])
            ->get();

        return [
            'latest'  => $notes->first(),
            'earlier' => $notes->skip(1)->values(),
        ];
    }

    /**
     * Where this office's month currently stands.
     *
     * Follows the batch rather than recording who it was handed to, so
     * the label keeps up: Digos forwards to the province and reads "with
     * PESO Davao del Sur", the province forwards to FO and Digos now
     * reads "with Davao del Sur Field Office" without doing anything.
     *
     * Reports the furthest-along holder when a month is split across
     * tiers — which happens when a straggler submits after the batch has
     * already gone up.
     *
     * @return array{state: string, holder: ?string, at: ?string, count: int, held: int}
     */
    public function batchStatus(Office $office, int $year, int $month): array
    {
        $ids = $this->listedOfficesFor($office);

        if ($ids === []) {
            return ['state' => 'none', 'holder' => null, 'at' => null, 'count' => 0, 'held' => 0];
        }

        $rows = DB::table('sprs_submissions as s')
            ->leftJoin('offices as h', 'h.id', '=', 's.current_office_id')
            ->whereIn('s.office_id', $ids)
            ->where('s.period_year', $year)
            ->where('s.period_month', $month)
            ->select([
                's.status',
                's.current_office_id',
                'h.name as holder',
                's.submitted_at',
                's.endorsed_at',
                's.approved_at',
            ])
            ->get();

        $claimed = $rows->whereNotIn('status', [null, 'draft']);

        if ($claimed->isEmpty()) {
            return [
                'state'  => 'not_started',
                'holder' => null,
                'at'     => null,
                'count'  => 0,
                'held'   => 0,
            ];
        }

        $held = $claimed->where('current_office_id', $office->id);

        // Anything still in hand outranks anything already sent — the
        // office has work here, and saying "forwarded" while two reports
        // sit unforwarded would be wrong.
        if ($held->isNotEmpty()) {
            return [
                'state'  => 'with_you',
                'holder' => null,
                'at'     => null,
                'count'  => $claimed->count(),
                'held'   => $held->count(),
            ];
        }

        if ($claimed->every(fn ($r) => $r->status === 'approved')) {
            return [
                'state'  => 'approved',
                'holder' => null,
                'at'     => $claimed->max('approved_at'),
                'count'  => $claimed->count(),
                'held'   => 0,
            ];
        }

        if ($claimed->contains(fn ($r) => $r->status === 'returned')) {
            return [
                'state'  => 'returned',
                'holder' => null,
                'at'     => null,
                'count'  => $claimed->count(),
                'held'   => 0,
            ];
        }

        $moved = $claimed->whereNotNull('holder')->first();

        return [
            'state'  => 'forwarded',
            'holder' => $moved?->holder,
            'at'     => $claimed->max('endorsed_at') ?? $claimed->max('submitted_at'),
            'count'  => $claimed->count(),
            'held'   => 0,
        ];
    }

    /**
     * Years the period picker should offer.
     *
     * Driven by what's in the database rather than a fixed window. A
     * hardcoded `now()->year - 2` offers empty years on a fresh install
     * and hides real ones once there are more than three years of
     * history.
     *
     * The current year is always included — you can't file for a period
     * the picker won't show, and the first submission of January has to
     * be reachable before it exists.
     *
     * @return array<int, int>
     */
    public function availableYears(Office $office): array
    {
        $officeIds = $this->filingDescendantsOf($office);
        $officeIds[] = $office->id;

        $years = DB::table('sprs_submissions')
            ->whereIn('office_id', $officeIds)
            ->distinct()
            ->pluck('period_year')
            ->map(fn ($y) => (int) $y)
            ->push((int) now()->year)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        return $years;
    }

    /**
     * Reports currently sitting with this office, waiting to be passed on.
     *
     * The chain cascades, so "waiting for me" can't be read off the status
     * alone — a report at `submitted` might be with a PESO, the province
     * or FO. current_office_id says which.
     *
     * @return Collection<int, object>
     */
    public function heldBy(Office $office, int $year, int $month): Collection
    {
        $rows = DB::table('sprs_submissions as s')
            ->join('offices as o', 'o.id', '=', 's.office_id')
            ->leftJoin('offices as p', 'p.id', '=', 'o.parent_office_id')
            ->leftJoin('role_types as r', 'r.id', '=', 'o.office_type')
            ->where('s.current_office_id', $office->id)
            ->where('s.period_year', $year)
            ->where('s.period_month', $month)
            ->whereIn('s.status', ['submitted', 'endorsed_to_ro', 'returned'])
            ->select([
                's.id',
                's.office_id',
                's.status',
                'o.name as office_name',
                'o.parent_office_id',
                'p.name as parent_name',
                'r.name as role_name',
            ])
            ->get();

        // Nested, not flat. Listing a JPO next to the PESO it reports
        // through reads as four peer offices, when really one of them
        // sits under another — which matters when someone is checking
        // whether the batch is complete.
        $depths = $this->depthsUnder($office);

        foreach ($rows as $row) {
            $row->depth = $depths[$row->office_id] ?? 0;
        }

        return $this->nest($rows, $office->id);
    }

    /**
     * Order rows so each office is followed by the offices beneath it.
     *
     * @param  Collection<int, object>  $rows
     * @return Collection<int, object>
     */
    private function nest(Collection $rows, int $rootId): Collection
    {
        $byParent = $rows->groupBy('parent_office_id');
        $ordered  = collect();

        // The forwarding office's own report leads, then each branch in
        // full before the next one starts.
        $own = $rows->firstWhere('office_id', $rootId);

        if ($own) {
            $ordered->push($own);
        }

        $walk = function (int $parentId) use (&$walk, $byParent, $ordered, $rootId) {
            foreach ($byParent->get($parentId, collect())->sortBy('office_name') as $row) {
                if ((int) $row->office_id === $rootId) {
                    continue;
                }

                $ordered->push($row);
                $walk((int) $row->office_id);
            }
        };

        $walk($rootId);

        // Anything whose parent isn't in the batch — an office FO filed
        // for, which skipped the tiers between on its way up.
        $seen = $ordered->pluck('office_id')->all();

        foreach ($rows as $row) {
            if (! in_array($row->office_id, $seen, true)) {
                $ordered->push($row);
            }
        }

        return $ordered;
    }

    /**
     * How many tabs hold at least one value, per submission.
     *
     * One query per section rather than one per submission — fifteen
     * offices across eight tabs would otherwise be 120 round trips.
     *
     * @param  array<int, int>  $submissionIds
     * @return array<int, int>
     */
    private function filledTabCounts(array $submissionIds): array
    {
        if ($submissionIds === []) {
            return [];
        }

        $counts = [];

        foreach (config('sprs_sections') as $config) {
            $table = (new $config['model'])->getTable();

            if ($config['dynamic'] ?? false) {
                // A repeating tab counts as filled if it has any rows.
                $ids = DB::table($table)
                    ->whereIn('sprs_submission_id', $submissionIds)
                    ->distinct()
                    ->pluck('sprs_submission_id');
            } else {
                $fields = $this->fieldList($config);
                $names  = array_column($fields, 'field');

                if ($names === []) {
                    continue;
                }

                $ids = DB::table($table)
                    ->whereIn('sprs_submission_id', $submissionIds)
                    ->where(function ($q) use ($names) {
                        foreach ($names as $field) {
                            $q->orWhereNotNull($field);
                        }
                    })
                    ->pluck('sprs_submission_id');
            }

            foreach ($ids as $id) {
                $counts[$id] = ($counts[$id] ?? 0) + 1;
            }
        }

        return $counts;
    }

    /** @return array<int, string> */
    private function persistedSectionKeys(): array
    {
        return array_keys(config('sprs_sections'));
    }

    /**
     * How many offices under $root have filed, by status.
     *
     * @return array{total:int, counted:int, draft:int, missing:int, by_status:array<string,int>}
     */
    public function completion(Office $root, int $year, int $month): array
    {
        // The same offices the compilation list shows: direct reports,
        // plus this office's own report when it files one.
        //
        // Leaving the root out gave "0 of 14" above a list of fifteen
        // rows, with the combined panel below it showing figures the
        // count said didn't exist.
        $ids = $this->listedOfficesFor($root);

        $byStatus = DB::table('sprs_submissions')
            ->whereIn('office_id', $ids)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->groupBy('status')
            ->pluck(DB::raw('COUNT(*)'), 'status')
            ->map(fn ($n) => (int) $n)
            ->all();

        $counted = 0;

        foreach (self::COUNTED as $status) {
            $counted += $byStatus[$status] ?? 0;
        }

        return [
            'total'     => count($ids),
            'counted'   => $counted,
            'draft'     => $byStatus['draft'] ?? 0,
            'missing'   => count($ids) - array_sum($byStatus),
            'by_status' => $byStatus,
        ];
    }

    /**
     * Offices under $root that haven't filed for this period.
     *
     * Draft and not-started are separated because they mean different
     * things — one office opened the form and stalled, the other never
     * started. The first needs a nudge, the second may not know it's
     * due.
     *
     * Starts from offices and joins outward, so an office with no
     * submission row still appears. Query the submissions table instead
     * and the ones you most want to see simply aren't there.
     *
     * @return Collection<int, object>
     */
    public function outstanding(Office $root, int $year, int $month): Collection
    {
        return DB::table('offices as o')
            ->leftJoin('sprs_submissions as s', function ($join) use ($year, $month) {
                $join->on('s.office_id', '=', 'o.id')
                    ->where('s.period_year', '=', $year)
                    ->where('s.period_month', '=', $month);
            })
            ->leftJoin('role_types as r', 'r.id', '=', 'o.office_type')
            ->whereIn('o.id', $this->listedOfficesFor($root) ?: [0])
            // Anything not yet claimed by the office. Draft counts as
            // outstanding — numbers typed into a form nobody submitted
            // are not a report.
            ->where(function ($q) {
                $q->whereNull('s.status')->orWhere('s.status', 'draft');
            })
            ->orderByDesc('r.hierarchy_level')
            ->orderBy('o.name')
            ->select([
                'o.id',
                'o.name',
                'r.name as role_name',
                DB::raw("COALESCE(s.status, 'not_started') as status"),
            ])
            ->get();
    }

    /**
     * Offices directly below this one that don't file themselves.
     *
     * RO's children are field offices: they review rather than report, so
     * they never appear in a list built from submissions. Listing the
     * fifteen PESOs beneath them instead was the wrong unit — RO wants to
     * know Davao del Sur hasn't sent theirs, and to click through for
     * which of its offices is holding it up.
     *
     * @return array<int, int>
     */
    public function nonFilingChildrenOf(Office $root): array
    {
        return DB::table('offices as o')
            ->join('role_types as r', 'r.id', '=', 'o.office_type')
            ->where('o.parent_office_id', $root->id)
            ->whereNotIn('r.hierarchy_level', [1, 2, 3])
            ->pluck('o.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * One row per field office, for RO.
     *
     * RO thinks in field offices, not in the twenty-one PESOs beneath
     * them — "Davao del Sur is done, Davao City hasn't sent theirs yet".
     * A flat list of every office is the wrong unit at this level, and
     * approval happens per field office anyway, because the six won't all
     * endorse on the same day.
     *
     * Six small queries rather than one clever one. At this size the
     * clever version costs more to read than it saves to run.
     *
     * @return Collection<int, object>
     */
    public function fieldOfficeSummaries(Office $root, int $year, int $month): Collection
    {
        return Office::whereIn('id', $this->nonFilingChildrenOf($root))
            ->orderBy('name')
            ->get()
            ->map(function (Office $fieldOffice) use ($year, $month) {
                $officeIds = $this->filingDescendantsOf($fieldOffice);

                $byStatus = DB::table('sprs_submissions')
                    ->whereIn('office_id', $officeIds ?: [0])
                    ->where('period_year', $year)
                    ->where('period_month', $month)
                    ->groupBy('status')
                    ->pluck(DB::raw('COUNT(*)'), 'status')
                    ->map(fn ($n) => (int) $n)
                    ->all();

                $awaiting = $byStatus['endorsed_to_ro'] ?? 0;
                $approved = $byStatus['approved'] ?? 0;
                $withFo   = ($byStatus['submitted'] ?? 0) + ($byStatus['rejected'] ?? 0);
                $drafts   = $byStatus['draft'] ?? 0;
                $total    = count($officeIds);

                return (object) [
                    'id'          => $fieldOffice->id,
                    'name'        => $fieldOffice->name,
                    'total'       => $total,
                    'awaiting'    => $awaiting,
                    'approved'    => $approved,
                    'with_fo'     => $withFo,
                    'drafts'      => $drafts,
                    'not_started' => $total - array_sum($byStatus),
                    // What RO needs to know at a glance. Ordered by
                    // urgency: anything waiting outranks anything settled.
                    'state'       => match (true) {
                        $awaiting > 0            => 'awaiting',
                        $approved > 0 && $withFo === 0 && $drafts === 0 => 'approved',
                        $approved > 0            => 'partly_approved',
                        $withFo > 0              => 'with_fo',
                        $total === 0             => 'no_offices',
                        default                  => 'nothing',
                    },
                ];
            });
    }

    /**
     * Offices reporting directly to $root, and only those.
     *
     * The compilation list is one tier, not the whole subtree. PESO Digos
     * City compiles its nine municipalities and five JPOs; what sits under
     * PESO Bansalan is Bansalan's business until Digos opens that card and
     * asks for the breakdown.
     *
     * Using the full subtree put UM Bansalan College in Digos's list as
     * though it reported there, which is both wrong and unhelpful — an
     * office three tiers down would appear alongside direct reports with
     * nothing to say which was which.
     *
     * @return array<int, int>
     */
    private function directChildrenOf(Office $root): array
    {
        return DB::table('offices as o')
            ->join('role_types as r', 'r.id', '=', 'o.office_type')
            ->where('o.parent_office_id', $root->id)
            ->whereIn('r.hierarchy_level', [1, 2, 3])
            ->pluck('o.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Exactly the offices the compilation list shows: direct reports,
     * plus this office's own report when it files one.
     *
     * Every count on the page has to agree with the rows beneath it.
     *
     * @return array<int, int>
     */
    private function listedOfficesFor(Office $root): array
    {
        $ids   = $this->directChildrenOf($root);
        $level = $root->officeType?->hierarchy_level;

        if ($level !== null && $level <= 3) {
            $ids[] = $root->id;
        }

        return $ids;
    }

    /**
     * Descendants that actually file a report — levels 1 to 3.
     *
     * FO and RO review; they have no submission of their own. Counting
     * them made RO's totals read "21 offices" when only 15 can file, and
     * put six field offices in a list of who hasn't submitted, where they
     * would have sat forever.
     *
     * @return array<int, int>
     */
    private function filingDescendantsOf(Office $root): array
    {
        $ids = $this->descendantsOf($root);

        if ($ids === []) {
            return [];
        }

        return DB::table('offices as o')
            ->join('role_types as r', 'r.id', '=', 'o.office_type')
            ->whereIn('o.id', $ids)
            ->whereIn('r.hierarchy_level', [1, 2, 3])
            ->pluck('o.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * How far each office sits below $root, for indentation.
     *
     * Walks the tree rather than subtracting hierarchy_level, because a
     * level no longer implies a depth: municipalities report to the city
     * and both are level 2, so the arithmetic version gave every
     * municipality a depth of 0 and flattened the list.
     *
     * @return array<int, int> office id => depth
     */
    private function depthsUnder(Office $root): array
    {
        $ids = $this->descendantsOf($root);

        if ($ids === []) {
            return [$root->id => 0];
        }

        $parents = DB::table('offices')
            ->whereIn('id', $ids)
            ->pluck('parent_office_id', 'id')
            ->all();

        $depths = [$root->id => 0];

        foreach ($ids as $id) {
            $chain = [];
            $node  = $id;

            // Climb to the root, then fill in everything on the way — so
            // a deep branch costs one walk, not one per office.
            while ($node !== null && ! isset($depths[$node])) {
                $chain[] = $node;
                $node = $parents[$node] ?? null;
            }

            $depth = $depths[$node] ?? 0;

            foreach (array_reverse($chain) as $step) {
                $depths[$step] = ++$depth;
            }
        }

        return $depths;
    }

    /** @return array<int, int> */
    private function descendantsOf(Office $root): array
    {
        return $root->descendantIds(includeSelf: false);
    }

    /** @return array<int, array{field:string, label:string, kind:string, total:bool}> */
    private function fieldList(array $config): array
    {
        $rows = isset($config['groups'])
            ? collect($config['groups'])->flatMap(fn ($g) => $g['rows'])
            : collect($config['rows'] ?? []);

        // Skips rows with no `field` — section headings and figures
        // computed from other rows. Neither has a column to sum across
        // offices, and reading $r['field'] on them threw "Undefined array
        // key field".
        return $rows
            ->filter(fn ($r) => isset($r['field']))
            ->map(fn ($r) => [
                'field' => $r['field'],
                'label' => $r['label'],
                'kind'  => $r['kind'] ?? 'number',
            ])
            ->values()
            ->all();
    }
}