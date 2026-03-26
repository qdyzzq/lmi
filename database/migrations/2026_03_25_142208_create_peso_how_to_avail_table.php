<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

 
return new class extends Migration {
    public function up(): void {
        Schema::create('peso_how_to_avail', function (Blueprint $table) {
            $table->id();
            $table->longText('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
 
        DB::table('peso_how_to_avail')->insert([
            'content'    => 'See the Directory of Public Employment Service Offices/Job Placement Offices below to find the nearest PESO/JPO in your area.',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    public function down(): void { Schema::dropIfExists('peso_how_to_avail'); }
};