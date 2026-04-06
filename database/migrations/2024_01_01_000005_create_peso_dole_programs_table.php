<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peso_dole_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('acronym', 50)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed data
        DB::table('peso_dole_programs')->insert([
            ['name' => 'Special Program for Employment of Students',                         'acronym' => 'SPES',      'description' => null, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Job Fairs',                                                           'acronym' => null,        'description' => null, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Philippine Job Board Network',                                        'acronym' => 'PhilJobNet','description' => null, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'National Skills Registration Program',                               'acronym' => 'NSRP',      'description' => null, 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DOLE Government Internship Program',                                 'acronym' => 'DOLE-GIP',  'description' => null, 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tulong Panghanapbuhay sa Ating Disadvantaged/Displaced Workers',     'acronym' => 'TUPAD',     'description' => null, 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DOLE Integrated Livelihood and Emergency Employment Program',        'acronym' => 'DILEEP',    'description' => null, 'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('peso_dole_programs');
    }
};
