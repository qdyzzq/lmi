<?php

namespace App\Models\Module4;

use Illuminate\Database\Eloquent\Model;

class ProgramQualification extends Model
{
     protected $fillable = ['program_id', 'type', 'content', 'sort_order'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
