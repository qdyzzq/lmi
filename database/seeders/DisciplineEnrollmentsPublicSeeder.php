<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplineEnrollmentsPublicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Source: 2023-2024 Davao Region Enrollment by Discipline Group
     *         and City/Province (Public Institutions)
     */
    public function run(): void
    {
        $academicYear = '2023-2024';
        $institutionType = 'Public';

        $data = [
            // Davao City
            [
                'academic_year'    => $academicYear,
                'province'         => 'Davao City',
                'institution_type' => $institutionType,
                'agriculture'      => 274,
                'architecture'     => 324,
                'business'         => 2007,
                'criminal_justice' => 0,
                'education'        => 3153,
                'engineering'      => 2011,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 377,
                'it'               => 846,
                'law'              => 102,
                'maritime'         => 0,
                'mass_comm'        => 161,
                'mathematics'      => 288,
                'medical'          => 0,
                'natural_science'  => 554,
                'other_disciplines'=> 166,
                'religion'         => 0,
                'service_trades'   => 0,
                'social_sciences'  => 571,
                'grand_total'      => 10834,
            ],

            // Davao de Oro
            [
                'academic_year'    => $academicYear,
                'province'         => 'Davao de Oro',
                'institution_type' => $institutionType,
                'agriculture'      => 1490,
                'architecture'     => 0,
                'business'         => 7797,
                'criminal_justice' => 1576,
                'education'        => 5287,
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
                'service_trades'   => 393,
                'social_sciences'  => 0,
                'grand_total'      => 16543,
            ],

            // Davao del Norte
            [
                'academic_year'    => $academicYear,
                'province'         => 'Davao del Norte',
                'institution_type' => $institutionType,
                'agriculture'      => 1935,
                'architecture'     => 0,
                'business'         => 11308,
                'criminal_justice' => 1760,
                'education'        => 4895,
                'engineering'      => 0,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 0,
                'it'               => 1560,
                'law'              => 0,
                'maritime'         => 0,
                'mass_comm'        => 240,
                'mathematics'      => 0,
                'medical'          => 149,
                'natural_science'  => 141,
                'other_disciplines'=> 431,
                'religion'         => 0,
                'service_trades'   => 1669,
                'social_sciences'  => 0,
                'grand_total'      => 24088,
            ],

            // Davao del Sur
            [
                'academic_year'    => $academicYear,
                'province'         => 'Davao del Sur',
                'institution_type' => $institutionType,
                'agriculture'      => 1053,
                'architecture'     => 0,
                'business'         => 1936,
                'criminal_justice' => 0,
                'education'        => 1764,
                'engineering'      => 404,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 0,
                'it'               => 1019,
                'law'              => 0,
                'maritime'         => 0,
                'mass_comm'        => 215,
                'mathematics'      => 33,
                'medical'          => 0,
                'natural_science'  => 51,
                'other_disciplines'=> 40,
                'religion'         => 0,
                'service_trades'   => 0,
                'social_sciences'  => 0,
                'grand_total'      => 6515,
            ],

            // Davao Occidental
            [
                'academic_year'    => $academicYear,
                'province'         => 'Davao Occidental',
                'institution_type' => $institutionType,
                'agriculture'      => 357,
                'architecture'     => 0,
                'business'         => 1656,
                'criminal_justice' => 266,
                'education'        => 1134,
                'engineering'      => 75,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 0,
                'it'               => 229,
                'law'              => 0,
                'maritime'         => 0,
                'mass_comm'        => 240,
                'mathematics'      => 0,
                'medical'          => 0,
                'natural_science'  => 297,
                'other_disciplines'=> 773,
                'religion'         => 0,
                'service_trades'   => 0,
                'social_sciences'  => 0,
                'grand_total'      => 5027,
            ],

            // Davao Oriental
            [
                'academic_year'    => $academicYear,
                'province'         => 'Davao Oriental',
                'institution_type' => $institutionType,
                'agriculture'      => 874,
                'architecture'     => 0,
                'business'         => 7625,
                'criminal_justice' => 1641,
                'education'        => 3640,
                'engineering'      => 647,
                'arts'             => 0,
                'general'          => 0,
                'home_economics'   => 0,
                'humanities'       => 0,
                'it'               => 1042,
                'law'              => 0,
                'maritime'         => 0,
                'mass_comm'        => 218,
                'mathematics'      => 367,
                'medical'          => 444,
                'natural_science'  => 343,
                'other_disciplines'=> 395,
                'religion'         => 0,
                'service_trades'   => 0,
                'social_sciences'  => 0,
                'grand_total'      => 17236,
            ],
        ];

        // Map column names from seeder to DB schema
        $rows = array_map(function ($item) {
            return [
                'academic_year'     => $item['academic_year'],
                'province'          => $item['province'],
                'institution_type'  => $item['institution_type'],
                'agriculture'       => $item['agriculture'],
                'architecture'      => $item['architecture'],
                'business'          => $item['business'],
                'criminal_justice'  => $item['criminal_justice'],
                'education'         => $item['education'],
                'engineering'       => $item['engineering'],
                'arts'              => $item['arts'],
                'general'           => $item['general'],
                'home_economics'    => $item['home_economics'],
                'humanities'        => $item['humanities'],
                'it'                => $item['it'],
                'law'               => $item['law'],
                'maritime'          => $item['maritime'],
                'mass_comm'         => $item['mass_comm'],
                'mathematics'       => $item['mathematics'],
                'medical'           => $item['medical'],
                'natural_science'   => $item['natural_science'],
                'other_disciplines' => $item['other_disciplines'],
                'religion'          => $item['religion'],
                'service_trades'    => $item['service_trades'],
                'social_sciences'   => $item['social_sciences'],
                'grand_total'       => $item['grand_total'],
            ];
        }, $data);

        DB::table('discipline_enrollments')->insert($rows);
    }
}