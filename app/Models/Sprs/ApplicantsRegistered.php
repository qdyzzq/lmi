<?php

namespace App\Models\Sprs;

class ApplicantsRegistered extends SubmissionSection
{
    protected $table = 'applicants_registered';

    protected $fillable = [
        'sprs_submission_id',
        'total_applicants',
        'male',
        'female',
    ];
}
