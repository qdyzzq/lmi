<?php

namespace App\Models\Module3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplySideAnalysis extends Model
{
    use HasFactory;

    protected $table = 'supply_side_analysis';

    protected $fillable = [
        'province',
        'academic_year',
        'analysis_text',
        'is_active',
        'updated_by',
        'status',        // 'pending' | 'published'
        'submitted_by',
        'submitted_at',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'submitted_at' => 'datetime',
    ];

    // ─── Scopes ───────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForProvince($query, $province)
    {
        return $query->where('province', $province);
    }

    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    // ─── Relationships ────────────────────────────────────

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ─── Helpers ──────────────────────────────────────────

    public static function getDefaultText(): string
    {
        return "Supply is robust but misaligned. While total enrollment in Davao Region is increasing, the Education and Business Administration disciplines account for over 45% of the total student population, potentially saturating those labor markets.\n\nConversely, Engineering & Tech enrollments are steady but may not meet the projected infrastructure boom demands.\n\nLicensure performance varies significantly, with Nursing leading at 75%, while CPA performance remains a concern at 25%.";
    }
}