<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peso_objectives', function (Blueprint $table) {
            $table->id();
            $table->longText('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed data
        DB::table('peso_objectives')->insert([
            'content'    => 'Facilitate job matching of job seekers with enterprises through job search assistance, provision of labor market information, and career, vocational, and employment counseling.',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('peso_objectives');
    }
};
