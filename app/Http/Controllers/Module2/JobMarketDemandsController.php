<?php

namespace App\Http\Controllers\Module2;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Module2\LmiSubmission;
use App\Models\Module2\JobTitle;
use Carbon\Carbon;

class JobMarketDemandsController extends Controller
{
    /**
     * Display the Job Market Demands page
     * Handles: Hard-to-Fill Roles, Critical Skills Requirements, High Volume Jobs
     */
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
        $ninetyDaysAgo    = $now->copy()->subDays(90); // LIVE window — keep at 90
        $archiveThreshold = $now->copy()->subDays(20); // ARCHIVE cutoff — change THIS to test

        // ↓ Build archive options from expired submissions (older than threshold)
        $expiredSubmissions = LmiSubmission::where('status', 'approved')
            ->where('created_at', '<', $archiveThreshold)
            ->orderBy('created_at', 'desc')
            ->get();

        $archiveOptions = [];
        foreach ($expiredSubmissions as $sub) {
            $approvedAt = Carbon::parse($sub->created_at);
            $key = $approvedAt->year . '-' . str_pad($approvedAt->month, 2, '0', STR_PAD_LEFT);
            if (!isset($archiveOptions[$key])) {
                $archiveOptions[$key] = [
                    'year'       => $approvedAt->year,
                    'month'      => $approvedAt->month,
                    'month_name' => $approvedAt->format('F'),
                ];
            }
        }
        krsort($archiveOptions);
        $archiveOptions = array_values($archiveOptions);
        // ↑ End archive options
        
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

        return view('Public.Module2.JobMarketDemands', [
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
            'hard_to_fill' => [],
            'archive_options' => $archiveOptions, // ← ADDED
            'matrix_results' => $matrixResults,
            'matrix_results_paginated' => collect($matrixResults)->chunk(10),
            'total_matrix_results' => count($matrixResults),
            // ↓ INSERTED: date options for Critical Skills Requirements year/month filter
            'matrix_date_options' => $this->buildMatrixDateOptions(),
            // ↑ END INSERTED
        ]);
    }

    /**
     * API endpoint — returns hard-to-fill roles data for archive filter (no page reload)
     */
    public function hardToFillData(Request $request)
    {
        $now              = Carbon::now();
        $ninetyDaysAgo    = $now->copy()->subDays(90);
        $archiveThreshold = $now->copy()->subDays(20);

        $filterYears  = array_filter((array) $request->input('archive_years', []));
        $filterMonths = array_filter((array) $request->input('archive_months', []));
        $isArchive    = !empty($filterYears);

        if ($isArchive) {
            $query = LmiSubmission::with(['hardToFillRoles'])
                ->where('status', 'approved')
                ->where('created_at', '<', $archiveThreshold);

            if (!empty($filterMonths)) {
                // Match any (year + month) combination within the selected ranges
                $query->where(function ($q) use ($filterYears, $filterMonths) {
                    foreach ($filterYears as $year) {
                        foreach ($filterMonths as $month) {
                            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                            $end   = $start->copy()->endOfMonth();
                            $q->orWhereBetween('created_at', [$start, $end]);
                        }
                    }
                });
            } else {
                // Year(s) only — include all months within those years
                $query->where(function ($q) use ($filterYears) {
                    foreach ($filterYears as $year) {
                        $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
                        $end   = Carbon::createFromDate($year, 12, 31)->endOfYear();
                        $q->orWhereBetween('created_at', [$start, $end]);
                    }
                });
            }

            $submissions = $query->orderBy('created_at', 'desc')->get();
        } else {
            // No filter — show live 90-day window
            $submissions = LmiSubmission::with(['hardToFillRoles'])
                ->where('status', 'approved')
                ->where('created_at', '>=', $ninetyDaysAgo)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Build archive label for the banner
        $archiveLabel = '';
        if ($isArchive) {
            if (!empty($filterMonths)) {
                // Show month range or specific months
                $sortedMonths = $filterMonths;
                sort($sortedMonths);
                $sortedYears  = $filterYears;
                sort($sortedYears);

                if (count($sortedYears) >= 2 && count($sortedMonths) >= 2) {
                    $fromYear  = min($sortedYears);
                    $toYear    = max($sortedYears);
                    $fromMonth = Carbon::createFromDate(2000, min($sortedMonths), 1)->format('F');
                    $toMonth   = Carbon::createFromDate(2000, max($sortedMonths), 1)->format('F');
                    $archiveLabel = $fromYear === $toYear
                        ? "{$fromMonth} – {$toMonth} {$fromYear}"
                        : "{$fromMonth} {$fromYear} – {$toMonth} {$toYear}";
                } elseif (count($sortedMonths) >= 2) {
                    $year      = $sortedYears[0];
                    $fromMonth = Carbon::createFromDate(2000, min($sortedMonths), 1)->format('F');
                    $toMonth   = Carbon::createFromDate(2000, max($sortedMonths), 1)->format('F');
                    $archiveLabel = "{$fromMonth} – {$toMonth} {$year}";
                } else {
                    $year         = $sortedYears[0];
                    $month        = $sortedMonths[0];
                    $archiveLabel = Carbon::createFromDate($year, $month, 1)->format('F Y');
                }
            } else {
                $sortedYears = $filterYears;
                sort($sortedYears);
                $archiveLabel = count($sortedYears) >= 2
                    ? min($sortedYears) . ' – ' . max($sortedYears)
                    : $sortedYears[0];
            }
        }

        // Quarter banner text
        $quarterText = 'No active data';
        if ($submissions->isNotEmpty()) {
            $oldest = Carbon::parse($submissions->last()->created_at);
            $newest = Carbon::parse($submissions->first()->created_at)->addDays(90);
            $quarterText = $isArchive
                ? 'Archived data for ' . $archiveLabel
                : 'Showing data from ' . $oldest->format('F j, Y') . ' to ' . $newest->format('F j, Y');
        }

        // Build flat roles array
        $roles = [];
        $index = 0;
        foreach ($submissions as $submission) {
            foreach ($submission->hardToFillRoles as $role) {
                $techSkills = $role->technical_skills_missing;
                if (is_string($techSkills)) $techSkills = json_decode($techSkills, true) ?? [];
                if (!is_array($techSkills))  $techSkills = [];

                $softSkills = $role->soft_skills_missing;
                if (is_string($softSkills)) $softSkills = json_decode($softSkills, true) ?? [];
                if (!is_array($softSkills))  $softSkills = [];

                $reasons = $role->difficulty_reasons;
                if (is_string($reasons)) $reasons = json_decode($reasons, true) ?? [];
                if (!is_array($reasons))  $reasons = [];
                $flatReasons = [];
                foreach ($reasons as $r) {
                    if (is_array($r))       $flatReasons = array_merge($flatReasons, array_filter($r));
                    elseif (!empty($r))     $flatReasons[] = $r;
                }

                $roles[] = [
                    'submission_id'      => $submission->id,
                    'index'              => $index,
                    'job_title'          => mb_strtoupper(trim($role->job_title), 'UTF-8'),
                    'vacancy_duration'   => $role->vacancy_duration,
                    'classification'     => $role->job_classification ?? '',
                    'difficulty_reasons' => $flatReasons,
                    'tech_skills'        => array_values(array_filter(array_map('strtoupper', $techSkills))),
                    'soft_skills'        => array_values(array_filter(array_map('strtoupper', $softSkills))),
                    'sector'             => $submission->industry_sector ?? '',
                ];
                $index++;
            }
        }

        return response()->json([
            'is_archive'    => $isArchive,
            'archive_label' => $archiveLabel,
            'quarter_text'  => $quarterText,
            'roles'         => $roles,
        ]);
    }
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

    // ================================================
    // PRIVATE HELPER METHODS
    // ================================================

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

    // ↓ INSERTED: helper — builds unique year+month list from ALL approved submissions
    private function buildMatrixDateOptions(): array
    {
        return $this->getMatrixDateOptions();
    }

    // ↓ INSERTED: shared logic extracted so the API endpoint can reuse it
    private function getMatrixDateOptions(): array
    {
        $submissions = LmiSubmission::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->pluck('created_at');

        $options = [];
        foreach ($submissions as $ts) {
            $dt  = Carbon::parse($ts);
            $key = $dt->year . '-' . str_pad($dt->month, 2, '0', STR_PAD_LEFT);
            if (!isset($options[$key])) {
                $options[$key] = [
                    'year'       => $dt->year,
                    'month'      => $dt->month,
                    'month_name' => $dt->format('F'),
                ];
            }
        }
        krsort($options);
        return array_values($options);
    }

    // ↓ INSERTED: API endpoint — returns fresh date options for the filter dropdowns
    public function matrixDateOptions(Request $request)
    {
        return response()->json([
            'options' => $this->getMatrixDateOptions(),
        ]);
    }
    // ↑ END INSERTED

    // ↓ INSERTED: API endpoint — returns matrix data filtered by year and optional month
    public function matrixData(Request $request)
    {
        $filterYears  = array_filter((array) $request->input('years', []));
        $filterMonths = array_filter((array) $request->input('months', []));

        $query = LmiSubmission::with(['hardToFillRoles', 'diagnoses'])
            ->where('status', 'approved');

        if (!empty($filterYears) && !empty($filterMonths)) {
            // Year(s) + Month(s): match any combination of year+month pairs
            $query->where(function ($q) use ($filterYears, $filterMonths) {
                foreach ($filterYears as $year) {
                    foreach ($filterMonths as $month) {
                        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                        $end   = $start->copy()->endOfMonth();
                        $q->orWhereBetween('created_at', [$start, $end]);
                    }
                }
            });
        } elseif (!empty($filterYears)) {
            // Year(s) only: scope to full year(s)
            $query->where(function ($q) use ($filterYears) {
                foreach ($filterYears as $year) {
                    $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
                    $end   = Carbon::createFromDate($year, 12, 31)->endOfYear();
                    $q->orWhereBetween('created_at', [$start, $end]);
                }
            });
        }

        $submissions = $query->orderBy('created_at', 'desc')->get();

        $matrixResults = [];
        foreach ($submissions as $submission) {
            foreach ($submission->hardToFillRoles as $role) {
                $techSkills = $role->technical_skills_missing;
                if (is_string($techSkills)) $techSkills = json_decode($techSkills, true) ?? [];
                $softSkills = $role->soft_skills_missing;
                if (is_string($softSkills)) $softSkills = json_decode($softSkills, true) ?? [];

                $hasTechnicalSkills = false;
                $hardSkillsArray    = [];
                if (!empty($techSkills) && is_array($techSkills)) {
                    $filtered = array_filter($techSkills, fn($s) => !empty(trim($s)));
                    if (!empty($filtered)) {
                        $hasTechnicalSkills = true;
                        $hardSkillsArray = array_map(fn($s) => [
                            'name'     => mb_strtoupper(trim($s), 'UTF-8'),
                            'category' => $role->job_classification ?? 'General',
                        ], $filtered);
                    }
                }

                $hasSoftSkills   = false;
                $softSkillsArray = [];
                if (!empty($softSkills) && is_array($softSkills)) {
                    $filtered = array_filter($softSkills, fn($s) => !empty(trim($s)));
                    if (!empty($filtered)) {
                        $hasSoftSkills   = true;
                        $softSkillsArray = array_map(fn($s) => [
                            'name'     => mb_strtoupper(trim($s), 'UTF-8'),
                            'category' => $role->job_classification ?? 'General',
                        ], $filtered);
                    }
                }

                $difficultyReasons = $role->difficulty_reasons;
                if (is_string($difficultyReasons)) $difficultyReasons = json_decode($difficultyReasons, true) ?? [];
                if (!is_array($difficultyReasons)) $difficultyReasons = [];

                $flatReasons = [];
                foreach ($difficultyReasons as $reason) {
                    if (is_array($reason))            $flatReasons = array_merge($flatReasons, array_filter($reason));
                    elseif (is_string($reason) && !empty($reason)) $flatReasons[] = $reason;
                }

                $hasTechnicalCheckbox = in_array('Technical / Hard Skills Missing', $flatReasons);
                $hasSoftCheckbox      = in_array('Soft / Employability Skills Missing', $flatReasons);

                $impact    = 'Medium';
                $diagnosis = $submission->diagnoses->where('lmi_hard_to_fill_role_id', $role->id)->first();
                if ($diagnosis && $diagnosis->impact_level) {
                    $impact = $diagnosis->impact_level;
                }

                $matrixResults[] = [
                    'role'                   => $this->formatJobTitle($role->job_title),
                    'role_normalized'        => $role->job_title_normalized ?? $this->normalizeTitle($role->job_title),
                    'sector'                 => $role->job_classification ?? 'General',
                    'hard_skills'            => array_values($hardSkillsArray),
                    'soft_skills'            => array_values($softSkillsArray),
                    'has_technical_checkbox' => $hasTechnicalCheckbox,
                    'has_soft_checkbox'      => $hasSoftCheckbox,
                    'impact'                 => $impact,
                    'salary_range'           => $role->salary_range ?? 'Not specified',
                ];
            }
        }

        return response()->json([
            'results'       => $matrixResults,
            'total'         => count($matrixResults),
            'filter_years'  => $filterYears,
            'filter_months' => $filterMonths,
        ]);
    }
    // ↑ END INSERTED
}