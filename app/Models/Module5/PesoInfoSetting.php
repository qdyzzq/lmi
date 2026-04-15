<?php

namespace App\Models\Module5;

use Illuminate\Database\Eloquent\Model;

class PesoInfoSetting extends Model
{
    protected $table = 'peso_info_settings';
    protected $fillable = ['key', 'value'];

    // Helper: get all settings as a keyed array
    public static function allKeyed(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}