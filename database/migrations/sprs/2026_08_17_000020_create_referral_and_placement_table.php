<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Private Establishment: Referred and Placed both break down into
     * Local + Overseas, each with Total/Female. Fully confirmed against
     * the real form.
     *
     * Government Establishment is NOT symmetric with Private:
     *   - Referred breaks down into Local + "Overseas Government
     *     Offices" (Total/Female each)
     *   - Placed has NO Local row at all — "Overseas Government
     *     Offices Total" appears twice with no distinguishing label.
     *     Named _1 / _2 here as a placeholder; rename once it's clear
     *     what actually differs between the two (e.g. new vs renewed).
     *
     * TODO: resolve the _1 / _2 labels before building the form UI,
     * otherwise you ship two identical unlabeled inputs.
     */
    public function up(): void
    {
        Schema::create('referral_and_placement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sprs_submission_id')->unique()->constrained('sprs_submissions')->cascadeOnDelete();

            // ---- Private Establishment ----
            $table->unsignedInteger('pvt_referred_total')->nullable();
            $table->unsignedInteger('pvt_referred_local_total')->nullable();
            $table->unsignedInteger('pvt_referred_local_female')->nullable();
            $table->unsignedInteger('pvt_referred_overseas_total')->nullable();
            $table->unsignedInteger('pvt_referred_overseas_female')->nullable();
            $table->unsignedInteger('pvt_placed_total')->nullable();
            $table->unsignedInteger('pvt_placed_local_total')->nullable();
            $table->unsignedInteger('pvt_placed_local_female')->nullable();
            $table->unsignedInteger('pvt_placed_overseas_total')->nullable();
            $table->unsignedInteger('pvt_placed_overseas_female')->nullable();

            // ---- Government Establishment ----
            $table->unsignedInteger('govt_referred_total')->nullable();
            $table->unsignedInteger('govt_referred_local_total')->nullable();
            $table->unsignedInteger('govt_referred_local_female')->nullable();
            $table->unsignedInteger('govt_referred_overseas_total')->nullable();  // "Overseas Government Offices Total"
            $table->unsignedInteger('govt_referred_overseas_female')->nullable();
            $table->unsignedInteger('govt_placed_total')->nullable();
            $table->unsignedInteger('govt_placed_overseas_total_1')->nullable(); // TODO: rename once distinguished
            $table->unsignedInteger('govt_placed_overseas_female_1')->nullable();
            $table->unsignedInteger('govt_placed_overseas_total_2')->nullable(); // TODO: rename once distinguished
            $table->unsignedInteger('govt_placed_overseas_female_2')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_and_placement');
    }
};
