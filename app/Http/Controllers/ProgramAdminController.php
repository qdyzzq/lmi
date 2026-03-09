<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramQualification;
use App\Models\ProgramHowToApply;
use App\Models\ProgramStory;
use App\Models\ProgramTestimonial;
use App\Models\CarouselSlide;
use App\Models\CtaSection;
use App\Models\FieldOffice;
use App\Models\OfficeType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProgramAdminController extends Controller
{
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

        $fieldOffices = FieldOffice::ordered()->get();

        $ctaSection = CtaSection::first();

        return view('admin.programStories_editor', compact('programs', 'carouselSlides', 'qualificationTypes', 'fieldOffices', 'ctaSection'));
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

        $fieldOffices = FieldOffice::ordered()->get();

        $ctaSection = CtaSection::first();

        return view('programStories', compact('programs', 'carouselSlides', 'isPreview', 'fieldOffices', 'ctaSection'));
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
            'logo'        => 'nullable|image',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = 'images/' . $request->file('logo')->store('logo-programs', 'public_images');
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
            'logo'        => 'nullable|image',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = 'images/' . $request->file('logo')->store('logo-programs', 'public_images');
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
            'image'      => 'required|image',
        ]);

        $data['image_path'] = 'images/' . $request->file('image')->store('stories', 'public_images');
        $data['sort_order'] = ProgramStory::where('program_id', $data['program_id'])->max('sort_order') + 1;
        $data['is_active']  = true;
        unset($data['image']);

        $story = ProgramStory::create($data);
        $this->markDirty($data['program_id']);
        return response()->json(['success' => true, 'story' => $story]);
    }

    public function updateStory(Request $request, ProgramStory $story)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'link'  => 'nullable|url',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = 'images/' . $request->file('image')->store('stories', 'public_images');
        }

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
            'color'         => 'required|string',
            'image'         => 'required|image',
        ]);

        $data['image_path'] = 'images/' . $request->file('image')->store('carousel', 'public_images');
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
            'color'         => 'required|string',
            'image'         => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = 'images/' . $request->file('image')->store('carousel', 'public_images');
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

    // ===== OFFICE TYPES =====
    public function getOfficeTypes(): JsonResponse
    {
        return response()->json(
            OfficeType::orderBy('name')->pluck('name')
        );
    }

    public function storeOfficeType(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:office_types,name',
        ]);

        $type = OfficeType::create([
            'name' => strtoupper(trim($request->name)),
        ]);

        return response()->json(['success' => true, 'name' => $type->name]);
    }

    public function updateOfficeType(Request $request, string $name): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:office_types,name',
        ]);

        $type = OfficeType::where('name', strtoupper(trim($name)))->firstOrFail();
        $newName = strtoupper(trim($request->name));
        $type->update(['name' => $newName]);

        return response()->json(['success' => true, 'name' => $newName]);
    }

    public function destroyOfficeType(string $name): JsonResponse
    {
        $type = OfficeType::where('name', strtoupper(trim($name)))->firstOrFail();
        $type->delete();

        return response()->json(['success' => true]);
    }

    // ===== FIELD OFFICES (PESO/JPO Directory) =====
    public function storeFieldOffice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'office_type'  => 'required|string|max:100',
            'province'     => 'required|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
        ]);

        $data['sort_order'] = FieldOffice::where('province', $data['province'])->max('sort_order') + 1;
        $office = FieldOffice::create($data);

        return response()->json(['success' => true, 'id' => $office->id]);
    }

    public function updateFieldOffice(Request $request, FieldOffice $office): JsonResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'office_type'  => 'required|string|max:100',
            'province'     => 'required|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
        ]);

        $office->update($data);

        return response()->json(['success' => true]);
    }

    public function destroyFieldOffice(FieldOffice $office): JsonResponse
    {
        $office->delete();

        return response()->json(['success' => true]);
    }

    // ── Publish Directory — snapshots all field offices for the public view ──
    // POST /admin/field-offices/publish
    public function publishDirectory(): JsonResponse
    {
        $snapshot = FieldOffice::ordered()
            ->get()
            ->groupBy('province')
            ->map(fn($offices) => $offices->map(fn($o) => [
                'id'       => $o->id,
                'name'     => $o->name,
                'manager'  => $o->manager_name ?? '',
                'email'    => $o->email ?? '',
                'address'  => $o->address ?? '',
                'type'     => $o->office_type,
                'province' => $o->province,
            ])->values())
            ->toArray();

        // Store snapshot in cache so the public page reads from it
        \Cache::put('field_offices_published_snapshot', $snapshot, now()->addYears(10));
        \Cache::put('field_offices_published_at', now()->toIso8601String(), now()->addYears(10));

        // Clear the dirty flag — next page load will show green button
        \Cache::forget('field_offices_last_modified_at');
        \Cache::forget('field_offices_changelog');

        return response()->json(['success' => true, 'published_at' => now()->toIso8601String()]);
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

    public function touchDirectory(Request $request): JsonResponse
    {
        \Cache::put('field_offices_last_modified_at', now()->toIso8601String(), now()->addYears(10));

        // Append the change entry to the persistent changelog
        $entry = $request->only(['action', 'label', 'type', 'province', 'time']);
        if (!empty($entry)) {
            $log = \Cache::get('field_offices_changelog', []);
            $log[] = $entry;
            \Cache::put('field_offices_changelog', $log, now()->addYears(10));
        }

        return response()->json(['success' => true]);
    }

}