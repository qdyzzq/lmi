<?php

namespace Database\Seeders\Sprs;

use App\Models\Sprs\ClassType;
use Illuminate\Database\Seeder;

class ClassTypeSeeder extends Seeder
{
    public function run(): void{
        $classes = ['1st Class', '2nd Class', '3rd Class', '4th Class', 'Component City', 'Jpo', 'Highly Urbanized'];

        foreach ($classes as $name){
            ClassType::firstOrCreate(['name' => $name]);
        }
    }
}