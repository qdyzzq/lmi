<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stable machine identifier for each role. Since the unique index
     * on hierarchy_level was dropped, PESO City and PESO Municipality
     * both sit at level 2 and the only way to tell them apart in code
     * was matching the display name string — which breaks the first
     * time someone edits a name in an admin screen.
     *
     * Expected values: JPO, PESO_CITY, PESO_MUNI, PESO_PROV, FO, RO
     *
     * Nullable so it can be added to existing rows, but the unique
     * index still applies once populated (MySQL permits multiple NULLs
     * in a unique index). Backfill your six rows, then treat it as
     * required in the seeder and any admin form.
     */
    public function up(): void
    {
        Schema::table('role_types', function (Blueprint $table) {
            $table->string('code', 32)->nullable()->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('role_types', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }
};
