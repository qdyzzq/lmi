<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analysis_templates', function (Blueprint $table) {
            // Status: null = direct save (old records), 'pending' = admin submitted, 'published' = statistician approved
            $table->string('status', 20)->nullable()->default(null)->after('is_active');

            // Who submitted the draft (admin name or id)
            $table->string('submitted_by')->nullable()->after('status');

            // When it was submitted
            $table->timestamp('submitted_at')->nullable()->after('submitted_by');
        });
    }

    public function down(): void
    {
        Schema::table('analysis_templates', function (Blueprint $table) {
            $table->dropColumn(['status', 'submitted_by', 'submitted_at']);
        });
    }
};