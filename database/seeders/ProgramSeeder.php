<?php
namespace Database\Seeders;
use App\Models\Program;
use App\Models\ProgramQualification;
use App\Models\ProgramStory;
use App\Models\ProgramTestimonial;
use Illuminate\Database\Seeder;
use App\Models\CarouselSlide;


class ProgramSeeder extends Seeder
{
    public function run()
    {
        CarouselSlide::insert([
    [
        'title'         => 'From GIP Beneficiary to City HR Leader',
        'excerpt'       => 'Genevieve Elan Palmera\'s journey from an intern to a permanent HR position showcases the transformative power of the Government Internship Program.',
        'link'          => 'https://ro11.dole.gov.ph/news/from-dole-gip-beneficiary-to-city-hr-leader-a-youth-employment-success-story/',
        'image_path'    => 'images/testimonials/GIP.jpg',
        'program_label' => 'GIP',
      
        'sort_order'    => 1,
        'is_active'     => 1,
        'created_at'    => now(),
        'updated_at'    => now(),
    ],
    [
        'title'         => 'Camp Holidays JobStart Success',
        'excerpt'       => 'From interns to full-fledged employees - discover how JobStart graduates transformed their careers through hands-on experience and dedication.',
        'link'          => 'https://ro11.dole.gov.ph/news/the-success-story-of-camp-holidays-jobstart-graduates-from-interns-to-full-fledged-employees/',
        'image_path'    => 'images/testimonials/jobstart.jpg',
        'program_label' => 'JobStart',
     
        'sort_order'    => 2,
        'is_active'     => 1,
        'created_at'    => now(),
        'updated_at'    => now(),
    ],
    [
        'title'         => 'SPES Grantee Achieves Latin Honors',
        'excerpt'       => 'Khacley Marino\'s inspiring journey from SPES beneficiary to graduating with Latin honors proves that financial support can unlock academic excellence.',
        'link'          => 'https://ro11.dole.gov.ph/news/spes-grantee-achieves-latin-honors-and-graduation-success-khacley-marinos-inspiring-journey/',
        'image_path'    => 'images/testimonials/spes.jpeg',
        'program_label' => 'SPES',
        'sort_order'    => 3,
        'is_active'     => 1,
        'created_at'    => now(),
        'updated_at'    => now(),
    ],
    [
        'title'         => 'The Courage to Begin with CDSP',
        'excerpt'       => 'Philip Tecson\'s journey fresh out of college with DOLE\'s Career Development Service Program shows how proper guidance can shape a successful career path.',
        'link'          => 'https://ro11.dole.gov.ph/news/the-courage-to-begin-philip-tecsons-journey-fresh-out-of-college-with-doles-career-development-service-program/',
        'image_path'    => 'images/testimonials/CDSP.jpg',
        'program_label' => 'CDSP',
     
        'sort_order'    => 4,
        'is_active'     => 1,
        'created_at'    => now(),
        'updated_at'    => now(),
    ],
]);
        // ── GIP ──
        $gip = Program::create([
            'name'        => 'Government Internship Program',
            'acronym'     => 'GIP',
            'description' => 'A youth employability program which aims to provide 3–6 months internship opportunity in the government for high school, technical-vocational or college graduates to build their capabilities and make them more employable.',
            'color'       => 'green',
            'logo_path'   => 'images/logo-programs/gip_logo.png',
            'sort_order'  => 1,
        ]);
        ProgramQualification::insert([
            ['program_id' => $gip->id, 'type' => 'qualification', 'content' => '18 to 30 years old, with exceptions determined by DOLE Regional Offices', 'sort_order' => 1],
            ['program_id' => $gip->id, 'type' => 'qualification', 'content' => 'Individuals aged 31+ may qualify under specific conditions (no/intermittent work experience, laid off, or displaced by disasters)', 'sort_order' => 2],
            ['program_id' => $gip->id, 'type' => 'requirement',   'content' => 'High school / Senior High School Graduate or equivalent, or technical-vocational graduate', 'sort_order' => 1],
            ['program_id' => $gip->id, 'type' => 'requirement',   'content' => 'Victims of armed conflicts, rebel returnees, PWDs and Indigenous Peoples also eligible', 'sort_order' => 2],
        ]);
        ProgramTestimonial::create([
            'program_id'  => $gip->id,
            'quote'       => 'The program allowed me to prove my capability and work ethic. It was the bridge from where I started, uncertain and struggling, to where I stand now, more confident, more skilled, and part of an institution I deeply respect.',
            'author_name' => 'Genevieve Elan Palmera',
            'author_role' => 'GIP Beneficiary',
        ]);
        foreach ([
            ['title' => 'From GIP Beneficiary to City HR Leader',         'image_path' => 'images/gip-story/gipstory-1.jpg', 'link' => 'https://ro11.dole.gov.ph/news/from-dole-gip-beneficiary-to-city-hr-leader-a-youth-employment-success-story/'],
            ['title' => 'From Financial Hardship to Summa Cum Laude',     'image_path' => 'images/gip-story/gipstory-2.jpg', 'link' => 'https://ro11.dole.gov.ph/news/from-financial-hardship-to-academic-excellence-sharny-lee-basartes-journey-from-dole-gip-beneficiary-to-summa-cum-laude-graduate/'],
            ['title' => 'From GIP to Development Management Officer II',  'image_path' => 'images/gip-story/gipstory-3.jpg', 'link' => 'https://ro11.dole.gov.ph/news/from-dole-gip-beneficiary-to-development-management-officer-ii-rasty-vistars-path-to-success-in-samals-local-government/'],
            ['title' => 'From GIP Beneficiary to Information Officer II', 'image_path' => 'images/gip-story/gipstory-4.jpg', 'link' => 'https://ro11.dole.gov.ph/news/from-gip-beneficiary-to-information-officer-ii-novy-b-cretas-journey-of-excellence/'],
            ['title' => 'A Journey of Youth Employability',               'image_path' => 'images/gip-story/gipstory-5.jpg', 'link' => 'https://ro11.dole.gov.ph/news/a-journey-of-youth-employability-program-beneficiary/'],
        ] as $i => $s) {
            ProgramStory::create(array_merge($s, ['program_id' => $gip->id, 'sort_order' => $i + 1]));
        }

        // ── JobStart ──
        $jobstart = Program::create([
            'name'        => 'JobStart Program',
            'acronym'     => 'JobStart',
            'description' => 'A youth employability program which aims to shorten the school-to-work transition of youth not in education, employment, or training by providing them with career coaching, life skills and technical training, and internships with employers.',
            'color'       => 'red',
            'logo_path'   => 'images/logo-programs/jobstart_logo.png',
            'sort_order'  => 2,
        ]);
        ProgramQualification::insert([
            ['program_id' => $jobstart->id, 'type' => 'qualification', 'content' => 'Filipino Citizen; 18–24 years old', 'sort_order' => 1],
            ['program_id' => $jobstart->id, 'type' => 'qualification', 'content' => 'Currently not in education, employment, or training (NEET)', 'sort_order' => 2],
            ['program_id' => $jobstart->id, 'type' => 'requirement',   'content' => 'Reached at least Grade 7 or first year high school', 'sort_order' => 1],
            ['program_id' => $jobstart->id, 'type' => 'requirement',   'content' => '0–12 months work experience', 'sort_order' => 2],
            ['program_id' => $jobstart->id, 'type' => 'requirement',   'content' => 'Actively looking for work', 'sort_order' => 3],
        ]);
        ProgramTestimonial::create([
            'program_id'  => $jobstart->id,
            'quote'       => 'This program gave me not just a job, but also confidence, direction, and growth. I am truly grateful to DOLE and the JobStart Program for helping me believe in myself and shaping my career journey.',
            'author_name' => 'Elen C. Ocon',
            'author_role' => 'JobStart Beneficiary',
        ]);
        foreach ([
            ['title' => 'Camp Holidays: From Interns to Full-Fledged Employees',                        'image_path' => 'images/jobstart-story/jobstart-1.jpg', 'link' => 'https://ro11.dole.gov.ph/news/the-success-story-of-camp-holidays-jobstart-graduates-from-interns-to-full-fledged-employees/'],
            ['title' => "From Job Seeker to Job Maker: A Woman's Rise Through JobStart",                'image_path' => 'images/jobstart-story/jobstart-2.jpg', 'link' => 'https://ro11.dole.gov.ph/news/from-job-seeker-to-job-maker-a-womans-rise-through-jobstart-philippines-program/'],
            ['title' => "Building a Brighter Future: Malijah Mamalinta's Journey Through JobStart",     'image_path' => 'images/jobstart-story/jobstart-3.jpg', 'link' => 'https://ro11.dole.gov.ph/news/building-a-brighter-future-malijah-mamalintas-journey-through-jobstart-philippines/'],
            ['title' => 'First JobStart Batch in Davao Oriental: 87 Graduates, 45 Hired on the Spot',  'image_path' => 'images/jobstart-story/jobstart-4.jpg', 'link' => 'https://ro11.dole.gov.ph/news/first-jobstart-batch-in-davao-oriental-87-graduates-45-hired-on-the-spot/'],
            ['title' => 'DOLE JobStart Paves the Way for Young Samaleños to Land Full-Time Jobs at CRBC', 'image_path' => 'images/jobstart-story/jobstart-5.jpg', 'link' => 'https://ro11.dole.gov.ph/news/dole-jobstart-paves-the-way-for-young-samalenos-to-land-full-time-jobs-at-crbc-after-graduation/'],
        ] as $i => $s) {
            ProgramStory::create(array_merge($s, ['program_id' => $jobstart->id, 'sort_order' => $i + 1]));
        }

        // ── SPES ──
        $spes = Program::create([
            'name'        => 'Special Program for Employment of Students',
            'acronym'     => 'SPES',
            'description' => 'A youth employability program which aims to provide short-term employment to underprivileged students, out-of-school youth, and dependents of displaced or would-be displaced workers. The program helps augment the family\'s income and ensures beneficiaries are able to pursue their education.',
            'color'       => 'blue',
            'logo_path'   => 'images/logo-programs/spes_logo.png',
            'sort_order'  => 3,
        ]);
        ProgramQualification::insert([
            ['program_id' => $spes->id, 'type' => 'qualification', 'content' => 'Students or OSY who are at least 15 but not more than 30 years of age', 'sort_order' => 1],
            ['program_id' => $spes->id, 'type' => 'qualification', 'content' => 'Combined net income after tax of parents does not exceed the regional poverty threshold', 'sort_order' => 2],
            ['program_id' => $spes->id, 'type' => 'requirement',   'content' => 'Must have obtained a passing general weighted average during the last semester or school year attended', 'sort_order' => 1],
            ['program_id' => $spes->id, 'type' => 'requirement',   'content' => 'Must be certified by the barangay or local SWDO as OSY', 'sort_order' => 2],
        ]);
        ProgramTestimonial::create([
            'program_id'  => $spes->id,
            'quote'       => 'The program helped me by easing the financial pressure that made it hard for me to stay in school. The income I earned supported my education and personal needs, allowing me to focus more on my studies instead of worrying every day about expenses.',
            'author_name' => 'Mark Jay G. Quinto',
            'author_role' => 'SPES Beneficiary',
        ]);
        foreach ([
            ['title' => "From SPES Beneficiary to DOLE XI Accountant: Risha's Full-Circle Journey",                          'image_path' => 'images/spes-story/spes-1.jpg',  'link' => 'https://ro11.dole.gov.ph/news/from-spes-beneficiary-to-dole-xi-accountant-rishas-full-circle-journey/'],
            ['title' => 'Former SPES Beneficiary Now Leads PESO San Isidro',                                                  'image_path' => 'images/spes-story/spes-2.jpg',  'link' => 'https://ro11.dole.gov.ph/news/former-spes-beneficiary-now-leads-peso-san-isidro/'],
            ['title' => 'Once SPES Baby, Now Proud Regular Employee',                                                         'image_path' => 'images/spes-story/spes-3.jpeg', 'link' => 'https://ro11.dole.gov.ph/news/once-spes-baby-now-proud-regular-employee/'],
            ['title' => "SPES Grantee Achieves Latin Honors and Graduation Success: Khacley Marino's Inspiring Journey",     'image_path' => 'images/spes-story/spes-4.jpg',  'link' => 'https://ro11.dole.gov.ph/news/spes-grantee-achieves-latin-honors-and-graduation-success-khacley-marinos-inspiring-journey/'],
        ] as $i => $s) {
            ProgramStory::create(array_merge($s, ['program_id' => $spes->id, 'sort_order' => $i + 1]));
        }

        // ── CDSP ──
        $cdsp = Program::create([
            'name'        => 'Career Development Support Program',
            'acronym'     => 'CDSP',
            'description' => 'CDSP is a public employment service which aims to address gaps in employability dimensions — personal and environmental factors, job objectives, skills and requirements to perform the job, job search skills, and ability to maintain a job — through career, vocational, and employment counseling. The objective is to assist individuals to find the right job, identify appropriate upskilling or reskilling interventions, and progress in their chosen career path.',
            'color'       => 'yellow',
            'logo_path'   => 'images/logo-programs/cdsp_logo.png',
            'sort_order'  => 4,
        ]);
        ProgramQualification::insert([
            ['program_id' => $cdsp->id, 'type' => 'beneficiary', 'content' => 'Jobseekers',              'sort_order' => 1],
            ['program_id' => $cdsp->id, 'type' => 'beneficiary', 'content' => 'Employers',               'sort_order' => 2],
            ['program_id' => $cdsp->id, 'type' => 'beneficiary', 'content' => 'Students & Youth',        'sort_order' => 3],
            ['program_id' => $cdsp->id, 'type' => 'beneficiary', 'content' => 'Migrant Workers',         'sort_order' => 4],
            ['program_id' => $cdsp->id, 'type' => 'beneficiary', 'content' => 'Long-term Unemployed',    'sort_order' => 5],
            ['program_id' => $cdsp->id, 'type' => 'beneficiary', 'content' => 'Persons with Disabilities', 'sort_order' => 6],
            ['program_id' => $cdsp->id, 'type' => 'service',     'content' => 'Career Counseling',       'sort_order' => 1],
            ['program_id' => $cdsp->id, 'type' => 'service',     'content' => 'Vocational Counseling',   'sort_order' => 2],
            ['program_id' => $cdsp->id, 'type' => 'service',     'content' => 'Employment Counseling',   'sort_order' => 3],
        ]);
        ProgramTestimonial::create([
            'program_id'  => $cdsp->id,
            'quote'       => 'Overall, the program gave me more confidence, discipline, and a clearer perspective on the path I took.',
            'author_name' => 'Rian Jes Kryst L. Lamban',
            'author_role' => 'CDSP Beneficiary',
        ]);
        foreach ([
            ['title' => "The Courage to Begin: Philip Tecson's Journey with DOLE's CDSP",                            'image_path' => 'images/cdsp-story/cdsp-1.jpg', 'link' => 'https://ro11.dole.gov.ph/news/the-courage-to-begin-philip-tecsons-journey-fresh-out-of-college-with-doles-career-development-service-program/'],
            ['title' => 'DOLE and PESO Advance CDSP Through Unified School-to-Work Framework',                        'image_path' => 'images/cdsp-story/cdsp-2.jpg', 'link' => 'https://ro11.dole.gov.ph/news/dole-and-peso-advances-cdsp-through-unified-school-to-work-transition-framework-for-oriental-dabawenyos/'],
            ['title' => 'CDSP Prepares Tech-Voc Students for the Realities of Work',                                  'image_path' => 'images/cdsp-story/cdsp-3.png', 'link' => 'https://ro11.dole.gov.ph/news/cdsp-prepares-tech-voc-students-for-the-realities-of-work/'],
            ['title' => 'Byaheng CDSP Kickstarts at Far-Flung High School',                                           'image_path' => 'images/cdsp-story/cdsp-4.jpg', 'link' => 'https://ro11.dole.gov.ph/news/byaheng-cdsp-kickstarts-at-far-flung-high-school/'],
            ['title' => 'Davao Job Mismatch Concern for Career Advocates, CDSP Among Its Solutions',                  'image_path' => 'images/cdsp-story/cdsp-5.jpg', 'link' => 'https://ro11.dole.gov.ph/news/davor-job-mismatch-concern-for-career-advocates-cdsp-among-its-solutions/'],
        ] as $i => $s) {
            ProgramStory::create(array_merge($s, ['program_id' => $cdsp->id, 'sort_order' => $i + 1]));
        }

        // ── Job Fairs ──
        $jobfair = Program::create([
            'name'        => 'Job Fairs',
            'acronym'     => 'Job Fair',
            'description' => 'An employment facilitation strategy aimed to fast-track the meeting of jobseekers and employers/recruitment agencies in one venue at a specific date to reduce cost, time, and effort particularly on the part of the applicants. This is open to all unemployed, skilled and unskilled workers, college and senior high school graduates, graduates of training institutions, displaced workers, and employees seeking advancement.',
            'color'       => 'cyan',
            'logo_path'   => 'images/logo-programs/jobfair_logo.jpg',
            'sort_order'  => 5,
        ]);
        ProgramQualification::insert([
            ['program_id' => $jobfair->id, 'type' => 'objective', 'content' => 'Provide a convenient venue for job seekers to meet potential employers, reducing expenses and travel burdens', 'sort_order' => 1],
            ['program_id' => $jobfair->id, 'type' => 'objective', 'content' => 'Support employers in sourcing skilled workers and combat illegal recruitment', 'sort_order' => 2],
            ['program_id' => $jobfair->id, 'type' => 'objective', 'content' => 'Offer training, self-employment assistance, and welfare services for OFWs and their dependents', 'sort_order' => 3],
            ['program_id' => $jobfair->id, 'type' => 'service',   'content' => 'Job matching through PhilJobNet', 'sort_order' => 1],
            ['program_id' => $jobfair->id, 'type' => 'service',   'content' => 'Career, Vocational, and Employment Counseling', 'sort_order' => 2],
            ['program_id' => $jobfair->id, 'type' => 'service',   'content' => 'Training and Referral Services', 'sort_order' => 3],
            ['program_id' => $jobfair->id, 'type' => 'service',   'content' => 'Livelihood Assistance', 'sort_order' => 4],
            ['program_id' => $jobfair->id, 'type' => 'service',   'content' => 'Assistance on the Issuance of Pre-Employment Requirements', 'sort_order' => 5],
        ]);
        ProgramTestimonial::create([
            'program_id'  => $jobfair->id,
            'quote'       => 'Participating in the job fair has been highly beneficial, providing numerous opportunities. As a result, companies have been reaching out for second interviews and extending job offers. This has significantly helped me advance my career and secure a position.',
            'author_name' => 'Kenn Zyrez A. Unabia',
            'author_role' => 'Job Fair Participant',
        ]);
        foreach ([
            ['title' => 'Successful Kadayawan Job Fair 2025: 129 Hired On-the-Spot Amidst 3,900 Vacancies', 'image_path' => 'images/jobfair-story/jobfair-1.jpg', 'link' => 'https://ro11.dole.gov.ph/news/successful-kadayawan-job-fair-2025-129-hired-on-the-spot-amidst-3900-vacancies/'],
            ['title' => 'First JobStart Batch in Davao Oriental: 87 Graduates, 45 Hired on the Spot',       'image_path' => 'images/jobfair-story/jobfair-2.jpg', 'link' => 'https://ro11.dole.gov.ph/news/first-jobstart-batch-in-davao-oriental-87-graduates-45-hired-on-the-spot/'],
            ['title' => '172 Jobseekers Hired On-the-Spot at Kalayaan Job Fair in Davao City',              'image_path' => 'images/jobfair-story/jobfair-3.png', 'link' => 'https://ro11.dole.gov.ph/news/172-jobseekers-hired-on-the-spot-at-kalayaan-job-fair-in-davao-city/'],
            ['title' => '21.6% of Jobseekers Hired On-the-Spot in Davao Labor Day Job Fair',               'image_path' => 'images/jobfair-story/jobfair-4.png', 'link' => 'https://ro11.dole.gov.ph/news/21-6-jobseekers-hired-on-the-spot-in-davao-labor-day-job-fair/'],
            ['title' => "Instant Yes: Arnel's Job Fair Success Story",                                      'image_path' => 'images/jobfair-story/jobfair-5.jpg', 'link' => 'https://ro11.dole.gov.ph/news/instant-yes-arnels-job-fair-success-story/'],
        ] as $i => $s) {
            ProgramStory::create(array_merge($s, ['program_id' => $jobfair->id, 'sort_order' => $i + 1]));
        }
    }
}