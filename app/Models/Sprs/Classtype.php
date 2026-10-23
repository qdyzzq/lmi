<?php

namespace App\Models\Sprs;

use Illuminate\Database\Eloquent\Model;

class Classtype extends Model
{
    protected $table = 'class_types';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function offices()
    {
        return $this->hasMany(Office::class, 'class_type');
    }
}