<?php

namespace App\Models\Sprs;

class VacanciesSolicited extends SubmissionSection
{
    protected $table = 'vacancies_solicited';

    protected $fillable = [
        'sprs_submission_id',
        'total_vacancies',
        'overseas',
        'local',
        'government_local',
        'government_overseas',
    ];
}
