<?php 

namespace Database\Seeders\Sprs;

use App\Models\Sprs\ProvinceType;
use Illuminate\Database\Seeder;

class ProvinceTypeSeeder extends Seeder
{
    public function run(): void{
        $provinces = ['DOFO', 'DORFO', 'DSFO','DOCFO','DNFO', 'DCFO'];

        foreach ($provinces as $name){
            ProvinceType::firstOrCreate(['name' => $name]);
        }

    }
}