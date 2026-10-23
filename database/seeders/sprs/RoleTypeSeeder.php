<?php

namespace Database\Seeders\Sprs;

use App\Models\Sprs\RoleType;
use Illuminate\Database\Seeder;

class RoleTypeSeeder extends Seeder
{
    /**
     * `code` is the stable machine identifier. hierarchy_level can't do
     * that job — Peso_City and Peso_Municipality are both level 2 — and
     * `name` can't either, since it's display text someone may edit.
     *
     * updateOrCreate rather than firstOrCreate so re-running backfills
     * the code onto rows that already exist without it.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'JPO',               'code' => 'JPO',        'hierarchy_level' => 1],
            ['name' => 'Peso_City',         'code' => 'PESO_CITY',  'hierarchy_level' => 2],
            ['name' => 'Peso_Municipality', 'code' => 'PESO_MUNI',  'hierarchy_level' => 2],
            ['name' => 'Peso_Province',     'code' => 'PESO_PROV',  'hierarchy_level' => 3],
            ['name' => 'FO',                'code' => 'FO',         'hierarchy_level' => 4],
            ['name' => 'RO',                'code' => 'RO',         'hierarchy_level' => 5],
        ];

        foreach ($roles as $role) {
            RoleType::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}