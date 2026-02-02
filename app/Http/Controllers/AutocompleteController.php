<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LmiSubmission;

class AutocompleteController extends Controller
{
    /**
     * Get all autocomplete data (job titles, technical skills, soft skills)
     * Used for form autocomplete suggestions
     */
    public function getAutocompleteData()
    {
        return response()->json([
            'success' => true,
            'job_titles' => $this->getJobTitles(),
            'technical_skills' => $this->getTechnicalSkills(),
            'soft_skills' => $this->getSoftSkills(),
        ]);
    }

    /**
     * Get all unique job titles from approved submissions
     */
    private function getJobTitles()
    {
        $jobTitles = DB::table('lmi_hard_to_fill_roles')
            ->join('lmi_submissions', 'lmi_hard_to_fill_roles.lmi_submission_id', '=', 'lmi_submissions.id')
            ->where('lmi_submissions.status', 'approved')
            ->select('lmi_hard_to_fill_roles.job_title')
            ->distinct()
            ->orderBy('job_title')
            ->pluck('job_title')
            ->filter(function($title) {
                return !empty(trim($title));
            })
            ->map(function($title) {
                return $this->formatText($title);
            })
            ->unique()
            ->values()
            ->toArray();

        return $jobTitles;
    }

    /**
     * Get all unique technical skills from approved submissions
     */
    private function getTechnicalSkills()
    {
        $technicalSkills = [];

        $roles = DB::table('lmi_hard_to_fill_roles')
            ->join('lmi_submissions', 'lmi_hard_to_fill_roles.lmi_submission_id', '=', 'lmi_submissions.id')
            ->where('lmi_submissions.status', 'approved')
            ->whereNotNull('lmi_hard_to_fill_roles.technical_skills_missing')
            ->select('lmi_hard_to_fill_roles.technical_skills_missing')
            ->get();

        foreach ($roles as $role) {
            $skills = $role->technical_skills_missing;
            
            // Decode if JSON string
            if (is_string($skills)) {
                $skills = json_decode($skills, true) ?? [];
            }
            
            if (is_array($skills)) {
                foreach ($skills as $skill) {
                    if (!empty(trim($skill))) {
                        $technicalSkills[] = trim($skill);
                    }
                }
            }
        }

        // Remove duplicates and format
        $technicalSkills = array_unique($technicalSkills);
        $technicalSkills = array_map(function($skill) {
            return $this->formatText($skill);
        }, $technicalSkills);
        
        // Sort alphabetically
        sort($technicalSkills);

        return array_values($technicalSkills);
    }

    /**
     * Get all unique soft skills from approved submissions
     */
    private function getSoftSkills()
    {
        $softSkills = [];

        $roles = DB::table('lmi_hard_to_fill_roles')
            ->join('lmi_submissions', 'lmi_hard_to_fill_roles.lmi_submission_id', '=', 'lmi_submissions.id')
            ->where('lmi_submissions.status', 'approved')
            ->whereNotNull('lmi_hard_to_fill_roles.soft_skills_missing')
            ->select('lmi_hard_to_fill_roles.soft_skills_missing')
            ->get();

        foreach ($roles as $role) {
            $skills = $role->soft_skills_missing;
            
            // Decode if JSON string
            if (is_string($skills)) {
                $skills = json_decode($skills, true) ?? [];
            }
            
            if (is_array($skills)) {
                foreach ($skills as $skill) {
                    if (!empty(trim($skill))) {
                        $softSkills[] = trim($skill);
                    }
                }
            }
        }

        // Remove duplicates and format
        $softSkills = array_unique($softSkills);
        $softSkills = array_map(function($skill) {
            return $this->formatText($skill);
        }, $softSkills);
        
        // Sort alphabetically
        sort($softSkills);

        return array_values($softSkills);
    }

    /**
     * Format text for display (proper capitalization)
     */
    private function formatText($text)
    {
        if (empty($text)) return '';
        
        // Remove extra spaces and trim
        $formatted = trim(preg_replace('/\s+/', ' ', $text));
        
        // Use mb_convert_case for proper title case
        $formatted = mb_convert_case($formatted, MB_CASE_TITLE, 'UTF-8');
        
        return $formatted;
    }

    /**
     * Search specific autocomplete type
     */
    public function search(Request $request)
    {
        $type = $request->input('type', 'job_titles'); // job_titles, technical_skills, soft_skills
        $query = $request->input('query', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query must be at least 2 characters',
                'results' => []
            ]);
        }

        $results = [];
        
        switch ($type) {
            case 'job_titles':
                $results = $this->getJobTitles();
                break;
            case 'technical_skills':
                $results = $this->getTechnicalSkills();
                break;
            case 'soft_skills':
                $results = $this->getSoftSkills();
                break;
        }

        // Filter by query
        $results = array_filter($results, function($item) use ($query) {
            return stripos($item, $query) !== false;
        });

        // Limit to 20 results
        $results = array_slice($results, 0, 20);

        return response()->json([
            'success' => true,
            'results' => array_values($results),
            'count' => count($results)
        ]);
    }
}