<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Who currently holds the report, as distinct from whose it is.
     *
     * The chain now cascades: a JPO submits to its PESO, the PESO
     * compiles and forwards to the province, the province forwards to
     * FO, FO endorses to RO. The same row passes through four pairs of
     * hands, and `status` can't say where it is — `submitted` used to
     * mean "with FO" only because FO was the only recipient.
     *
     *   office_id          whose report it is. Never changes.
     *   current_office_id  who holds it right now. Moves up the tree.
     *
     * "What's waiting for me" becomes `where current_office_id = my
     * office`, which works at every level without a status per tier.
     *
     * Null means the owning office still has it — a draft, or something
     * sent back to them.
     */
    public function up(): void
    {
        Schema::table('sprs_submissions', function (Blueprint $table) {
            $table->foreignId('current_office_id')
                ->nullable()
                ->after('office_id')
                ->constrained('offices')
                ->nullOnDelete();

            $table->index(['current_office_id', 'period_year', 'period_month']);
        });

        // Backfill: anything already moving sits with the parent of the
        // office that owns it. Drafts stay null, which is correct — the
        // office still has them.
        DB::statement("
            UPDATE sprs_submissions s
            JOIN offices o ON o.id = s.office_id
            SET s.current_office_id = o.parent_office_id
            WHERE s.status <> 'draft'
              AND o.parent_office_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('sprs_submissions', function (Blueprint $table) {
            $table->dropIndex(['current_office_id', 'period_year', 'period_month']);
            $table->dropForeign(['current_office_id']);
            $table->dropColumn('current_office_id');
        });
    }
};