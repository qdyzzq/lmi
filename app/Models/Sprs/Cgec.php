<?php

namespace App\Models\Sprs;

/**
 * Career Guidance and Employment Coaching.
 */
class Cgec extends SubmissionSection
{
    protected $table = 'cgec';

    protected $fillable = [
        'sprs_submission_id',
        'career_guidance_female',
        'employment_coaching_female',
    ];
}
