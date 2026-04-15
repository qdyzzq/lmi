<?php

namespace App\Models\Module4;

use Illuminate\Database\Eloquent\Model;

class ProgramTestimonial extends Model
{
      protected $fillable = [
        'program_id', 'quote', 
        'author_name', 'author_role', 'is_active'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
