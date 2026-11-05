<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Creates the initial admin and statistician accounts.
 *
 * Credentials come from .env (SEED_ADMIN_* / SEED_STATISTICIAN_*), never from
 * this file. If a password is not set, a strong random one is generated and
 * printed once in the console; change it after the first login.
 */
class UserSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            [
                'id'         => 1,
                'name'       => 'Admin User',
                'email'      => env('SEED_ADMIN_EMAIL', 'admin@lmi.com'),
                'phone_number'=> env('SEED_ADMIN_PHONE'),
                'role'       => 'admin',
                'department' => 'Administration',
                'password'   => env('SEED_ADMIN_PASSWORD'),
            ],
            [
                'id'         => 2,
                'name'       => 'Statistician',
                'email'      => env('SEED_STATISTICIAN_EMAIL', 'statistician@lmi.com'),
                'phone_number'=> env('SEED_STATISTICIAN_PHONE'),
                'role'       => 'statistician',
                'department' => 'Data Analysis',
                'password'   => env('SEED_STATISTICIAN_PASSWORD'),
            ],
        ];

        foreach ($accounts as $account) {
            if (empty($account['password'])) {
                $account['password'] = $this->generatePassword();
                $this->command?->warn("Generated password for {$account['email']}: {$account['password']}  (change it after first login)");
            }

            $account['password']   = Hash::make($account['password']);
            $account['created_at'] = now();
            $account['updated_at'] = now();

            DB::table('users')->insert($account);
        }
    }

    /** Random password that satisfies the password policy (upper, lower, number, symbol, 16 chars). */
    protected function generatePassword(): string
    {
        return Str::password(16, true, true, true, false) . 'Aa1!';
    }
}
