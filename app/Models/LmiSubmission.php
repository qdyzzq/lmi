<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmiSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'respondent_name',
        'position',
        'contact_number',
        'contact_type',       // ← ADDED: 'mobile' or 'telephone'
        'email',
        'industry_sector',
        'company_size',
        'status',
        'admin_notes',
        'submitted_at',
        'reviewed_at',
        'reviewed_by'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    public function hardToFillRoles()
    {
        return $this->hasMany(LmiHardToFillRole::class);
    }

    public function diagnoses()
    {
        return $this->hasMany(LmiDiagnosis::class);
    }

    public function diagnosis()
    {
        return $this->hasOne(LmiDiagnosis::class);
    }

    public function engagement()
    {
        return $this->hasOne(LmiEngagement::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Handy accessor: returns the number with its prefix label ──
    // e.g. "+63 9123456789" or "082-123-4567"
    public function getFormattedContactAttribute(): string
    {
        if ($this->contact_type === 'telephone') {
            return $this->contact_number;
        }
        return '+63 ' . $this->contact_number;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}