<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cta_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('subtitle', 1000);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Seed the single default row so CtaSection::first() always returns something
        DB::table('cta_sections')->insert([
            'title'        => 'Ready to Start Your Journey?',
            'subtitle'     => "Join thousands of youth who have transformed their careers through DOLE's employment programs.",
            'published_at' => null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cta_sections');
    }
};