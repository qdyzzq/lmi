<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cgec', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprs_submission_id')->unique()->constrained('sprs_submissions')->cascadeOnDelete();

            $table->unsignedInteger('career_guidance_total')->nullable();
            $table->unsignedInteger('career_guidance_female')->nullable();
            $table->unsignedInteger('employment_coaching_total')->nullable();
            $table->unsignedInteger('employment_coaching_female')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cgec');
    }
};
