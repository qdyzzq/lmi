<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The only one-to-many tab — a submission can list any number of
     * activities, so no unique() on the foreign key.
     *
     * Two fixes from the original: the constrained() table name was
     * 'sprs_submission' (singular, does not exist), and there was a
     * manual index('sprs_submission_id') duplicating the index that
     * foreignId() already creates.
     */
    public function up(): void
    {
        Schema::create('other_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprs_submission_id')->constrained('sprs_submissions')->cascadeOnDelete();

            $table->string('activity_name');
            $table->unsignedInteger('count')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_activities');
    }
};
