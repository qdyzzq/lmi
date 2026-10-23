<?php

/*
|--------------------------------------------------------------------------
| SPRS form structure
|--------------------------------------------------------------------------
|
| The single source of truth for the form: which tabs exist, which rows are
| in each, and which database column each row writes to.
|
| The prototype kept this in a JavaScript `categories` array where rows had
| a label but no field name, so an input could only be identified by its
| position in the table. Every field below is verified against the create
| migrations — every row maps to a real column, and every column has a row.
|
| Render Blade from this, and hand the same structure to the browser as JSON
| so the JS never holds its own copy that can drift out of sync.
|
| 'kind' controls the input: 'number' (default) or 'boolean'.
| 'total' marks a summary row for styling.
|
*/

return [
    'vacancies_solicited' => [
        'label' => 'Vacancies Solicited',
        'description' => 'Track job vacancies solicited from employers this month.',
        'model' => \App\Models\Sprs\VacanciesSolicited::class,
        'rows' => [
            ['label' => 'Total Vacancies', 'header' => true],
            ['label' => 'Overseas', 'field' => 'overseas'],
            ['label' => 'Local', 'field' => 'local'],
            ['label' => 'Government Local', 'field' => 'government_local'],
            ['label' => 'Government Overseas', 'field' => 'government_overseas'],
        ],
    ],

    'applicants_registered' => [
        'label' => 'Applicants Registered',
        'description' => 'Track jobseekers who registered this month.',
        'model' => \App\Models\Sprs\ApplicantsRegistered::class,
        'rows' => [
            ['label' => 'Total Applicants', 'header' => true],
            ['label' => 'Male', 'field' => 'male'],
            ['label' => 'Female', 'field' => 'female'],
        ],
    ],

    'referral_and_placement' => [
        'label' => 'Referral & Placement',
        'description' => 'Applicants referred and placed, private & government establishments.',
        'model' => \App\Models\Sprs\ReferralAndPlacement::class,
        'groups' => [
            [
                'header' => 'Referral & Placement (Private Establishment)',
                'rows' => [
                    ['label' => 'Total Applicants Referred', 'header' => true],
                    ['label' => 'Local Employment Total', 'field' => 'pvt_referred_local_total'],
                    ['label' => 'Female', 'field' => 'pvt_referred_local_female'],
                    ['label' => 'Overseas Employment Total', 'field' => 'pvt_referred_overseas_total'],
                    ['label' => 'Female', 'field' => 'pvt_referred_overseas_female'],
                    ['label' => 'Total Applicants Placed', 'header' => true],
                    ['label' => 'Local Employment Total', 'field' => 'pvt_placed_local_total'],
                    ['label' => 'Female', 'field' => 'pvt_placed_local_female'],
                    ['label' => 'Overseas Employment Total', 'field' => 'pvt_placed_overseas_total'],
                    ['label' => 'Female', 'field' => 'pvt_placed_overseas_female'],
                ],
            ],
            [
                'header' => 'Referral & Placement (Government Establishment)',
                'rows' => [
                    ['label' => 'Total Applicants Referred', 'header' => true],
                    ['label' => 'Local Employment Total', 'field' => 'govt_referred_local_total'],
                    ['label' => 'Female', 'field' => 'govt_referred_local_female'],
                    ['label' => 'Overseas Government Offices Total', 'field' => 'govt_referred_overseas_total'],
                    ['label' => 'Female', 'field' => 'govt_referred_overseas_female'],
                    ['label' => 'Total Applicants Placed', 'header' => true],
                    ['label' => 'Overseas Government Offices Total', 'field' => 'govt_placed_overseas_total_1'],
                    ['label' => 'Female', 'field' => 'govt_placed_overseas_female_1'],
                    ['label' => 'Overseas Government Offices Total', 'field' => 'govt_placed_overseas_total_2'],
                    ['label' => 'Female', 'field' => 'govt_placed_overseas_female_2'],
                ],
            ],
        ],
    ],

    'cgec' => [
        'label' => 'CGEC',
        'description' => 'Students provided with career guidance & employment coaching.',
        'model' => \App\Models\Sprs\Cgec::class,
        'rows' => [
            ['label' => 'Students Provided with Career Guidance', 'header' => true],
            ['label' => 'Female', 'field' => 'career_guidance_female'],
            ['label' => 'Students Provided with Employment Coaching', 'header' => true],
            ['label' => 'Female', 'field' => 'employment_coaching_female'],
        ],
    ],

    'dole_programs' => [
        'label' => 'DOLE Programs',
        'description' => 'Program-level accomplishments — organized by program.',
        'model' => \App\Models\Sprs\DoleProgram::class,
        'groups' => [
            [
                'header' => 'SPES (Special Programs for the Employment of Students)',
                'rows' => [
                    ['label' => 'No. of Beneficiaries (Regular SPES)', 'field' => 'spes_regular_total'],
                    ['label' => 'Female', 'field' => 'spes_regular_female'],
                    ['label' => 'No. of Beneficiaries (LGU Implemented)', 'field' => 'spes_lgu_implemented_total'],
                    ['label' => 'Female', 'field' => 'spes_lgu_implemented_female'],
                ],
            ],
            [
                'header' => 'Local and Special Recruitment Activity (LRA/SRA)',
                'rows' => [
                    ['label' => 'No. of LRA/SRA conducted', 'field' => 'lra_sra_conducted'],
                ],
            ],
            [
                'header' => 'JOBFAIRS / TNK',
                'rows' => [
                    ['label' => 'No. of Job Fairs/TNK Conducted', 'field' => 'job_fairs_tnk_conducted'],
                ],
            ],
            [
                'header' => 'JobStart Philippines Program',
                'rows' => [
                    ['label' => 'No. of Beneficiaries assisted (DOLE Funded)', 'field' => 'jobstart_dole_funded_total'],
                    ['label' => 'Female', 'field' => 'jobstart_dole_funded_female'],
                    ['label' => 'No. of Beneficiaries assisted (LGU Funded)', 'field' => 'jobstart_lgu_funded_total'],
                    ['label' => 'Female', 'field' => 'jobstart_lgu_funded_female'],
                ],
            ],
            [
                'header' => 'Government Internship Program (GIP) Facilitation',
                'rows' => [
                    ['label' => 'No. of Beneficiaries assisted', 'field' => 'gip_beneficiaries_total'],
                    ['label' => 'Female', 'field' => 'gip_beneficiaries_female'],
                ],
            ],
            [
                'header' => 'Tulong Panghanapbuhay sa Ating Disadvantaged/Displaced Workers (TUPAD)',
                'rows' => [
                    ['label' => 'No. of Beneficiaries assisted', 'field' => 'tupad_beneficiaries_total'],
                    ['label' => 'Female', 'field' => 'tupad_beneficiaries_female'],
                ],
            ],
            [
                'header' => 'Tulong at Alalay sa Taong may Kapansanan (TULAY)',
                'rows' => [
                    ['label' => 'No. of Beneficiaries assisted', 'field' => 'tulay_beneficiaries_total'],
                    ['label' => 'Female', 'field' => 'tulay_beneficiaries_female'],
                ],
            ],
            [
                'header' => 'Livelihood Assistance',
                'rows' => [
                    ['label' => 'No. of Beneficiaries assisted', 'field' => 'livelihood_beneficiaries_total'],
                    ['label' => 'Female', 'field' => 'livelihood_beneficiaries_female'],
                ],
            ],
            [
                'header' => 'MicroBiz Incubation Program (Grow Micro)',
                'rows' => [
                    ['label' => 'No. of Beneficiaries assisted', 'field' => 'microbiz_beneficiaries_total'],
                    ['label' => 'Female', 'field' => 'microbiz_beneficiaries_female'],
                ],
            ],
            [
                'header' => 'Community Skills Training / Livelihood & Entrepreneurship Program (CBT)',
                'rows' => [
                    ['label' => 'No. of Trainings Provided', 'field' => 'cbt_trainings_provided'],
                ],
            ],
            [
                'header' => 'OWWA Help Desk',
                'rows' => [
                    ['label' => 'No. of OFW\'s assisted', 'field' => 'owwa_assisted_total'],
                    ['label' => 'Female', 'field' => 'owwa_assisted_female'],
                ],
            ],
            [
                'header' => 'OFW Reintegration Program',
                'rows' => [
                    ['label' => 'No. of OFW\'s assisted', 'field' => 'ofw_reintegration_assisted_total'],
                    ['label' => 'Female', 'field' => 'ofw_reintegration_assisted_female'],
                ],
            ],
            [
                'header' => 'Anti-Illegal Recruitment Campaign (AIR Campaign)',
                'rows' => [
                    ['label' => 'No. of Advocacies/Campaign', 'field' => 'air_campaign_advocacies'],
                ],
            ],
            [
                'header' => 'Anti-Child Labor Advocacy',
                'rows' => [
                    ['label' => 'No. of Advocacies/Campaign', 'field' => 'anti_child_labor_advocacies'],
                ],
            ],
            [
                'header' => 'Kasambahay Law Advocacy',
                'rows' => [
                    ['label' => 'No. of Advocacies/Campaign', 'field' => 'kasambahay_law_advocacies'],
                ],
            ],
            [
                'header' => 'On the Job Training (OJT) Facilitation / Workers Appreciation Program (WAP)',
                'rows' => [
                    ['label' => 'No. of OJTs assisted', 'field' => 'ojt_wap_assisted_total'],
                    ['label' => 'Female', 'field' => 'ojt_wap_assisted_female'],
                ],
            ],
            [
                'header' => 'Workers Hiring for Infrastructure Projects (WHIP)',
                'rows' => [
                    ['label' => 'No. of Placed under WHIP', 'field' => 'whip_placed_total'],
                    ['label' => 'Female', 'field' => 'whip_placed_female'],
                ],
            ],
            [
                'header' => 'Registration of Workers Association (RWA)',
                'rows' => [
                    ['label' => 'No. of Associations Registered', 'field' => 'rwa_associations_registered'],
                ],
            ],
            [
                'header' => 'Labor Complaints Referral',
                'rows' => [
                    ['label' => 'No. of Pax Assisted/Referred to DOLE, NLRC, NCMB', 'field' => 'labor_complaints_referred_total'],
                    ['label' => 'Female', 'field' => 'labor_complaints_referred_female'],
                ],
            ],
            [
                'header' => 'Green our DOLE Program',
                'rows' => [
                    ['label' => 'No. of Advocacies/Campaign', 'field' => 'green_dole_advocacies'],
                ],
            ],
        ],
    ],

    'lmi' => [
        'label' => 'LMI',
        'description' => 'Labor Market Information — Forms 1, 2 and 5.',
        'model' => \App\Models\Sprs\Lmi::class,
        'groups' => [
            [
                'header' => 'LMI Form 1 — Individuals Reached with LMI',
                'rows' => [
                    ['label' => 'Youth', 'field' => 'form1_youth'],
                    ['label' => 'Non-Youth', 'field' => 'form1_non_youth'],
                ],
            ],
            [
                'header' => 'Optional',
                'rows' => [
                    ['label' => 'Female Youth', 'field' => 'form1_female_youth'],
                    ['label' => 'Female Non-Youth', 'field' => 'form1_female_non_youth'],
                    // Computed, not stored — a saved copy can disagree with
                    // its own parts as soon as one of them is edited.
                    ['label' => 'Total Female Reached with LMI', 'computed' => true,
                     'sum_of' => ['form1_female_youth', 'form1_female_non_youth']],
                ],
            ],
            [
                'header' => 'LMI Form 2',
                'rows' => [
                    ['label' => 'Institutions Reached', 'field' => 'form2_institutions_reached'],
                ],
            ],
            [
                'header' => 'LMI Form 5 — Individuals Reached with New Media',
                'rows' => [
                    ['label' => 'Youth', 'field' => 'form5_youth'],
                    ['label' => 'Non-Youth', 'field' => 'form5_non_youth'],
                ],
            ],
            [
                'header' => 'Optional',
                'rows' => [
                    ['label' => 'Female Youth', 'field' => 'form5_female_youth'],
                    ['label' => 'Female Non-Youth', 'field' => 'form5_female_non_youth'],
                    ['label' => 'Total Female Reached with New Media', 'computed' => true,
                     'sum_of' => ['form5_female_youth', 'form5_female_non_youth']],
                ],
            ],
        ],
    ],

    'employment_awards' => [
        'label' => 'Employment Awards',
        'description' => 'Recipients of employment-related awards.',
        'model' => \App\Models\Sprs\EmploymentAwards::class,
        'rows' => [
            ['label' => 'International Awards', 'field' => 'international_awards'],
            ['label' => 'National Awards', 'field' => 'national_awards'],
            ['label' => 'Leadership / Membership Contribution', 'field' => 'leadership_membership_orgs'],
        ],
    ],

    'other_activities' => [
        'label' => 'Other Activities',
        'description' => 'Add any activity not captured in the categories above.',
        'model' => \App\Models\Sprs\OtherActivity::class,
        // The one repeating tab: rows are user-created, not fixed. Each row is
        // an other_activities record with its own id, so edits and deletes
        // target a real key instead of a position in the table.
        'dynamic' => true,
        'row_fields' => ['activity_name', 'count'],
    ],
];