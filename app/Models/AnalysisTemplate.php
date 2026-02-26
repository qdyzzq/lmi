<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisTemplate extends Model
{
    use HasFactory;

    protected $table = 'analysis_templates';

    protected $fillable = [
        'template_key',
        'year',
        'month',
        'template_text',
        'is_active',
        'updated_by',
        'status',
        'submitted_by',
        'submitted_at',
    ];

    protected $casts = [
        'year'         => 'integer',
        'month'        => 'integer',
        'is_active'    => 'boolean',
        'submitted_at' => 'datetime',
    ];

    /**
     * The 4 quarters your system uses
     */
    public const QUARTERS = [
        1  => 'January',
        4  => 'April',
        7  => 'July',
        10 => 'October',
    ];

    // ─── Scopes ───────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('year', (int)$year);
    }

    public function scopeForMonth($query, $month)
    {
        return $query->where('month', (int)$month);
    }

    // ─── Relationships ────────────────────────────────────

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ─── Helpers ──────────────────────────────────────────

    /**
     * Returns the month name for this template (e.g. "January")
     */
    public function getMonthNameAttribute(): string
    {
        return self::QUARTERS[$this->month] ?? 'Unknown';
    }

    /**
     * Returns the full label like "January 2030"
     */
    public function getPeriodLabelAttribute(): string
    {
        return $this->month_name . ' ' . $this->year;
    }
}