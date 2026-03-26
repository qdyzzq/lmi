<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PesoInfoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'description' => 'The Public Employment Service Office (PESO) is a <strong class="text-amber-300">non-fee charging</strong> multi-employment service facility or entity established and accredited pursuant to <strong class="text-blue-200">Republic Act No. 8759</strong> otherwise known as the PESO Act of 1999. The office was established through a Memorandum of Agreement between the <strong class="text-blue-200">Department of Labor &amp; Employment (DOLE)</strong> and the LGU of Davao City in the year 1994, and was institutionalized under Resolution No. 02190-12 Series of 2012 with its corresponding City Ordinance No. 0391-12. The office was established with the aim of assisting jobseekers in finding stable and sustainable employment for a qualified workforce gainfully employed in country and overseas.',

            'objective' => 'Facilitate job matching of job seekers with enterprises through job search assistance, provision of labor market information, and career, vocational, and employment counseling.',

            'core_services' => json_encode([
                'Labor Market Information',
                'Referral and Placement',
                'Career Development Support Program (CDSP)',
            ]),

            'dole_programs' => json_encode([
                'SPES', 'Job Fairs', 'PhilJobNet',
                'NSRP', 'DOLE-GIP', 'TUPAD', 'DILEEP',
            ]),

            'beneficiaries' => json_encode([
                'Jobseekers', 'Employers', 'Students', 'Youth',
                'Migrant Workers', 'Long-Term Unemployed', 'Displaced Workers',
                'Indigenous People', 'Persons with Disabilities',
                'Senior Citizens', 'Graduates of Educational Institutions',
            ]),

            'how_to_avail' => 'See the Directory of Public Employment Service Offices below to find the nearest PESO in your area.',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('peso_info_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}