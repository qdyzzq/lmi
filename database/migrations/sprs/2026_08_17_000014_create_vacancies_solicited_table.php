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
        Schema::create('vacancies_solicited', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprs_submission_id')->unique()->constrained('sprs_submissions')->cascadeOnDelete();

            $table->unsignedInteger('total_vacancies')->nullable();
            $table->unsignedInteger('overseas')->nullable();
            $table->unsignedInteger('local')->nullable();
            $table->unsignedInteger('government_local')->nullable();
            $table->unsignedInteger('government_overseas')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies_solicited');
    }
};
