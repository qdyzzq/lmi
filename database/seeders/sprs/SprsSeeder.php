<?php

namespace Database\Seeders\Sprs;

use Illuminate\Database\Seeder;

class SprsSeeder extends Seeder
{
    /**
     * Order matters: role types before offices (offices need office_type,
     * which is NOT NULL), and FieldOfficeSeeder before OfficeSeeder so
     * the RO and FO rows exist to be parented to.
     */
    public function run(): void
    {
        $this->call([
            ClassTypeSeeder::class,
            ProvinceTypeSeeder::class,
            RoleTypeSeeder::class,
            FieldOfficeSeeder::class,
            OfficeSeeder::class,
            SuperAdminSeeder::class,
            UserSeeder::class,
        ]);
    }
}