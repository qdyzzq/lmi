<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('position_titles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        // Seed the position titles
        DB::table('position_titles')->insert([
            ['name' => 'JPO MANAGER',       'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PESO MANAGER',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DISTRICT HEAD', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('position_titles');
    }
};
