<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    DB::statement("CREATE OR REPLACE VIEW view_quarterly_labor_metrics AS
        SELECT year,
            CASE month WHEN 1 THEN 'Q1' WHEN 4 THEN 'Q2' 
                       WHEN 7 THEN 'Q3' WHEN 10 THEN 'Q4' END AS quarter,
            ROUND(labor_force / 1000, 2) AS labor_force_thousands,
            ROUND(unemployed / 1000, 2)  AS unemployed_thousands,
            employment_rate,
            unemployment_rate,
            underemployment_rate,
            labor_force_participation_rate AS lfpr
        FROM regional_labor_market_statistics
        WHERE month IN (1, 4, 7, 10)");
}

public function down()
{
    DB::statement("DROP VIEW IF EXISTS view_quarterly_labor_metrics");
}
};
