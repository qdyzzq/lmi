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
        Schema::create('discipline_enrollments', function (Blueprint $table) {
            $table->id();
            $table->string('academic_year'); // e.g., "2024-2025"
            $table->string('province'); // Davao City, Davao del Sur, etc.
            $table->enum('institution_type', ['Private', 'Public']); // Private or Public
            
            // All 21 disciplines (matching CHED classification)
            $table->integer('agriculture')->default(0);              // Agricultural, Forestry, and Fisheries
            $table->integer('architecture')->default(0);             // Architecture and Town Planning
            $table->integer('business')->default(0);                 // Business Administration and Related
            $table->integer('criminal_justice')->default(0);         // Criminal Justice / Criminology
            $table->integer('education')->default(0);                // Education Science and Teacher Training
            $table->integer('engineering')->default(0);              // Engineering and Technology
            $table->integer('arts')->default(0);                     // Fine and Applied Arts
            $table->integer('general')->default(0);                  // General
            $table->integer('home_economics')->default(0);           // Home Economics
            $table->integer('humanities')->default(0);               // Humanities
            $table->integer('it')->default(0);                       // IT-Related Disciplines
            $table->integer('law')->default(0);                      // Law and Jurisprudence
            $table->integer('maritime')->default(0);                 // Maritime
            $table->integer('mass_comm')->default(0);                // Mass Communication and Documentation
            $table->integer('mathematics')->default(0);              // Mathematics
            $table->integer('medical')->default(0);                  // Medical and Allied
            $table->integer('natural_science')->default(0);          // Natural Science
            $table->integer('other_disciplines')->default(0);        // Other Disciplines
            $table->integer('religion')->default(0);                 // Religion and Theology
            $table->integer('service_trades')->default(0);           // Service Trades
            $table->integer('social_sciences')->default(0);          // Social and Behavioral Sciences
            
            $table->integer('grand_total')->default(0);
            
            // Track who submitted
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Ensure unique combination of academic year, province, and institution type
            $table->unique(['academic_year', 'province', 'institution_type'], 'unique_enrollment_record');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipline_enrollments');
    }
};