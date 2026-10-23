<?php

namespace App\Models\Sprs;

use Illuminate\Database\Eloquent\Model;

class Roletype extends Model
{
    protected $table = 'role_types';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'hierarchy_level',
    ];

    protected $casts = [
        'hierarchy_level' => 'integer',
    ];

    // hierarchy_level is not unique — PESO City and PESO Municipality
    // are both level 2. Use these for tier checks, `code` for an exact role.
    public const JPO        = 1;
    public const PESO_LOCAL = 2;
    public const PESO_PROV  = 3;
    public const FIELD      = 4;
    public const REGIONAL   = 5;

    public function offices()
    {
        return $this->hasMany(Office::class, 'office_type');
    }
}