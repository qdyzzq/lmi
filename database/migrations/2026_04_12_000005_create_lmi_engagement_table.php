<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lmi_engagement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lmi_submission_id')
                  ->constrained('lmi_submissions')
                  ->cascadeOnDelete();
            $table->json('lmi_features');
            $table->text('specific_inputs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lmi_engagement');
    }
};

