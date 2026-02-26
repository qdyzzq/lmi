<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarouselSlide extends Model
{
     protected $fillable = [
        'title', 'excerpt', 'link', 'image_path',
        'program_label', 'color', 'sort_order', 'is_active'
    ];
}
