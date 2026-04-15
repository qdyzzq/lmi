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
        Schema::create('graduation_rates', function (Blueprint $table) {
            $table->id();
            $table->string('graduate_year'); // e.g., "2024-2025" (year students will graduate)
            $table->string('enrollment_year'); // e.g., "2021-2022" (year students enrolled, 4 years prior)
            $table->decimal('graduation_rate', 5, 2)->default(60.00); // Percentage: 60.00 means 60%
            $table->integer('base_enrollees')->nullable(); // Total enrollees from enrollment_year
            $table->integer('projected_graduates')->nullable(); // Calculated: base_enrollees * (graduation_rate / 100)
            $table->text('notes')->nullable(); // Optional admin notes
            $table->timestamps();
            
            // Ensure unique combination of graduate_year
            $table->unique('graduate_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graduation_rates');
    }
};