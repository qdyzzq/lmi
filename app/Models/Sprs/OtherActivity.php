<?php

namespace App\Models\Sprs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The one repeating tab — a submission can list any number of these,
 * so it doesn't extend SubmissionSection (no unique FK, has its own id
 * as a meaningful key for row edits and deletes).
 */
class OtherActivity extends Model
{
    protected $table = 'other_activities';

    protected $fillable = [
        'sprs_submission_id',
        'activity_name',
        'count',
    ];

    protected $casts = [
        'count' => 'integer',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(SprsSubmission::class, 'sprs_submission_id');
    }
}
