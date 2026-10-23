<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a `province` column to both labor market tables.
     *
     * 'Regional' = the existing region-wide data (all current rows get this
     * automatically through the default), anything else = a province name,
     * stored as a plain string like discipline_enrollments.province.
     */
    public function up(): void
    {
        Schema::table('regional_labor_market_statistics', function (Blueprint $table) {
            $table->string('province', 100)->default('Regional')->after('id');
            $table->index(['province', 'year', 'month'], 'rlms_province_year_month_idx');
        });

        Schema::table('pending_labor_market_data', function (Blueprint $table) {
            $table->string('province', 100)->default('Regional')->after('id');
            $table->index(['province', 'year', 'month'], 'plmd_province_year_month_idx');
        });
    }

    public function down(): void
    {
        Schema::table('regional_labor_market_statistics', function (Blueprint $table) {
            $table->dropIndex('rlms_province_year_month_idx');
            $table->dropColumn('province');
        });

        Schema::table('pending_labor_market_data', function (Blueprint $table) {
            $table->dropIndex('plmd_province_year_month_idx');
            $table->dropColumn('province');
        });
    }
};
