<?php

namespace App\Models\Sprs;

class EmploymentAwards extends SubmissionSection
{
    protected $table = 'employment_awards';

    protected $fillable = [
        'sprs_submission_id',
        'international_awards',
        'national_awards',
        'leadership_membership_orgs',
    ];
}
