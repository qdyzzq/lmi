<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LmiSubmission;
use App\Models\JobTitle;

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
        // Fetch ALL quarterly data for the Alpine.js table (allows frontend filtering)
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
            ->whereIn('month', [1, 4, 7, 10]) // Only quarterly data (Jan, Apr, Jul, Oct)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function($stat) {
                // Map month to quarter name for display
                $quarterNames = [1 => 'Jan', 4 => 'Apr', 7 => 'Jul', 10 => 'Oct'];
                $stat->period = $quarterNames[$stat->month] . ' ' . $stat->year;
                return $stat;
            });

        // ================================================
        // JOB MARKET DATA (Static/Hardcoded)
        // ================================================
        return view('home', [
            'regionalStats' => $regionalStats
        ]);
    }

    public function jobMarket(Request $request)
    {
        // ================================================
        // GET SELECTED YEAR (default to latest year)
        // ================================================
        $availableYears = JobTitle::where('status', 'approved')
            ->distinct()
            ->pluck('year')
            ->sort()
            ->values()
            ->toArray();

        $selectedYear = $request->input('year', max($availableYears ?: [date('Y')]));

        // ================================================
        // GET APPROVED LMI SUBMISSIONS
        // ================================================
        $approvedSubmissions = LmiSubmission::with(['hardToFillRoles', 'diagnoses'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

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
                    'index' => $index
                ];
            }
        }
        
        // Sort by normalized title alphabetically
        ksort($groupedRoles);

        // ================================================
        // AGGREGATE SKILLS FROM APPROVED SUBMISSIONS
        // ================================================
        $dynamicTechSkills = [];
        $dynamicSoftSkills = [];
        $allSectors = []; // Track unique sectors

        foreach ($approvedSubmissions as $submission) {
            foreach ($submission->hardToFillRoles as $role) {
                // Use job_classification as the sector
                $sector = $role->job_classification;
                
                // Add to sectors list
                if (!in_array($sector, $allSectors)) {
                    $allSectors[] = $sector;
                }
                
                // Collect technical skills
                $techSkills = $role->technical_skills_missing;
                if (is_string($techSkills)) {
                    $techSkills = json_decode($techSkills, true) ?? [];
                }
                if (is_array($techSkills)) {
                    foreach ($techSkills as $skill) {
                        if (!empty($skill)) {
                            $dynamicTechSkills[] = [
                                'name' => $skill,
                                'sector' => $sector,
                                'job_title' => $role->job_title
                            ];
                        }
                    }
                }
                
                // Collect soft skills
                $softSkills = $role->soft_skills_missing;
                if (is_string($softSkills)) {
                    $softSkills = json_decode($softSkills, true) ?? [];
                }
                if (is_array($softSkills)) {
                    foreach ($softSkills as $skill) {
                        if (!empty($skill)) {
                            $dynamicSoftSkills[] = [
                                'name' => $skill,
                                'sector' => $sector,
                                'job_title' => $role->job_title
                            ];
                        }
                    }
                }
            }
        }

        // Remove duplicates and count occurrences
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

        // Sort by count (most common first)
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
                // Decode skills
                $techSkills = $role->technical_skills_missing;
                if (is_string($techSkills)) {
                    $techSkills = json_decode($techSkills, true) ?? [];
                }
                
                $softSkills = $role->soft_skills_missing;
                if (is_string($softSkills)) {
                    $softSkills = json_decode($softSkills, true) ?? [];
                }
                
                // Determine types and build skill name
                $types = [];
                $skillName = '';
                $hardSkillsArray = [];
                $softSkillsArray = [];
                
                if (!empty($techSkills) && is_array($techSkills)) {
                    $types[] = 'Hard';
                    $hardSkillsArray = array_map(function($skill) use ($role) {
                        return [
                            'name' => $skill,
                            'category' => $role->job_classification ?? 'General'
                        ];
                    }, $techSkills);
                }
                
                if (!empty($softSkills) && is_array($softSkills)) {
                    $types[] = 'Soft';
                    $softSkillsArray = array_map(function($skill) use ($role) {
                        return [
                            'name' => $skill,
                            'category' => $role->job_classification ?? 'General'
                        ];
                    }, $softSkills);
                }
                
                // Build skill name
                if (!empty($types)) {
                    if (count($types) == 2) {
                        $skillName = 'Technical & Soft Skills Gap';
                    } elseif ($types[0] == 'Hard') {
                        $skillName = 'Technical Skills Gap';
                    } else {
                        $skillName = 'Soft Skills Gap';
                    }
                }
                
                // Determine impact based on difficulty level
                $impact = 'Medium';
                if (isset($role->difficulty_level)) {
                    $difficulty = strtolower($role->difficulty_level);
                    if (in_array($difficulty, ['critical', 'very high', 'high'])) {
                        $impact = 'High';
                    } elseif (in_array($difficulty, ['low', 'very low'])) {
                        $impact = 'Low';
                    }
                }
                
                // Only add if there are skills
                if (!empty($hardSkillsArray) || !empty($softSkillsArray)) {
                    $matrixResults[] = [
                        'role' => $this->formatJobTitle($role->job_title), // Format for display
                        'role_normalized' => $role->job_title_normalized ?? $this->normalizeTitle($role->job_title),
                        'sector' => $role->job_classification ?? 'General',
                        'skill' => $skillName,
                        'types' => $types,
                        'hard_skills' => $hardSkillsArray,
                        'soft_skills' => $softSkillsArray,
                        'impact' => $impact
                    ];
                }
            }
        }

        // ================================================
        // GET HIGH VOLUME JOBS FOR SELECTED YEAR AND COMPARISON
        // ================================================
        $currentYearJobs = $this->getHighVolumeJobs($selectedYear);
        $previousYearJobs = $this->getHighVolumeJobs($selectedYear - 1);
        
        // Prepare comparison data
        $comparisonData = $this->prepareComparisonData($currentYearJobs, $previousYearJobs, $selectedYear);

        return view('JobMarketDemands', [
            'approvedSubmissions' => $approvedSubmissions,
            'groupedRoles' => $groupedRoles,
            
            // DYNAMIC SKILLS from real submissions
            'tech_skills' => $techSkillsCounts,
            'soft_skills' => $softSkillsCounts,
            'sectors' => $allSectors,

            // Year Selection
            'available_years' => $availableYears,
            'selected_year' => $selectedYear,

            // High Volume Jobs (DYNAMIC from database)
            'high_volume_jobs' => $currentYearJobs,
            'previous_year_jobs' => $previousYearJobs,
            'comparison_data' => $comparisonData,

            'hard_to_fill' => [
                ['role' => 'Senior Data Scientist', 'days' => 120, 'bottleneck' => 'Skills Gap', 'year' => 2023],
                ['role' => 'Licensed Civil Engineer', 'days' => 95, 'bottleneck' => 'Experience Gap', 'year' => 2023],
                ['role' => 'Full Stack Developer', 'days' => 85, 'bottleneck' => 'High Competition', 'year' => 2023],
                ['role' => 'Specialized Surgeon', 'days' => 88, 'bottleneck' => 'License/Cert', 'year' => 2024],
            ],

            // DYNAMIC MATRIX RESULTS with Pagination
            'matrix_results' => $matrixResults,
            'matrix_results_paginated' => collect($matrixResults)->chunk(10), // 10 per page
            'total_matrix_results' => count($matrixResults)
        ]);
    }

    /**
     * Get high volume jobs from approved job titles for a specific year
     */
    private function getHighVolumeJobs($year)
    {
        // Get top 10 job titles for the specified year
        $jobs = JobTitle::where('status', 'approved')
            ->where('year', $year)
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($job) {
                return [
                    'title' => $job->title,
                    'count' => $job->count,
                    'year' => $job->year
                ];
            })
            ->toArray();

        return $jobs;
    }

    /**
     * Prepare comparison data between current and previous year
     */
    private function prepareComparisonData($currentYearJobs, $previousYearJobs, $selectedYear)
    {
        // Create a map of previous year jobs for easy lookup
        $previousYearMap = [];
        foreach ($previousYearJobs as $job) {
            $previousYearMap[strtolower($job['title'])] = $job['count'];
        }

        // Prepare comparison data
        $comparisonData = [];
        foreach ($currentYearJobs as $job) {
            $titleKey = strtolower($job['title']);
            $previousCount = $previousYearMap[$titleKey] ?? 0;
            $currentCount = $job['count'];
            
            // Calculate percentage change
            $change = 0;
            if ($previousCount > 0) {
                $change = (($currentCount - $previousCount) / $previousCount) * 100;
            } elseif ($currentCount > 0) {
                $change = 100; // New job title
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

    /**
     * Helper function to normalize job titles (fallback if model doesn't have it yet)
     */
    private function normalizeTitle($title)
    {
        if (empty($title)) return '';
        
        // Convert to lowercase
        $normalized = strtolower(trim($title));
        
        // Replace multiple spaces with single space
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        
        // Remove special characters (keep letters, numbers, spaces)
        $normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
        
        return $normalized;
    }

    /**
     * Format job title for display (proper capitalization, clean formatting)
     * Works with ANY user input - no hardcoded word lists
     */
    private function formatJobTitle($title)
    {
        if (empty($title)) return '';
        
        // Remove extra spaces and trim
        $formatted = trim(preg_replace('/\s+/', ' ', $title));
        
        // Use mb_convert_case for proper title case (works with any language/characters)
        // MB_CASE_TITLE capitalizes the first letter of each word
        $formatted = mb_convert_case($formatted, MB_CASE_TITLE, 'UTF-8');
        
        return $formatted;
    }
}