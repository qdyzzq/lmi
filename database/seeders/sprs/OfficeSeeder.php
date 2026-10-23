<?php

namespace Database\Seeders\Sprs;

use App\Models\Sprs\ClassType;
use App\Models\Sprs\Office;
use App\Models\Sprs\ProvinceType;
use App\Models\Sprs\RoleType;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Real offices for Davao del Sur (DSFO).
     *
     *   Davao del Sur Field Office          level 4
     *   └── PESO Davao del Sur              level 3   province
     *       └── PESO Digos City             level 2   city
     *           ├── PESO Bansalan           level 2   municipality
     *           │   └── UM Bansalan College level 1
     *           ├── PESO Hagonoy … Sulop    level 2
     *           └── 5 JPO offices           level 1
     *
     * Note the municipalities sit under the city, not the province — so a
     * level 2 office reports to another level 2 office. hierarchy_level
     * says what kind of office it is, not where it sits; routing follows
     * parent_office_id, which is why that works.
     *
     * It does mean nothing can infer depth from level arithmetic any
     * more. Anywhere that needs indentation walks the tree instead.
     *
     * PESO Kiblawan is now included — it was on the exclusion list
     * originally. So is JPO Davao Del Sur State College.
     *
     * updateOrCreate throughout, so re-running re-parents rows created
     * under the old shape. firstOrCreate would find them and skip the
     * update, leaving every municipality pointing at the province.
     */
    public function run(): void
    {
        $dsfo = ProvinceType::where('name', 'DSFO')->firstOrFail();

        $pesoProvince     = RoleType::where('code', 'PESO_PROV')->firstOrFail();
        $pesoCity         = RoleType::where('code', 'PESO_CITY')->firstOrFail();
        $pesoMunicipality = RoleType::where('code', 'PESO_MUNI')->firstOrFail();
        $jpo              = RoleType::where('code', 'JPO')->firstOrFail();

        // Created by FieldOfficeSeeder, which must run first.
        $fieldOffice = Office::where('name', 'Davao del Sur Field Office')->firstOrFail();

        $classByName = fn (string $name) => ClassType::where('name', $name)->firstOrFail()->id;

        // ---- Province, reporting to the field office ----
        $province = Office::updateOrCreate(
            ['name' => 'PESO Davao del Sur'],
            [
                'office_type'      => $pesoProvince->id,
                'parent_office_id' => $fieldOffice->id,
                'province_id'      => $dsfo->id,
                'class_type'       => $classByName('1st Class'),
            ]
        );

        // ---- Digos City, reporting to the province ----
        $digos = Office::updateOrCreate(
            ['name' => 'PESO Digos City'],
            [
                'office_type'      => $pesoCity->id,
                'parent_office_id' => $province->id,
                'province_id'      => $dsfo->id,
                'class_type'       => $classByName('Component City'),
            ]
        );

        // ---- Municipalities, reporting to the city ----
        $municipalOffices = [
            'PESO Bansalan'   => '1st Class',
            'PESO Hagonoy'    => '3rd Class',
            'PESO Kiblawan'   => '3rd Class',
            'PESO Magsaysay'  => '3rd Class',
            'PESO Malalag'    => '2nd Class',
            'PESO Matanao'    => '2nd Class',
            'PESO Padada'     => '3rd Class',
            'PESO Sta. Cruz'  => '1st Class',
            'PESO Sulop'      => '3rd Class',
        ];

        $municipalities = [];

        foreach ($municipalOffices as $name => $class) {
            $municipalities[$name] = Office::updateOrCreate(
                ['name' => $name],
                [
                    'office_type'      => $pesoMunicipality->id,
                    'parent_office_id' => $digos->id,
                    'province_id'      => $dsfo->id,
                    'class_type'       => $classByName($class),
                ]
            );
        }

        // ---- JPOs, each under the PESO of its city or municipality ----
        $jpoOffices = [
            'JPO Cor Jesu College, Inc.'                      => 'PESO Digos City',
            'JPO UM Digos College (SPAMAST Digos)'            => 'PESO Digos City',
            'JPO Davao Del Sur State College (SPAMAST Digos)' => 'PESO Digos City',
            'JPO Polytechnic College of Davao del Sur'        => 'PESO Digos City',
            'Mary Mediatrix of All Graces Academy, Inc.'      => 'PESO Digos City',
            'University of Mindanao Bansalan College'         => 'PESO Bansalan',
        ];

        $parents = ['PESO Digos City' => $digos] + $municipalities;

        foreach ($jpoOffices as $name => $parentName) {
            Office::updateOrCreate(
                ['name' => $name],
                [
                    'office_type'      => $jpo->id,
                    'parent_office_id' => $parents[$parentName]->id,
                    'province_id'      => $dsfo->id,
                    'class_type'       => $classByName('Jpo'),
                ]
            );
        }
    }
}