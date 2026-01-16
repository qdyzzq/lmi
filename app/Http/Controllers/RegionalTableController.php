<?php

namespace App\Http\Controllers;

use App\Models\RegionalTable;
use Illuminate\Http\Request;

class RegionalTableController extends Controller
{
    public function index(Request $request)
    {
        $startYear = $request->start_year;
        $endYear   = $request->end_year;

        $tableStats = RegionalTable::yearRange($startYear, $endYear)
            ->orderBy('year', 'desc')
            ->orderedQuarter()
            ->paginate(10); 
        return response()->json($tableStats);
    }
}
