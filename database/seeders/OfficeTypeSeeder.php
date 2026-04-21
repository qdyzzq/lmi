<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('office_types')->insert([
            [
                'id'         => 1,
                'name'       => 'PESO',
                'created_at' => '2026-03-29 22:53:54',
                'updated_at' => '2026-03-29 22:53:54',
            ],
            [
                'id'         => 2,
                'name'       => 'JPO',
                'created_at' => '2026-03-29 22:53:54',
                'updated_at' => '2026-03-29 22:53:54',
            ],
        ]);
    }
}