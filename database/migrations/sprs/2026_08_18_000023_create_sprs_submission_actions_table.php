<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Append-only audit trail for the review workflow. This is where a
     * rejection note lives.
     *
     * sprs_submissions records the latest state and who set it; this
     * table records how it got there. Needed because the reject ->
     * fix -> resubmit loop can run more than once: a single
     * rejection_note column on the submission would be overwritten on
     * the second rejection, losing the first reason.
     *
     * Never UPDATE or DELETE rows here — only INSERT. Write one row in
     * the same transaction as every sprs_submissions.status change,
     * stamping the submission's current revision so each note is
     * pinned to the round it belongs to.
     *
     * action: submitted | returned | endorsed | rejected | approved
     * See App\Enums\SubmissionStatus for the status values.
     */
    public function up(): void
    {
        Schema::create('sprs_submission_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprs_submission_id')->constrained('sprs_submissions')->cascadeOnDelete();
            $table->foreignId('actor_id')->constrained('users');

            $table->string('action');
            $table->string('from_status')->nullable(); // null on the first submit
            $table->string('to_status');
            $table->text('note')->nullable();          // rejection / return reason
            $table->unsignedTinyInteger('revision');

            $table->timestamps();

            $table->index(['sprs_submission_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sprs_submission_actions');
    }
};
