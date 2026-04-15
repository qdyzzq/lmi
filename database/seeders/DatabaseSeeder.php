<?php

namespace Database\Seeders;

use App\Models\SupplySideAnalysis;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(UserSeeder::class);
        $this->call(ProgramSeeder::class);
        $this->call(RegionalLaborMarketStatisticsSeeder::class);
        $this->call(OfficeTypeSeeder::class);
        $this->call(PesoDirectorySeeder ::class);

        $this->call(DisciplineEnrollmentPublicSeeder ::class);
        $this->call(DisciplineEnrollmentPrivateSeeder ::class);
        $this->call(DisciplineEnrollmentPublicRegionSeeder ::class); 
        $this->call(DisciplineEnrollmentPrivateRegionSeeder ::class); 
        $this->call(DisciplineEnrollmentsPublicSeeder ::class);
        $this->call(DisciplineEnrollmentsPrivateSeeder ::class);
        $this->call(DisciplineEnrollmentsPublicRegionSeeder ::class);
        $this->call(DisciplineEnrollmentsPrivateRegion ::class);

        $this->call(LicensureRatesSeeder ::class);

        $this->call(SupplySideAnalysisSeeder ::class);

    }
}
