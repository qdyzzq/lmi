<?php

namespace App\Models\Sprs;

use App\Models\User;
use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * The envelope. One row per office per reporting period; the actual
 * numbers live in the eight tab tables that hang off it.
 *
 * Never write `status` directly from a controller — go through the
 * transition service (step 3) so the audit row is always written in
 * the same transaction.
 */
class SprsSubmission extends Model
{
    protected $table = 'sprs_submissions';

    protected $fillable = [
        'office_id',
        'period_year',
        'period_month',
        'status',
        'revision',
    ];

    protected $casts = [
        'status'       => SubmissionStatus::class,
        'period_year'  => 'integer',
        'period_month' => 'integer',
        'revision'     => 'integer',
        'submitted_at' => 'datetime',
        'endorsed_at'  => 'datetime',
        'approved_at'  => 'datetime',
    ];

    // ---- ownership and actors ----

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function endorsedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'endorsed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(SprsSubmissionAction::class)->latest('created_at');
    }

    // ---- the eight tabs ----

    public function vacanciesSolicited(): HasOne
    {
        return $this->hasOne(VacanciesSolicited::class);
    }

    public function applicantsRegistered(): HasOne
    {
        return $this->hasOne(ApplicantsRegistered::class);
    }

    public function referralAndPlacement(): HasOne
    {
        return $this->hasOne(ReferralAndPlacement::class);
    }

    public function cgec(): HasOne
    {
        return $this->hasOne(Cgec::class);
    }

    public function lmi(): HasOne
    {
        return $this->hasOne(Lmi::class);
    }

    public function employmentAwards(): HasOne
    {
        return $this->hasOne(EmploymentAwards::class);
    }

    public function doleProgram(): HasOne
    {
        return $this->hasOne(DoleProgram::class);
    }

    /** The only one-to-many tab. */
    public function otherActivities(): HasMany
    {
        return $this->hasMany(OtherActivity::class);
    }

    /** @return array<int, string> relation names of every tab */
    public static function sectionRelations(): array
    {
        return [
            'vacanciesSolicited',
            'applicantsRegistered',
            'referralAndPlacement',
            'cgec',
            'lmi',
            'employmentAwards',
            'doleProgram',
            'otherActivities',
        ];
    }

    // ---- scopes ----

    public function scopePeriod(Builder $q, int $year, int $month): Builder
    {
        return $q->where('period_year', $year)->where('period_month', $month);
    }

    public function scopeWithStatus(Builder $q, SubmissionStatus ...$statuses): Builder
    {
        return $q->whereIn('status', array_map(fn ($s) => $s->value, $statuses));
    }

    /**
     * Restrict to the submissions this user is allowed to see: their own
     * office plus everything beneath it in the tree.
     */
    public function scopeVisibleTo(Builder $q, User $user): Builder
    {
        if ($user->office === null) {
            return $q->whereRaw('1 = 0');
        }

        return $q->whereIn('office_id', $user->office->descendantIds());
    }

    // ---- convenience ----

    public function isEditable(): bool
    {
        return $this->status->isEditableByOwner();
    }

    /** The most recent rejection or return note, if any. */
    public function latestNote(): ?string
    {
        return $this->actions()
            ->whereIn('action', ['rejected', 'returned'])
            ->whereNotNull('note')
            ->value('note');
    }

    public function periodLabel(): string
    {
        return \Carbon\Carbon::create($this->period_year, $this->period_month, 1)
            ->format('F Y');
    }
}
