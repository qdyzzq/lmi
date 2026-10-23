<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Several rows in the prototype were rendered as editable cells when
     * they are actually section headings on the paper form — "Total
     * Vacancies" is the label above Overseas / Local / Government, not a
     * number anyone types.
     *
     * Every column dropped here is derivable from the rows beneath it, so
     * no information is lost. The form now shows these as read-only
     * computed values via 'sum_of' in config/sprs_sections.php.
     *
     * CGEC is deliberately untouched — its two "_total" columns are the
     * only record of how many students were reached, and Female alone
     * can't imply them. See the note in the config.
     */
    public function up(): void
    {
        Schema::table('vacancies_solicited', function (Blueprint $table) {
            $table->dropColumn('total_vacancies');
        });

        Schema::table('applicants_registered', function (Blueprint $table) {
            $table->dropColumn('total_applicants');
        });

        Schema::table('referral_and_placement', function (Blueprint $table) {
            $table->dropColumn([
                'pvt_referred_total',
                'pvt_placed_total',
                'govt_referred_total',
                'govt_placed_total',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('vacancies_solicited', function (Blueprint $table) {
            $table->unsignedInteger('total_vacancies')->nullable()->after('sprs_submission_id');
        });

        Schema::table('applicants_registered', function (Blueprint $table) {
            $table->unsignedInteger('total_applicants')->nullable()->after('sprs_submission_id');
        });

        Schema::table('referral_and_placement', function (Blueprint $table) {
            $table->unsignedInteger('pvt_referred_total')->nullable();
            $table->unsignedInteger('pvt_placed_total')->nullable();
            $table->unsignedInteger('govt_referred_total')->nullable();
            $table->unsignedInteger('govt_placed_total')->nullable();
        });
    }
};