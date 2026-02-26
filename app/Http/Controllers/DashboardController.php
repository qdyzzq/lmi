<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LmiSubmission;
use App\Models\JobTitle;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard page
     * Handles: Consolidated Regional Statistics Table & Job Market Data
     */
    public function index(Request $request)
    {
        // ================================================
        // CONSOLIDATED REGIONAL STATISTICS TABLE
        // ================================================
        $regionalStats = DB::table('regional_labor_market_statistics')
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
            ->whereIn('month', [1, 4, 7, 10])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function($stat) {
                $quarterNames = [1 => 'Jan', 4 => 'Apr', 7 => 'Jul', 10 => 'Oct'];
                $stat->period = $quarterNames[$stat->month] . ' ' . $stat->year;
                return $stat;
            });

        return view('home', [
            'regionalStats' => $regionalStats
        ]);
    }

    public function jobMarket(Request $request)
    {
        // ================================================
        // GET SELECTED YEAR
        // ================================================
        $availableYears = JobTitle::where('status', 'approved')
            ->distinct()
            ->pluck('year')
            ->sort()
            ->values()
            ->toArray();

        $selectedYear = $request->input('year', max($availableYears ?: [date('Y')]));

        // ================================================
        // GET APPROVED SUBMISSIONS WITH 90-DAY FILTER
        // Each submission has its own 90-day timer
        // ================================================
        $now = Carbon::now();
        $ninetyDaysAgo = $now->copy()->subDays(90);
        
        $approvedSubmissions = LmiSubmission::with(['hardToFillRoles', 'diagnoses'])
            ->where('status', 'approved')
            ->where('created_at', '>=', $ninetyDaysAgo)  // Only last 90 days
            ->orderBy('created_at', 'desc')
            ->get();

        // ================================================
        // CALCULATE QUARTER INFO BASED ON ACTIVE DATA
        // ================================================
        $quarterInfo = $this->getQuarterInfo($approvedSubmissions);

        // ================================================
        // GROUP ROLES BY NORMALIZED TITLE
        // ================================================
        $groupedRoles = [];
        
        foreach($approvedSubmissions as $submission) {
            foreach($submission->hardToFillRoles as $index => $role) {
                $normalizedTitle = $role->job_title_normalized ?? $this->normalizeTitle($role->job_title);
                
                if (!isset($groupedRoles[$normalizedTitle])) {
                    $groupedRoles[$normalizedTitle] = [];
                }
                
                $groupedRoles[$normalizedTitle][] = [
                    'role' => $role,
                    'submission' => $submission,
                    'index' => $index,
                    'display_title' => mb_strtoupper(trim($role->job_title), 'UTF-8'),
                ];
            }
        }
        
        ksort($groupedRoles);

        // ================================================
        // AGGREGATE SKILLS FROM APPROVED SUBMISSIONS
        // ================================================
        $dynamicTechSkills = [];
        $dynamicSoftSkills = [];
        $allSectors = [];

        foreach ($approvedSubmissions as $submission) {
            foreach ($submission->hardToFillRoles as $role) {
                $sector = $role->job_classification;
                
                if (!in_array($sector, $allSectors)) {
                    $allSectors[] = $sector;
                }
                
                // Technical skills
                $techSkills = $role->technical_skills_missing;
                if (is_string($techSkills)) {
                    $techSkills = json_decode($techSkills, true) ?? [];
                }
                if (is_array($techSkills)) {
                    foreach ($techSkills as $skill) {
                        if (!empty($skill)) {
                            $dynamicTechSkills[] = [
                                'name' => mb_strtoupper(trim($skill), 'UTF-8'),
                                'sector' => $sector,
                                'job_title' => $role->job_title
                            ];
                        }
                    }
                }
                
                // Soft skills
                $softSkills = $role->soft_skills_missing;
                if (is_string($softSkills)) {
                    $softSkills = json_decode($softSkills, true) ?? [];
                }
                if (is_array($softSkills)) {
                    foreach ($softSkills as $skill) {
                        if (!empty($skill)) {
                            $dynamicSoftSkills[] = [
                                'name' => mb_strtoupper(trim($skill), 'UTF-8'),
                                'sector' => $sector,
                                'job_title' => $role->job_title
                            ];
                        }
                    }
                }
            }
        }

        // Count skills
        $techSkillsCounts = [];
        foreach ($dynamicTechSkills as $skill) {
            $key = $skill['name'] . '|' . $skill['sector'];
            if (!isset($techSkillsCounts[$key])) {
                $techSkillsCounts[$key] = [
                    'name' => $skill['name'],
                    'sector' => $skill['sector'],
                    'count' => 0
                ];
            }
            $techSkillsCounts[$key]['count']++;
        }

        $softSkillsCounts = [];
        foreach ($dynamicSoftSkills as $skill) {
            $key = $skill['name'] . '|' . $skill['sector'];
            if (!isset($softSkillsCounts[$key])) {
                $softSkillsCounts[$key] = [
                    'name' => $skill['name'],
                    'sector' => $skill['sector'],
                    'count' => 0
                ];
            }
            $softSkillsCounts[$key]['count']++;
        }

        usort($techSkillsCounts, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        usort($softSkillsCounts, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        // ================================================
        // BUILD DYNAMIC MATRIX RESULTS
        // ================================================
        $matrixResults = [];

        foreach ($approvedSubmissions as $submission) {
            foreach ($submission->hardToFillRoles as $role) {
                $techSkills = $role->technical_skills_missing;
                if (is_string($techSkills)) {
                    $techSkills = json_decode($techSkills, true) ?? [];
                }
                
                $softSkills = $role->soft_skills_missing;
                if (is_string($softSkills)) {
                    $softSkills = json_decode($softSkills, true) ?? [];
                }
                
                $hasTechnicalSkills = false;
                $hardSkillsArray = [];
                if (!empty($techSkills) && is_array($techSkills)) {
                    $filteredTechSkills = array_filter($techSkills, function($skill) {
                        return !empty(trim($skill));
                    });
                    
                    if (!empty($filteredTechSkills)) {
                        $hasTechnicalSkills = true;
                        $hardSkillsArray = array_map(function($skill) use ($role) {
                            return [
                                'name' => mb_strtoupper(trim($skill), 'UTF-8'),
                                'category' => $role->job_classification ?? 'General'
                            ];
                        }, $filteredTechSkills);
                    }
                }
                
                $hasSoftSkills = false;
                $softSkillsArray = [];
                if (!empty($softSkills) && is_array($softSkills)) {
                    $filteredSoftSkills = array_filter($softSkills, function($skill) {
                        return !empty(trim($skill));
                    });
                    
                    if (!empty($filteredSoftSkills)) {
                        $hasSoftSkills = true;
                        $softSkillsArray = array_map(function($skill) use ($role) {
                            return [
                                'name' => mb_strtoupper(trim($skill), 'UTF-8'),
                                'category' => $role->job_classification ?? 'General'
                            ];
                        }, $filteredSoftSkills);
                    }
                }
                
                $difficultyReasons = $role->difficulty_reasons;
                if (is_string($difficultyReasons)) {
                    $difficultyReasons = json_decode($difficultyReasons, true) ?? [];
                }
                if (!is_array($difficultyReasons)) {
                    $difficultyReasons = [];
                }
                
                $flatReasons = [];
                foreach ($difficultyReasons as $reason) {
                    if (is_array($reason)) {
                        $flatReasons = array_merge($flatReasons, array_filter($reason));
                    } elseif (is_string($reason) && !empty($reason)) {
                        $flatReasons[] = $reason;
                    }
                }
                
                $hasTechnicalCheckbox = in_array('Technical / Hard Skills Missing', $flatReasons);
                $hasSoftCheckbox = in_array('Soft / Employability Skills Missing', $flatReasons);
                
                $skillName = '';
                $types = [];
                
                if ($hasTechnicalSkills && $hasSoftSkills) {
                    $skillName = 'Technical & Soft Skills Gap';
                    $types = ['Hard', 'Soft'];
                } elseif ($hasTechnicalSkills) {
                    $skillName = 'Technical Skills Gap';
                    $types = ['Hard'];
                } elseif ($hasSoftSkills) {
                    $skillName = 'Soft Skills Gap';
                    $types = ['Soft'];
                } elseif ($hasTechnicalCheckbox && $hasSoftCheckbox) {
                    $skillName = 'Technical & Soft Skills (Not Specified)';
                } elseif ($hasTechnicalCheckbox) {
                    $skillName = 'Technical Skills (Not Specified)';
                } elseif ($hasSoftCheckbox) {
                    $skillName = 'Soft Skills (Not Specified)';
                } else {
                    $skillName = 'No Skills Specified';
                }
                
                $impact = 'Medium';
                $diagnosis = $submission->diagnoses->where('lmi_hard_to_fill_role_id', $role->id)->first();
                if ($diagnosis && $diagnosis->impact_level) {
                    $impact = $diagnosis->impact_level;
                }
                
                $matrixResults[] = [
                    'role' => $this->formatJobTitle($role->job_title),
                    'role_normalized' => $role->job_title_normalized ?? $this->normalizeTitle($role->job_title),
                    'sector' => $role->job_classification ?? 'General',
                    'skill' => $skillName,
                    'types' => $types,
                    'hard_skills' => $hardSkillsArray,
                    'soft_skills' => $softSkillsArray,
                    'has_technical_checkbox' => $hasTechnicalCheckbox,
                    'has_soft_checkbox' => $hasSoftCheckbox,
                    'impact' => $impact,
                    'salary_range' => $role->salary_range ?? 'Not specified'
                ];
            }
        }

        // ================================================
        // HIGH VOLUME JOBS
        // ================================================
        $currentYearJobs = $this->getHighVolumeJobs($selectedYear);
        $previousYearJobs = $this->getHighVolumeJobs($selectedYear - 1);
        $comparisonData = $this->prepareComparisonData($currentYearJobs, $previousYearJobs, $selectedYear);

        return view('JobMarketDemands', [
            'approvedSubmissions' => $approvedSubmissions,
            'groupedRoles' => $groupedRoles,
            
            // QUARTER INFO
            'quarter_info' => $quarterInfo,
            
            'tech_skills' => $techSkillsCounts,
            'soft_skills' => $softSkillsCounts,
            'sectors' => $allSectors,
            'available_years' => $availableYears,
            'selected_year' => $selectedYear,
            'high_volume_jobs' => $currentYearJobs,
            'previous_year_jobs' => $previousYearJobs,
            'comparison_data' => $comparisonData,
            'hard_to_fill' => [ ],
            'matrix_results' => $matrixResults,
            'matrix_results_paginated' => collect($matrixResults)->chunk(10),
            'total_matrix_results' => count($matrixResults)
        ]);
    }

    /**
     * Get quarter info based on active submissions
     * Shows the date range of all currently active submissions
     */
    private function getQuarterInfo($approvedSubmissions)
    {
        if ($approvedSubmissions->isEmpty()) {
            return [
                'has_data' => false,
                'display_text' => 'No active data',
                'start_date' => null,
                'end_date' => null
            ];
        }

        // Find oldest and newest submission
        $oldestSubmission = $approvedSubmissions->last(); // Last in desc order = oldest
        $newestSubmission = $approvedSubmissions->first(); // First in desc order = newest

        $startDate = Carbon::parse($oldestSubmission->created_at);
        $endDate = Carbon::parse($newestSubmission->created_at)->addDays(90);

        return [
            'has_data' => true,
            'display_text' => 'Showing data from ' . $startDate->format('F j, Y') . ' to ' . $endDate->format('F j, Y'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_submissions' => $approvedSubmissions->count()
        ];
    }

    /**
     * API endpoint — returns chart data for a given year (no page reload)
     */
    public function chartData(Request $request)
    {
        $availableYears = JobTitle::where('status', 'approved')
            ->distinct()
            ->pluck('year')
            ->sort()
            ->values()
            ->toArray();

        $selectedYear = $request->input('year', max($availableYears ?: [date('Y')]));

        $currentYearJobs  = $this->getHighVolumeJobs($selectedYear);
        $previousYearJobs = $this->getHighVolumeJobs($selectedYear - 1);
        $comparisonData   = $this->prepareComparisonData($currentYearJobs, $previousYearJobs, $selectedYear);

        $hasPreviousData = collect($comparisonData)->some(fn($d) => $d['previous_count'] > 0);

        return response()->json([
            'selected_year'    => $selectedYear,
            'previous_year'    => $selectedYear - 1,
            'available_years'  => $availableYears,
            'comparison_data'  => $comparisonData,
            'has_previous_data' => $hasPreviousData,
        ]);
    }

    private function getHighVolumeJobs($year)
    {
        $jobs = JobTitle::where('status', 'approved')
            ->where('year', $year)
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($job) {
                return [
                    'title' => mb_strtoupper(trim($job->title), 'UTF-8'),
                    'count' => $job->count,
                    'year' => $job->year
                ];
            })
            ->toArray();

        return $jobs;
    }

    private function prepareComparisonData($currentYearJobs, $previousYearJobs, $selectedYear)
    {
        $previousYearMap = [];
        foreach ($previousYearJobs as $job) {
            $previousYearMap[strtolower($job['title'])] = $job['count'];
        }

        $comparisonData = [];
        foreach ($currentYearJobs as $job) {
            $titleKey = strtolower($job['title']);
            $previousCount = $previousYearMap[$titleKey] ?? 0;
            $currentCount = $job['count'];
            
            $change = 0;
            if ($previousCount > 0) {
                $change = (($currentCount - $previousCount) / $previousCount) * 100;
            } elseif ($currentCount > 0) {
                $change = 100;
            }

            $comparisonData[] = [
                'title' => $job['title'],
                'current_year' => $selectedYear,
                'previous_year' => $selectedYear - 1,
                'current_count' => $currentCount,
                'previous_count' => $previousCount,
                'change' => round($change, 1),
                'is_new' => $previousCount == 0,
                'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable')
            ];
        }

        return $comparisonData;
    }

    private function normalizeTitle($title)
    {
        if (empty($title)) return '';
        $normalized = strtolower(trim($title));
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
        return $normalized;
    }

    private function formatJobTitle($title)
    {
        if (empty($title)) return '';
        $formatted = trim(preg_replace('/\s+/', ' ', $title));
        $formatted = mb_strtoupper($formatted, 'UTF-8');
        return $formatted;
    }
}