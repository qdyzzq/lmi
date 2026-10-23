<?php

namespace Database\Seeders\Sprs;

use App\Models\Sprs\Office;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * One test account per hierarchy level, at fixed ids from 6 (1-5 are
     * already taken).
     *
     * The id is only applied on create. On re-run the account is matched
     * by email and updated in place — reassigning an id would break every
     * foreign key pointing at it.
     *
     * `role` stays null on all of them. That column is for accounts
     * outside the hierarchy (super_admin, statistician); a value there
     * short-circuits getAccessRoleAttribute() before it reaches the
     * office.
     */
    private const PASSWORD = 'password';

    /**
     * Every test account shares one number, so OTP codes all land on a
     * handset you actually hold. Fine for testing; real accounts get
     * their own number from the create-account form.
     */
    private const PHONE = '639127404378';

    public function run(): void
    {
        //  id => [email, name, office name]
        $accounts = [
            6  => ['ro@sprs.test',       'RO Reviewer',         'DOLE Regional Office XI'],
            7  => ['fo.dsfo@sprs.test',  'FO Davao del Sur',    'Davao del Sur Field Office'],
            8  => ['fo.dnfo@sprs.test',  'FO Davao del Norte',  'Davao del Norte Field Office'],
            9  => ['fo.dcfo@sprs.test',  'FO Davao City',       'Davao City Field Office'],
            10 => ['prov@sprs.test',     'PESO Province DdS',   'PESO Davao del Sur'],
            11 => ['digos@sprs.test',    'PESO Digos City',     'PESO Digos City'],
            12 => ['bansalan@sprs.test', 'PESO Bansalan',       'PESO Bansalan'],
            13 => ['jpo@sprs.test',      'JPO Cor Jesu',        'JPO Cor Jesu College, Inc.'],

            // The Bansalan branch, for testing the full chain:
            //   UM Bansalan College -> PESO Bansalan -> PESO Digos City
            //   -> PESO Davao del Sur -> Davao del Sur FO -> DOLE RO XI
            //
            // Cor Jesu reports straight to Digos, so it only exercises
            // four hops. This one goes through a municipality as well.
            14 => ['umbansalan@sprs.test', 'JPO UM Bansalan',    'University of Mindanao Bansalan College'],
        ];

        foreach ($accounts as $id => [$email, $name, $officeName]) {
            $office = Office::where('name', $officeName)->first();

            if (! $office) {
                $this->command->warn("  skipped {$email} — no office named \"{$officeName}\"");
                continue;
            }

            $user = User::firstOrNew(['email' => $email]);

            if (! $user->exists) {
                if (User::whereKey($id)->exists()) {
                    $this->command->warn("  skipped {$email} — id {$id} is already taken");
                    continue;
                }

                $user->id = $id;
            }

            $user->fill([
                'name'         => $name,
                'office_id'    => $office->id,
                'is_active'    => true,
                'role'         => null,
                'phone_number' => self::PHONE,
            ]);

            $user->password          = Hash::make(self::PASSWORD);
            $user->email_verified_at = now();
            $user->save();
        }

        $this->report();
    }

    private function report(): void
    {
        $rows = User::whereIn('email', [
            'ro@sprs.test', 'fo.dsfo@sprs.test', 'prov@sprs.test',
            'digos@sprs.test', 'jpo@sprs.test',
        ])->with('office.officeType')->get();

        $this->command->newLine();
        $this->command->info('  SPRS test users — password: ' . self::PASSWORD);
        $this->command->newLine();

        foreach ($rows->sortByDesc(fn ($u) => $u->office?->officeType?->hierarchy_level) as $u) {
            $this->command->line(sprintf(
                '  L%s  #%-3d %-22s %-30s %s',
                $u->office?->officeType?->hierarchy_level ?? '?',
                $u->id,
                $u->email,
                $u->office?->name,
                $u->phone_number
            ));
        }

        $this->command->newLine();
    }
}