<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pending_labor_market_data', function (Blueprint $table) {
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
            
            // Track who submitted and when
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Composite index for year and month
            $table->index(['year', 'month']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('pending_labor_market_data');
    }
};