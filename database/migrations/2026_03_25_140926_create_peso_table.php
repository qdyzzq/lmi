<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
 
return new class extends Migration {
    public function up(): void {
        Schema::create('peso_info_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key', 100)->unique();
            $table->string('title', 255);
            $table->longText('content');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
 
        // Seed default row
        DB::table('peso_info_sections')->insert([
            'section_key' => 'about',
            'title'       => 'What is PESO?',
            'content'     => 'The Public Employment Service Office (PESO) is a non-fee charging multi-employment service facility...',
            'is_active'   => true,
            'sort_order'  => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
    public function down(): void { Schema::dropIfExists('peso_info_sections'); }
};