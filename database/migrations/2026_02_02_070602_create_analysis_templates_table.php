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
        Schema::create('analysis_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_key', 50);
            $table->integer('year');
            $table->integer('month');  // 1, 4, 7, 10
            $table->text('template_text');
            $table->boolean('is_active')->default(1);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // One template per key + year + month combo
            $table->unique(['template_key', 'year', 'month']);
        });

        // Seed defaults for all 4 quarters of the current year
        $year = (int)date('Y');
        $quarters = [1, 4, 7, 10];

        $defaults = [
            'employment'      => 'The employment rate in {current_period} was estimated at {current_rate}. This was {trend} than the recorded rate in {previous_period} of {previous_rate}.',
            'underemployment' => 'The underemployment rate in {current_period} {trend} to {current_rate}, from {previous_rate} in {previous_period}.',
            'unemployment'    => 'The unemployment rate {trend} to {current_rate} in {current_period}, from its rate in {previous_period} of {previous_rate}.',
            'lfpr'            => 'The country\'s labor force participation rate (LFPR) in {current_period} was recorded at {current_rate}, {trend} than the estimated LFPR in {previous_period} at {previous_rate}.',
        ];

        $rows = [];
        foreach ($quarters as $month) {
            foreach ($defaults as $key => $text) {
                $rows[] = [
                    'template_key' => $key,
                    'year'         => $year,
                    'month'        => $month,
                    'template_text'=> $text,
                    'is_active'    => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
        }

        DB::table('analysis_templates')->insert($rows);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_templates');
    }
};