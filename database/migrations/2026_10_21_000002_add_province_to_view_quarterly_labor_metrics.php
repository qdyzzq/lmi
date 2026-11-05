<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Recreates view_quarterly_labor_metrics with the `province` column so the
     * public charts can be filtered by Regional / province.
     * Same columns and logic as the original view; only `province` is added.
     * Also creates the view on a fresh install (it was never in a migration before).
     */
    public function up(): void
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

    /**
     * Restores the original view (without `province`).
     */
    public function down(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW `view_quarterly_labor_metrics` AS
            SELECT
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
