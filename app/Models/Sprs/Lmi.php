<?php

namespace App\Models\Sprs;

/**
 * Labor Market Information — DOLE Forms 1, 2 and 5.
 *
 * "Total Female Reached" isn't stored on either form: it's Female Youth
 * + Female Non-Youth, computed in the view. A stored copy could drift
 * out of step with the two numbers it's made of.
 */
class Lmi extends SubmissionSection
{
    protected $table = 'lmi';

    protected $fillable = [
        'sprs_submission_id',

        // Form 1 — Individuals Reached with LMI
        'form1_youth',
        'form1_non_youth',
        'form1_female_youth',
        'form1_female_non_youth',

        // Form 2
        'form2_institutions_reached',

        // Form 5 — Individuals Reached with New Media
        'form5_youth',
        'form5_non_youth',
        'form5_female_youth',
        'form5_female_non_youth',
    ];
}