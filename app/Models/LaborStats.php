<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaborStats extends Model
{
    protected $table = 'regional_stats';

    protected $fillable = [
        'year',
        'quarter',
        'labor_force_rate',
        'employment_rate',
        'unemployment_rate',
        'underemployment_rate',
    ];
}
