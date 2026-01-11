<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $regionalStats = [
            [
                'period' => 'Jul 2025',
                'labor_force' => 2378,
                'employed' => 2293,
                'unemployed' => 86,
                'underemployed' => 241,
                'emp_rate' => 96.4,
                'unemp_rate' => 3.6,
                'underemp_rate' => 10.5,
                'particip_rate' => 57.7
            ],
            [
                'period' => 'Apr 2025',
                'labor_force' => 2385,
                'employed' => 2300,
                'unemployed' => 85,
                'underemployed' => 220,
                'emp_rate' => 96.4,
                'unemp_rate' => 3.6,
                'underemp_rate' => 9.6,
                'particip_rate' => 58.2
            ],
            [
                'period' => 'Jan 2025',
                'labor_force' => 2390,
                'employed' => 2310,
                'unemployed' => 80,
                'underemployed' => 200,
                'emp_rate' => 96.7,
                'unemp_rate' => 3.3,
                'underemp_rate' => 8.7,
                'particip_rate' => 58.6
            ],
        ];

        

        return view('home', [
            'regionalStats' => $regionalStats,

            // JOB MARKET DATA
            'high_volume_jobs' => [
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
            ],

            'hard_to_fill' => [
                ['role' => 'Senior Data Scientist', 'days' => 120, 'bottleneck' => 'Skills Gap', 'year' => 2023],
                ['role' => 'Licensed Civil Engineer', 'days' => 95, 'bottleneck' => 'Experience Gap', 'year' => 2023],
                ['role' => 'Full Stack Developer', 'days' => 85, 'bottleneck' => 'High Competition', 'year' => 2023],
                ['role' => 'Specialized Surgeon', 'days' => 88, 'bottleneck' => 'License/Cert', 'year' => 2024],
            ],

            'soft_skills' => [
                ['name' => 'English Proficiency', 'sector' => 'BPO/IT'],
                ['name' => 'Safety Compliance', 'sector' => 'Construction'],
                ['name' => 'Customer Empathy', 'sector' => 'BPO/IT'],
                ['name' => 'Crisis Mgmt', 'sector' => 'General'],
                ['name' => 'Adaptability', 'sector' => 'General'],
            ],

            'tech_skills' => [
                ['name' => 'Python / SQL', 'sector' => 'BPO/IT'],
                ['name' => 'Heavy Machinery Op', 'sector' => 'Construction'],
                ['name' => 'Data Analysis', 'sector' => 'BPO/IT'],
                ['name' => 'Specialized Surgery', 'sector' => 'Healthcare'],
                ['name' => 'Generative AI', 'sector' => 'BPO/IT'],
                ['name' => 'Climate Resilience', 'sector' => 'Agriculture'],
                ['name' => 'Robotics Maintenance', 'sector' => 'Manufacturing'],
            ],

            'matrix_results' => [
                ['role' => 'Senior Java Developer', 'sector' => 'BPO/IT', 'skill' => 'Spring Boot Framework', 'type' => 'Hard', 'req' => 'Expert', 'obs' => 'Novice', 'impact' => 'High'],
                ['role' => 'Customer Service Rep', 'sector' => 'BPO/IT', 'skill' => 'English Fluency (C1)', 'type' => 'Soft', 'req' => 'Competent', 'obs' => 'Basic', 'impact' => 'Critical'],
                ['role' => 'Site Engineer', 'sector' => 'Construction', 'skill' => 'Project Mgmt (Primavera)', 'type' => 'Hard', 'req' => 'Competent', 'obs' => 'Novice', 'impact' => 'High'],
                ['role' => 'ICU Nurse', 'sector' => 'Healthcare', 'skill' => 'Critical Care Cert', 'type' => 'Hard', 'req' => 'Expert', 'obs' => 'Competent', 'impact' => 'Critical'],
            ]
        ]);
    }
}
