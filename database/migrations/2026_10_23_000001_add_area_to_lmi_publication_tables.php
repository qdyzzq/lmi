<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds an `area` column to the Module 6 tables.
     *
     *  publication_issues    : 'Regional' | province name | 'National'
     *  weekly_issues         : 'Regional' | 'National'
     *  weekly_card_settings  : 'Regional' | 'National'  (one card per area)
     *
     * Every existing row becomes 'Regional' through the default,
     * so the live data keeps working unchanged.
     */
    public function up(): void
    {
        Schema::table('publication_issues', function (Blueprint $table) {
            $table->string('area', 50)->default('Regional')->after('group_id');
            $table->index(['group_id', 'area'], 'publication_issues_group_area_idx');
        });

        Schema::table('weekly_issues', function (Blueprint $table) {
            $table->string('area', 20)->default('Regional')->after('id');
            $table->index(['area', 'year'], 'weekly_issues_area_year_idx');
        });

        Schema::table('weekly_card_settings', function (Blueprint $table) {
            $table->string('area', 20)->default('Regional')->after('id');
            $table->unique('area', 'weekly_card_settings_area_unique');
        });
    }

    public function down(): void
    {
        Schema::table('publication_issues', function (Blueprint $table) {
            $table->dropIndex('publication_issues_group_area_idx');
            $table->dropColumn('area');
        });

        Schema::table('weekly_issues', function (Blueprint $table) {
            $table->dropIndex('weekly_issues_area_year_idx');
            $table->dropColumn('area');
        });

        Schema::table('weekly_card_settings', function (Blueprint $table) {
            $table->dropUnique('weekly_card_settings_area_unique');
            $table->dropColumn('area');
        });
    }
};
