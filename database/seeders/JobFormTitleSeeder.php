<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobFormTitleSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            // ── 2025 Data ──
            ['year' => 2025, 'title' => 'Domestic Helper',            'count' => 13573, 'status' => 'approved'],
            ['year' => 2025, 'title' => 'Call Center Agent',          'count' => 7619,  'status' => 'approved'],
            ['year' => 2025, 'title' => 'Laborer',                    'count' => 6750,  'status' => 'approved'],
            ['year' => 2025, 'title' => 'Customer Relations Officer', 'count' => 6133,  'status' => 'approved'],
            ['year' => 2025, 'title' => 'Customer Service Assistant', 'count' => 5854,  'status' => 'approved'],
            ['year' => 2025, 'title' => 'Household Attendant',        'count' => 3552,  'status' => 'approved'],
            ['year' => 2025, 'title' => 'Cashier',                    'count' => 1791,  'status' => 'approved'],
            ['year' => 2025, 'title' => 'Driver',                     'count' => 1706,  'status' => 'approved'],
            ['year' => 2025, 'title' => 'Production Worker',          'count' => 1615,  'status' => 'approved'],
            ['year' => 2025, 'title' => 'Office Clerk',               'count' => 1001,  'status' => 'approved'],

            // ── 2024 Data ──
            ['year' => 2024, 'title' => 'Domestic Helper',            'count' => 40472, 'status' => 'approved'],
            ['year' => 2024, 'title' => 'Call Center Agent',          'count' => 23492, 'status' => 'approved'],
            ['year' => 2024, 'title' => 'Customer Service Assistant', 'count' => 16962, 'status' => 'approved'],
            ['year' => 2024, 'title' => 'Laborer',                    'count' => 14335, 'status' => 'approved'],
            ['year' => 2024, 'title' => 'Cashier',                    'count' => 12180, 'status' => 'approved'],
            ['year' => 2024, 'title' => 'Salesclerk',                 'count' => 9541,  'status' => 'approved'],
            ['year' => 2024, 'title' => 'Production Worker',          'count' => 4867,  'status' => 'approved'],
            ['year' => 2024, 'title' => 'Household Attendant',        'count' => 4063,  'status' => 'approved'],
            ['year' => 2024, 'title' => 'Customer Relation Officer',  'count' => 4049,  'status' => 'approved'],
            ['year' => 2024, 'title' => 'Driver (General)',           'count' => 3756,  'status' => 'approved'],
        ];

        foreach ($records as &$record) {
            $record['submitted_by'] = null;
            $record['reviewed_by']  = null;
            $record['reviewed_at']  = null;
            $record['rejection_reason'] = null;
            $record['created_at']   = now();
            $record['updated_at']   = now();
        }

        DB::table('job_title_forms')->insert($records);
    }
}