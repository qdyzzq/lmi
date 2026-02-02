<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lmi_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('respondent_name');
            $table->string('position');
            $table->string('contact_number');
            $table->string('email');
            $table->string('industry_sector');
            $table->string('company_size');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('lmi_hard_to_fill_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lmi_submission_id')->constrained()->onDelete('cascade');
            $table->string('job_title');
            $table->string('job_classification');
            $table->string('vacancy_duration');
            $table->json('difficulty_reasons'); // Store as JSON array
            $table->json('technical_skills_missing')->nullable(); // Store tags as JSON
            $table->json('soft_skills_missing')->nullable(); // Store tags as JSON
            $table->timestamps();
        });

        Schema::create('lmi_diagnosis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lmi_submission_id')->constrained()->onDelete('cascade');
            $table->enum('impact_level', ['High', 'Medium', 'Low']);
            $table->timestamps();
        });

        Schema::create('lmi_engagement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lmi_submission_id')->constrained()->onDelete('cascade');
            $table->json('lmi_features'); // Store selected features as JSON
            $table->text('specific_inputs')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lmi_engagement');
        Schema::dropIfExists('lmi_diagnosis');
        Schema::dropIfExists('lmi_hard_to_fill_roles');
        Schema::dropIfExists('lmi_submissions');
    }
};