<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStory extends Model
{
     protected $fillable = [
        'program_id', 'title', 'link', 
        'image_path', 'sort_order', 'is_active'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
