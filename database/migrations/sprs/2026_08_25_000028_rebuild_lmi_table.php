<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The LMI tab was two booleans ("was the monthly report submitted?").
     * The real form is three separate DOLE forms with headcounts:
     *
     *   LMI Form 1 — Individuals Reached with LMI
     *   LMI Form 2 — Institutions Reached
     *   LMI Form 5 — Individuals Reached with New Media
     *
     * Nothing carries over — booleans to counts is a different question
     * entirely — so the table is dropped and rebuilt rather than altered.
     *
     * "Total Female Reached" is not a column: it's female_youth +
     * female_non_youth, computed in the form. Storing a derived value
     * means it can disagree with its own parts.
     */
    public function up(): void
    {
        Schema::dropIfExists('lmi');

        Schema::create('lmi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprs_submission_id')->unique()->constrained('sprs_submissions')->cascadeOnDelete();

            // ---- LMI Form 1: Individuals Reached with LMI ----
            $table->unsignedInteger('form1_youth')->nullable();
            $table->unsignedInteger('form1_non_youth')->nullable();
            // optional
            $table->unsignedInteger('form1_female_youth')->nullable();
            $table->unsignedInteger('form1_female_non_youth')->nullable();
            // Total Female is female_youth + female_non_youth — computed in
            // the form, not stored. A stored copy can disagree with its own
            // parts the moment one of them is edited.

            // ---- LMI Form 2 ----
            $table->unsignedInteger('form2_institutions_reached')->nullable();

            // ---- LMI Form 5: Individuals Reached with New Media ----
            $table->unsignedInteger('form5_youth')->nullable();
            $table->unsignedInteger('form5_non_youth')->nullable();
            // optional
            $table->unsignedInteger('form5_female_youth')->nullable();
            $table->unsignedInteger('form5_female_non_youth')->nullable();
            // Computed, same as Form 1.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lmi');

        Schema::create('lmi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprs_submission_id')->unique()->constrained('sprs_submissions')->cascadeOnDelete();

            $table->boolean('monthly_report_submitted')->nullable();
            $table->boolean('quarterly_analysis_submitted')->nullable();

            $table->timestamps();
        });
    }
};