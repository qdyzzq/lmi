<?php

namespace App\Http\Controllers\Module6;

use App\Http\Controllers\Controller;
use App\Models\Module6\WeeklyIssue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WeeklyIssueController extends Controller
{
    private const MONTHS = [
        'January'   => 1,  'February'  => 2,  'March'    => 3,
        'April'     => 4,  'May'        => 5,  'June'     => 6,
        'July'      => 7,  'August'     => 8,  'September'=> 9,
        'October'   => 10, 'November'   => 11, 'December' => 12,
    ];

    // ── Admin page data helper ────────────────────────────────────────────────

    /**
     * Returns all weekly issues grouped by year then month for the admin blade.
     * Shape: [ year => [ month => [ ...issues ] ] ]
     */
    public static function buildAdminWeeklyData(): array
    {
        $years = WeeklyIssue::select('year')->distinct()->orderByDesc('year')->pluck('year')->toArray();

        $data = [];
        foreach ($years as $year) {
            $issues = WeeklyIssue::forYear($year)
                ->orderBy('month_order')
                ->orderBy('week_number')
                ->get()
                ->map(fn($i) => $i->toFrontendArray())
                ->values()
                ->toArray();

            $data[$year] = $issues;
        }

        return [
            'years'       => $years,
            'issuesByYear'=> $data,
        ];
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────

    /**
     * POST /admin/lmi-weekly
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year'        => ['required', 'integer', 'min:2000', 'max:2100'],
            'month'       => ['required', 'string', 'in:' . implode(',', array_keys(self::MONTHS))],
            'week_number' => ['required', 'integer', 'min:1', 'max:5'],
            'date_range'  => ['nullable', 'string', 'max:60'],
            'image'       => ['required', 'image', 'max:5120'], // 5 MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('weekly-issues', 'public');
        }

        $issue = WeeklyIssue::create([
            'year'        => $validated['year'],
            'month'       => $validated['month'],
            'month_order' => self::MONTHS[$validated['month']],
            'week_number' => $validated['week_number'],
            'date_range'  => $validated['date_range'] ?? null,
            'image_path'  => $imagePath,
        ]);

        return response()->json(['success' => true, 'issue' => $issue->toFrontendArray()]);
    }

    /**
     * POST /admin/lmi-weekly/{id}  (multipart can't use PUT/PATCH directly)
     * Only image is editable after creation.
     */
public function update(Request $request, int $id): JsonResponse
{
    $issue = WeeklyIssue::findOrFail($id);

    $validated = $request->validate([
        'year'        => ['sometimes', 'integer', 'min:2000', 'max:2100'],
        'month'       => ['sometimes', 'string', 'in:' . implode(',', array_keys(self::MONTHS))],
        'week_number' => ['sometimes', 'integer', 'min:1', 'max:5'],
        'date_range'  => ['nullable', 'string', 'max:60'],
        'image'       => ['nullable', 'image', 'max:5120'],
    ]);

    if (isset($validated['year']))        $issue->year        = $validated['year'];
    if (isset($validated['month']))       { $issue->month = $validated['month']; $issue->month_order = self::MONTHS[$validated['month']]; }
    if (isset($validated['week_number'])) $issue->week_number = $validated['week_number'];
    if (array_key_exists('date_range', $validated)) $issue->date_range = $validated['date_range'];

    if ($request->hasFile('image')) {
        if ($issue->image_path) Storage::disk('public')->delete($issue->image_path);
        $issue->image_path = $request->file('image')->store('weekly-issues', 'public');
    }

    $issue->save();

    return response()->json(['success' => true, 'issue' => $issue->toFrontendArray()]);
}

    /**
     * DELETE /admin/lmi-weekly/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $issue = WeeklyIssue::findOrFail($id);

        if ($issue->image_path) {
            Storage::disk('public')->delete($issue->image_path);
        }

        $issue->delete();

        return response()->json(['success' => true]);
    }
    public function data(): JsonResponse
{
    return response()->json(self::buildAdminWeeklyData());
}
}