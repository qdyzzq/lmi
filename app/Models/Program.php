<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
     protected $fillable = [
        'name', 'subtitle', 'description',
        'color', 'logo_path', 'sort_order', 'is_active'
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

    public function testimonial()
    {
        return $this->hasOne(ProgramTestimonial::class)
                    ->where('is_active', true);
    }
}
