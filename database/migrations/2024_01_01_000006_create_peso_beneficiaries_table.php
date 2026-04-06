<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peso_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed data
        DB::table('peso_beneficiaries')->insert([
            ['name' => 'Jobseekers',                           'is_active' => true, 'sort_order' => 1,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Employers',                            'is_active' => true, 'sort_order' => 2,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Students',                             'is_active' => true, 'sort_order' => 3,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Youth',                                'is_active' => true, 'sort_order' => 4,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Migrant Workers',                      'is_active' => true, 'sort_order' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Long-Term Unemployed',                 'is_active' => true, 'sort_order' => 6,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Displaced Workers',                    'is_active' => true, 'sort_order' => 7,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Indigenous People',                    'is_active' => true, 'sort_order' => 8,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Persons with Disabilities',            'is_active' => true, 'sort_order' => 9,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Senior Citizens',                      'is_active' => true, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Graduates of Educational Institutions','is_active' => true, 'sort_order' => 11, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('peso_beneficiaries');
    }
};
