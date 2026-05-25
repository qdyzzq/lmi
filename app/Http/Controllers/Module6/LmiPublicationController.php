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
        // Public only sees published snapshots
        $groupData  = $this->buildPublicGroupData();
        $weeklyData = $this->buildWeeklyData();

        return view('Public.Module6.publication', compact('groupData', 'weeklyData'));
    }

    // ── ADMIN PAGE ────────────────────────────────────────────────────────────

    public function adminIndex()
    {
        // Admin sees live draft data + publish status
        $groupData  = $this->buildAdminGroupData();
        $weeklyData = $this->buildWeeklyData();
        $weeklyCardImagePath = null; // set if needed

        return view('admin.Module6.publication-editor', compact('groupData', 'weeklyData', 'weeklyCardImagePath'));
    }

    // ── PREVIEW ───────────────────────────────────────────────────────────────

    public function preview()
    {
        $groupData  = $this->buildAdminGroupData();
        $weeklyData = $this->buildWeeklyData();

        return view('Public.Module6.publication', compact('groupData', 'weeklyData'));
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
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'year'        => $yearRule,
            'drive_url'   => ['required', 'string', 'max:500'],
        ]);

        $driveFileId = PublicationIssue::extractDriveId($validated['drive_url']);

        $issue = PublicationIssue::create([
            'group_id'      => $validated['group_id'],
            'title'         => $validated['title'],
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
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'year'        => $yearRule,
            'drive_url'   => ['required', 'string', 'max:500'],
        ]);

        $issue->update([
            'title'         => $validated['title'],
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

    private function buildWeeklyData(): array
    {
        $years = WeeklyIssue::select('year')->distinct()->orderByDesc('year')->pluck('year')->toArray();

        $issuesByYear = [];
        foreach ($years as $year) {
            $issuesByYear[$year] = WeeklyIssue::where('year', $year)
                ->orderBy('month_order')->orderBy('week_number')->get()
                ->map(fn($i) => $i->toFrontendArray())->values()->toArray();
        }

        return ['years' => $years, 'issuesByYear' => $issuesByYear];
    }

    private function issueToArray(PublicationIssue $issue): array
    {
        return [
            'id'            => $issue->id,
            'title'         => $issue->title,
            'description'   => $issue->description,
            'year'          => $issue->year,
            'drive_file_id' => $issue->drive_file_id,
            'thumbnail_url' => $issue->driveThumbnailUrl(),
            'view_url'      => $issue->driveViewUrl(),
            'sort_order'    => $issue->sort_order,
        ];
    }
}