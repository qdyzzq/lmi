<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropForeign(['office_type']);
            $table->dropForeign(['province_id']);
            $table->dropForeign(['class_type']);
            $table->dropColumn(['office_type', 'province_id', 'class_type']);
        });

        Schema::table('class_types', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }

    /**
     * office_type is re-added as nullable here to match the state left
     * by 2026_08_12_000011. The original version of this down() made it
     * NOT NULL, which meant rolling back and re-migrating produced a
     * different schema than you started with.
     */
    public function down(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('class_types', function (Blueprint $table) {
            $table->foreignId('office_type')->nullable()->constrained('role_types');
            $table->foreignId('province_id')->nullable()->constrained('provinces_type');
            $table->foreignId('class_type')->nullable()->constrained('class_types');
        });
    }
};
