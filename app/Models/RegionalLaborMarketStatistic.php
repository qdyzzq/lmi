<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class RegionalLaborMarketStatistic extends Model
{
    protected $table = 'regional_labor_market_statistics';

    protected $fillable = [
        'year', 'month', 'household_population', 'labor_force', 
        'employed', 'underemployed', 'unemployed', 
        'labor_force_participation_rate', 'employment_rate', 
        'underemployment_rate', 'unemployment_rate'
    ];

    // Helper to format the period for the frontend table
    public function getFormattedPeriodAttribute()
    {
        $date = \DateTime::createFromFormat('!m', $this->month);
        $monthName = $date->format('M');
        return "{$monthName} {$this->year}";
    }
}