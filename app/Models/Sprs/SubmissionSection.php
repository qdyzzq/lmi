<?php

namespace App\Models\Sprs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Shared base for the seven one-to-one tab tables.
 *
 * Every one of them is the same shape: an id, a unique
 * sprs_submission_id, a pile of nullable unsigned integers, and
 * timestamps. Subclasses only declare $table and $fillable.
 *
 * Not a table itself — never call SubmissionSection::query().
 */
abstract class SubmissionSection extends Model
{
    public function submission(): BelongsTo
    {
        return $this->belongsTo(SprsSubmission::class, 'sprs_submission_id');
    }

    /**
     * Blank counts and empty strings arrive from the form as '' — store
     * them as NULL so "not answered" stays distinct from "answered zero".
     */
    public function setAttribute($key, $value)
    {
        if ($value === '' && $key !== 'sprs_submission_id') {
            $value = null;
        }

        return parent::setAttribute($key, $value);
    }
}
