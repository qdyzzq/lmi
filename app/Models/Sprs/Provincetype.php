<?php

namespace App\Models\Sprs;

use Illuminate\Database\Eloquent\Model;

class Provincetype extends Model
{
    protected $table = 'provinces_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function offices()
    {
        return $this->hasMany(Office::class, 'province_id');
    }
}