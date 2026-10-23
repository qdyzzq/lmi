<?php

namespace App\Http\Controllers\Module6;

use App\Http\Controllers\Controller;
use App\Models\Module6\WeeklyCardSetting;
use App\Models\Module6\WeeklyIssue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WeeklyCardSettingController extends Controller
{
    /**
     * Which card the request is for: 'Regional' (default) or 'National'.
     * Read from the query string or the form body.
     */
    private function area(Request $request): string
    {
        return $request->input('area') === 'National' ? 'National' : 'Regional';
    }
    /**
     * GET /admin/lmi-weekly-card
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => WeeklyCardSetting::instance($this->area($request))->toFrontendArray(),
        ]);
    }

    /**
     * POST /admin/lmi-weekly-card/text
     */
    public function updateText(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => ['nullable', 'string', 'max:100'],
            'subtitle'    => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $setting = WeeklyCardSetting::instance($this->area($request));
        $setting->title             = $validated['title']       ?? $setting->title;
        $setting->subtitle          = $validated['subtitle']    ?? $setting->subtitle;
        $setting->description       = $validated['description'] ?? $setting->description;
        $setting->has_draft_changes = true; // ← mark as dirty
        $setting->save();

        return response()->json([
            'success' => true,
            'data'    => $setting->toFrontendArray(),
        ]);
    }

    /**
     * POST /admin/lmi-weekly-card/media
     */
    public function updateMedia(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image'    => ['nullable', 'image', 'max:5120'],
            'link_url' => ['nullable', 'url', 'max:500'],
        ]);

        $setting = WeeklyCardSetting::instance($this->area($request));

        if ($request->hasFile('image')) {
            if ($setting->image_path) {
                Storage::disk('public')->delete($setting->image_path);
            }
            $setting->image_path = $request->file('image')
                ->storeAs('weekly-card', uniqid() . '.' . $request->file('image')->getClientOriginalExtension(), 'public');
        }

        if (array_key_exists('link_url', $validated)) {
            $setting->link_url = $validated['link_url'];
        }

        $setting->has_draft_changes = true; // ← mark as dirty
        $setting->save();

        return response()->json([
            'success' => true,
            'data'    => $setting->toFrontendArray(),
        ]);
    }

    /**
     * PATCH /admin/lmi-weekly/toggle-publish
     */
    public function togglePublish(Request $request): JsonResponse
    {
        $area    = $this->area($request);
        $setting = WeeklyCardSetting::instance($area);
        $setting->is_published = !$setting->is_published;

        if ($setting->is_published) {
            $setting->published_snapshot = $this->buildSnapshot($area);
            $setting->has_draft_changes  = false;
            $setting->published_at       = now();
        }

        $setting->save();

        return response()->json([
            'success'      => true,
            'publishState' => [
                'is_published'      => $setting->is_published,
                'has_draft_changes' => $setting->has_draft_changes,
            ],
        ]);
    }

    /**
     * PATCH /admin/lmi-weekly/republish
     */
    public function republish(Request $request): JsonResponse
    {
        $area    = $this->area($request);
        $setting = WeeklyCardSetting::instance($area);
        $setting->is_published       = true;
        $setting->published_snapshot = $this->buildSnapshot($area);
        $setting->has_draft_changes  = false;
        $setting->published_at       = now();
        $setting->save();

        return response()->json([
            'success'      => true,
            'publishState' => [
                'is_published'      => true,
                'has_draft_changes' => false,
            ],
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Snapshot all weekly issues at publish time.
     */
    private function buildSnapshot(string $area = 'Regional'): array
    {
        return WeeklyIssue::where('area', $area)
            ->orderByDesc('year')
            ->orderBy('month_order')
            ->orderBy('week_number')
            ->get()
            ->map(fn($i) => $i->toFrontendArray())
            ->values()
            ->toArray();
    }
}