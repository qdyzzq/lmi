<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LicensureRatesSeeder extends Seeder
{
    public function run(): void
    {
        $records = [

            // =========================================================
            // YEAR 2025
            // =========================================================

            // Engineering, Architecture & Technical
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Architect', 'takers' => 87, 'passers' => 48, 'passing_rate' => 55.17],
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Chemical Engineer', 'takers' => 17, 'passers' => 11, 'passing_rate' => 64.71],
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Civil Engineer', 'takers' => 986, 'passers' => 276, 'passing_rate' => 27.99],
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Electronics Engineer', 'takers' => 59, 'passers' => 23, 'passing_rate' => 38.98],
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Electronics Technician', 'takers' => 38, 'passers' => 23, 'passing_rate' => 60.53],
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Geodetic Engineer', 'takers' => 210, 'passers' => 141, 'passing_rate' => 67.14],
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Mechanical Engineer', 'takers' => 40, 'passers' => 0, 'passing_rate' => 0.00],
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Registered Electrical Engineer', 'takers' => 386, 'passers' => 204, 'passing_rate' => 52.85],
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Registered Master Electrician', 'takers' => 97, 'passers' => 42, 'passing_rate' => 43.30],
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Certified Plant Mechanic', 'takers' => 6, 'passers' => 2, 'passing_rate' => 33.33],
            ['year' => 2025, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Master Plumber', 'takers' => 609, 'passers' => 289, 'passing_rate' => 47.45],

            // Healthcare & Nursing
            ['year' => 2025, 'sector' => 'Healthcare & Nursing', 'profession' => 'Physician', 'takers' => 313, 'passers' => 184, 'passing_rate' => 58.79],
            ['year' => 2025, 'sector' => 'Healthcare & Nursing', 'profession' => 'Nurse', 'takers' => 555, 'passers' => 343, 'passing_rate' => 61.80],
            ['year' => 2025, 'sector' => 'Healthcare & Nursing', 'profession' => 'Midwife', 'takers' => 482, 'passers' => 324, 'passing_rate' => 67.22],
            ['year' => 2025, 'sector' => 'Healthcare & Nursing', 'profession' => 'Dentist (Written)', 'takers' => 19, 'passers' => 13, 'passing_rate' => 68.42],
            ['year' => 2025, 'sector' => 'Healthcare & Nursing', 'profession' => 'Medical Technologist', 'takers' => 1593, 'passers' => 1160, 'passing_rate' => 72.82],
            ['year' => 2025, 'sector' => 'Healthcare & Nursing', 'profession' => 'Pharmacist', 'takers' => 224, 'passers' => 129, 'passing_rate' => 57.59],
            ['year' => 2025, 'sector' => 'Healthcare & Nursing', 'profession' => 'Occupational Therapist', 'takers' => 34, 'passers' => 18, 'passing_rate' => 52.94],
            ['year' => 2025, 'sector' => 'Healthcare & Nursing', 'profession' => 'Physical Therapist', 'takers' => 15, 'passers' => 9, 'passing_rate' => 60.00],
            ['year' => 2025, 'sector' => 'Healthcare & Nursing', 'profession' => 'Respiratory Therapist', 'takers' => 127, 'passers' => 113, 'passing_rate' => 88.98],

            // Natural Sciences
            ['year' => 2025, 'sector' => 'Natural Sciences', 'profession' => 'Environmental Planner', 'takers' => 39, 'passers' => 32, 'passing_rate' => 82.05],
            ['year' => 2025, 'sector' => 'Natural Sciences', 'profession' => 'Food Technologist', 'takers' => 104, 'passers' => 74, 'passing_rate' => 71.15],

            // Education
            ['year' => 2025, 'sector' => 'Education', 'profession' => 'Professional Teachers - Elem (Davao)', 'takers' => 1420, 'passers' => 877, 'passing_rate' => 61.76],
            ['year' => 2025, 'sector' => 'Education', 'profession' => 'Professional Teachers - Sec (Davao)', 'takers' => 2660, 'passers' => 2007, 'passing_rate' => 75.45],
            ['year' => 2025, 'sector' => 'Education', 'profession' => 'Professional Teachers - Elem (Digos)', 'takers' => 71, 'passers' => 41, 'passing_rate' => 57.75],
            ['year' => 2025, 'sector' => 'Education', 'profession' => 'Professional Teachers - Sec (Digos)', 'takers' => 761, 'passers' => 493, 'passing_rate' => 64.78],
            ['year' => 2025, 'sector' => 'Education', 'profession' => 'Professional Teachers - Elem (Mati)', 'takers' => 145, 'passers' => 98, 'passing_rate' => 67.59],
            ['year' => 2025, 'sector' => 'Education', 'profession' => 'Professional Teachers - Sec (Mati)', 'takers' => 179, 'passers' => 108, 'passing_rate' => 60.34],
            ['year' => 2025, 'sector' => 'Education', 'profession' => 'Professional Teachers - Elem (Tagum)', 'takers' => 366, 'passers' => 246, 'passing_rate' => 67.21],
            ['year' => 2025, 'sector' => 'Education', 'profession' => 'Professional Teachers - Sec (Tagum)', 'takers' => 970, 'passers' => 683, 'passing_rate' => 70.41],

            // Social Work & Behavioral Sciences
            ['year' => 2025, 'sector' => 'Social Work & Behavioral Sciences', 'profession' => 'Social Worker', 'takers' => 1034, 'passers' => 851, 'passing_rate' => 82.30],
            ['year' => 2025, 'sector' => 'Social Work & Behavioral Sciences', 'profession' => 'Guidance Counselor', 'takers' => 81, 'passers' => 54, 'passing_rate' => 66.67],
            ['year' => 2025, 'sector' => 'Social Work & Behavioral Sciences', 'profession' => 'Psychologist', 'takers' => 51, 'passers' => 43, 'passing_rate' => 84.31],
            ['year' => 2025, 'sector' => 'Social Work & Behavioral Sciences', 'profession' => 'Psychometrician', 'takers' => 962, 'passers' => 816, 'passing_rate' => 84.82],
            ['year' => 2025, 'sector' => 'Social Work & Behavioral Sciences', 'profession' => 'Librarian', 'takers' => 134, 'passers' => 90, 'passing_rate' => 67.16],

            // Real Estate Industry
            ['year' => 2025, 'sector' => 'Real Estate Industry', 'profession' => 'Real Estate Appraiser', 'takers' => 136, 'passers' => 91, 'passing_rate' => 66.91],
            ['year' => 2025, 'sector' => 'Real Estate Industry', 'profession' => 'Real Estate Broker', 'takers' => 166, 'passers' => 114, 'passing_rate' => 68.67],

            // Defense Industry
            ['year' => 2025, 'sector' => 'Defense Industry', 'profession' => 'Criminologist', 'takers' => 3112, 'passers' => 2076, 'passing_rate' => 66.71],

            // Business, Finance & Logistics
            ['year' => 2025, 'sector' => 'Business, Finance & Logistics', 'profession' => 'Certified Public Accountant', 'takers' => 511, 'passers' => 201, 'passing_rate' => 39.33],

            // =========================================================
            // YEAR 2024
            // =========================================================

            // Engineering, Architecture & Technical
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Agri-Bio Engineering', 'takers' => 275, 'passers' => 163, 'passing_rate' => 59.27],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Architect', 'takers' => 197, 'passers' => 129, 'passing_rate' => 65.48],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Chemical Engineer', 'takers' => 29, 'passers' => 20, 'passing_rate' => 68.97],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Civil Engineer', 'takers' => 2210, 'passers' => 690, 'passing_rate' => 31.22],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Electronics Engineer', 'takers' => 162, 'passers' => 50, 'passing_rate' => 30.86],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Electronics Technician', 'takers' => 122, 'passers' => 69, 'passing_rate' => 56.56],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Geodetic Engineer', 'takers' => 147, 'passers' => 80, 'passing_rate' => 54.42],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Mechanical Engineer', 'takers' => 125, 'passers' => 21, 'passing_rate' => 16.80],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Metallurgical Engineer', 'takers' => 11, 'passers' => 6, 'passing_rate' => 54.55],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Mining Engineer', 'takers' => 46, 'passers' => 41, 'passing_rate' => 89.13],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Registered Electrical Engineer', 'takers' => 449, 'passers' => 248, 'passing_rate' => 55.23],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Registered Master Electrician', 'takers' => 187, 'passers' => 51, 'passing_rate' => 27.27],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Certified Plant Mechanic', 'takers' => 14, 'passers' => 4, 'passing_rate' => 28.57],
            ['year' => 2024, 'sector' => 'Engineering, Architecture & Technical', 'profession' => 'Master Plumber', 'takers' => 766, 'passers' => 291, 'passing_rate' => 37.99],

            // Healthcare & Nursing
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Physician', 'takers' => 985, 'passers' => 549, 'passing_rate' => 55.74],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Nurse', 'takers' => 3699, 'passers' => 2986, 'passing_rate' => 80.72],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Midwife', 'takers' => 984, 'passers' => 677, 'passing_rate' => 68.80],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Dentist (Written)', 'takers' => 19, 'passers' => 12, 'passing_rate' => 63.16],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Medical Technologist', 'takers' => 1911, 'passers' => 1326, 'passing_rate' => 69.39],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Radiologic Technology', 'takers' => 482, 'passers' => 267, 'passing_rate' => 55.39],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'X-Ray Technologist', 'takers' => 18, 'passers' => 8, 'passing_rate' => 44.44],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Pharmacist', 'takers' => 779, 'passers' => 525, 'passing_rate' => 67.39],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Nutritionist Dietitian', 'takers' => 160, 'passers' => 115, 'passing_rate' => 71.88],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Veterinary Medicine', 'takers' => 84, 'passers' => 45, 'passing_rate' => 53.57],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Occupational Therapist', 'takers' => 101, 'passers' => 47, 'passing_rate' => 46.53],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Physical Therapist', 'takers' => 115, 'passers' => 82, 'passing_rate' => 71.30],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Respiratory Therapist', 'takers' => 148, 'passers' => 132, 'passing_rate' => 89.19],
            ['year' => 2024, 'sector' => 'Healthcare & Nursing', 'profession' => 'Speech Language Pathologist', 'takers' => 3, 'passers' => 3, 'passing_rate' => 100.00],

            // Natural Sciences
            ['year' => 2024, 'sector' => 'Natural Sciences', 'profession' => 'Environmental Planner', 'takers' => 38, 'passers' => 25, 'passing_rate' => 65.79],
            ['year' => 2024, 'sector' => 'Natural Sciences', 'profession' => 'Agriculturist', 'takers' => 722, 'passers' => 302, 'passing_rate' => 41.83],
            ['year' => 2024, 'sector' => 'Natural Sciences', 'profession' => 'Chemist', 'takers' => 78, 'passers' => 26, 'passing_rate' => 33.33],
            ['year' => 2024, 'sector' => 'Natural Sciences', 'profession' => 'Chemical Technician', 'takers' => 203, 'passers' => 127, 'passing_rate' => 62.56],
            ['year' => 2024, 'sector' => 'Natural Sciences', 'profession' => 'Fisheries Professionals', 'takers' => 201, 'passers' => 122, 'passing_rate' => 60.70],
            ['year' => 2024, 'sector' => 'Natural Sciences', 'profession' => 'Food Technologist', 'takers' => 150, 'passers' => 63, 'passing_rate' => 42.00],
            ['year' => 2024, 'sector' => 'Natural Sciences', 'profession' => 'Forester', 'takers' => 195, 'passers' => 87, 'passing_rate' => 44.62],

            // Education
            ['year' => 2024, 'sector' => 'Education', 'profession' => 'Professional Teachers - Elem (Davao)', 'takers' => 1682, 'passers' => 1116, 'passing_rate' => 66.35],
            ['year' => 2024, 'sector' => 'Education', 'profession' => 'Professional Teachers - Sec (Davao)', 'takers' => 4209, 'passers' => 2850, 'passing_rate' => 67.71],
            ['year' => 2024, 'sector' => 'Education', 'profession' => 'Professional Teachers - Elem (Digos)', 'takers' => 110, 'passers' => 69, 'passing_rate' => 62.73],
            ['year' => 2024, 'sector' => 'Education', 'profession' => 'Professional Teachers - Sec (Digos)', 'takers' => 1130, 'passers' => 636, 'passing_rate' => 56.28],
            ['year' => 2024, 'sector' => 'Education', 'profession' => 'Professional Teachers - Elem (Mati)', 'takers' => 152, 'passers' => 119, 'passing_rate' => 78.29],
            ['year' => 2024, 'sector' => 'Education', 'profession' => 'Professional Teachers - Sec (Mati)', 'takers' => 294, 'passers' => 184, 'passing_rate' => 62.59],
            ['year' => 2024, 'sector' => 'Education', 'profession' => 'Professional Teachers - Elem (Tagum)', 'takers' => 623, 'passers' => 460, 'passing_rate' => 73.84],
            ['year' => 2024, 'sector' => 'Education', 'profession' => 'Professional Teachers - Sec (Tagum)', 'takers' => 1354, 'passers' => 902, 'passing_rate' => 66.62],

            // Social Work & Behavioral Sciences
            ['year' => 2024, 'sector' => 'Social Work & Behavioral Sciences', 'profession' => 'Social Worker', 'takers' => 3396, 'passers' => 1939, 'passing_rate' => 57.10],
            ['year' => 2024, 'sector' => 'Social Work & Behavioral Sciences', 'profession' => 'Psychologist', 'takers' => 42, 'passers' => 38, 'passing_rate' => 90.48],
            ['year' => 2024, 'sector' => 'Social Work & Behavioral Sciences', 'profession' => 'Psychometrician', 'takers' => 1224, 'passers' => 816, 'passing_rate' => 66.67],
            ['year' => 2024, 'sector' => 'Social Work & Behavioral Sciences', 'profession' => 'Librarian', 'takers' => 93, 'passers' => 41, 'passing_rate' => 44.09],

            // Real Estate Industry
            ['year' => 2024, 'sector' => 'Real Estate Industry', 'profession' => 'Real Estate Broker', 'takers' => 172, 'passers' => 141, 'passing_rate' => 81.98],

            // Defense Industry
            ['year' => 2024, 'sector' => 'Defense Industry', 'profession' => 'Criminologist', 'takers' => 2649, 'passers' => 1568, 'passing_rate' => 59.19],

            // Business, Finance & Logistics
            ['year' => 2024, 'sector' => 'Business, Finance & Logistics', 'profession' => 'Certified Public Accountant', 'takers' => 1030, 'passers' => 349, 'passing_rate' => 33.88],
            ['year' => 2024, 'sector' => 'Business, Finance & Logistics', 'profession' => 'Custom Broker', 'takers' => 339, 'passers' => 123, 'passing_rate' => 36.28],
        ];

        DB::table('licensure_rates')->insert($records);
    }
}