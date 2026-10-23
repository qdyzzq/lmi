<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Two more sets of columns that turned out not to be inputs.
     *
     * CGEC — "Students Provided with Career Guidance" and "...Employment
     * Coaching" are titles on the paper form, and the only field under
     * each is Female. There is no Total row, so the two _total columns
     * have nothing to hold.
     *
     * LMI — "Total Female Reached with LMI" and "...with New Media" are
     * Female Youth + Female Non-Youth. Storing a value that's always the
     * sum of two others means it can disagree with them; computing it in
     * the form means it can't.
     *
     * Every drop is guarded. Depending on which version of 000028 a
     * database was built from — and on anything dropped by hand — these
     * columns may or may not be present, and MySQL fails the whole ALTER
     * if any named column is missing.
     */
    public function up(): void
    {
        $this->dropIfPresent('cgec', ['career_guidance_total', 'employment_coaching_total']);
        $this->dropIfPresent('lmi', ['form1_total_female', 'form5_total_female']);
    }

    public function down(): void
    {
        $this->addIfMissing('cgec', [
            'career_guidance_total'     => 'sprs_submission_id',
            'employment_coaching_total' => 'career_guidance_female',
        ]);

        $this->addIfMissing('lmi', [
            'form1_total_female' => 'form1_female_non_youth',
            'form5_total_female' => 'form5_female_non_youth',
        ]);
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function dropIfPresent(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $present = array_values(array_filter(
            $columns,
            fn (string $column) => Schema::hasColumn($table, $column)
        ));

        if ($present === []) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($present) {
            $blueprint->dropColumn($present);
        });
    }

    /**
     * @param  array<string, string>  $columns  name => column to sit after
     */
    private function addIfMissing(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        foreach ($columns as $column => $after) {
            if (Schema::hasColumn($table, $column)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($column, $after) {
                $blueprint->unsignedInteger($column)->nullable()->after($after);
            });
        }
    }
};