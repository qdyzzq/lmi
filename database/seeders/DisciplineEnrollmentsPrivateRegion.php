<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplineEnrollmentsPrivateRegion extends Seeder
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
                'institution_type'  => 'Private',
'agriculture'       => 810,
'architecture'      => 3001,
'business'          => 37514,
'criminal_justice'  => 20561,
'education'         => 33187,
'engineering'       => 14689,
'arts'              => 1442,
'general'           => 0,
'home_economics'    => 114,
'humanities'        => 1308,
'it'                => 8816,
'law'               => 967,
'maritime'          => 6359,
'mass_comm'         => 517,
'mathematics'       => 70,
'medical'           => 30683,
'natural_science'   => 1043,
'other_disciplines' => 2193,
'religion'          => 1224,
'service_trades'    => 5661,
'social_sciences'   => 6923,
'grand_total'       => 177082,
            ],
        ];

        DB::table('discipline_enrollments')->insert($records);
    }
}