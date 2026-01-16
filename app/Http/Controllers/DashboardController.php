<?php

namespace App\Http\Controllers;

use App\Models\LaborStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LaborMarketData;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $year = $request->year ?? 2025;
    $quarter = $request->quarter; 
    // ---  REAL DATABASE QUERIES ---
    $years = DB::table('regional_stats')
        ->select('year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');
        $quarters = DB::table('regional_stats')
        ->select('quarter')
        ->distinct()
        ->orderByRaw("FIELD(quarter, 'Annual', 'January', 'April', 'July', 'October')")
        ->pluck('quarter');

    $regionalStats = DB::table('regional_stats')
        ->select('quarter', 'labor_force_rate', 'employment_rate', 'underemployment_rate', 'unemployment_rate')
        ->where('year', $year)
        ->when($quarter, function ($query) use ($quarter) {
            return $query->where('quarter', $quarter);
        })
        ->get();

        $latest = LaborStats::orderBy('year', 'desc')
                ->orderBy('id', 'desc') // Use ID to ensure we get October (ID 38) not January (ID 35)
                ->first();
    $regionalTableStats = DB::table('regional_labor_market_statistics')
            ->select(
                'year',
                'month',
                'labor_force',
                'employed',
                'unemployed',
                'underemployed',
                'employment_rate as emp_rate',
                'unemployment_rate as unemp_rate',
                'underemployment_rate as underemp_rate',
                'labor_force_participation_rate as particip_rate'
            )
            ->whereIn('month', [1, 4, 7, 10]) // Only Jan, Apr, Jul, Oct
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function($stat) {
                // Format period for quarterly data
                $quarterNames = [
                    1 => 'Jan',
                    4 => 'Apr',
                    7 => 'Jul',
                    10 => 'Oct'
                ];
                
                $stat->period = $quarterNames[$stat->month] . ' ' . $stat->year;
                
                return $stat;
            });
    

    // Fallback in case table is empty
    $defaultYear = $latest ? $latest->year : date('Y');
    $defaultQuarter = $latest ? $latest->quarter : 'Annual';

    // Fetch lists for the dropdowns
    $years = LaborStats::distinct()->orderBy('year', 'desc')->pluck('year');
    $quarters = LaborStats::distinct()->pluck('quarter'); 
    
    $high_volume_jobs = [
        ['title' => 'Customer Service Rep', 'count' => 1250],
        ['title' => 'Sales Associate', 'count' => 880],
        ['title' => 'Construction Worker', 'count' => 750],
        ['title' => 'Admin Assistant', 'count' => 520],
        ['title' => 'Delivery Rider', 'count' => 480],
        ['title' => 'Production Operator', 'count' => 450],
        ['title' => 'Registered Nurse', 'count' => 320],
        ['title' => 'Accountant', 'count' => 280],
        ['title' => 'IT Support Specialist', 'count' => 200],
        ['title' => 'Teacher', 'count' => 180],
    ];

    $hard_to_fill = [
        ['role' => 'Senior Data Scientist', 'days' => 120, 'bottleneck' => 'Skills Gap', 'year' => 2023],
        ['role' => 'Licensed Civil Engineer', 'days' => 95, 'bottleneck' => 'Experience Gap', 'year' => 2023],
        ['role' => 'Full Stack Developer', 'days' => 85, 'bottleneck' => 'High Competition', 'year' => 2023],
        ['role' => 'Specialized Surgeon', 'days' => 88, 'bottleneck' => 'License/Cert', 'year' => 2024],
    ];

    $soft_skills = [
        ['name' => 'English Proficiency', 'sector' => 'BPO/IT'],
        ['name' => 'Safety Compliance', 'sector' => 'Construction'],
        ['name' => 'Customer Empathy', 'sector' => 'BPO/IT'],
        ['name' => 'Crisis Mgmt', 'sector' => 'General'],
        ['name' => 'Adaptability', 'sector' => 'General'],
    ];

    $tech_skills = [
        ['name' => 'Python / SQL', 'sector' => 'BPO/IT'],
        ['name' => 'Heavy Machinery Op', 'sector' => 'Construction'],
        ['name' => 'Data Analysis', 'sector' => 'BPO/IT'],
        ['name' => 'Specialized Surgery', 'sector' => 'Healthcare'],
        ['name' => 'Generative AI', 'sector' => 'BPO/IT'],
        ['name' => 'Climate Resilience', 'sector' => 'Agriculture'],
        ['name' => 'Robotics Maintenance', 'sector' => 'Manufacturing'],
    ];

    $matrix_results = [
        ['role' => 'Senior Java Developer', 'sector' => 'BPO/IT', 'skill' => 'Spring Boot Framework', 'type' => 'Hard', 'req' => 'Expert', 'obs' => 'Novice', 'impact' => 'High'],
        ['role' => 'Customer Service Rep', 'sector' => 'BPO/IT', 'skill' => 'English Fluency (C1)', 'type' => 'Soft', 'req' => 'Competent', 'obs' => 'Basic', 'impact' => 'Critical'],
        ['role' => 'Site Engineer', 'sector' => 'Construction', 'skill' => 'Project Mgmt (Primavera)', 'type' => 'Hard', 'req' => 'Competent', 'obs' => 'Novice', 'impact' => 'High'],
        ['role' => 'ICU Nurse', 'sector' => 'Healthcare', 'skill' => 'Critical Care Cert', 'type' => 'Hard', 'req' => 'Expert', 'obs' => 'Competent', 'impact' => 'Critical'],
    ];

    // --- MERGE EVERYTHING INTO ONE VIEW RETURN ---
    return view('home', [
        'years' => $years,
        'quarters' => $quarters,
        'regionalStats' => $regionalStats, 
        'defaultYear' => $defaultYear,
        'defaultQuarter' => $defaultQuarter,
        'regionalTableStats' => $regionalTableStats,
        'high_volume_jobs' => $high_volume_jobs,
        'hard_to_fill' => $hard_to_fill,
        'soft_skills' => $soft_skills,
        'tech_skills' => $tech_skills,
        'matrix_results' => $matrix_results
    ]);
}
}