<?php

namespace App\Models\Module4;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'name', 'acronym', 'subtitle', 'description',
        'color', 'logo_path', 'sort_order', 'is_active', 'is_published',
        'published_snapshot', 'has_draft_changes',
    ];

    protected $casts = [
        'published_snapshot' => 'array',
        'has_draft_changes'  => 'boolean',
    ];

    public function qualifications()
    {
        return $this->hasMany(ProgramQualification::class)->orderBy('sort_order');
    }

    public function howToApply()
    {
        return $this->hasMany(ProgramHowToApply::class)->orderBy('sort_order');
    }

    public function stories()
    {
        return $this->hasMany(ProgramStory::class)
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

    public function testimonials()
    {
        return $this->hasMany(ProgramTestimonial::class)
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }

}