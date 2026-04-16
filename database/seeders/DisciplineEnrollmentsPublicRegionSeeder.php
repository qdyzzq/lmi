<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplineEnrollmentsPublicRegionSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================
        // Academic Year 2023-2024 | Institution Type: Public
        // Scope: Overall Davao Region (grand total across all provinces)
        // =========================================================

        $records = [
            [
                'academic_year'     => '2023-2024',
                'province'          => 'Davao Region',
                'institution_type'  => 'Public',
                'agriculture'       => 5983,
                'architecture'      => 324,
                'business'          => 32329,
                'criminal_justice'  => 5243,
                'education'         => 19873,
                'engineering'       => 3137,
                'arts'              => 0,
                'general'           => 0,
                'home_economics'    => 0,
                'humanities'        => 377,
                'it'                => 4696,
                'law'               => 102,
                'maritime'          => 0,
                'mass_comm'         => 1074,
                'mathematics'       => 688,
                'medical'           => 593,
                'natural_science'   => 1386,
                'other_disciplines' => 1805,
                'religion'          => 0,
                'service_trades'    => 2062,
                'social_sciences'   => 571,
                'grand_total'       => 80243,
            ],
        ];

        $records = [
            [
                'academic_year'     => '2020-2021',
                'province'          => 'Davao Region',
                'institution_type'  => 'Public',
                'agriculture'       => 8462,
                'architecture'      => 225,
                'business'          => 14240,
                'criminal_justice'  => 2103,
                'education'         => 14405,
                'engineering'       => 2139,
                'arts'              => 0,
                'general'           => 500,
                'home_economics'    => 0,
                'humanities'        => 335,
                'it'                => 2736,
                'law'               => 85,
                'maritime'          => 0,
                'mass_comm'         => 116,
                'mathematics'       => 439,
                'medical'           => 191,
                'natural_science'   => 623,
                'other_disciplines' => 1219,
                'religion'          => 0,
                'service_trades'    => 915,
                'social_sciences'   => 138,
                'grand_total'       => 48871,
            ],
        ];


        DB::table('discipline_enrollments')->insert($records);
    }
}