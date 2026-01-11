<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmploymentStat;

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
}
