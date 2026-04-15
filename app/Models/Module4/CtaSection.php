<?php

namespace App\Models\Module4;

use Illuminate\Database\Eloquent\Model;

class CtaSection extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'published_title',
        'published_subtitle',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}