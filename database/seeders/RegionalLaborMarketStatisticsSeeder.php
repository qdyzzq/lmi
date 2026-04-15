<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionalLaborMarketStatisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // 2015
            ['year' => 2015, 'month' => 1,  'household_population' => 3168, 'labor_force' => 2066, 'employed' => 1941, 'underemployed' => 374, 'unemployed' => 125, 'labor_force_participation_rate' => 65.2, 'employment_rate' => 93.9, 'underemployment_rate' => 19.3, 'unemployment_rate' => 6.1],
            ['year' => 2015, 'month' => 4,  'household_population' => 3187, 'labor_force' => 2037, 'employed' => 1915, 'underemployed' => 318, 'unemployed' => 121, 'labor_force_participation_rate' => 63.9, 'employment_rate' => 94.0, 'underemployment_rate' => 16.6, 'unemployment_rate' => 5.9],
            ['year' => 2015, 'month' => 7,  'household_population' => 3180, 'labor_force' => 2010, 'employed' => 1897, 'underemployed' => 478, 'unemployed' => 113, 'labor_force_participation_rate' => 63.2, 'employment_rate' => 94.4, 'underemployment_rate' => 25.2, 'unemployment_rate' => 5.6],
            ['year' => 2015, 'month' => 10, 'household_population' => 3193, 'labor_force' => 2051, 'employed' => 1946, 'underemployed' => 288, 'unemployed' => 113, 'labor_force_participation_rate' => 64.4, 'employment_rate' => 94.5, 'underemployment_rate' => 14.8, 'unemployment_rate' => 5.5],

            // 2016
            ['year' => 2016, 'month' => 1,  'household_population' => 3331, 'labor_force' => 2145, 'employed' => 2040, 'underemployed' => 409, 'unemployed' => 105, 'labor_force_participation_rate' => 64.4, 'employment_rate' => 95.1, 'underemployment_rate' => 20.0, 'unemployment_rate' => 4.9],
            ['year' => 2016, 'month' => 4,  'household_population' => 3342, 'labor_force' => 2071, 'employed' => 1961, 'underemployed' => 326, 'unemployed' => 110, 'labor_force_participation_rate' => 62.0, 'employment_rate' => 94.7, 'underemployment_rate' => 16.6, 'unemployment_rate' => 5.3],
            ['year' => 2016, 'month' => 7,  'household_population' => 3362, 'labor_force' => 2132, 'employed' => 2041, 'underemployed' => 325, 'unemployed' => 91,  'labor_force_participation_rate' => 63.4, 'employment_rate' => 95.7, 'underemployment_rate' => 15.9, 'unemployment_rate' => 4.3],
            ['year' => 2016, 'month' => 10, 'household_population' => 3387, 'labor_force' => 2097, 'employed' => 2024, 'underemployed' => 296, 'unemployed' => 73,  'labor_force_participation_rate' => 61.9, 'employment_rate' => 96.5, 'underemployment_rate' => 14.6, 'unemployment_rate' => 3.5],

            // 2017
            ['year' => 2017, 'month' => 1,  'household_population' => 3411, 'labor_force' => 2106, 'employed' => 1980, 'underemployed' => 290, 'unemployed' => 125, 'labor_force_participation_rate' => 61.7, 'employment_rate' => 94.1, 'underemployment_rate' => 14.6, 'unemployment_rate' => 5.9],
            ['year' => 2017, 'month' => 4,  'household_population' => 3434, 'labor_force' => 2197, 'employed' => 2090, 'underemployed' => 362, 'unemployed' => 107, 'labor_force_participation_rate' => 64.0, 'employment_rate' => 95.1, 'underemployment_rate' => 17.3, 'unemployment_rate' => 4.9],
            ['year' => 2017, 'month' => 7,  'household_population' => 3440, 'labor_force' => 2062, 'employed' => 1964, 'underemployed' => 346, 'unemployed' => 98,  'labor_force_participation_rate' => 59.9, 'employment_rate' => 95.3, 'underemployment_rate' => 17.6, 'unemployment_rate' => 4.7],
            ['year' => 2017, 'month' => 10, 'household_population' => 3468, 'labor_force' => 2265, 'employed' => 2175, 'underemployed' => 461, 'unemployed' => 90,  'labor_force_participation_rate' => 65.3, 'employment_rate' => 96.0, 'underemployment_rate' => 21.2, 'unemployment_rate' => 4.0],

            // 2018
            ['year' => 2018, 'month' => 1,  'household_population' => 3479, 'labor_force' => 2163, 'employed' => 2061, 'underemployed' => 367, 'unemployed' => 101, 'labor_force_participation_rate' => 62.2, 'employment_rate' => 95.3, 'underemployment_rate' => 17.8, 'unemployment_rate' => 4.7],
            ['year' => 2018, 'month' => 4,  'household_population' => 3488, 'labor_force' => 2079, 'employed' => 1968, 'underemployed' => 372, 'unemployed' => 111, 'labor_force_participation_rate' => 59.6, 'employment_rate' => 94.6, 'underemployment_rate' => 18.9, 'unemployment_rate' => 5.4],
            ['year' => 2018, 'month' => 7,  'household_population' => 3517, 'labor_force' => 2075, 'employed' => 2006, 'underemployed' => 341, 'unemployed' => 69,  'labor_force_participation_rate' => 59.0, 'employment_rate' => 96.7, 'underemployment_rate' => 17.0, 'unemployment_rate' => 3.3],
            ['year' => 2018, 'month' => 10, 'household_population' => 3535, 'labor_force' => 2135, 'employed' => 2054, 'underemployed' => 164, 'unemployed' => 81,  'labor_force_participation_rate' => 60.4, 'employment_rate' => 96.2, 'underemployment_rate' => 8.0,  'unemployment_rate' => 3.8],

            // 2019
            ['year' => 2019, 'month' => 1,  'household_population' => 3475, 'labor_force' => 2092, 'employed' => 1998, 'underemployed' => 292, 'unemployed' => 94,  'labor_force_participation_rate' => 60.2, 'employment_rate' => 95.5, 'underemployment_rate' => 14.6, 'unemployment_rate' => 4.5],
            ['year' => 2019, 'month' => 4,  'household_population' => 3476, 'labor_force' => 2016, 'employed' => 1954, 'underemployed' => 154, 'unemployed' => 62,  'labor_force_participation_rate' => 58.0, 'employment_rate' => 96.9, 'underemployment_rate' => 7.9,  'unemployment_rate' => 3.1],
            ['year' => 2019, 'month' => 7,  'household_population' => 3508, 'labor_force' => 2073, 'employed' => 2005, 'underemployed' => 243, 'unemployed' => 68,  'labor_force_participation_rate' => 59.1, 'employment_rate' => 96.7, 'underemployment_rate' => 12.1, 'unemployment_rate' => 3.3],
            ['year' => 2019, 'month' => 10, 'household_population' => 3617, 'labor_force' => 2228, 'employed' => 2137, 'underemployed' => 203, 'unemployed' => 91,  'labor_force_participation_rate' => 61.6, 'employment_rate' => 95.9, 'underemployment_rate' => 9.5,  'unemployment_rate' => 4.1],

            // 2020
            ['year' => 2020, 'month' => 1,  'household_population' => 3539, 'labor_force' => 2084, 'employed' => 1988, 'underemployed' => 282, 'unemployed' => 96,  'labor_force_participation_rate' => 58.9, 'employment_rate' => 95.4, 'underemployment_rate' => 14.2, 'unemployment_rate' => 4.6],
            ['year' => 2020, 'month' => 4,  'household_population' => 3555, 'labor_force' => 1966, 'employed' => 1614, 'underemployed' => 337, 'unemployed' => 352, 'labor_force_participation_rate' => 55.3, 'employment_rate' => 82.1, 'underemployment_rate' => 20.9, 'unemployment_rate' => 17.9],
            ['year' => 2020, 'month' => 7,  'household_population' => 3565, 'labor_force' => 2121, 'employed' => 1952, 'underemployed' => 179, 'unemployed' => 169, 'labor_force_participation_rate' => 59.5, 'employment_rate' => 92.0, 'underemployment_rate' => 9.2,  'unemployment_rate' => 8.0],
            ['year' => 2020, 'month' => 10, 'household_population' => 3580, 'labor_force' => 2023, 'employed' => 1885, 'underemployed' => 170, 'unemployed' => 138, 'labor_force_participation_rate' => 56.5, 'employment_rate' => 93.2, 'underemployment_rate' => 9.0,  'unemployment_rate' => 6.8],

            // 2021
            ['year' => 2021, 'month' => 1,  'household_population' => 3629, 'labor_force' => 2040, 'employed' => 1930, 'underemployed' => 208, 'unemployed' => 110, 'labor_force_participation_rate' => 56.2, 'employment_rate' => 94.6, 'underemployment_rate' => 10.8, 'unemployment_rate' => 5.4],
            ['year' => 2021, 'month' => 4,  'household_population' => 3645, 'labor_force' => 2058, 'employed' => 1886, 'underemployed' => 330, 'unemployed' => 172, 'labor_force_participation_rate' => 56.5, 'employment_rate' => 91.6, 'underemployment_rate' => 17.5, 'unemployment_rate' => 8.4],
            ['year' => 2021, 'month' => 7,  'household_population' => 3656, 'labor_force' => 2125, 'employed' => 1940, 'underemployed' => 298, 'unemployed' => 185, 'labor_force_participation_rate' => 58.1, 'employment_rate' => 91.3, 'underemployment_rate' => 15.4, 'unemployment_rate' => 8.7],
            ['year' => 2021, 'month' => 10, 'household_population' => 3674, 'labor_force' => 2126, 'employed' => 1987, 'underemployed' => 419, 'unemployed' => 139, 'labor_force_participation_rate' => 57.9, 'employment_rate' => 93.5, 'underemployment_rate' => 21.1, 'unemployment_rate' => 6.5],

            // 2022
            ['year' => 2022, 'month' => 1,  'household_population' => 3694, 'labor_force' => 2198, 'employed' => 2092, 'underemployed' => 230, 'unemployed' => 106, 'labor_force_participation_rate' => 59.5, 'employment_rate' => 95.2, 'underemployment_rate' => 11.0, 'unemployment_rate' => 4.8],
            ['year' => 2022, 'month' => 4,  'household_population' => 3712, 'labor_force' => 2131, 'employed' => 2050, 'underemployed' => 166, 'unemployed' => 81,  'labor_force_participation_rate' => 57.4, 'employment_rate' => 96.2, 'underemployment_rate' => 8.1,  'unemployment_rate' => 3.8],
            ['year' => 2022, 'month' => 7,  'household_population' => 3716, 'labor_force' => 2330, 'employed' => 2262, 'underemployed' => 174, 'unemployed' => 68,  'labor_force_participation_rate' => 62.7, 'employment_rate' => 97.1, 'underemployment_rate' => 7.7,  'unemployment_rate' => 2.9],
            ['year' => 2022, 'month' => 10, 'household_population' => 3721, 'labor_force' => 2311, 'employed' => 2230, 'underemployed' => 174, 'unemployed' => 81,  'labor_force_participation_rate' => 62.1, 'employment_rate' => 96.5, 'underemployment_rate' => 7.8,  'unemployment_rate' => 3.5],

            // 2023
            ['year' => 2023, 'month' => 1,  'household_population' => 3750, 'labor_force' => 2370, 'employed' => 2263, 'underemployed' => 143, 'unemployed' => 107, 'labor_force_participation_rate' => 63.2, 'employment_rate' => 95.5, 'underemployment_rate' => 6.3,  'unemployment_rate' => 4.5],
            ['year' => 2023, 'month' => 4,  'household_population' => 3760, 'labor_force' => 2376, 'employed' => 2298, 'underemployed' => 200, 'unemployed' => 78,  'labor_force_participation_rate' => 63.2, 'employment_rate' => 96.7, 'underemployment_rate' => 8.7,  'unemployment_rate' => 3.3],
            ['year' => 2023, 'month' => 7,  'household_population' => 3789, 'labor_force' => 2201, 'employed' => 2117, 'underemployed' => 309, 'unemployed' => 84,  'labor_force_participation_rate' => 58.1, 'employment_rate' => 96.2, 'underemployment_rate' => 14.6, 'unemployment_rate' => 3.8],
            ['year' => 2023, 'month' => 10, 'household_population' => 3780, 'labor_force' => 2415, 'employed' => 2345, 'underemployed' => 171, 'unemployed' => 70,  'labor_force_participation_rate' => 63.9, 'employment_rate' => 97.1, 'underemployment_rate' => 7.3,  'unemployment_rate' => 2.9],

            // 2024
            ['year' => 2024, 'month' => 1,  'household_population' => 3817, 'labor_force' => 2325, 'employed' => 2241, 'underemployed' => 260, 'unemployed' => 84,  'labor_force_participation_rate' => 60.9, 'employment_rate' => 96.4, 'underemployment_rate' => 11.6, 'unemployment_rate' => 3.6],
            ['year' => 2024, 'month' => 4,  'household_population' => 3821, 'labor_force' => 2365, 'employed' => 2280, 'underemployed' => 162, 'unemployed' => 85,  'labor_force_participation_rate' => 61.9, 'employment_rate' => 96.4, 'underemployment_rate' => 7.1,  'unemployment_rate' => 3.6],
            ['year' => 2024, 'month' => 7,  'household_population' => 3831, 'labor_force' => 2394, 'employed' => 2320, 'underemployed' => 86,  'unemployed' => 74,  'labor_force_participation_rate' => 62.5, 'employment_rate' => 96.9, 'underemployment_rate' => 3.7,  'unemployment_rate' => 3.1],
            ['year' => 2024, 'month' => 10, 'household_population' => 3867, 'labor_force' => 2463, 'employed' => 2401, 'underemployed' => 103, 'unemployed' => 62,  'labor_force_participation_rate' => 63.7, 'employment_rate' => 97.5, 'underemployment_rate' => 4.3,  'unemployment_rate' => 2.5],

            // 2025
            ['year' => 2025, 'month' => 1,  'household_population' => 3864, 'labor_force' => 2481, 'employed' => 2409, 'underemployed' => 80,  'unemployed' => 72,  'labor_force_participation_rate' => 64.2, 'employment_rate' => 97.1, 'underemployment_rate' => 3.3,  'unemployment_rate' => 2.9],
            ['year' => 2025, 'month' => 4,  'household_population' => 3880, 'labor_force' => 2468, 'employed' => 2409, 'underemployed' => 94,  'unemployed' => 59,  'labor_force_participation_rate' => 63.6, 'employment_rate' => 97.6, 'underemployment_rate' => 3.9,  'unemployment_rate' => 2.4],
            ['year' => 2025, 'month' => 7,  'household_population' => 3898, 'labor_force' => 2378, 'employed' => 2293, 'underemployed' => 241, 'unemployed' => 86,  'labor_force_participation_rate' => 61.0, 'employment_rate' => 96.4, 'underemployment_rate' => 10.5, 'unemployment_rate' => 3.6],
            ['year' => 2025, 'month' => 10, 'household_population' => 3906, 'labor_force' => 2453, 'employed' => 2392, 'underemployed' => 129, 'unemployed' => 61,  'labor_force_participation_rate' => 62.8, 'employment_rate' => 97.5, 'underemployment_rate' => 5.4,  'unemployment_rate' => 2.5],
        ];

        $now = now();

        $rows = array_map(fn($row) => array_merge($row, [
            'created_at' => $now,
            'updated_at' => $now,
        ]), $data);

        // Insert in chunks to avoid hitting query size limits
        foreach (array_chunk($rows, 20) as $chunk) {
            DB::table('regional_labor_market_statistics')->insert($chunk);
        }
    }
}