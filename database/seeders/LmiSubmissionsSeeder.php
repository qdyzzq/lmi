<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LmiSubmissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Real survey responses from DOLE XI Industry Skills Needs Survey ──

        $submissions = [

            // 1. Dynata Philippines, Inc.
            [
                'submission' => [
                    'company_name'    => 'Dynata Philippines, Inc.',
                    'respondent_name' => 'Flor Vimin Biliran',
                    'position'        => 'HR Generalist',
                    'contact_number'  => 'N/A',
                    'contact_type'    => 'mobile',
                    'email'           => 'DavaoHR@dynata.com',
                    'industry_sector' => 'Information & Communication (Software Companies, ISPs, Telecoms, TV/Radio Stations, Non-Voice Tech BPO)',
                    'company_size'    => '201 – 500',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-01-22 11:26:01',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Telephone Interviewer',
                        'job_title_normalized'    => 'Telephone Interviewer',
                        'job_classification'      => 'IT, Software, Data & Digital Creative',
                        'salary_range'            => null,
                        'vacancies'               => 3,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Lack of required technical/hard skills', 'Weak soft skills (Attitude, work values mismatch, professionalism, interpersonal skills)', 'Salary expectations are too high'],
                        'technical_skills_missing'=> ['Proficiency in Scheduling', 'Email Management', 'Project Management Tools'],
                        'soft_skills_missing'     => [],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Lack of required technical/hard skills', 'Weak soft skills'],
                        'coordination_frequency'  => 'Occasionally (During OJT placement)',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => ['A directory of job placement offices and Public Employment Services Office (PESO)', 'Job fair Schedules'],
                    'specific_inputs' => null,
                ],
            ],

            // 2. PRYCE GASES, INC.
            [
                'submission' => [
                    'company_name'    => 'PRYCE GASES, INC.',
                    'respondent_name' => 'Ardhen Nacion',
                    'position'        => 'Operations Supervisor',
                    'contact_number'  => 'N/A',
                    'contact_type'    => 'mobile',
                    'email'           => 'arden.nacion@prycegases.com',
                    'industry_sector' => 'Retailer of LPG',
                    'company_size'    => '51 – 200',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-01-28 09:00:18',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Production Worker',
                        'job_title_normalized'    => 'Production Worker',
                        'job_classification'      => 'Manufacturing and Production',
                        'salary_range'            => null,
                        'vacancies'               => 2,
                        'vacancy_duration'        => 'Less than 30 Days',
                        'difficulty_reasons'      => ['Location or mobility issues'],
                        'technical_skills_missing'=> ['Fabrication & Metalworking', 'Valve Assembly & Testing'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Time Management & Organization (e.g., prioritizing tasks, meeting deadlines)'],
                        'impact_level'            => 'Low',
                        'rejection_reasons'       => ['Location or mobility issues'],
                        'coordination_frequency'  => 'Never',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => ['Job fair Schedules'],
                    'specific_inputs' => null,
                ],
            ],

            // 3. Magsaysay Maritime Corporation
            [
                'submission' => [
                    'company_name'    => 'Magsaysay Maritime Corporation',
                    'respondent_name' => 'Richard Maynopas',
                    'position'        => 'TSDM / Regional Officer',
                    'contact_number'  => 'N/A',
                    'contact_type'    => 'mobile',
                    'email'           => 'annaliza.claveria@magsaysay.com',
                    'industry_sector' => 'Maritime Industry',
                    'company_size'    => 'More than 500',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-02-03 09:18:56',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Captain, Engineers, Officers',
                        'job_title_normalized'    => 'Ship Officer',
                        'job_classification'      => 'Administrative, HR & Office Support',
                        'salary_range'            => null,
                        'vacancies'               => 5,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Lack of required technical/hard skills', 'Insufficient work experience'],
                        'technical_skills_missing'=> ['Proficiency in maneuvering Large Foreign Vessels', 'Engine & Technical Management of Foreign Vessels'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Emotional Intelligence (e.g., self-awareness, empathy, handling pressure)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Lack of required technical/hard skills', 'Insufficient work experience'],
                        'coordination_frequency'  => 'Rarely (Only when invited to graduations/events)',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => ['A directory of job placement offices and Public Employment Services Office (PESO)', 'Job fair Schedules'],
                    'specific_inputs' => null,
                ],
            ],

            // 4. Therma South, Inc.
            [
                'submission' => [
                    'company_name'    => 'Therma South, Inc.',
                    'respondent_name' => 'Karla Marie Beronilla',
                    'position'        => 'HR Senior Generalist',
                    'contact_number'  => '09670792293',
                    'contact_type'    => 'mobile',
                    'email'           => 'karla.beronilla@aboitizpower.com',
                    'industry_sector' => 'Electricity, Gas, Water & Waste Management (Power Plants, Electric Co-ops, Water Districts, Garbage/Recycling Firms)',
                    'company_size'    => '201 – 500',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-02-09 09:41:30',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Engineers',
                        'job_title_normalized'    => 'Engineer',
                        'job_classification'      => 'Construction, Engineering & Architecture',
                        'salary_range'            => 'PHP 60,000 - PHP 89,999',
                        'vacancies'               => 3,
                        'vacancy_duration'        => '60-90 Days',
                        'difficulty_reasons'      => ['Lack of required technical/hard skills'],
                        'technical_skills_missing'=> ['Electrical Maintenance & Troubleshooting', 'Reading Technical Drawings', 'Electrical Design and Simulation Software'],
                        'soft_skills_missing'     => [],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Lack of required technical/hard skills'],
                        'coordination_frequency'  => 'Rarely (Only when invited to graduations/events)',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates (e.g., "How many IT grads will graduate next year?")',
                        'A channel to submit real-time feedback on curriculum quality',
                        'A directory of job placement offices and Public Employment Services Office (PESO)',
                        'Job fair Schedules',
                    ],
                    'specific_inputs' => null,
                ],
            ],

            // 5. Blue Lotus Hotel
            [
                'submission' => [
                    'company_name'    => 'Blue Lotus Hotel',
                    'respondent_name' => 'Cyril Berboso',
                    'position'        => 'Human Resources Head',
                    'contact_number'  => '9454016510',
                    'contact_type'    => 'mobile',
                    'email'           => 'hrcy0407@gmail.com',
                    'industry_sector' => 'Accommodation & Food Service (Hotels, Resorts, Restaurants, Fast Food Chains, Catering Services)',
                    'company_size'    => '51 – 200',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-02-09 11:59:02',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Security Officer, IT and Accounting',
                        'job_title_normalized'    => 'Security Officer',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => 'PHP 15,000 - PHP 25,000',
                        'vacancies'               => 2,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Lack of required technical/hard skills'],
                        'technical_skills_missing'=> ['Surveillance Technology', 'Cybersecurity Knowledge & Setup', 'Hospitality Software Proficiency'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Time Management & Organization (e.g., prioritizing tasks, meeting deadlines)', 'Emotional Intelligence (e.g., self-awareness, empathy, handling pressure)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Lack of required technical/hard skills', 'Weak soft skills'],
                        'coordination_frequency'  => 'Occasionally (During OJT placement)',
                    ],
                    [
                        'job_title'               => 'Housekeeping Head',
                        'job_title_normalized'    => 'Housekeeping Supervisor',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => 'PHP 15,000 - PHP 25,000',
                        'vacancies'               => 1,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Weak soft skills (Attitude, work values mismatch, professionalism, interpersonal skills)'],
                        'technical_skills_missing'=> ['Hospitality Software Proficiency', 'Inventory and Asset Management', 'Sanitation and Chemical Safety'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Leadership & Initiative (e.g., self-motivation, taking ownership, proactiveness)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Weak soft skills'],
                        'coordination_frequency'  => 'Occasionally (During OJT placement)',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => ['Job fair Schedules'],
                    'specific_inputs' => null,
                ],
            ],

            // 6. AboitizPower
            [
                'submission' => [
                    'company_name'    => 'AboitizPower',
                    'respondent_name' => 'Mikaela Damay',
                    'position'        => 'Talent Attraction Sr. Specialist',
                    'contact_number'  => 'N/A',
                    'contact_type'    => 'mobile',
                    'email'           => 'mikaela.leynes.damay@aboitizpower.com',
                    'industry_sector' => 'Electricity, Gas, Water & Waste Management (Power Plants, Electric Co-ops, Water Districts, Garbage/Recycling Firms)',
                    'company_size'    => '201 – 500',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-02-10 11:11:23',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Boiler Specialist',
                        'job_title_normalized'    => 'Boiler Specialist',
                        'job_classification'      => 'Construction, Engineering & Architecture',
                        'salary_range'            => 'PHP 60,000 - PHP 89,999',
                        'vacancies'               => 2,
                        'vacancy_duration'        => '60-90 Days',
                        'difficulty_reasons'      => ['Insufficient work experience', 'Certification/Licensing requirements not met', 'Salary expectations are too high', 'Location or mobility issues'],
                        'technical_skills_missing'=> ['Mechanical Troubleshooting and Repair', 'Boiler Operation and Control', 'Rigging and Piping'],
                        'soft_skills_missing'     => [],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Insufficient work experience', 'Certification/Licensing requirements not met'],
                        'coordination_frequency'  => 'Occasionally (During OJT placement)',
                    ],
                    [
                        'job_title'               => 'Bulk Materials Handling Specialist',
                        'job_title_normalized'    => 'Materials Handling Specialist',
                        'job_classification'      => 'Construction, Engineering & Architecture',
                        'salary_range'            => 'PHP 60,000 - PHP 89,999',
                        'vacancies'               => 2,
                        'vacancy_duration'        => '60-90 Days',
                        'difficulty_reasons'      => ['Insufficient work experience', 'Certification/Licensing requirements not met'],
                        'technical_skills_missing'=> ['Heavy Machinery Handling', 'Inventory Management Systems (ERP)', 'Stationary Equipment Maintenance'],
                        'soft_skills_missing'     => [],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Insufficient work experience'],
                        'coordination_frequency'  => 'Occasionally (During OJT placement)',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'A channel to submit real-time feedback on curriculum quality',
                        'A directory of job placement offices and Public Employment Services Office (PESO)',
                        'Job fair Schedules',
                    ],
                    'specific_inputs' => null,
                ],
            ],

            // 7. Cavista Technology Inc.
            [
                'submission' => [
                    'company_name'    => 'Cavista Technology Inc.',
                    'respondent_name' => 'Sahara May G. Lao',
                    'position'        => 'General Manager',
                    'contact_number'  => '+639989808985',
                    'contact_type'    => 'mobile',
                    'email'           => 'Sgulfo@axxess.com',
                    'industry_sector' => 'Healthcare Technology / BPO',
                    'company_size'    => '51 – 200',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-02-11 02:25:46',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Accountant Services Analyst',
                        'job_title_normalized'    => 'Accounting Analyst',
                        'job_classification'      => 'Accounting & Finance',
                        'salary_range'            => 'PHP 30,000 - PHP 59,999',
                        'vacancies'               => 3,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Lack of required technical/hard skills', 'Weak soft skills (Attitude, work values mismatch, professionalism, interpersonal skills)', 'Salary expectations are too high'],
                        'technical_skills_missing'=> ['In-depth background on accounting and healthcare billing'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Professionalism & Accountability (e.g., work ethic, punctuality, responsibility, discipline)', 'Teamwork & Collaboration (e.g., working well with others, interpersonal skills)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Lack of required technical/hard skills', 'Weak soft skills'],
                        'coordination_frequency'  => 'Never',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => ['A channel to submit real-time feedback on curriculum quality'],
                    'specific_inputs' => null,
                ],
            ],

            // 8. Club Samal Resorts Development, Inc.
            [
                'submission' => [
                    'company_name'    => 'Club Samal Resorts Development, Inc.',
                    'respondent_name' => 'Eric John I. Esparagosa',
                    'position'        => 'Human Resource Manager',
                    'contact_number'  => '09171249593',
                    'contact_type'    => 'mobile',
                    'email'           => 'hrd.clubsamalresort@gmail.com',
                    'industry_sector' => 'Accommodation & Food Service (Hotels, Resorts, Restaurants, Fast Food Chains, Catering Services)',
                    'company_size'    => '51 – 200',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-02-15 10:38:57',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Receptionist / Resort Staff',
                        'job_title_normalized'    => 'Receptionist',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => 'PHP 15,000',
                        'vacancies'               => 3,
                        'vacancy_duration'        => '60-90 Days',
                        'difficulty_reasons'      => ['Weak soft skills (Attitude, work values mismatch, professionalism, interpersonal skills)', 'Salary expectations are too high', 'Location or mobility issues'],
                        'technical_skills_missing'=> ['PMS operation (e.g., hotel booking systems)', 'Basic accounting', 'Email correspondence', 'Reservation handling'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Teamwork & Collaboration (e.g., working well with others, interpersonal skills)', 'Leadership & Initiative (e.g., self-motivation, taking ownership, proactiveness)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Weak soft skills', 'Salary expectations are too high'],
                        'coordination_frequency'  => 'Rarely (Only when invited to graduations/events)',
                    ],
                    [
                        'job_title'               => 'Marketing Specialist',
                        'job_title_normalized'    => 'Marketing Specialist',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => 'PHP 20,000',
                        'vacancies'               => 1,
                        'vacancy_duration'        => '60-90 Days',
                        'difficulty_reasons'      => ['Weak soft skills (Attitude, work values mismatch, professionalism, interpersonal skills)'],
                        'technical_skills_missing'=> ['Digital marketing skills', 'Proposal writing', 'Social media management', 'Basic data analysis'],
                        'soft_skills_missing'     => ['Professionalism & Accountability (e.g., work ethic, punctuality, responsibility, discipline)', 'Teamwork & Collaboration (e.g., working well with others, interpersonal skills)', 'Time Management & Organization (e.g., prioritizing tasks, meeting deadlines)', 'Emotional Intelligence (e.g., self-awareness, empathy, handling pressure)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Weak soft skills'],
                        'coordination_frequency'  => 'Rarely (Only when invited to graduations/events)',
                    ],
                    [
                        'job_title'               => 'Cook / Kitchen Staff',
                        'job_title_normalized'    => 'Cook',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => 'PHP 18,000',
                        'vacancies'               => 4,
                        'vacancy_duration'        => '60-90 Days',
                        'difficulty_reasons'      => ['Weak soft skills (Attitude, work values mismatch, professionalism, interpersonal skills)', 'Location or mobility issues'],
                        'technical_skills_missing'=> ['Basic knife skills', 'Food preparation techniques', 'Kitchen hygiene standards', 'Inventory handling'],
                        'soft_skills_missing'     => ['Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Professionalism & Accountability (e.g., work ethic, punctuality, responsibility, discipline)', 'Adaptability & Willingness to Learn (e.g., flexibility, coachability, handling change)', 'Time Management & Organization (e.g., prioritizing tasks, meeting deadlines)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Weak soft skills', 'Location or mobility issues'],
                        'coordination_frequency'  => 'Rarely (Only when invited to graduations/events)',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates (e.g., "How many IT grads will graduate next year?")',
                        'A channel to submit real-time feedback on curriculum quality',
                        'A directory of job placement offices and Public Employment Services Office (PESO)',
                        'Job fair Schedules',
                    ],
                    'specific_inputs' => null,
                ],
            ],

            // 9. iQor Philippines-Davao
            [
                'submission' => [
                    'company_name'    => 'iQor Philippines-Davao',
                    'respondent_name' => 'Benj Silvano',
                    'position'        => 'Recruitment Manager',
                    'contact_number'  => '09296988684',
                    'contact_type'    => 'mobile',
                    'email'           => 'benji.silvano@iqor.com',
                    'industry_sector' => 'Administrative & Support Services / Information & Communication',
                    'company_size'    => 'More than 500',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-02-18 21:42:29',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Telehealth Customer Service Representatives',
                        'job_title_normalized'    => 'Customer Service Representative',
                        'job_classification'      => 'Customer Service & BPO (Contact Center)',
                        'salary_range'            => 'PHP 18,000 - PHP 21,000',
                        'vacancies'               => 10,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Lack of required technical/hard skills', 'Weak soft skills (Attitude, work values mismatch, professionalism, interpersonal skills)', 'Salary expectations are too high'],
                        'technical_skills_missing'=> ['In-depth background on accounting and healthcare billing'],
                        'soft_skills_missing'     => ['Communication Skills (e.g., verbal clarity, professional writing, active listening)', 'Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Adaptability & Willingness to Learn (e.g., flexibility, coachability, handling change)', 'Emotional Intelligence (e.g., self-awareness, empathy, handling pressure)'],
                        'impact_level'            => 'Low',
                        'rejection_reasons'       => ['Weak soft skills', 'Lack of required technical/hard skills'],
                        'coordination_frequency'  => 'Rarely (Only when invited to graduations/events)',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates (e.g., "How many IT grads will graduate next year?")',
                        'A directory of job placement offices and Public Employment Services Office (PESO)',
                        'Job fair Schedules',
                    ],
                    'specific_inputs' => null,
                ],
            ],

            // 10. Aboitiz Renewables, Inc. | Hedcor
            [
                'submission' => [
                    'company_name'    => 'Aboitiz Renewables, Inc. | Hedcor',
                    'respondent_name' => 'Adlawan, Rubynah Ai-ar',
                    'position'        => 'Head of Talent Optimization, Culture and Engagement',
                    'contact_number'  => '09175104742',
                    'contact_type'    => 'mobile',
                    'email'           => 'rubynah.adlawan@aboitizpower.com',
                    'industry_sector' => 'Electricity, Gas, Water & Waste Management (Power Plants, Electric Co-ops, Water Districts, Garbage/Recycling Firms)',
                    'company_size'    => 'More than 500',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-02-19 11:07:17',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Plant Engineer / PV or Wind Engineer',
                        'job_title_normalized'    => 'Plant Engineer',
                        'job_classification'      => 'Construction, Engineering & Architecture',
                        'salary_range'            => 'PHP 30,000 - PHP 59,999',
                        'vacancies'               => 2,
                        'vacancy_duration'        => 'More than 90 Days',
                        'difficulty_reasons'      => ['Lack of required technical/hard skills', 'Insufficient work experience', 'Certification/Licensing requirements not met', 'Location or mobility issues'],
                        'technical_skills_missing'=> ['PV System Design', 'Electrical Engineering Fundamentals', 'Grid Integration and Interconnection'],
                        'soft_skills_missing'     => ['Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Professionalism & Accountability (e.g., work ethic, punctuality, responsibility, discipline)'],
                        'impact_level'            => 'High',
                        'rejection_reasons'       => ['Lack of required technical/hard skills', 'Insufficient work experience', 'Certification/Licensing requirements not met'],
                        'coordination_frequency'  => 'Frequently (We sit on advisory boards/curriculum reviews)',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates (e.g., "How many IT grads will graduate next year?")',
                        'A directory of job placement offices and Public Employment Services Office (PESO)',
                    ],
                    'specific_inputs' => null,
                ],
            ],

            // 11. Blue Lotus Hotel Corp (2nd submission)
            [
                'submission' => [
                    'company_name'    => 'Blue Lotus Hotel Corp',
                    'respondent_name' => 'Cyril Berboso',
                    'position'        => 'Human Resources Manager',
                    'contact_number'  => '09454016519',
                    'contact_type'    => 'mobile',
                    'email'           => 'hr2@bluelotushotel.com',
                    'industry_sector' => 'Accommodation & Food Service (Hotels, Resorts, Restaurants, Fast Food Chains, Catering Services)',
                    'company_size'    => '51 – 200',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-02-25 08:39:28',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Housekeeping Manager and Supervisor',
                        'job_title_normalized'    => 'Housekeeping Manager',
                        'job_classification'      => 'Tourism, Hospitality & Food Service',
                        'salary_range'            => 'PHP 25,000',
                        'vacancies'               => 2,
                        'vacancy_duration'        => 'More than 90 Days',
                        'difficulty_reasons'      => ['Weak soft skills (Attitude, work values mismatch, professionalism, interpersonal skills)'],
                        'technical_skills_missing'=> ['Hospitality Software Proficiency', 'Inventory and Asset Management', 'Sanitation and Chemical Safety'],
                        'soft_skills_missing'     => ['Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Weak soft skills'],
                        'coordination_frequency'  => 'Occasionally (During OJT placement)',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates (e.g., "How many IT grads will graduate next year?")',
                        'A channel to submit real-time feedback on curriculum quality',
                        'A directory of job placement offices and Public Employment Services Office (PESO)',
                        'Job fair Schedules',
                    ],
                    'specific_inputs' => null,
                ],
            ],

            // 12. Apo Agua Infrastructura, Inc.
            [
                'submission' => [
                    'company_name'    => 'Apo Agua Infrastructura, Inc.',
                    'respondent_name' => 'Jackie Tiu Bot',
                    'position'        => 'People & Culture Manager',
                    'contact_number'  => 'N/A',
                    'contact_type'    => 'mobile',
                    'email'           => 'jacqueline.tiubot@apoagua.com',
                    'industry_sector' => 'Electricity, Gas, Water & Waste Management (Power Plants, Electric Co-ops, Water Districts, Garbage/Recycling Firms)',
                    'company_size'    => '51 – 200',
                    'status'          => 'approved',
                    'submitted_at'    => '2026-02-26 09:10:50',
                ],
                'roles' => [
                    [
                        'job_title'               => 'Asset / Capex and Risk Management Specialist',
                        'job_title_normalized'    => 'Risk Management Specialist',
                        'job_classification'      => 'Construction, Engineering & Architecture',
                        'salary_range'            => 'PHP 90,000 - PHP 149,999',
                        'vacancies'               => 1,
                        'vacancy_duration'        => '30-60 Days',
                        'difficulty_reasons'      => ['Lack of required technical/hard skills', 'Weak soft skills (Attitude, work values mismatch, professionalism, interpersonal skills)'],
                        'technical_skills_missing'=> ['Hydraulic Modeling & Simulation', 'Water Safety Plans', 'Regulatory Compliance Analysis', 'Asset Condition Assessment & Monitoring'],
                        'soft_skills_missing'     => ['Critical Thinking & Problem-Solving (e.g., analytical skills, decision-making, creativity)', 'Professionalism & Accountability (e.g., work ethic, punctuality, responsibility, discipline)'],
                        'impact_level'            => 'Medium',
                        'rejection_reasons'       => ['Lack of required technical/hard skills', 'Weak soft skills'],
                        'coordination_frequency'  => 'Occasionally (During OJT placement)',
                    ],
                ],
                'engagement' => [
                    'lmi_features'    => [
                        'Viewing the supply of graduates (e.g., "How many IT grads will graduate next year?")',
                        'A directory of job placement offices and Public Employment Services Office (PESO)',
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
                'reviewed_by'     => null, // Set to a valid users.id if needed
                'created_at'      => $now,
                'updated_at'      => $now,
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
                    'updated_at'               => $now,
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
                    'updated_at'                   => $now,
                ]);
            }

            // 4. lmi_engagement (1 per submission)
            DB::table('lmi_engagement')->insert([
                'lmi_submission_id' => $submissionId,
                'lmi_features'      => json_encode($data['engagement']['lmi_features']),
                'specific_inputs'   => $data['engagement']['specific_inputs'],
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }
    }
}