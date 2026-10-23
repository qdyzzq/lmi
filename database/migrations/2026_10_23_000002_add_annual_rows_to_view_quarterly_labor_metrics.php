<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Provincial data is annual only and stored with month = 0.
     * The chart view now also returns those rows, labelled 'Annual'.
     * Regional rows (quarters 1, 4, 7, 10) are returned exactly as before.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW `view_quarterly_labor_metrics` AS
            SELECT
                `province`,
                `year`,
                CASE `month` WHEN 0 THEN 'Annual' WHEN 1 THEN 'Q1' WHEN 4 THEN 'Q2' WHEN 7 THEN 'Q3' WHEN 10 THEN 'Q4' END AS `quarter`,
                ROUND(`labor_force` / 1000, 2) AS `labor_force_thousands`,
                ROUND(`unemployed` / 1000, 2)  AS `unemployed_thousands`,
                `employment_rate`,
                `unemployment_rate`,
                `underemployment_rate`,
                `labor_force_participation_rate` AS `lfpr`
            FROM `regional_labor_market_statistics`
            WHERE `month` IN (0, 1, 4, 7, 10)
        ");
    }

    /**
     * Back to the quarters-only view (with province).
     */
    public function down(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW `view_quarterly_labor_metrics` AS
            SELECT
                `province`,
                `year`,
                CASE `month` WHEN 1 THEN 'Q1' WHEN 4 THEN 'Q2' WHEN 7 THEN 'Q3' WHEN 10 THEN 'Q4' END AS `quarter`,
                ROUND(`labor_force` / 1000, 2) AS `labor_force_thousands`,
                ROUND(`unemployed` / 1000, 2)  AS `unemployed_thousands`,
                `employment_rate`,
                `unemployment_rate`,
                `underemployment_rate`,
                `labor_force_participation_rate` AS `lfpr`
            FROM `regional_labor_market_statistics`
            WHERE `month` IN (1, 4, 7, 10)
        ");
    }
};
