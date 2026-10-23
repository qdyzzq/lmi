<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * total_applicants is nullable like every other tab column. The
     * form saves as a draft before it is complete, so a NOT NULL here
     * would throw an integrity error on partial input. Require it in
     * the FormRequest at submit time instead, along with the
     * male + female = total check.
     */
    public function up(): void
    {
        Schema::create('applicants_registered', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprs_submission_id')->unique()->constrained('sprs_submissions')->cascadeOnDelete();

            $table->unsignedInteger('total_applicants')->nullable();
            $table->unsignedInteger('male')->nullable();
            $table->unsignedInteger('female')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants_registered');
    }
};
