<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplineEnrollmentPublicSeeder extends Seeder 
{
    public function run(): void
    {
        $records = [
            // =========================================================
            // Academic Year 2022-2023 | Institution Type: Public
            // =========================================================

            // Davao City
            [
                'academic_year'    => '2022-2023',
                'province'         => 'Davao City',
                'institution_type' => 'Public',
                'agriculture'      => 258,
                'architecture'     => 305,
                'business'         => 2130,
                'criminal_justice' => 0,
                'education'        => 2898,
                'engineering'      => 1327,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 368,
                'it'               => 822,
                'law'              => 131,
                'maritime'         => 0,
                'mass_comm'        => 169,
                'mathematics'      => 272,
                'medical'          => 0,
                'natural_science'  => 595,
                'other_disciplines'=> 146,
                'religion'         => 0,
                'service_trades'   => 0,
                'social_sciences'  => 505,
                'grand_total'      => 9926,
            ],

            // Davao de Oro
            [
                'academic_year'    => '2022-2023',
                'province'         => 'Davao de Oro',
                'institution_type' => 'Public',
                'agriculture'      => 1734,
                'architecture'     => 0,
                'business'         => 6448,
                'criminal_justice' => 1384,
                'education'        => 4723,
                'engineering'      => 0,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 0,
                'it'               => 0,
                'law'              => 0,
                'maritime'         => 0,
                'mass_comm'        => 0,
                'mathematics'      => 0,
                'medical'          => 0,
                'natural_science'  => 0,
                'other_disciplines'=> 0,
                'religion'         => 0,
                'service_trades'   => 294,
                'social_sciences'  => 0,
                'grand_total'      => 14583,
            ],

            // Davao del Norte
            [
                'academic_year'    => '2022-2023',
                'province'         => 'Davao del Norte',
                'institution_type' => 'Public',
                'agriculture'      => 1911,
                'architecture'     => 0,
                'business'         => 9483,
                'criminal_justice' => 1123,
                'education'        => 4590,
                'engineering'      => 379,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 0,
                'it'               => 1405,
                'law'              => 0,
                'maritime'         => 0,
                'mass_comm'        => 235,
                'mathematics'      => 0,
                'medical'          => 75,
                'natural_science'  => 179,
                'other_disciplines'=> 492,
                'religion'         => 0,
                'service_trades'   => 1463,
                'social_sciences'  => 0,
                'grand_total'      => 21335,
            ],

            // Davao del Sur
            [
                'academic_year'    => '2022-2023',
                'province'         => 'Davao del Sur',
                'institution_type' => 'Public',
                'agriculture'      => 761,
                'architecture'     => 0,
                'business'         => 1965,
                'criminal_justice' => 0,
                'education'        => 1927,
                'engineering'      => 416,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 0,
                'it'               => 675,
                'law'              => 0,
                'maritime'         => 0,
                'mass_comm'        => 256,
                'mathematics'      => 0,
                'medical'          => 0,
                'natural_science'  => 0,
                'other_disciplines'=> 0,
                'religion'         => 0,
                'service_trades'   => 0,
                'social_sciences'  => 0,
                'grand_total'      => 6000,
            ],

            // Davao Occidental
            [
                'academic_year'    => '2022-2023',
                'province'         => 'Davao Occidental',
                'institution_type' => 'Public',
                'agriculture'      => 341,
                'architecture'     => 0,
                'business'         => 1564,
                'criminal_justice' => 424,
                'education'        => 1188,
                'engineering'      => 46,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 0,
                'it'               => 207,
                'law'              => 0,
                'maritime'         => 0,
                'mass_comm'        => 129,
                'mathematics'      => 0,
                'medical'          => 0,
                'natural_science'  => 194,
                'other_disciplines'=> 488,
                'religion'         => 0,
                'service_trades'   => 0,
                'social_sciences'  => 0,
                'grand_total'      => 4581,
            ],

            // Davao Oriental
            [
                'academic_year'    => '2022-2023',
                'province'         => 'Davao Oriental',
                'institution_type' => 'Public',
                'agriculture'      => 862,
                'architecture'     => 0,
                'business'         => 6764,
                'criminal_justice' => 1529,
                'education'        => 3651,
                'engineering'      => 570,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 0,
                'it'               => 809,
                'law'              => 0,
                'maritime'         => 0,
                'mass_comm'        => 216,
                'mathematics'      => 318,
                'medical'          => 317,
                'natural_science'  => 305,
                'other_disciplines'=> 481,
                'religion'         => 0,
                'service_trades'   => 0,
                'social_sciences'  => 0,
                'grand_total'      => 15822,
            ],
        ];

        DB::table('discipline_enrollments')->insert($records);
    }
}