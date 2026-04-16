<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lmi_hard_to_fill_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lmi_submission_id')
                  ->constrained('lmi_submissions')
                  ->cascadeOnDelete();
            $table->string('job_title');
            $table->string('job_title_normalized')->nullable()->index();
            $table->string('job_classification');
            $table->string('salary_range')->nullable();
            $table->integer('vacancies')->nullable();
            $table->string('vacancy_duration');
            $table->json('difficulty_reasons');
            $table->json('technical_skills_missing')->nullable();
            $table->json('soft_skills_missing')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lmi_hard_to_fill_roles');
    }
};

