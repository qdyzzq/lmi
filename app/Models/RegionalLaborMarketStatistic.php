<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionalLaborMarketStatistic extends Model
{
    use HasFactory;

    protected $table = 'regional_labor_market_statistics';

    protected $fillable = [
        'year',
        'month',
        'household_population',
        'labor_force',
        'employed',
        'underemployed',
        'unemployed',
        'labor_force_participation_rate',
        'employment_rate',
        'underemployment_rate',
        'unemployment_rate',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'household_population' => 'decimal:3',
        'labor_force' => 'decimal:3',
        'employed' => 'decimal:3',
        'underemployed' => 'decimal:3',
        'unemployed' => 'decimal:3',
        'labor_force_participation_rate' => 'decimal:2',
        'employment_rate' => 'decimal:2',
        'underemployment_rate' => 'decimal:2',
        'unemployment_rate' => 'decimal:2',
    ];
}