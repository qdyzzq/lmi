<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Labor Market Information tab of the SPRS form. Unrelated to the
     * separate LMI project that shares this database — consider
     * renaming to sprs_lmi if that ever causes confusion.
     */
    public function up(): void
    {
        Schema::create('lmi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprs_submission_id')->unique()->constrained('sprs_submissions')->cascadeOnDelete();

            $table->boolean('monthly_report_submitted')->nullable();
            $table->boolean('quarterly_analysis_submitted')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lmi');
    }
};
