<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionalTable extends Model
{
    use HasFactory;

    protected $table = 'regional_labor_market_statistics';
    protected $fillable = [
    'year',
    'quarter',
    'household_population',
    'labor_force',
    'employed',
    'underemployed',
    'unemployed',
    'labor_force_rate', 
    'employment_rate',
    'underemployment_rate',
    'unemployment_rate',
];

public function scopeYearRange($query, $start, $end)
{
    if ($start && $end) {
        $query->whereBetween('year', [$start, $end]);
    }
}public function scopeOrderedQuarter($query)
{
    return $query->orderByRaw(
        "FIELD(quarter, 'January', 'April', 'July', 'October')"
    );
}


}
