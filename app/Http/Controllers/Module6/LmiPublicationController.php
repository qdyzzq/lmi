<?php

namespace App\Http\Controllers\Module6;

use App\Http\Controllers\Controller;
use App\Models\Module6\PublicationIssue;
use App\Models\Module6\PublicationGroupPublish;
use App\Models\Module6\WeeklyIssue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LmiPublicationController extends Controller
{
    // Allowed areas for annual publications (must match the admin dropdown)
    private const AREAS = 'Regional,Davao City,Davao de Oro,Davao del Norte,Davao del Sur,Davao Occidental,Davao Oriental,National';

    const GROUPS = [
        'jlmf' => [
            'id'          => 'jlmf',
            'title'       => 'Jobs and Labor Market Forecast',
            'description' => "Information on Key Growth Sectors, Emerging Industries, In Demand Occupations, and action agendas for industry gaps.",
            'year_type'   => 'range',
            'badge'       => 'Annual',
            'color'       => 'bg-blue-900',
        ],
        'lmp' => [
            'id'          => 'lmp',
            'title'       => 'Labor Market Profile',
            'description' => "Comprehensive demographic and economic landscape analysis. Ideal for policy makers and investors seeking regional depth.",
            'year_type'   => 'single',
            'badge'       => 'Annual',
            'color'       => 'bg-red-900',
        ],
        'lmu' => [
            'id'          => 'lmu',
            'title'       => 'Labor Market Updates: Regional Skills Profiles',
            'description' => "Annual publication providing labor market information based on data from the PESO Employment Information System (PEIS).",
            'year_type'   => 'single',
            'badge'       => 'Annual',
            'color'       => 'bg-[#8B6B5A]',
        ],
    ];

    // ── PUBLIC PAGE ───────────────────────────────────────────────────────────

    public function publicIndex()
    {
        $groupData = $this->buildPublicGroupData();

        // Weekly: one card per area (Regional / National). Only PUBLISHED cards are shown,
        // and their issues come from each card's published snapshot, not the live DB.
        $cards = [
            'Regional' => \App\Models\Module6\WeeklyCardSetting::instance('Regional'),
            // National is not created from the public page — only shown once it exists and is published
            'National' => \App\Models\Module6\WeeklyCardSetting::where('area', 'National')->first(),
        ];

        $weeklyCards    = [];
        $weeklySnapshot = [];
        foreach ($cards as $area => $card) {
            if (!$card || !$card->is_published) continue;

            $weeklyCards[$area] = $card->toFrontendArray();

            foreach ($card->published_snapshot ?? [] as $issue) {
                $issue['area']    = $area;   // older snapshots have no area → they belong to this card
                $weeklySnapshot[] = $issue;
            }
        }

        $weeklyData        = $this->groupSnapshotIssues($weeklySnapshot);
        $weeklyCardSetting = $weeklyCards['Regional'] ?? ($weeklyCards['National'] ?? ['is_published' => false]);

        return view('Public.Module6.publication', compact('groupData', 'weeklyData', 'weeklyCardSetting', 'weeklyCards'));
    }

    // ── ADMIN PAGE ────────────────────────────────────────────────────────────

   public function adminIndex()
    {
        $groupData          = $this->buildAdminGroupData();
        $weeklyData         = $this->buildWeeklyData('Regional');   // weekly editor opens on Regional
        $weeklyCardSetting  = \App\Models\Module6\WeeklyCardSetting::instance('Regional')->toFrontendArray();

        return view('admin.Module6.publication-editor', compact('groupData', 'weeklyData', 'weeklyCardSetting'));
    }

    // ── PREVIEW ───────────────────────────────────────────────────────────────

    public function preview()
    {
        $groupData   = $this->buildAdminGroupData();
        $weeklyData  = $this->buildWeeklyData();   // all areas, each issue carries its area

        // Preview shows every weekly card (published or not); National only if it exists
        $weeklyCards = [
            'Regional' => \App\Models\Module6\WeeklyCardSetting::instance('Regional')->toFrontendArray(),
        ];
        $national = \App\Models\Module6\WeeklyCardSetting::where('area', 'National')->first();
        if ($national) {
            $weeklyCards['National'] = $national->toFrontendArray();
        }
        foreach ($weeklyCards as $area => $card) {
            $weeklyCards[$area]['is_published'] = true;   // force visible in preview
        }
        $weeklyCardSetting = $weeklyCards['Regional'];

        return view('Public.Module6.publication', compact('groupData', 'weeklyData', 'weeklyCardSetting', 'weeklyCards'));
    }

    // ── JSON DATA ENDPOINTS ───────────────────────────────────────────────────

    public function data(): JsonResponse
    {
        return response()->json($this->buildAdminGroupData());
    }

    public function weeklyData(): JsonResponse
    {
        return response()->json($this->buildWeeklyData());
    }

    // ── PUBLISH / UNPUBLISH / REPUBLISH ──────────────────────────────────────

    public function togglePublish(string $groupId): JsonResponse
    {
        if (!array_key_exists($groupId, self::GROUPS)) {
            return response()->json(['success' => false, 'message' => 'Invalid group.'], 422);
        }

        $pub = PublicationGroupPublish::forGroup($groupId);

        if ($pub->is_published) {
            // Unpublish
            $pub->update(['is_published' => false]);
        } else {
            // Publish — take a snapshot of current issues
            $issues = PublicationIssue::where('group_id', $groupId)
                ->orderBy('sort_order')->orderByDesc('year')->get()
                ->map(fn($i) => $this->issueToArray($i))->values()->toArray();

            $pub->update([
                'is_published'       => true,
                'has_draft_changes'  => false,
                'published_at'       => now(),
                'published_snapshot' => $issues,
            ]);
        }

        return response()->json([
            'success'   => true,
            'groupData' => $this->buildAdminGroupData(),
        ]);
    }

    public function republish(string $groupId): JsonResponse
    {
        if (!array_key_exists($groupId, self::GROUPS)) {
            return response()->json(['success' => false, 'message' => 'Invalid group.'], 422);
        }

        $pub = PublicationGroupPublish::forGroup($groupId);

        $issues = PublicationIssue::where('group_id', $groupId)
            ->orderBy('sort_order')->orderByDesc('year')->get()
            ->map(fn($i) => $this->issueToArray($i))->values()->toArray();

        $pub->update([
            'is_published'       => true,
            'has_draft_changes'  => false,
            'published_at'       => now(),
            'published_snapshot' => $issues,
        ]);

        return response()->json([
            'success'   => true,
            'groupData' => $this->buildAdminGroupData(),
        ]);
    }

    // ── CRUD — ANNUAL ISSUES ──────────────────────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $groupId = $request->input('group_id');

        if (!array_key_exists($groupId, self::GROUPS)) {
            return response()->json(['success' => false, 'message' => 'Invalid group.'], 422);
        }

        $group    = self::GROUPS[$groupId];
        $yearRule = $group['year_type'] === 'range'
            ? ['required', 'string', 'max:20', 'regex:/^\d{4}-\d{4}$/']
            : ['required', 'string', 'max:10', 'regex:/^\d{4}$/'];

        $validated = $request->validate([
            'group_id'    => ['required', 'string', 'in:lmp,jlmf,lmu'],
            'area'        => ['nullable', 'string', 'in:' . self::AREAS],
            'title'       => ['required', 'string', 'max:255'],
            'subtitle'    => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'year'        => $yearRule,
            'drive_url'   => ['required', 'string', 'max:500'],
        ]);

        $driveFileId = PublicationIssue::extractDriveId($validated['drive_url']);

        $issue = PublicationIssue::create([
            'group_id'      => $validated['group_id'],
            'area'          => $validated['area'] ?? 'Regional',
            'title'         => $validated['title'],
            'subtitle'      => $validated['subtitle'],
            'description'   => $validated['description'],
            'year'          => $validated['year'],
            'drive_file_id' => $driveFileId,
            'sort_order'    => PublicationIssue::where('group_id', $groupId)->max('sort_order') + 1,
        ]);

        // Mark as having unpublished changes
        PublicationGroupPublish::forGroup($groupId)->update(['has_draft_changes' => true]);

        return response()->json([
            'success'   => true,
            'issue'     => $this->issueToArray($issue),
            'groupData' => $this->buildAdminGroupData(),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $issue   = PublicationIssue::findOrFail($id);
        $groupId = $issue->group_id;
        $group   = self::GROUPS[$groupId];

        $yearRule = $group['year_type'] === 'range'
            ? ['required', 'string', 'max:20', 'regex:/^\d{4}-\d{4}$/']
            : ['required', 'string', 'max:10', 'regex:/^\d{4}$/'];

        $validated = $request->validate([
            'area'        => ['nullable', 'string', 'in:' . self::AREAS],
            'title'       => ['required', 'string', 'max:255'],
            'subtitle'    => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'year'        => $yearRule,
            'drive_url'   => ['required', 'string', 'max:500'],
        ]);

        $issue->update([
            'area'          => $validated['area'] ?? $issue->area ?? 'Regional',
            'title'         => $validated['title'],
            'subtitle'      => $validated['subtitle'],
            'description'   => $validated['description'],
            'year'          => $validated['year'],
            'drive_file_id' => PublicationIssue::extractDriveId($validated['drive_url']),
        ]);

        // Mark as having unpublished changes
        PublicationGroupPublish::forGroup($groupId)->update(['has_draft_changes' => true]);

        return response()->json([
            'success'   => true,
            'issue'     => $this->issueToArray($issue->fresh()),
            'groupData' => $this->buildAdminGroupData(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $issue   = PublicationIssue::findOrFail($id);
        $groupId = $issue->group_id;
        $issue->delete();

        // Mark as having unpublished changes
        PublicationGroupPublish::forGroup($groupId)->update(['has_draft_changes' => true]);

        return response()->json([
            'success'   => true,
            'groupData' => $this->buildAdminGroupData(),
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Admin sees live issues + publish status per group.
     */
    private function buildAdminGroupData(): array
    {
        $groupData = [];

        foreach (self::GROUPS as $id => $group) {
            $pub = PublicationGroupPublish::forGroup($id);

            $issues = PublicationIssue::where('group_id', $id)
                ->orderBy('sort_order')->orderByDesc('year')->get()
                ->map(fn($i) => $this->issueToArray($i))->values()->toArray();

            $groupData[$id] = array_merge($group, [
                'issues'           => $issues,
                'is_published'     => $pub->is_published,
                'has_draft_changes'=> $pub->has_draft_changes,
                'published_at'     => $pub->published_at?->toIso8601String(),
            ]);
        }

        return $groupData;
    }

    /**
     * Public sees only published snapshots for published groups.
     */
    private function buildPublicGroupData(): array
    {
        $groupData = [];

        foreach (self::GROUPS as $id => $group) {
            $pub = PublicationGroupPublish::forGroup($id);

            // Only include groups that are published
            if (!$pub->is_published) {
                continue;
            }

            $issues = $pub->published_snapshot ?? [];

            $groupData[$id] = array_merge($group, [
                'issues'           => $issues,
                'is_published'     => true,
                'has_draft_changes'=> false,
            ]);
        }

        return $groupData;
    }

    private function buildWeeklyData(?string $area = null): array
    {
        $years = WeeklyIssue::when($area, fn($q) => $q->where('area', $area))
            ->select('year')->distinct()->orderByDesc('year')->pluck('year')->toArray();

        $issuesByYear = [];
        foreach ($years as $year) {
            $issuesByYear[$year] = WeeklyIssue::where('year', $year)
                ->when($area, fn($q) => $q->where('area', $area))
                ->orderBy('month_order')->orderBy('week_number')->get()
                ->map(fn($i) => $i->toFrontendArray())->values()->toArray();
        }

        return ['years' => $years, 'issuesByYear' => $issuesByYear];
    }

    private function issueToArray(PublicationIssue $issue): array
    {
        return [
            'id'            => $issue->id,
            'area'          => $issue->area ?? 'Regional',
            'title'         => $issue->title,
            'subtitle'      => $issue->subtitle,
            'description'   => $issue->description,
            'year'          => $issue->year,
            'drive_file_id' => $issue->drive_file_id,
            'thumbnail_url' => $issue->driveThumbnailUrl(),
            'view_url'      => $issue->driveViewUrl(),
            'sort_order'    => $issue->sort_order,
        ];
    }

    /**
     * Reshapes the flat WeeklyCardSetting published_snapshot into
     * ['years' => [...], 'issuesByYear' => [ year => [...issues] ]]
     * — the same shape buildWeeklyData() produces, so the blade works identically.
     */
    private function groupSnapshotIssues(array $snapshot): array
    {
        $years        = [];
        $issuesByYear = [];

        foreach ($snapshot as $issue) {
            $year = $issue['year'] ?? null;
            if (!$year) continue;

            if (!in_array($year, $years, true)) {
                $years[]             = $year;
                $issuesByYear[$year] = [];
            }

            $issuesByYear[$year][] = $issue;
        }

        rsort($years); // descending, same as buildWeeklyData()

        return ['years' => $years, 'issuesByYear' => $issuesByYear];
    }
}
