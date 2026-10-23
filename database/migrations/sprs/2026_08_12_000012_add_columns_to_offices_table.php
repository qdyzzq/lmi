<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->foreignId('office_type')->nullable()->after('name')->constrained('role_types');
            $table->foreignId('province_id')->nullable()->after('office_type')->constrained('provinces_type');
            $table->foreignId('class_type')->nullable()->after('province_id')->constrained('class_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropForeign(['office_type']);
            $table->dropForeign(['province_id']);
            $table->dropForeign(['class_type']);
            $table->dropColumn(['name', 'office_type', 'province_id', 'class_type']);
        });
    }
};
