<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramHowToApply extends Model
{
    
    protected $table = 'program_how_to_apply';
     protected $fillable = ['program_id', 'content', 'link', 'sort_order'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
