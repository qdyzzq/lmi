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

         $records = [
            [
                'academic_year'     => '2023-2024',
                'province'          => 'Davao Region',
                'institution_type'  => 'Private',
'agriculture'       => 175,
'architecture'      => 2286,
'business'          => 26761,
'criminal_justice'  => 9863,
'education'         => 23931,
'engineering'       => 9806,
'arts'              => 579,
'general'           => 4162,
'home_economics'    => 37,
'humanities'        => 286,
'it'                => 4012,
'law'               => 1822,
'maritime'          => 3543,
'mass_comm'         => 102,
'mathematics'       => 100,
'medical'           => 19697,
'natural_science'   => 1028,
'other_disciplines' => 1501,
'religion'          => 232,
'service_trades'    => 2378,
'social_sciences'   => 2819,
'grand_total'       => 115120,
            ],
        ];

        DB::table('discipline_enrollments')->insert($records);
    }
}
