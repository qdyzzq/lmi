<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * KpiController@getKpiCards reads view_kpi_cards, which was never created by a
     * migration. The view returns the latest month from regional_labor_market_statistics,
     * the same row the controller falls back to, renamed to the columns it expects.
     *
     * Skipped when the view already exists so a hand-made production view is kept.
     */
    public function up()
    {
        if (Schema::hasView('view_kpi_cards')) {
            return;
        }

        DB::statement("CREATE VIEW view_kpi_cards AS
            SELECT year,
                month,
                employment_rate,
                employed AS employed_persons,
                unemployment_rate,
                unemployed AS unemployed_persons,
                underemployment_rate,
                underemployed AS seeking_more_hours,
                labor_force_participation_rate AS participation_rate,
                labor_force AS active_workforce,
                household_population AS population_15_plus
            FROM regional_labor_market_statistics
            ORDER BY year DESC, month DESC
            LIMIT 1");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS view_kpi_cards");
    }
};
