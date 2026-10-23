<?php

namespace App\Enums;

/**
 * The states a submission can be in. Cast this on the model:
 *
 *   protected $casts = ['status' => SubmissionStatus::class];
 *
 * so a typo produces an error instead of silently creating a state
 * nothing in the app handles.
 */
enum SubmissionStatus: string
{
    case Draft        = 'draft';          // office is still filling it in
    case Submitted    = 'submitted';      // sent up, awaiting FO review
    case Returned     = 'returned';       // FO sent it back down, with a note
    case EndorsedToRo = 'endorsed_to_ro'; // FO passed it up, awaiting RO
    case Rejected     = 'rejected';       // RO sent it back to FO, with a note
    case Approved     = 'approved';       // final

    /**
     * Statuses the office that owns the submission can still edit.
     */
    public function isEditableByOwner(): bool
    {
        return in_array($this, [self::Draft, self::Returned], true);
    }

    /**
     * Statuses FO can still correct while reviewing. Once RO has it or
     * has approved it, FO is out of the loop — corrections after that
     * go through a rejection so there is a record of the change.
     */
    public function isEditableByReviewer(): bool
    {
        return in_array($this, [self::Submitted, self::Rejected], true);
    }

    /**
     * @return array<int, self>
     */
    public function allowedNext(): array
    {
        return match ($this) {
            self::Draft        => [self::Submitted],
            self::Submitted    => [self::Returned, self::EndorsedToRo],
            self::Returned     => [self::Submitted],
            self::EndorsedToRo => [self::Rejected, self::Approved],
            self::Rejected     => [self::EndorsedToRo],
            self::Approved     => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedNext(), true);
    }
}
