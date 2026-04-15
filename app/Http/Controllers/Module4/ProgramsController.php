<?php

namespace App\Http\Controllers\Module4;
use App\Http\Controllers\Controller;
use App\Models\Module4\Program;
use App\Models\Module4\CarouselSlide;
use App\Models\Module4\CtaSection;
use App\Models\Module5\FieldOffice;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProgramsController extends Controller
{
    // ── PUBLIC PAGE ───────────────────────────────────────────────────────────
    // Reads from published_snapshot only — live edits never show here
    // until the admin clicks Publish / Republish.
    public function index()
    {
        $programs = Program::where('is_active', true)
            ->where('is_published', true)
            ->whereNotNull('published_snapshot')
            ->orderBy('sort_order')
            ->get();

        $carouselSlides = CarouselSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $fieldOffices = FieldOffice::orderBy('province')->orderBy('name')->get();

        $ctaSection = CtaSection::first();

        return view('Public.Module4.programStories', compact('programs', 'carouselSlides', 'fieldOffices', 'ctaSection'));
    }

    // ── ADMIN PAGE ────────────────────────────────────────────────────────────
    // Always loads live relations so the editor sees current draft data.
    public function admin(): View
    {
        $programs = Program::with([
            'qualifications',
            'howToApply',
            'stories',
            'testimonials',
        ])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        $carouselSlides = CarouselSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $qualificationTypes = \App\Models\ProgramQualification::distinct()
            ->pluck('type')
            ->sort()
            ->values();

        $fieldOffices = FieldOffice::orderBy('province')->orderBy('name')->get();

        return view('program-admin', compact('programs', 'carouselSlides', 'qualificationTypes', 'fieldOffices'));
    }

    // ── FRAGMENT (AJAX accordion refresh) ────────────────────────────────────
    public function fragment(Program $program)
    {
        $program->load(['qualifications', 'howToApply', 'stories', 'testimonials']);
        return view('partials.program-row', compact('program'));
    }

    // ── PUBLISH / REPUBLISH / UNPUBLISH ───────────────────────────────────────
    // PATCH /admin/programs/{program}/toggle-publish
    //
    // Publishing / Republishing → snapshot all current live data into JSON
    //                             → set is_published = true
    // Unpublishing              → hide from public, keep snapshot for later
    public function togglePublish(Program $program): JsonResponse
    {
        if ($program->is_published) {
            // Unpublish
            $program->update([
                'is_published'      => false,
                'has_draft_changes' => false,
            ]);
        } else {
            // Publish or Republish — freeze everything into a snapshot
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

    // ── MARK DIRTY (call after any admin edit) ────────────────────────────────
    // Signals that live data has changed since the last publish.
    // The editor will show an amber "Unpublished changes" warning banner.
    public static function markDirty(int $programId): void
    {
        Program::where('id', $programId)
               ->where('is_published', true)
               ->update(['has_draft_changes' => true]);
    }
}