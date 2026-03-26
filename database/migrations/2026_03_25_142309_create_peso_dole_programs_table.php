<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
 
return new class extends Migration {
    public function up(): void {
        Schema::create('peso_dole_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('acronym', 50)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
 
        DB::table('peso_dole_programs')->insert([
            ['name' => 'Special Program for Employment of Students',             'acronym' => 'SPES',      'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Job Fairs',                                              'acronym' => null,        'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Philippine Job Board Network',                           'acronym' => 'PhilJobNet','sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'National Skills Registration Program',                   'acronym' => 'NSRP',     'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DOLE Government Internship Program',                     'acronym' => 'DOLE-GIP', 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tulong Panghanapbuhay sa Ating Disadvantaged Workers',   'acronym' => 'TUPAD',    'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DOLE Integrated Livelihood and Emergency Employment Program','acronym' => 'DILEEP','sort_order' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    public function down(): void { Schema::dropIfExists('peso_dole_programs'); }
};