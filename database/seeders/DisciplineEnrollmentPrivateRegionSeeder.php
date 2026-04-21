<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplineEnrollmentPrivateRegionSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================
        // Academic Year 2022-2023 | Institution Type: Private
        // Scope: Overall Davao Region (grand total across all provinces)
        // =========================================================

        $records = [
            [
                'academic_year'     => '2022-2023',
                'province'          => 'Davao Region',
                'institution_type'  => 'Private',
                'agriculture'       => 2426,
                'architecture'      => 2757,
                'business'          => 35653,
                'criminal_justice'  => 19264,
                'education'         => 36513,
                'engineering'       => 13487,
                'arts'              => 1085,
                'general'           => 0,
                'home_economics'    => 89,
                'humanities'        => 1197,
                'it'                => 6939,
                'law'               => 1382,
                'maritime'          => 5548,
                'mass_comm'         => 458,
                'mathematics'       => 93,
                'medical'           => 28543,
                'natural_science'   => 752,
                'other_disciplines' => 2182,
                'religion'          => 1094,
                'service_trades'    => 3910,
                'social_sciences'   => 6028,
                'grand_total'       => 169400,
            ],
        ];

        DB::table('discipline_enrollments')->insert($records);
    }
}