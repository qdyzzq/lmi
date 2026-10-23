<?php

namespace Database\Seeders\Sprs;

use App\Models\Sprs\Office;
use App\Models\Sprs\ProvinceType;
use App\Models\Sprs\RoleType;
use Illuminate\Database\Seeder;

class FieldOfficeSeeder extends Seeder
{
    /**
     * The top two levels of the tree, which OfficeSeeder doesn't create.
     *
     * Without these, FO and RO users have no office to belong to, and
     * nothing below can have a parent — the whole hierarchy is a flat
     * list of level 1-3 offices with no one above them.
     *
     * Runs before OfficeSeeder so the parents exist first.
     */
    public function run(): void
    {
        $ro = RoleType::where('code', 'RO')->firstOrFail();
        $fo = RoleType::where('code', 'FO')->firstOrFail();

        $regional = Office::updateOrCreate(
            ['name' => 'DOLE Regional Office XI'],
            [
                'office_type'      => $ro->id,
                'parent_office_id' => null,   // top of the tree
                'province_id'      => null,   // covers all field offices
                'class_type'       => null,
            ]
        );

        // provinces_type holds field office codes, so each one gets a
        // matching FO office reporting to the RO.
        $fieldOffices = [
            'DSFO'  => 'Davao del Sur Field Office',
            'DNFO'  => 'Davao del Norte Field Office',
            'DORFO' => 'Davao Oriental Field Office',
            'DOCFO' => 'Davao de Oro Field Office',
            'DOFO'  => 'Davao Occidental Field Office',
            'DCFO'  => 'Davao City Field Office',
        ];

        foreach ($fieldOffices as $code => $name) {
            $province = ProvinceType::where('name', $code)->first();

            if (! $province) {
                continue;
            }

            Office::updateOrCreate(
                ['name' => $name],
                [
                    'office_type'      => $fo->id,
                    'parent_office_id' => $regional->id,
                    'province_id'      => $province->id,
                    'class_type'       => null,
                ]
            );
        }
    }
}