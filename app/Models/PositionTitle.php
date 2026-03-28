<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PositionTitle extends Model
{
    use HasFactory;

    protected $table = 'position_titles';

    protected $fillable = ['name'];

    // ── Relationships ──────────────────────────────────────────
    public function fieldOffices()
    {
        return $this->hasMany(FieldOffice::class, 'position_title_id');
    }
}