<?php

namespace App\Models\Module4;

use Illuminate\Database\Eloquent\Model;

class CarouselSlide extends Model
{
     protected $fillable = [
        'title', 'excerpt', 'link', 'image_path',
        'program_label',  'sort_order', 'is_active'
    ];

    
}
