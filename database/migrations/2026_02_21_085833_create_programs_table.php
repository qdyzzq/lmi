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
          Schema::create('programs', function (Blueprint $table) {
        $table->id();
        $table->string('name');           // e.g. "Government Internship Program"
        $table->string('acronym');        // e.g. "GIP"
        $table->string('subtitle');       // e.g. "3–6 month internship opportunity in government"
        $table->text('description');      // the main program details paragraph
        $table->string('color');          // e.g. "green", "red", "blue"
        $table->string('logo_path')->nullable();
        $table->integer('sort_order')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
