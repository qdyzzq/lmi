<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add salary_range to pending_labor_market_data
        Schema::table('pending_labor_market_data', function (Blueprint $table) {
            $table->string('salary_range')->nullable()->after('unemployment_rate');
        });

        // Add salary_range to lmi_hard_to_fill_roles
        Schema::table('lmi_hard_to_fill_roles', function (Blueprint $table) {
            $table->string('salary_range')->nullable()->after('job_classification');
        });
    }

    public function down()
    {
        Schema::table('pending_labor_market_data', function (Blueprint $table) {
            $table->dropColumn('salary_range');
        });

        Schema::table('lmi_hard_to_fill_roles', function (Blueprint $table) {
            $table->dropColumn('salary_range');
        });
    }
};