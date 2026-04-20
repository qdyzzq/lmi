<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LmiFormSeeder extends Seeder
{
    public function run(): void
    {
        $submissions = [

            // 1. Therma South, Inc.
            [
                'submission' => [
                    'company_name'    => 'Therma South, Inc.',
                    'respondent_name' => 'Karla Marie Beronilla',
                    'position'        => 'HR Senior Generalist',
                    'contact_number'  => '639670792293',
                    'contact_type'    => 'mobile',
                    'email'           => 'karla.beronilla@aboitizpower.com',
                    'industry_sector' => 'Electricity, Gas, Water & Waste Management (Power Plants, Electric Co-ops, Water Districts, Garbage/Recycling Firms)',
                    'company_size'    => '201-500',
                    'status'          => 'pending',
                    'submitted_at'    => '2026-04-16 09:18:56',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Engineers',
                        'job_title_normalized'    => 'Engineer',
                        'job_classification'      => 'Construction, Engineering & Architecture',
                        'salary_range'            => '₱60,000 - ₱89,999',
                        'vacancies'               => 3,
                        'vacancy_duration'        => '60-90 Days',
                        'difficulty_reasons'      => ['Technical / Hard Skills Missing'],
                        'technical_skills_missing'=> ['Electrical Maintenance & Troubleshooting', 'Reading Technical Drawings', 'Electrical Design and Simulation Software'],
                        'soft_skills_missing'     => [],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Lack of practical / hands-on experience'],
                        'coordination_frequency'  => 'Rarely',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates',
                        'A channel to submit real-time feedback',
                        'A directory of job placement offices',
                        'Job fair Schedules',
                    ],
                    'specific_inputs' => null,
                ],
            ],

            // 2. Blue Lotus Hotel
            [
                'submission' => [
                    'company_name'    => 'Blue Lotus Hotel',
                    'respondent_name' => 'Cyril Berboso',
                    'position'        => 'Human Resources Head',
                    'contact_number'  => '639454016510',
                    'contact_type'    => 'mobile',
                    'email'           => 'cyril25868@gmail.com',
                    'industry_sector' => 'Accommodation & Food Service (Hotels, Resorts, Restaurants, Fast Food Chains, Catering Services)',
                    'company_size'    => '51-200',
                'status'          => 'pending',
                    'submitted_at'    => '2026-04-16 09:18:56',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Security Officer, IT and Accounting',
                        'job_title_normalized'    => 'Security Officer',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => '25,000',
                        'vacancies'               => 2,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Technical / Hard Skills Missing', 'Soft / Employability Skills Missing'],
                        'technical_skills_missing'=> ['Surveillance Technology', 'Cybersecurity Knowledge & Setup', 'Hospitality Software Proficiency'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Time Management & Organization (e.g., prioritizing tasks, meeting deadlines)', 'Emotional Intelligence (e.g., self-awareness, empathy, handling pressure)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Lack of practical / hands-on experience', 'Poor communication skills'],
                        'coordination_frequency'  => 'Occasionally',
                    ],
                    [
                        'job_title'               => 'Housekeeping Head',
                        'job_title_normalized'    => 'Housekeeping Supervisor',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => '15,000
',
                        'vacancies'               => 1,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Soft / Employability Skills Missing'],
                        'technical_skills_missing'=> ['Hospitality Software Proficiency', 'Inventory and Asset Management', 'Sanitation and Chemical Safety'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Leadership & Initiative (e.g., self-motivation, taking ownership, proactiveness)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Poor communication skills'],
                        'coordination_frequency'  => 'Occasionally',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => ['Job fair Schedules'],
                    'specific_inputs' => null,
                ],
            ],

            // 3. Cavista Technology Inc.
            [
                'submission' => [
                    'company_name'    => 'Cavista Technology Inc.',
                    'respondent_name' => 'Sahara May G. Lao',
                    'position'        => 'General Manager',
                    'contact_number'  => '639989808985',
                    'contact_type'    => 'mobile',
                    'email'           => 'sgulfo@axxess.com',
                    'industry_sector' => 'Healthcare Technology / BPO',
                    'company_size'    => '51-200',
                   'status'          => 'pending',
                    'submitted_at'    => '2026-04-16 09:18:56',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Accountant Services Analyst',
                        'job_title_normalized'    => 'Accounting Analyst',
                        'job_classification'      => 'Accounting & Finance',
                        'salary_range'            => '₱30,000 - ₱59,999',
                        'vacancies'               => 3,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Technical / Hard Skills Missing', 'Soft / Employability Skills Missing'],
                        'technical_skills_missing'=> ['In-depth background on accounting and healthcare billing'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Professionalism & Accountability (e.g., work ethic, punctuality, responsibility, discipline)', 'Teamwork & Collaboration (e.g., working well with others, interpersonal skills)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Lack of practical / hands-on experience', 'Poor communication skills'],
                        'coordination_frequency'  => 'Never',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => ['A channel to submit real-time feedback'],
                    'specific_inputs' => null,
                ],
            ],

            // 4. Club Samal Resorts Development, Inc.
            [
                'submission' => [
                    'company_name'    => 'Club Samal Resorts Develo...',
                    'respondent_name' => 'ERIC JOHN I. ESPARAGOSA',
                    'position'        => 'Human Resource Manager',
                    'contact_number'  => '639171249593',
                    'contact_type'    => 'mobile',
                    'email'           => 'hrd.clubsamalresort@gmail.com',
                    'industry_sector' => 'Accommodation & Food Service (Hotels, Resorts, Restaurants, Fast Food Chains, Catering Services)',
                    'company_size'    => '51-200',
               'status'          => 'pending',
                    'submitted_at'    => '2026-04-16 09:18:56',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Receptionist / Resort Staff',
                        'job_title_normalized'    => 'Receptionist',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => '15,000
',
                        'vacancies'               => 3,
                        'vacancy_duration'        => '60-90 Days',
                        'difficulty_reasons'      => ['Soft / Employability Skills Missing', 'Technical / Hard Skills Missing'],
                        'technical_skills_missing'=> ['PMS operation (e.g., hotel booking systems)', 'Basic accounting', 'Email correspondence', 'Reservation handling'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Teamwork & Collaboration (e.g., working well with others, interpersonal skills)', 'Leadership & Initiative (e.g., self-motivation, taking ownership, proactiveness)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Poor communication skills', 'Low job readiness / poor interview performance'],
                        'coordination_frequency'  => 'Rarely',
                    ],
                    [
                        'job_title'               => 'Marketing Specialist',
                        'job_title_normalized'    => 'Marketing Specialist',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => '20,000',
                        'vacancies'               => 1,
                        'vacancy_duration'        => '60-90 Days',
                        'difficulty_reasons'      => ['Soft / Employability Skills Missing'],
                        'technical_skills_missing'=> ['Digital marketing skills', 'Proposal writing', 'Social media management', 'Basic data analysis'],
                        'soft_skills_missing'     => ['Professionalism & Accountability (e.g., work ethic, punctuality, responsibility, discipline)', 'Teamwork & Collaboration (e.g., working well with others, interpersonal skills)', 'Time Management & Organization (e.g., prioritizing tasks, meeting deadlines)', 'Emotional Intelligence (e.g., self-awareness, empathy, handling pressure)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Poor communication skills'],
                        'coordination_frequency'  => 'Rarely',
                    ],
                    [
                        'job_title'               => 'Cook / Kitchen Staff',
                        'job_title_normalized'    => 'Cook',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => '18,000',
                        'vacancies'               => 4,
                        'vacancy_duration'        => '60-90 Days',
                        'difficulty_reasons'      => ['Soft / Employability Skills Missing'],
                        'technical_skills_missing'=> ['Basic knife skills', 'Food preparation techniques', 'Kitchen hygiene standards', 'Inventory handling'],
                        'soft_skills_missing'     => ['Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Professionalism & Accountability (e.g., work ethic, punctuality, responsibility, discipline)', 'Adaptability & Willingness to Learn (e.g., flexibility, coachability, handling change)', 'Time Management & Organization (e.g., prioritizing tasks, meeting deadlines)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Poor communication skills', 'Low job readiness / poor interview performance'],
                        'coordination_frequency'  => 'Rarely',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates',
                        'A channel to submit real-time feedback',
                        'A directory of job placement offices',
                        'Job fair Schedules',
                    ],
                    'specific_inputs' => null,
                ],
            ],

            // 5. iQor Philippines-Davao
            [
                'submission' => [
                    'company_name'    => 'iQor Philippines-Davao',
                    'respondent_name' => 'Benj Silvano',
                    'position'        => 'Recruitment Manager',
                    'contact_number'  => '639296988684',
                    'contact_type'    => 'mobile',
                    'email'           => 'benj92silvano@gmail.com',
                    'industry_sector' => 'Administrative & Support Services / Information & Communication',
                    'company_size'    => 'More than 500',
             'status'          => 'pending',
                    'submitted_at'    => '2026-04-16 09:18:56',
                      ],
                'roles' => [
                    [
                        'job_title'               => 'Telehealth Customer Service Representatives',
                        'job_title_normalized'    => 'Customer Service Representative',
                        'job_classification'      => 'Customer Service & BPO (Contact Center)',
                        'salary_range'            => '18,000',
                        'vacancies'               => 10,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Technical / Hard Skills Missing', 'Soft / Employability Skills Missing'],
                        'technical_skills_missing'=> ['In-depth background on accounting and healthcare billing'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Adaptability & Willingness to Learn (e.g., flexibility, coachability, handling change)', 'Emotional Intelligence (e.g., self-awareness, empathy, handling pressure)'],
                        'impact_level'            => 'Low',
                        'rejection_reasons'       => ['Poor communication skills', 'Lack of practical / hands-on experience'],
                        'coordination_frequency'  => 'Rarely',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates',
                        'A directory of job placement offices',
                        'Job fair Schedules',
                    ],
                    'specific_inputs' => null,
                ],
            ],

            // 6. Aboitiz Renewables, Inc. | Hedcor
            [
                'submission' => [
                    'company_name'    => 'Aboitiz Renewables, Inc. | ...',
                    'respondent_name' => 'Adlawan, Rubynah Ai-ar',
                    'position'        => 'Head of Talent Optimization, Culture and Engagement',
                    'contact_number'  => '639175104742',
                    'contact_type'    => 'mobile',
                    'email'           => 'rubynah.adlawan@aboitizpower.com',
                    'industry_sector' => 'Electricity, Gas, Water & Waste Management (Power Plants, Electric Co-ops, Water Districts, Garbage/Recycling Firms)',
                    'company_size'    => 'More than 500',
         'status'          => 'pending',
                    'submitted_at'    => '2026-04-16 09:18:56',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Plant Engineer / PV or Wind Engineer',
                        'job_title_normalized'    => 'Plant Engineer',
                        'job_classification'      => 'Construction, Engineering & Architecture',
                        'salary_range'            => '₱30,000 - ₱59,999',
                        'vacancies'               => 2,
                        'vacancy_duration'        => 'More than 90 Days',
                        'difficulty_reasons'      => ['Technical / Hard Skills Missing', 'Soft / Employability Skills Missing'],
                        'technical_skills_missing'=> ['PV System Design', 'Electrical Engineering Fundamentals', 'Grid Integration and Interconnection'],
                        'soft_skills_missing'     => ['Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Professionalism & Accountability (e.g., work ethic, punctuality, responsibility, discipline)'],
                        'impact_level'            => 'High',
                        'rejection_reasons'       => ['Lack of practical / hands-on experience', 'Skills are outdated'],
                        'coordination_frequency'  => 'Frequently',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates',
                        'A directory of job placement offices',
                    ],
                    'specific_inputs' => null,
                ],
            ],

            // 7. Blue Lotus Hotel Corp (2nd submission)
            [
                'submission' => [
                    'company_name'    => 'Blue Lotus Hotel Corp',
                    'respondent_name' => 'Cyril Berboso',
                    'position'        => 'Human Resources Manager',
                    'contact_number'  => '639454016519',
                    'contact_type'    => 'mobile',
                    'email'           => 'hr2@bluelotushotel.com',
                    'industry_sector' => 'Accommodation & Food Service (Hotels, Resorts, Restaurants, Fast Food Chains, Catering Services)',
                    'company_size'    => '51-200',
                  'status'          => 'pending',
                    'submitted_at'    => '2026-04-16 09:18:56',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Housekeeping Manager and Supervisor',
                        'job_title_normalized'    => 'Housekeeping Manager',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => '25,000',
                        'vacancies'               => 2,
                        'vacancy_duration'        => 'More than 90 Days',
                        'difficulty_reasons'      => ['Soft / Employability Skills Missing', 'Technical / Hard Skills Missing'],
                        'technical_skills_missing'=> ['Hospitality Software Proficiency', 'Inventory and Asset Management', 'Sanitation and Chemical Safety'],
                        'soft_skills_missing'     => ['Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Poor communication skills'],
                        'coordination_frequency'  => 'Occasionally',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates',
                        'A channel to submit real-time feedback',
                        'A directory of job placement offices',
                        'Job fair Schedules',
                    ],
                    'specific_inputs' => null,
                ],
            ],

        ];

        // ── Insert all records ───────────────────────────────────────────
        foreach ($submissions as $data) {
            $now         = Carbon::now();
            $sub         = $data['submission'];
            $submittedAt = Carbon::parse($sub['submitted_at']);
            $isReviewed  = $sub['status'] !== 'pending';

            // 1. lmi_submissions
            $submissionId = DB::table('lmi_submissions')->insertGetId([
                'company_name'    => $sub['company_name'],
                'respondent_name' => $sub['respondent_name'],
                'position'        => $sub['position'],
                'contact_number'  => $sub['contact_number'],
                'contact_type'    => $sub['contact_type'],
                'email'           => $sub['email'],
                'industry_sector' => $sub['industry_sector'],
                'company_size'    => $sub['company_size'],
                'status'          => $sub['status'],
                'admin_notes'     => $isReviewed ? 'Reviewed and ' . $sub['status'] . '.' : null,
                'submitted_at'    => $submittedAt,
                'reviewed_at'     => $isReviewed ? $submittedAt->copy()->addDays(3) : null,
                'reviewed_by'     => null,
                'created_at'      => $now,
                'updated_at'      => null,
            ]);

            // 2. lmi_hard_to_fill_roles + 3. lmi_diagnosis (1 diagnosis per role)
            foreach ($data['roles'] as $role) {
                $roleId = DB::table('lmi_hard_to_fill_roles')->insertGetId([
                    'lmi_submission_id'        => $submissionId,
                    'job_title'                => $role['job_title'],
                    'job_title_normalized'     => $role['job_title_normalized'],
                    'job_classification'       => $role['job_classification'],
                    'salary_range'             => $role['salary_range'],
                    'vacancies'                => $role['vacancies'],
                    'vacancy_duration'         => $role['vacancy_duration'],
                    'difficulty_reasons'       => json_encode($role['difficulty_reasons']),
                    'technical_skills_missing' => json_encode($role['technical_skills_missing']),
                    'soft_skills_missing'      => json_encode($role['soft_skills_missing']),
                    'created_at'               => $now,
                    'updated_at'               => null,
                ]);

                DB::table('lmi_diagnosis')->insert([
                    'lmi_submission_id'            => $submissionId,
                    'lmi_hard_to_fill_role_id'     => $roleId,
                    'impact_level'                 => $role['impact_level'],
                    'rejection_reasons'            => json_encode($role['rejection_reasons']),
                    'rejection_reasons_other'      => null,
                    'coordination_frequency'       => $role['coordination_frequency'],
                    'coordination_frequency_other' => null,
                    'created_at'                   => $now,
                    'updated_at'                   => null,
                ]);
            }

            // 4. lmi_engagement (1 per submission)
            DB::table('lmi_engagement')->insert([
                'lmi_submission_id' => $submissionId,
                'lmi_features'      => json_encode($data['engagement']['lmi_features']),
                'specific_inputs'   => $data['engagement']['specific_inputs'],
                'created_at'        => $now,
                'updated_at'        => null,
            ]);
        }
    }
}