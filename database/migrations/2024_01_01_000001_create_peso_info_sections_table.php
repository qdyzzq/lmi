<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peso_info_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key', 100)->unique();
            $table->string('title', 255);
            $table->longText('content');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed data
        DB::table('peso_info_sections')->insert([
            'section_key' => 'about',
            'title'       => 'What is PESO?',
            'content'     => 'The Public Employment Service Office (PESO) is a non-fee charging multi-employment service facility or entity established and accredited pursuant to Republic Act No. 8759 otherwise known as the PESO Act of 1999. The office was established through a Memorandum of Agreement between the Department of Labor & Employment (DOLE) and the LGU of Davao City in the year 1994, and was institutionalized under Resolution No. 02190-12 Series of 2012 with its corresponding City Ordinance No. 0391-12. The office was established with the aim of assisting jobseekers in finding stable and sustainable employment for a qualified workforce gainfully employed in country and overseas.',
            'is_active'   => true,
            'sort_order'  => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('peso_info_sections');
    }
};
