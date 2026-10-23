<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reporting chain, as a tree:
     *   JPO / PESO City / PESO Municipality -> PESO Province -> FO -> RO
     *
     * province_id only covers province -> city/municipality. It gives
     * you nothing for province -> FO or FO -> RO, so "show me every
     * submission under me" cannot be expressed without this column.
     *
     * Nullable because the RO at the top of the chain has no parent.
     * Seed it right after seeding offices — an office with a null
     * parent that isn't the RO will silently disappear from every
     * subtree query.
     */
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->foreignId('parent_office_id')
                ->nullable()
                ->after('name')
                ->constrained('offices')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropForeign(['parent_office_id']);
            $table->dropColumn('parent_office_id');
        });
    }
};
