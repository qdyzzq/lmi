<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request\DB;
use App\Models\EmploymentStat;
use App\Models\EmploymentStatQuarterly;

class EmploymentStatsController extends Controller
{
     // Chart 1: Labor Force vs Employment Rate
    public function laborVsEmployment()
    {
        return EmploymentStat::orderBy('year')->get([
            'year',
            'labor_force_thousands',
            'employment_rate'
        ]);
    }

    // Chart 2: Unemployment volume
    public function unemploymentVolume()
    {
        return EmploymentStat::orderBy('year')->get([
            'year',
            'unemployed_thousands'
        ]);
    }

    public function quarterlyByYear($year)
    {
        return EmploymentStatQuarterly::where('year',$year)
        ->orderByRaw("Field(quarter, 'Q1','Q2','Q3','Q4')")
        ->get([
            'quarter',
            'labor_force_thousands',
            'employment_rate',
            'unemployed_thousands'
        ]);
    }
}
