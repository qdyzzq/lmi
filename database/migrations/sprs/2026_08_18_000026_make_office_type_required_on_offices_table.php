<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Role is derived, not stored: user -> office -> office_type -> role_types.
     * That only works if office_type is always present, so the guarantee is
     * moved out of the form and into the schema. Seeders, tinker, imports and
     * manual edits don't run form validation; this constraint applies to all
     * of them.
     *
     * users.office_id deliberately stays nullable — the other project sharing
     * this database has users with no office. That check belongs in the SPRS
     * auth guard:
     *
     *     abort_if($user->office_id === null, 403);
     *     $level = $user->office->officeType->hierarchy_level;
     *
     * Replaces the earlier draft of 000026, which added users.role_type_id.
     * Delete that file if you still have it — the two are alternatives, not
     * a pair.
     */
    public function up(): void
    {
        $orphans = DB::table('offices')->whereNull('office_type')->count();

        if ($orphans > 0) {
            throw new RuntimeException(
                "Cannot apply NOT NULL: {$orphans} office(s) have a null office_type. "
                . "Assign a role_types id to each one, then re-run this migration."
            );
        }

        Schema::table('offices', function (Blueprint $table) {
            $table->dropForeign(['office_type']);
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->unsignedBigInteger('office_type')->nullable(false)->change();
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->foreign('office_type')->references('id')->on('role_types');
        });
    }

    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropForeign(['office_type']);
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->unsignedBigInteger('office_type')->nullable()->change();
        });

        Schema::table('offices', function (Blueprint $table) {
            $table->foreign('office_type')->references('id')->on('role_types');
        });
    }
};
