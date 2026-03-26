<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
 
return new class extends Migration {
    public function up(): void {
        Schema::create('peso_core_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
 
        DB::table('peso_core_services')->insert([
            ['name' => 'Referral and Placement',                   'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Career Development Support Program (CDSP)','sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Labor Market Information',                  'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    public function down(): void { Schema::dropIfExists('peso_core_services'); }
};