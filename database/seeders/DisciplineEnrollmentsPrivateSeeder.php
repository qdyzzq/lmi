<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplineEnrollmentsPrivateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Source: 2023-2024 Davao Region Enrollment by Discipline Group
     *         and City/Province (Private Institutions)
     */
    public function run(): void
    {
        $academicYear = '2023-2024';
        $institutionType = 'Private';

        $data = [
            // Davao City
            [
                'province'          => 'Davao City',
                'agriculture'       => 305,
                'architecture'      => 3001,
                'business'          => 23993,
                'criminal_justice'  => 9579,
                'education'         => 15932,
                'engineering'       => 12504,
                'arts'              => 1442,
                'general'           => 0,
                'home_economics'    => 114,
                'humanities'        => 704,
                'it'                => 6469,
                'law'               => 734,
                'maritime'          => 6359,
                'mass_comm'         => 493,
                'mathematics'       => 58,
                'medical'           => 25208,
                'natural_science'   => 972,
                'other_disciplines' => 2071,
                'religion'          => 641,
                'service_trades'    => 3840,
                'social_sciences'   => 5561,
                'grand_total'       => 119980,
            ],

            // Davao de Oro
            [
                'province'          => 'Davao de Oro',
                'agriculture'       => 0,
                'architecture'      => 0,
                'business'          => 1920,
                'criminal_justice'  => 892,
                'education'         => 2550,
                'engineering'       => 0,
                'arts'              => 0,
                'general'           => 0,
                'home_economics'    => 0,
                'humanities'        => 73,
                'it'                => 87,
                'law'               => 0,
                'maritime'          => 0,
                'mass_comm'         => 0,
                'mathematics'       => 0,
                'medical'           => 0,
                'natural_science'   => 61,
                'other_disciplines' => 0,
                'religion'          => 0,
                'service_trades'    => 0,
                'social_sciences'   => 0,
                'grand_total'       => 5583,
            ],

            // Davao del Norte
            [
                'province'          => 'Davao del Norte',
                'agriculture'       => 0,
                'architecture'      => 0,
                'business'          => 7615,
                'criminal_justice'  => 5617,
                'education'         => 6716,
                'engineering'       => 1634,
                'arts'              => 0,
                'general'           => 0,
                'home_economics'    => 0,
                'humanities'        => 125,
                'it'                => 1457,
                'law'               => 233,
                'maritime'          => 0,
                'mass_comm'         => 0,
                'mathematics'       => 0,
                'medical'           => 3463,
                'natural_science'   => 10,
                'other_disciplines' => 0,
                'religion'          => 142,
                'service_trades'    => 1363,
                'social_sciences'   => 883,
                'grand_total'       => 29258,
            ],

            // Davao del Sur
            [
                'province'          => 'Davao del Sur',
                'agriculture'       => 505,
                'architecture'      => 0,
                'business'          => 3323,
                'criminal_justice'  => 3896,
                'education'         => 7187,
                'engineering'       => 551,
                'arts'              => 0,
                'general'           => 0,
                'home_economics'    => 0,
                'humanities'        => 406,
                'it'                => 803,
                'law'               => 0,
                'maritime'          => 0,
                'mass_comm'         => 24,
                'mathematics'       => 12,
                'medical'           => 1507,
                'natural_science'   => 0,
                'other_disciplines' => 122,
                'religion'          => 441,
                'service_trades'    => 458,
                'social_sciences'   => 479,
                'grand_total'       => 19714,
            ],

            // Davao Oriental
            [
                'province'          => 'Davao Oriental',
                'agriculture'       => 0,
                'architecture'      => 0,
                'business'          => 663,
                'criminal_justice'  => 577,
                'education'         => 802,
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
                'medical'           => 505,
                'natural_science'   => 0,
                'other_disciplines' => 0,
                'religion'          => 0,
                'service_trades'    => 0,
                'social_sciences'   => 0,
                'grand_total'       => 2547,
            ],

            // Davao Occidental
            [
                'province'          => 'Davao Occidental',
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
        ];

        $rows = array_map(fn ($item) => array_merge($item, [
            'academic_year'    => $academicYear,
            'institution_type' => $institutionType,
        ]), $data);

        DB::table('discipline_enrollments')->insert($rows);
    }
}