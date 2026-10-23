<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PESO City and PESO Municipality both sit at hierarchy_level 2,
     * so the level can no longer be unique. Use role_types.code
     * (added in 2026_08_18_000025) to tell same-level roles apart.
     */
    public function up(): void
    {
        Schema::table('role_types', function (Blueprint $table) {
            $table->dropUnique(['hierarchy_level']);
        });
    }

    public function down(): void
    {
        Schema::table('role_types', function (Blueprint $table) {
            $table->unique('hierarchy_level');
        });
    }
};
