<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add the new FK column (nullable first so existing rows don't break)
        Schema::table('field_offices', function (Blueprint $table) {
            $table->foreignId('office_type_id')
                  ->nullable()
                  ->after('name')
                  ->constrained('office_types')
                  ->nullOnDelete();
        });

        // Step 2: Migrate existing string values → matching office_types.id
        // This matches the old office_type string (e.g. 'PESO', 'JPO') to the
        // name column in office_types and fills in the new FK column.
        DB::statement('
            UPDATE field_offices fo
            JOIN office_types ot ON UPPER(TRIM(ot.name)) = UPPER(TRIM(fo.office_type))
            SET fo.office_type_id = ot.id
        ');

        // Step 3: Drop the old string column
        Schema::table('field_offices', function (Blueprint $table) {
            $table->dropIndex(['office_type']);  // drop index first if it exists
        });

        Schema::table('field_offices', function (Blueprint $table) {
            $table->dropColumn('office_type');
        });
    }

    public function down(): void
    {
        // Reverse: restore office_type string column from the FK
        Schema::table('field_offices', function (Blueprint $table) {
            $table->string('office_type')->nullable()->after('name');
        });

        DB::statement('
            UPDATE field_offices fo
            JOIN office_types ot ON fo.office_type_id = ot.id
            SET fo.office_type = ot.name
        ');

        Schema::table('field_offices', function (Blueprint $table) {
            $table->index('office_type');
            $table->dropForeign(['office_type_id']);
            $table->dropColumn('office_type_id');
        });
    }
};
