<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('month', 20);           // e.g. "April"
            $table->unsignedTinyInteger('month_order'); // 1–12 for sorting
            $table->unsignedTinyInteger('week_number'); // 1,2,3,4,5
            $table->string('date_range', 60)->nullable(); // e.g. "April 6–10, 2026"
            $table->string('image_path')->nullable();     // storage path
            $table->timestamps();

            $table->index(['year', 'month_order', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_issues');
    }
};