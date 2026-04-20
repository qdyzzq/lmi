<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'id'         => 1,
                'name'       => 'Admin User',
                'email'      => 'eppanuela@gmail.com',
                'phone_number'=> '639454431620',
                'role'       => 'admin',
                'department' => 'Administration',
                'password'   => '$2y$12$qnv.xCy7vNc8SCukLoG3YuikOyjh/U/8HWtQh8xclpnllrBlC79SG',
                'created_at' => '2026-01-20 08:15:51',
                'updated_at' => '2026-01-20 08:15:51',
            ],
            [
                'id'         => 2,
                'name'       => 'Statistician',
                'email'      => 'statistician@lmi.com',
                'phone_number'=> '639053236642',
                'role'       => 'statistician',
                'department' => 'Data Analysis',
                'password'   => '$2y$12$v.3gFWLq0XTpcbfqVJeiL.I3NbPw20DN2.SXpCtdnCucZmFwHGv6y',
                'created_at' => '2026-01-20 08:18:43',
                'updated_at' => '2026-01-20 08:18:43',
            ],

            [
                'id'         => 3,
                'name'       => 'Admin User',
                'email'      => 'admin@lmi.com',
                'phone_number'=> '639182870233',
                'role'       => 'admin',
                'department' => 'Administration',
                'password'   => '$2y$12$jYGGxbK9b2BS/GZntTGd/uHA5Av3HTPWNa.qoTDl6YSSjf38U3CZa',
                'created_at' => '2026-04-20 09:30:08',
                'updated_at' => '2026-04-20 09:30:09',
            ],
        ]);
    }
}