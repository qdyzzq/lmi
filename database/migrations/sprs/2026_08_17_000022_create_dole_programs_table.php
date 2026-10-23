<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "DOLE Programs" tab — 20 sub-programs, each with 1-4 fields.
     * Still one table per tab (same rule as every other tab), just a
     * wide one. One assumption flagged below: GIP Facilitation's two
     * rows were both labeled "No. of Beneficiaries assisted" in the
     * screenshot with no "Female" label on the second — treated here
     * as Total + Female, matching the pattern every other section uses.
     * Worth confirming against the real form.
     */
    public function up(): void
    {
        Schema::create('dole_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprs_submission_id')->unique()->constrained('sprs_submissions')->cascadeOnDelete();

            // ---- Special Program for Employment of Students (SPES) ----
            $table->unsignedInteger('spes_regular_total')->nullable();
            $table->unsignedInteger('spes_regular_female')->nullable();
            $table->unsignedInteger('spes_lgu_implemented_total')->nullable();
            $table->unsignedInteger('spes_lgu_implemented_female')->nullable();

            // ---- Local and Special Recruitment Activity (LRA/SRA) ----
            $table->unsignedInteger('lra_sra_conducted')->nullable();

            // ---- JOBFAIRS / TNK ----
            $table->unsignedInteger('job_fairs_tnk_conducted')->nullable();

            // ---- JobStart Philippines Program ----
            $table->unsignedInteger('jobstart_dole_funded_total')->nullable();
            $table->unsignedInteger('jobstart_dole_funded_female')->nullable();
            $table->unsignedInteger('jobstart_lgu_funded_total')->nullable();
            $table->unsignedInteger('jobstart_lgu_funded_female')->nullable();

            // ---- Government Internship Program (GIP) Facilitation ----
            $table->unsignedInteger('gip_beneficiaries_total')->nullable();
            $table->unsignedInteger('gip_beneficiaries_female')->nullable();

            // ---- TUPAD (Tulong Panghanapbuhay sa Ating Disadvantaged/Displaced Workers) ----
            $table->unsignedInteger('tupad_beneficiaries_total')->nullable();
            $table->unsignedInteger('tupad_beneficiaries_female')->nullable();

            // ---- TULAY (Tulong at Alalay sa Taong may Kapansanan) ----
            $table->unsignedInteger('tulay_beneficiaries_total')->nullable();
            $table->unsignedInteger('tulay_beneficiaries_female')->nullable();

            // ---- Livelihood Assistance ----
            $table->unsignedInteger('livelihood_beneficiaries_total')->nullable();
            $table->unsignedInteger('livelihood_beneficiaries_female')->nullable();

            // ---- MicroBiz Incubation Program (Grow Micro) ----
            $table->unsignedInteger('microbiz_beneficiaries_total')->nullable();
            $table->unsignedInteger('microbiz_beneficiaries_female')->nullable();

            // ---- Community Skills Training / Livelihood & Entrepreneurship Program (CBT) ----
            $table->unsignedInteger('cbt_trainings_provided')->nullable();

            // ---- OWWA Help Desk ----
            $table->unsignedInteger('owwa_assisted_total')->nullable();
            $table->unsignedInteger('owwa_assisted_female')->nullable();

            // ---- OFW Reintegration Program ----
            $table->unsignedInteger('ofw_reintegration_assisted_total')->nullable();
            $table->unsignedInteger('ofw_reintegration_assisted_female')->nullable();

            // ---- Anti-Illegal Recruitment Campaign (AIR Campaign) ----
            $table->unsignedInteger('air_campaign_advocacies')->nullable();

            // ---- Anti-Child Labor Advocacy ----
            $table->unsignedInteger('anti_child_labor_advocacies')->nullable();

            // ---- Kasambahay Law Advocacy ----
            $table->unsignedInteger('kasambahay_law_advocacies')->nullable();

            // ---- OJT Facilitation / Workers Appreciation Program (WAP) ----
            $table->unsignedInteger('ojt_wap_assisted_total')->nullable();
            $table->unsignedInteger('ojt_wap_assisted_female')->nullable();

            // ---- Workers Hiring for Infrastructure Projects (WHIP) ----
            $table->unsignedInteger('whip_placed_total')->nullable();
            $table->unsignedInteger('whip_placed_female')->nullable();

            // ---- Registration of Workers Association (RWA) ----
            $table->unsignedInteger('rwa_associations_registered')->nullable();

            // ---- Labor Complaints Referral ----
            $table->unsignedInteger('labor_complaints_referred_total')->nullable();
            $table->unsignedInteger('labor_complaints_referred_female')->nullable();

            // ---- Green Our DOLE Program ----
            $table->unsignedInteger('green_dole_advocacies')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dole_programs');
    }
};
