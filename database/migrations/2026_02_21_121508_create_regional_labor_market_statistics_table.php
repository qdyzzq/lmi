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
    Schema::create('regional_labor_market_statistics', function (Blueprint $table) {
        $table->id();
        $table->integer('year');
        $table->integer('month');
        $table->bigInteger('household_population')->nullable();
        $table->bigInteger('labor_force')->nullable();
        $table->bigInteger('employed')->nullable();
        $table->bigInteger('underemployed')->nullable();
        $table->bigInteger('unemployed')->nullable();
        $table->decimal('labor_force_participation_rate', 5, 2)->nullable();
        $table->decimal('employment_rate', 5, 2)->nullable();
        $table->decimal('underemployment_rate', 5, 2)->nullable();
        $table->decimal('unemployment_rate', 5, 2)->nullable();
        $table->timestamps();
        $table->unique(['year', 'month']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regional_labor_market_statistics');
    }
};
