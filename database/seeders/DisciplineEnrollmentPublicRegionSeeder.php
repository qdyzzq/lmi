<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplineEnrollmentPublicRegionSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================
        // Academic Year 2022-2023 | Institution Type: Public
        // Scope: Overall Davao Region (grand total across all provinces)
        // =========================================================

        $records = [
            [
                'academic_year'     => '2022-2023',
                'province'          => 'Davao Region',
                'institution_type'  => 'Public',
                'agriculture'       => 5867,
                'architecture'      => 305,
                'business'          => 28354,
                'criminal_justice'  => 4460,
                'education'         => 18977,
                'engineering'       => 2738,
                'arts'              => 0,
                'general'           => 0,
                'home_economics'    => 0,
                'humanities'        => 368,
                'it'                => 3918,
                'law'               => 131,
                'maritime'          => 0,
                'mass_comm'         => 1005,
                'mathematics'       => 590,
                'medical'           => 392,
                'natural_science'   => 1273,
                'other_disciplines' => 1607,
                'religion'          => 0,
                'service_trades'    => 1757,
                'social_sciences'   => 505,
                'grand_total'       => 72247,
            ],
        ];

        DB::table('discipline_enrollments')->insert($records);
    }
}