<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The envelope: one row per office, per reporting period. Doesn't
     * hold report data itself — each tab (vacancies_solicited,
     * applicants_registered, etc.) is a separate table linked back
     * here via sprs_submission_id.
     *
     * status values are defined in App\Enums\SubmissionStatus.
     * revision increments on each resubmit after a rejection, so
     * sprs_submission_actions rows can be pinned to the round they
     * belong to.
     */
    public function up(): void
    {
        Schema::create('sprs_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('offices');
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->string('status')->default('draft');
            $table->unsignedTinyInteger('revision')->default(1);

            $table->foreignId('submitted_by')->nullable()->constrained('users');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('endorsed_by')->nullable()->constrained('users');
            $table->timestamp('endorsed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->unique(['office_id', 'period_year', 'period_month']);
            $table->index(['status', 'office_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sprs_submissions');
    }
};
