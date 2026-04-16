<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplineEnrollmentPrivateSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            // =========================================================
            // Academic Year 2022-2023 | Institution Type: Private
            // =========================================================

            // Davao City
            [
                'academic_year'     => '2022-2023',
                'province'          => 'Davao City',
                'institution_type'  => 'Private',
                'agriculture'       => 275,
                'architecture'      => 2757,
                'business'          => 22271,
                'criminal_justice'  => 9712,
                'education'         => 16231,
                'engineering'       => 11434,
                'arts'              => 1085,
                'general'           => 0,
                'home_economics'    => 89,
                'humanities'        => 606,
                'it'                => 5317,
                'law'               => 854,
                'maritime'          => 5548,
                'mass_comm'         => 441,
                'mathematics'       => 77,
                'medical'           => 25429,
                'natural_science'   => 667,
                'other_disciplines' => 2182,
                'religion'          => 564,
                'service_trades'    => 2640,
                'social_sciences'   => 4971,
                'grand_total'       => 113150,
            ],

            // Davao de Oro
            [
                'academic_year'     => '2022-2023',
                'province'          => 'Davao de Oro',
                'institution_type'  => 'Private',
                'agriculture'       => 0,
                'architecture'      => 0,
                'business'          => 2100,
                'criminal_justice'  => 827,
                'education'         => 2572,
                'engineering'       => 0,
                'arts'              => 0,
                'general'           => 0,
                'home_economics'    => 0,
                'humanities'        => 128,
                'it'                => 24,
                'law'               => 0,
                'maritime'          => 0,
                'mass_comm'         => 0,
                'mathematics'       => 5,
                'medical'           => 0,
                'natural_science'   => 85,
                'other_disciplines' => 0,
                'religion'          => 0,
                'service_trades'    => 0,
                'social_sciences'   => 0,
                'grand_total'       => 5741,
            ],

            // Davao del Norte
            [
                'academic_year'     => '2022-2023',
                'province'          => 'Davao del Norte',
                'institution_type'  => 'Private',
                'agriculture'       => 0,
                'architecture'      => 0,
                'business'          => 7287,
                'criminal_justice'  => 5344,
                'education'         => 6809,
                'engineering'       => 1516,
                'arts'              => 0,
                'general'           => 0,
                'home_economics'    => 0,
                'humanities'        => 123,
                'it'                => 940,
                'law'               => 298,
                'maritime'          => 0,
                'mass_comm'         => 0,
                'mathematics'       => 0,
                'medical'           => 2417,
                'natural_science'   => 0,
                'other_disciplines' => 0,
                'religion'          => 153,
                'service_trades'    => 1035,
                'social_sciences'   => 674,
                'grand_total'       => 26596,
            ],

            // Davao del Sur
            [
                'academic_year'     => '2022-2023',
                'province'          => 'Davao del Sur',
                'institution_type'  => 'Private',
                'agriculture'       => 2151,
                'architecture'      => 0,
                'business'          => 3291,
                'criminal_justice'  => 2815,
                'education'         => 10168,
                'engineering'       => 537,
                'arts'              => 0,
                'general'           => 0,
                'home_economics'    => 0,
                'humanities'        => 340,
                'it'                => 658,
                'law'               => 230,
                'maritime'          => 0,
                'mass_comm'         => 17,
                'mathematics'       => 11,
                'medical'           => 354,
                'natural_science'   => 0,
                'other_disciplines' => 0,
                'religion'          => 377,
                'service_trades'    => 235,
                'social_sciences'   => 383,
                'grand_total'       => 21567,
            ],

            // Davao Oriental
            [
                'academic_year'     => '2022-2023',
                'province'          => 'Davao Oriental',
                'institution_type'  => 'Private',
                'agriculture'       => 0,
                'architecture'      => 0,
                'business'          => 0,
                'criminal_justice'  => 0,
                'education'         => 0,
                'engineering'       => 0,
                'arts'              => 0,
                'general'           => 0,
                'home_economics'    => 0,
                'humanities'        => 0,
                'it'                => 0,
                'law'               => 0,
                'maritime'          => 0,
                'mass_comm'         => 0,
                'mathematics'       => 0,
                'medical'           => 0,
                'natural_science'   => 0,
                'other_disciplines' => 0,
                'religion'          => 0,
                'service_trades'    => 0,
                'social_sciences'   => 0,
                'grand_total'       => 0,
            ],

            // Davao Occidental
            [
                'academic_year'     => '2022-2023',
                'province'          => 'Davao Occidental',
                'institution_type'  => 'Private',
                'agriculture'       => 0,
                'architecture'      => 0,
                'business'          => 704,
                'criminal_justice'  => 566,
                'education'         => 733,
                'engineering'       => 0,
                'arts'              => 0,
                'general'           => 0,
                'home_economics'    => 0,
                'humanities'        => 0,
                'it'                => 0,
                'law'               => 0,
                'maritime'          => 0,
                'mass_comm'         => 0,
                'mathematics'       => 0,
                'medical'           => 343,
                'natural_science'   => 0,
                'other_disciplines' => 0,
                'religion'          => 0,
                'service_trades'    => 0,
                'social_sciences'   => 0,
                'grand_total'       => 2346,
            ],
        ];

        DB::table('discipline_enrollments')->insert($records);
    }
}