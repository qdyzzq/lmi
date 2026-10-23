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
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropForeign(['office_type']);
        });

        Schema::table('class_types', function (Blueprint $table) {
            $table->unsignedBigInteger('office_type')->nullable()->change();
        });

        Schema::table('class_types', function (Blueprint $table) {
            $table->foreign('office_type')->references('id')->on('role_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropForeign(['office_type']);
        });

        Schema::table('class_types', function (Blueprint $table) {
            $table->unsignedBigInteger('office_type')->nullable(false)->change();
        });

        Schema::table('class_types', function (Blueprint $table) {
            $table->foreign('office_type')->references('id')->on('role_types');
        });
    }
};
