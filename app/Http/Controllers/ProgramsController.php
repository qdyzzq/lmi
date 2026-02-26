<?php

namespace App\Http\Controllers;
use App\Models\Program;
use App\Models\CarouselSlide;
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

        return view('programs-stories', compact('programs', 'carouselSlides'));
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

     $qualificationTypes = \App\Models\ProgramQualification::distinct()
        ->pluck('type')
        ->sort()
        ->values();

    return view('program-admin', compact('programs', 'carouselSlides', 'qualificationTypes'));
}
}
