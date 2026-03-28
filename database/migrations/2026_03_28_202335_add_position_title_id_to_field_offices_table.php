<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('field_offices', function (Blueprint $table) {
        if (!Schema::hasColumn('field_offices', 'position_title_id')) {
            $table->foreignId('position_title_id')
                  ->nullable()
                  ->after('persons_name')
                  ->constrained('position_titles')
                  ->nullOnDelete();
        }
    });
}

    public function down(): void
    {
        Schema::table('field_offices', function (Blueprint $table) {
            $table->dropForeign(['position_title_id']);
            $table->dropColumn('position_title_id');
        });
    }
};