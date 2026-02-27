<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\CarouselSlide;
use App\Models\ProgramQualification;
use Illuminate\View\View;

class ProgramsController extends Controller
{
    public function index()
    {
        $programs = Program::with([
            'qualifications',
            'howToApply',
            'stories',
            'testimonial'
        ])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        $carouselSlides = CarouselSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('programStories', compact('programs', 'carouselSlides'));
    }

    public function admin(): View
    {
        $programs = Program::with([
            'qualifications',
            'howToApply',
            'stories',
            'testimonial'
        ])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        $carouselSlides = CarouselSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $qualificationTypes = ProgramQualification::distinct()
            ->orderBy('type')
            ->pluck('type');

        return view('programStories_editor', compact('programs', 'carouselSlides', 'qualificationTypes'));
    }
}