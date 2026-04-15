<?php

namespace App\Http\Controllers\Module2;

use App\Http\Controllers\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Module2\LmiSubmission;

class AutocompleteController extends Controller
{
    /**
     * Static job titles from reference document
     */
    private array $staticJobTitles = [
        'Agricultural and Fisheries Workers',
        'Animators / Multimedia Artists',
        'Aquaculture Specialists',
        'Aquaculture Technicians',
        'Aquaculturists',
        'Artificial Intelligence (AI) Engineers',
        'AI Researchers',
        'Barangay Health Workers',
        'Call Center Agents',
        'Caregivers',
        'Cashiers with Digital Payment and POS System Competency',
        'Construction Workers',
        'Content Creators',
        'Customer Service Representatives',
        'Data Analysts',
        'Dive Tour Guides',
        'Doctors',
        'Early Childhood Teachers',
        'Engineers (Electrical, Chemical, Electronics, Agricultural, Biosystems)',
        'Enterprise Programmers (ERP: SAP, Oracle, Microsoft Dynamics)',
        'Environmental Scientists',
        'Farm Workers (Organic Agriculture, Bamboo Production, Beekeeping)',
        'Fisheries Professionals',
        'Foresters',
        'Free Diving Coaches',
        'Garment Sublimation Operators',
        'Graphic Designers',
        'Guidance Counselors',
        'Healthcare Professionals',
        'Healthcare Workers',
        'Hospitality and Tourism Workers',
        'Hospitality Workers (F&B, Accommodation, Guest Services)',
        'Information Technology Officers',
        'IT Specialists',
        'Linemen',
        'Marine Biologists',
        'Medical Field Educators',
        'Medical Instructors',
        'Medical Technologists',
        'Nurses',
        'Nursing Aides',
        'Occupational Therapists',
        'Ocean Energy Engineers',
        'Pharmacists',
        'Photo Editors',
        'Production Engineers (Digitally Enabled)',
        'Psychiatrists',
        'Psychologists',
        'Radio Announcers',
        'Renewable Tidal and Ocean Energy Technicians',
        'Respiratory Therapists',
        'Scuba Diving Instructors',
        'Seafarers (Hospitality and Service Units on Board Vessels)',
        'Skilled Machine Operators (Backhoe, Crane, Heavy Equipment)',
        'Social Workers',
        'Software Developers / Application Developers',
        'System Administrators',
        'System Programmers',
        'Technicians (RAC, Refrigeration, Electronics, Air-Conditioning)',
        'Virtual Assistants (AI-Integrated and Online Platforms)',
        'Visual Graphic Designers',
        'Warehouse Clerks',
        'Cybersecurity Specialists',
        'Digital Advertising Specialists',
    ];

    /**
     * Static hard/technical skills from reference document
     */
    private array $staticTechnicalSkills = [
        // IT
        'AWS / Azure / GCP',
        'Docker & Kubernetes',
        'Terraform & Ansible',
        'Serverless Computing',
        'Site Reliability Engineering (SRE)',
        'Python',
        'JavaScript / TypeScript',
        'Go',
        'Rust',
        'React',
        'Angular',
        'Vue.js',
        'Node.js',
        'Django',
        'Git / GitHub',
        'CI/CD Pipelines',
        'SQL (PostgreSQL / MySQL)',
        'NoSQL (MongoDB / Cassandra)',
        'Big Data (Spark / Hadoop)',
        'Snowflake & Databricks',
        'Data Visualization (Tableau / Power BI)',
        // Tourism
        'Revenue Management & Dynamic Pricing',
        'Food & Beverage (F&B) Management',
        'Point of Sale (POS) System Operation',
        'Housekeeping & Facility Management',
        'Health & Safety Compliance (HACCP)',
        'Itinerary Planning & Design',
        'Tour Guiding & Public Speaking',
        'Logistics & Transportation Coordination',
        'Cultural & Historical Knowledge',
        'Risk Assessment & Crisis Management',
        'Multilingual Proficiency',
        'Translation & Interpretation',
        'Technical Writing (Reports / Travel Guides)',
        'Intercultural Communication',
        'Search Engine Optimization (SEO) for Travel',
        'Social Media Management (SMM)',
        'Content Management Systems (CMS: WordPress)',
        'Customer Relationship Management (CRM: Salesforce, HubSpot)',
        'Digital Advertising (Google Ads / Meta Ads)',
        // Engineering
        'Computer-Aided Design (CAD: AutoCAD, SolidWorks, CATIA)',
        'Building Information Modeling (BIM: Revit)',
        '3D Modeling & Rendering',
        'Technical Drawing & Blueprint Reading',
        'Geometric Dimensioning and Tolerancing (GD&T)',
        'Finite Element Analysis (FEA)',
        'Computational Fluid Dynamics (CFD)',
        'MATLAB & Simulink',
        'Structural Analysis',
        'Thermodynamics & Heat Transfer Analysis',
        'CNC Machining & Tooling',
        'Additive Manufacturing (3D Printing)',
        'Robotics & Automation',
        'Materials Testing & Selection',
        'Quality Control (Six Sigma, Lean Manufacturing)',
    ];

    /**
     * Static soft skills from reference document
     */
    private array $staticSoftSkills = [
        'Communication Skills',
        'Verbal Clarity',
        'Professional Writing',
        'Active Listening',
        'Critical Thinking',
        'Problem-Solving',
        'Analytical Skills',
        'Decision-Making',
        'Creativity',
        'Professionalism',
        'Accountability',
        'Work Ethic',
        'Punctuality',
        'Responsibility',
        'Discipline',
        'Teamwork',
        'Collaboration',
        'Interpersonal Skills',
        'Adaptability',
        'Willingness to Learn',
        'Flexibility',
        'Coachability',
        'Handling Change',
        'Leadership',
        'Initiative',
        'Self-Motivation',
        'Taking Ownership',
        'Proactiveness',
        'Time Management',
        'Organization',
        'Prioritizing Tasks',
        'Meeting Deadlines',
        'Emotional Intelligence',
        'Self-Awareness',
        'Empathy',
        'Handling Pressure',
    ];

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
     * Get all unique job titles — merges DB (approved) with static list
     */
    private function getJobTitles()
    {
        $dbJobTitles = DB::table('lmi_hard_to_fill_roles')
            ->join('lmi_submissions', 'lmi_hard_to_fill_roles.lmi_submission_id', '=', 'lmi_submissions.id')
            ->where('lmi_submissions.status', 'approved')
            ->select('lmi_hard_to_fill_roles.job_title')
            ->distinct()
            ->orderBy('job_title')
            ->pluck('job_title')
            ->filter(fn($title) => !empty(trim($title)))
            ->map(fn($title) => $this->formatText($title))
            ->toArray();

        $merged = array_unique(array_merge(
            array_map(fn($t) => $this->formatText($t), $this->staticJobTitles),
            $dbJobTitles
        ));

        sort($merged);

        return array_values($merged);
    }

    /**
     * Get all unique technical skills — merges DB (approved) with static list
     */
    private function getTechnicalSkills()
    {
        $dbSkills = [];

        $roles = DB::table('lmi_hard_to_fill_roles')
            ->join('lmi_submissions', 'lmi_hard_to_fill_roles.lmi_submission_id', '=', 'lmi_submissions.id')
            ->where('lmi_submissions.status', 'approved')
            ->whereNotNull('lmi_hard_to_fill_roles.technical_skills_missing')
            ->select('lmi_hard_to_fill_roles.technical_skills_missing')
            ->get();

        foreach ($roles as $role) {
            $skills = $role->technical_skills_missing;

            if (is_string($skills)) {
                $skills = json_decode($skills, true) ?? [];
            }

            if (is_array($skills)) {
                foreach ($skills as $skill) {
                    if (!empty(trim($skill))) {
                        $dbSkills[] = trim($skill);
                    }
                }
            }
        }

        $merged = array_unique(array_merge(
            array_map(fn($s) => $this->formatText($s), $this->staticTechnicalSkills),
            array_map(fn($s) => $this->formatText($s), $dbSkills)
        ));

        sort($merged);

        return array_values($merged);
    }

    /**
     * Get all unique soft skills — merges DB (approved) with static list
     */
    private function getSoftSkills()
    {
        $dbSkills = [];

        $roles = DB::table('lmi_hard_to_fill_roles')
            ->join('lmi_submissions', 'lmi_hard_to_fill_roles.lmi_submission_id', '=', 'lmi_submissions.id')
            ->where('lmi_submissions.status', 'approved')
            ->whereNotNull('lmi_hard_to_fill_roles.soft_skills_missing')
            ->select('lmi_hard_to_fill_roles.soft_skills_missing')
            ->get();

        foreach ($roles as $role) {
            $skills = $role->soft_skills_missing;

            if (is_string($skills)) {
                $skills = json_decode($skills, true) ?? [];
            }

            if (is_array($skills)) {
                foreach ($skills as $skill) {
                    if (!empty(trim($skill))) {
                        $dbSkills[] = trim($skill);
                    }
                }
            }
        }

        $merged = array_unique(array_merge(
            array_map(fn($s) => $this->formatText($s), $this->staticSoftSkills),
            array_map(fn($s) => $this->formatText($s), $dbSkills)
        ));

        sort($merged);

        return array_values($merged);
    }

    /**
     * Format text for display (convert to uppercase)
     */
    private function formatText($text): string
    {
        if (empty($text)) return '';

        $formatted = trim(preg_replace('/\s+/', ' ', $text));
        $formatted = mb_strtoupper($formatted, 'UTF-8');

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

        $results = match ($type) {
            'technical_skills' => $this->getTechnicalSkills(),
            'soft_skills'      => $this->getSoftSkills(),
            default            => $this->getJobTitles(),
        };

        // Filter by query
        $results = array_filter($results, fn($item) => stripos($item, $query) !== false);

        // Limit to 20 results
        $results = array_slice($results, 0, 20);

        return response()->json([
            'success' => true,
            'results' => array_values($results),
            'count'   => count($results)
        ]);
    }
}