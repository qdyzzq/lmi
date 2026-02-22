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
        Schema::create('licensure_rates', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->string('sector');
            $table->string('profession');
            $table->integer('takers'); // Number of exam takers
            $table->integer('passers'); // Number of passers
            $table->decimal('passing_rate', 5, 2); // Calculated percentage (allows values like 100.00)
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->timestamps();

            // Foreign key (assuming you have a users table)
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for better query performance
            $table->index('year');
            $table->index('sector');
            $table->unique(['year', 'sector', 'profession']); // Prevent duplicate entries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licensure_rates');
    }
};