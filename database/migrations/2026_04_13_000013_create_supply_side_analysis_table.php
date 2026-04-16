<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_side_analysis', function (Blueprint $table) {
            $table->id();
            $table->string('province')->default('All Provinces');
            $table->string('academic_year');
            $table->text('analysis_text');
            $table->boolean('is_active')->default(true); // Only the latest version is active
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // 🔧 REMOVED UNIQUE CONSTRAINT - Now we can have multiple versions!
            // $table->unique(['province', 'academic_year']); ❌ REMOVED
            
            // Foreign key to users table
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index(['province', 'academic_year', 'is_active']); // Find active version
            $table->index(['province', 'created_at']); // For ordering archives by date
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_side_analysis');
    }
};