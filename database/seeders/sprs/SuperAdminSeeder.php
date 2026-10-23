<?php

namespace Database\Seeders\Sprs;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Two fixes from the original:
     *
     *  - The hardcoded 'id' => 3 is gone. Your users table already has
     *    rows at that id, so this threw a duplicate key error and there
     *    is no reason to pin it.
     *  - insert() -> updateOrInsert(), so re-running the seeder updates
     *    the account instead of crashing.
     */
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'superadmin@sprs.com'],
            [
                'name'         => 'Super Admin',
                'password'     => Hash::make('adminsprs'),
                'phone_number' => '639127404378',
                'office_id'    => null,   // outside the office hierarchy
                'is_active'    => true,
                'role'         => 'super_admin',
                'updated_at'   => now(),
                'created_at'   => now(),
            ]
        );
    }
}