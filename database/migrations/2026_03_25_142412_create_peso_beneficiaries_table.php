<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
 
return new class extends Migration {
    public function up(): void {
        Schema::create('peso_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
 
        DB::table('peso_beneficiaries')->insert([
            ['name' => 'Jobseekers',                           'sort_order' => 1,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Employers',                            'sort_order' => 2,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Students',                             'sort_order' => 3,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Youth',                                'sort_order' => 4,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Migrant Workers',                      'sort_order' => 5,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Long-Term Unemployed',                 'sort_order' => 6,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Displaced Workers',                    'sort_order' => 7,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Indigenous People',                    'sort_order' => 8,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Persons with Disabilities',            'sort_order' => 9,  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Senior Citizens',                      'sort_order' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Graduates of Educational Institutions','sort_order' => 11, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
    public function down(): void { Schema::dropIfExists('peso_beneficiaries'); }
};