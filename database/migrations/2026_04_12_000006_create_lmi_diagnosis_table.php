<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lmi_diagnosis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lmi_submission_id')
                  ->constrained('lmi_submissions')
                  ->cascadeOnDelete();
            $table->unsignedBigInteger('lmi_hard_to_fill_role_id');
            $table->enum('impact_level', ['High', 'Medium', 'Low']);
            $table->json('rejection_reasons')->nullable();
            $table->string('rejection_reasons_other')->nullable();
            $table->string('coordination_frequency')->nullable();
            $table->string('coordination_frequency_other')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lmi_diagnosis');
    }
};

