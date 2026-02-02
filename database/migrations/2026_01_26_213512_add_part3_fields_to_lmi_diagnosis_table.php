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
    Schema::table('lmi_diagnosis', function (Blueprint $table) {
        $table->json('rejection_reasons')->nullable()->after('impact_level');
        $table->string('rejection_reasons_other')->nullable()->after('rejection_reasons');
        $table->string('coordination_frequency')->nullable()->after('rejection_reasons_other');
        $table->string('coordination_frequency_other')->nullable()->after('coordination_frequency');
    });
}

public function down()
{
    Schema::table('lmi_diagnosis', function (Blueprint $table) {
        $table->dropColumn([
            'rejection_reasons',
            'rejection_reasons_other',
            'coordination_frequency',
            'coordination_frequency_other'
        ]);
    });
}
};