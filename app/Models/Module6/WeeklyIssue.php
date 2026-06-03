<?php

namespace App\Models\Module6;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WeeklyIssue extends Model
{
    protected $table = 'weekly_issues';

    protected $fillable = [
        'year',
        'month',
        'month_order',
        'week_number',
        'date_range',
        'image_path',
    ];

    protected $casts = [
        'year'        => 'integer',
        'month_order' => 'integer',
        'week_number' => 'integer',
    ];

    // ── Accessors ─────────────────────────────────────────────────────────────

    /**
     * Full public URL for the uploaded image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) return null;
        return Storage::url($this->image_path);
    }

    /**
     * Label shown on the card, e.g. "Week 1"
     */
    public function getWeekLabelAttribute(): string
    {
        return 'Week ' . $this->week_number;
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Build the array shape expected by both admin and public blade.
     */
    public function toFrontendArray(): array
    {
        return [
            'id'          => $this->id,
            'year'        => $this->year,
            'month'       => $this->month,
            'monthOrder'  => $this->month_order,
            'weekNumber'  => $this->week_number, 
            'weekLabel'   => $this->week_label,
            'dateRange'   => $this->date_range,
            'imageUrl'    => $this->image_url,
        ];
    }
}