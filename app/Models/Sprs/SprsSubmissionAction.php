<?php

namespace App\Models\Sprs;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only. Insert rows, never update or delete them — this is the
 * record of who did what and why, including the RO's rejection note.
 */
class SprsSubmissionAction extends Model
{
    protected $table = 'sprs_submission_actions';

    protected $fillable = [
        'sprs_submission_id',
        'actor_id',
        'action',
        'from_status',
        'to_status',
        'note',
        'revision',
    ];

    protected $casts = [
        'revision' => 'integer',
    ];

    public const SUBMITTED = 'submitted';
    public const RETURNED  = 'returned';
    public const ENDORSED  = 'endorsed';
    public const REJECTED  = 'rejected';
    public const APPROVED  = 'approved';

    public function submission(): BelongsTo
    {
        return $this->belongsTo(SprsSubmission::class, 'sprs_submission_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
