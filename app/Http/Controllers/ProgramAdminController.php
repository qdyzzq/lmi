<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramQualification;
use App\Models\ProgramHowToApply;
use App\Models\ProgramStory;
use App\Models\ProgramTestimonial;
use App\Models\CarouselSlide;
use Illuminate\Http\Request;

class ProgramAdminController extends Controller
{
    // ===== PROGRAMS =====
    public function storeProgram(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string',
            'subtitle'    => 'required|string',
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
            'subtitle'    => 'required|string',
            'description' => 'required|string',
            'color'       => 'required|string',
            'logo'        => 'nullable|image',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = 'images/' . $request->file('logo')->store('logo-programs', 'public_images');
        }

        unset($data['logo']);
        $program->update($data);
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
        return response()->json(['success' => true, 'qualification' => $q]);
    }

    public function updateQualification(Request $request, ProgramQualification $qualification)
    {
        $data = $request->validate([
            'type'    => 'required|string|max:50',
            'content' => 'required|string',
        ]);
        $qualification->update($data);
        return response()->json(['success' => true]);
    }

    public function destroyQualification(ProgramQualification $qualification)
    {
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
        return response()->json(['success' => true]);
    }

    public function destroyStep(ProgramHowToApply $step)
    {
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
        return response()->json(['success' => true]);
    }

    public function destroyStory(ProgramStory $story)
    {
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
            'author_role' => 'required|string',
        ]);
        $data['is_active'] = true;
        $t = ProgramTestimonial::create($data);
        return response()->json(['success' => true, 'testimonial' => $t]);
    }

    public function updateTestimonial(Request $request, ProgramTestimonial $testimonial)
    {
        $data = $request->validate([
            'quote'       => 'required|string',
            'author_name' => 'required|string',
            'author_role' => 'required|string',
        ]);
        $testimonial->update($data);
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
}