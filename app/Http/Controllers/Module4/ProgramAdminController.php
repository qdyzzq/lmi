<?php

namespace App\Http\Controllers\Module4;

use App\Models\Module4\Program;
use App\Models\Module4\ProgramQualification;
use App\Models\Module4\ProgramHowToApply;
use App\Models\Module4\ProgramStory;
use App\Models\Module4\ProgramTestimonial;
use App\Models\Module4\CarouselSlide;
use App\Models\Module4\CtaSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProgramAdminController extends Controller
{
    // ===== HELPER: store uploaded image directly (browser handles WebP conversion) =====
    private function storeImage($file, string $folder, string $disk = 'public_images'): string
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        if ($disk === 'public_images') {
            Storage::disk('public_images')->put($folder . '/' . $filename, file_get_contents($file));
            return $folder . '/' . $filename;
        }

        Storage::disk($disk)->put($folder . '/' . $filename, file_get_contents($file));
        return $folder . '/' . $filename;
    }

    // ===== INDEX (Admin Editor Page) =====
    public function index(): View
    {
        $programs = Program::with([
            'qualifications',
            'howToApply',
            'stories',
            'testimonials'
        ])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        $carouselSlides = CarouselSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $qualificationTypes = ProgramQualification::distinct()
            ->pluck('type')
            ->sort()
            ->values();

        $ctaSection = CtaSection::first();

        $directoryHasDraft = Program::where('is_active', true)
            ->where('is_published', true)
            ->where('has_draft_changes', true)
            ->exists();

        $directoryChangelog = [];

        return view('admin.Module4.programStories_editor', compact(
            'programs',
            'carouselSlides',
            'qualificationTypes',
            'ctaSection',
            'directoryHasDraft',
            'directoryChangelog'
        ));
    }

    // ===== DRAFT PREVIEW (Admin only — shows live draft data on the public blade) =====
    public function preview(): View
    {
        $programs = Program::with([
            'qualifications',
            'howToApply',
            'stories',
            'testimonials'
        ])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        $carouselSlides = CarouselSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $isPreview = true;

        $ctaSection = CtaSection::first();

        return view('Public.Module4.programStories', compact('programs', 'carouselSlides', 'isPreview', 'ctaSection'));
    }

    // ===== PROGRAMS =====
    public function storeProgram(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string',
            'acronym'     => 'nullable|string|max:50',
            'subtitle'    => 'nullable|string',
            'description' => 'required|string',
            'color'       => 'required|string',
            'logo' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = 'images/' . $this->storeImage($request->file('logo'), 'logo-programs');
        }

        $data['sort_order'] = Program::max('sort_order') + 1;
        $data['is_active']  = true;
        unset($data['logo']);

        $program = Program::create($data);
        return response()->json(['success' => true, 'program' => $program]);
    }

    public function updateProgram(Request $request, Program $program)
    {
        $data = $request->validate([
            'name'        => 'required|string',
            'acronym'     => 'nullable|string|max:50',
            'subtitle'    => 'nullable|string',
            'description' => 'nullable|string',
            'color'       => 'required|string',
            'logo'        => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('logo')) {
            // Delete the old logo if one exists
            if ($program->logo_path) {
                Storage::disk('public_images')->delete(str_replace('images/', '', $program->logo_path));
            }
            $data['logo_path'] = 'images/' . $this->storeImage($request->file('logo'), 'logo-programs');
        }

        unset($data['logo']);
        $program->update($data);
        $this->markDirty($program->id);
        return response()->json(['success' => true]);
    }

    public function destroyProgram(Program $program)
    {
        $program->update(['is_active' => false]);
        return response()->json(['success' => true]);
    }

    public function updateDescription(Request $request, Program $program)
    {
        $request->validate(['description' => 'required|string']);
        $program->update(['description' => $request->description]);
        $this->markDirty($program->id);
        return response()->json(['success' => true]);
    }

    public function destroyDescription(Program $program)
    {
        $program->update(['description' => null]);
        $this->markDirty($program->id);
        return response()->json(['success' => true]);
    }

    // ===== QUALIFICATIONS =====
    public function storeQualification(Request $request)
    {
        $data = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'type'       => 'required|string|max:50',
            'content'    => 'required|string',
        ]);
        $data['sort_order'] = ProgramQualification::where('program_id', $data['program_id'])->max('sort_order') + 1;
        $q = ProgramQualification::create($data);
        $this->markDirty($data['program_id']);
        return response()->json(['success' => true, 'qualification' => $q]);
    }

    public function updateQualification(Request $request, ProgramQualification $qualification)
    {
        $data = $request->validate([
            'type'    => 'required|string|max:50',
            'content' => 'required|string',
        ]);
        $qualification->update($data);
        $this->markDirty($qualification->program_id);
        return response()->json(['success' => true]);
    }

    public function destroyQualification(ProgramQualification $qualification)
    {
        $this->markDirty($qualification->program_id);
        $qualification->delete();
        return response()->json(['success' => true]);
    }

    // ===== STEPS =====
    public function storeStep(Request $request)
    {
        if ($request->filled('link')) {
            $link = trim($request->link);
            if (!preg_match('/^https?:\/\//i', $link)) {
                $link = 'https://' . $link;
            }
            $request->merge(['link' => $link]);
        }

        $data = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'content'    => 'required|string',
            'link'       => 'nullable|string|max:500',
        ]);
        $data['sort_order'] = ProgramHowToApply::where('program_id', $data['program_id'])->max('sort_order') + 1;
        $step = ProgramHowToApply::create($data);
        $this->markDirty($data['program_id']);
        return response()->json(['success' => true, 'step' => $step]);
    }

    public function updateStep(Request $request, ProgramHowToApply $step)
    {
        if ($request->filled('link')) {
            $link = trim($request->link);
            if (!preg_match('/^https?:\/\//i', $link)) {
                $link = 'https://' . $link;
            }
            $request->merge(['link' => $link]);
        }

        $data = $request->validate([
            'content' => 'required|string',
            'link'    => 'nullable|string|max:500',
        ]);
        $step->update($data);
        $this->markDirty($step->program_id);
        return response()->json(['success' => true]);
    }

    public function destroyStep(ProgramHowToApply $step)
    {
        $this->markDirty($step->program_id);
        $step->delete();
        return response()->json(['success' => true]);
    }

    // ===== STORIES =====
    public function storeStory(Request $request)
    {
        $data = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'title'      => 'required|string',
            'link'       => 'nullable|url',
            'image' => 'required|image|max:5120',
            'story_year' => 'required|numeric|min:2000|max:2100',
        ]);

        $data['link']       = $data['link'] ?: null;
        $data['image_path'] = 'images/' . $this->storeImage($request->file('image'), 'stories');
        $data['sort_order'] = ProgramStory::where('program_id', $data['program_id'])->max('sort_order') + 1;
        $data['is_active']  = true;

        unset($data['image']);

        $story = ProgramStory::create($data);

        $this->markDirty($data['program_id']);

        return response()->json([
            'success' => true,
            'story'   => $story
        ]);
    }

    public function updateStory(Request $request, ProgramStory $story)
    {
        $data = $request->validate([
            'title'      => 'required|string',
            'link'       => 'nullable|url',
            'image' => 'nullable|image|max:5120',
            'story_year' => 'required|numeric|min:2000|max:2100',
        ]);

        if ($request->hasFile('image')) {
            // Delete the old thumbnail if one exists
            if ($story->image_path) {
                Storage::disk('public_images')->delete(str_replace('images/', '', $story->image_path));
            }
            $data['image_path'] = 'images/' . $this->storeImage($request->file('image'), 'stories');
        }

        $data['link'] = $data['link'] ?: null;
        unset($data['image']);

        $story->update($data);

        $this->markDirty($story->program_id);

        return response()->json(['success' => true]);
    }

    public function destroyStory(ProgramStory $story)
    {
        $this->markDirty($story->program_id);
        $story->update(['is_active' => false]);
        return response()->json(['success' => true]);
    }

    public function filterStories(Request $request): JsonResponse
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'year'       => 'nullable|integer|min:2000|max:2100',
        ]);

        $query = ProgramStory::where('program_id', $request->program_id)
            ->where('is_active', true)
            ->orderBy('sort_order');

        if ($request->filled('year')) {
            $query->where('story_year', $request->year);
        }

        $stories = $query->get()->map(function ($s) {
            return [
                'id'         => $s->id,
                'title'      => $s->title,
                'link'       => $s->link,
                'image_path' => asset($s->image_path),
                'story_year' => $s->story_year,
                'program_id' => $s->program_id,
            ];
        });

        return response()->json([
            'success' => true,
            'stories' => $stories
        ]);
    }

    public function storyYears(Request $request): JsonResponse
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id'
        ]);

        $years = ProgramStory::where('program_id', $request->program_id)
            ->where('is_active', true)
            ->whereNotNull('story_year')
            ->distinct()
            ->orderByDesc('story_year')
            ->pluck('story_year');

        return response()->json([
            'success' => true,
            'years'   => $years
        ]);
    }

    // ===== TESTIMONIALS =====
    public function storeTestimonial(Request $request)
    {
        $data = $request->validate([
            'program_id'  => 'required|exists:programs,id',
            'quote'       => 'required|string',
            'author_name' => 'required|string',
            'author_role' => 'nullable|string',
        ]);
        $data['sort_order'] = ProgramTestimonial::where('program_id', $data['program_id'])->max('sort_order') + 1;
        $data['is_active']  = true;
        $t = ProgramTestimonial::create($data);
        $this->markDirty($data['program_id']);
        return response()->json(['success' => true, 'testimonial' => $t]);
    }

    public function updateTestimonial(Request $request, ProgramTestimonial $testimonial)
    {
        $data = $request->validate([
            'quote'       => 'required|string',
            'author_name' => 'required|string',
            'author_role' => 'nullable|string',
        ]);
        $testimonial->update($data);
        $this->markDirty($testimonial->program_id);
        return response()->json(['success' => true]);
    }

    public function destroyTestimonial(ProgramTestimonial $testimonial)
    {
        $this->markDirty($testimonial->program_id);
        $testimonial->update(['is_active' => false]);
        return response()->json(['success' => true]);
    }

    // ===== CAROUSEL =====
    public function storeSlide(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string',
            'excerpt'       => 'required|string',
            'link'          => 'nullable|url',
            'program_label' => 'required|string',
            'image'         => 'required|image|max:5120',
        ]);

        $data['image_path'] = $this->storeImage($request->file('image'), 'carousel', 'public');
        $data['sort_order'] = CarouselSlide::max('sort_order') + 1;
        $data['is_active']  = true;
        unset($data['image']);

        $slide = CarouselSlide::create($data);
        return response()->json(['success' => true, 'slide' => $slide]);
    }

    public function updateSlide(Request $request, CarouselSlide $slide)
    {
        $data = $request->validate([
        'title'         => 'required|string',
        'excerpt'       => 'required|string',
        'link'          => 'nullable|url',
        'program_label' => 'required|string',
        'image'         => 'nullable|image|max:5120',
    ]);

    if ($request->hasFile('image')) {
        // Only delete old image if it was an uploaded one (not a seeded public image)
        if ($slide->image_path && !str_starts_with($slide->image_path, 'images/')) {
            Storage::disk('public')->delete($slide->image_path);
        }
        $data['image_path'] = $this->storeImage($request->file('image'), 'carousel', 'public');
    }

    unset($data['image']);
    $slide->update($data);
    return response()->json(['success' => true]);
    }

    public function destroySlide(CarouselSlide $slide)
    {
        $slide->update(['is_active' => false]);
        return response()->json(['success' => true]);
    }

    public function togglePublish(Program $program): JsonResponse
    {
        if ($program->is_published) {
            // Unpublish — hide from public, keep snapshot intact
            $program->update([
                'is_published'      => false,
                'has_draft_changes' => false,
            ]);
        } else {
            // Publish or Republish — freeze current live data into snapshot
            $program->loadMissing(['qualifications', 'howToApply', 'stories', 'testimonials']);

            $program->update([
                'published_snapshot' => [
                    'name'        => $program->name,
                    'acronym'     => $program->acronym,
                    'subtitle'    => $program->subtitle,
                    'description' => $program->description,
                    'color'       => $program->color,
                    'logo_path'   => $program->logo_path,

                    'qualifications' => $program->qualifications->map(fn($q) => [
                        'id'      => $q->id,
                        'type'    => $q->type,
                        'content' => $q->content,
                    ])->values()->toArray(),

                    'how_to_apply' => $program->howToApply->map(fn($s) => [
                        'id'      => $s->id,
                        'content' => $s->content,
                        'link'    => $s->link,
                    ])->values()->toArray(),

                    'stories' => $program->stories->map(fn($s) => [
                        'id'         => $s->id,
                        'title'      => $s->title,
                        'link'       => $s->link,
                        'image_path' => $s->image_path,
                    ])->values()->toArray(),

                    'testimonials' => $program->testimonials->map(fn($t) => [
                        'id'          => $t->id,
                        'quote'       => $t->quote,
                        'author_name' => $t->author_name,
                        'author_role' => $t->author_role,
                    ])->values()->toArray(),

                    'snapshotted_at' => now()->toIso8601String(),
                ],
                'is_published'      => true,
                'has_draft_changes' => false,
            ]);
        }

        return response()->json(['success' => true]);
    }

    // ── Republish — re-snapshots without toggling is_published ──────────────
    // PATCH /admin/programs/{program}/republish
    public function republish(Program $program): JsonResponse
    {
        $program->loadMissing(['qualifications', 'howToApply', 'stories', 'testimonials']);

        $program->update([
            'published_snapshot' => [
                'name'        => $program->name,
                'acronym'     => $program->acronym,
                'subtitle'    => $program->subtitle,
                'description' => $program->description,
                'color'       => $program->color,
                'logo_path'   => $program->logo_path,

                'qualifications' => $program->qualifications->map(fn($q) => [
                    'id'      => $q->id,
                    'type'    => $q->type,
                    'content' => $q->content,
                ])->values()->toArray(),

                'how_to_apply' => $program->howToApply->map(fn($s) => [
                    'id'      => $s->id,
                    'content' => $s->content,
                    'link'    => $s->link,
                ])->values()->toArray(),

                'stories' => $program->stories->map(fn($s) => [
                    'id'         => $s->id,
                    'title'      => $s->title,
                    'link'       => $s->link,
                    'image_path' => $s->image_path,
                    'story_year' => $s->story_year,
                ])->values()->toArray(),

                'testimonials' => $program->testimonials->map(fn($t) => [
                    'id'          => $t->id,
                    'quote'       => $t->quote,
                    'author_name' => $t->author_name,
                    'author_role' => $t->author_role,
                ])->values()->toArray(),

                'snapshotted_at' => now()->toIso8601String(),
            ],
            'has_draft_changes' => false,
        ]);

        return response()->json(['success' => true]);
    }

    // ── Called after any edit to signal the published snapshot is outdated ──
    private function markDirty(int $programId): void
    {
        Program::where('id', $programId)
               ->where('is_published', true)
               ->update(['has_draft_changes' => true]);
    }

    public function destroyQualificationsByType(string $type, Program $program)
    {
        $this->markDirty($program->id);
        $program->qualifications()->where('type', $type)->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Stamp a last-modified timestamp so the admin UI knows there are
     * unpublished changes even after a page refresh.
     * Also appends the change entry to a persistent changelog.
     * Called automatically after every add / edit / delete.
     */

    // ===== CTA SECTION =====

    // GET /admin/cta-section
    public function getCtaSection(): JsonResponse
    {
        $cta = CtaSection::first();

        return response()->json([
            'success'  => true,
            'title'    => $cta?->title    ?? 'Ready to Start Your Journey?',
            'subtitle' => $cta?->subtitle ?? "Join thousands of youth who have transformed their careers through DOLE's employment programs.",
        ]);
    }

    // PUT /admin/cta-section
    public function updateCtaSection(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'subtitle' => 'required|string|max:1000',
        ]);

        $cta = CtaSection::first();

        if ($cta) {
            $cta->update($data);
            // Refresh to get the latest updated_at from DB
            $cta->refresh();
            $hasDraft = is_null($cta->published_at) || $cta->updated_at->gt($cta->published_at);
        } else {
            $cta = CtaSection::create($data);
            $hasDraft = true;
        }

        return response()->json(['success' => true, 'has_draft' => $hasDraft]);
    }

    // POST /admin/cta-section/publish
    public function publishCtaSection(): JsonResponse
    {
        $cta = CtaSection::first();

        if (!$cta) {
            return response()->json(['success' => false, 'message' => 'No CTA content to publish.'], 404);
        }

        // Snapshot the current draft values into the published columns
        $cta->update([
            'published_title'    => $cta->title,
            'published_subtitle' => $cta->subtitle,
            'published_at'       => now(),
        ]);

        return response()->json(['success' => true, 'has_draft' => false]);
    }
}