<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStory extends Model
{
     protected $fillable = [
        'program_id', 'title', 'link', 
        'image_path', 'story_year', 'sort_order', 'is_active'
    ];
     protected $casts = [
        'story_year' => 'integer',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
