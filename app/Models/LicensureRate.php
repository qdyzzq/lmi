<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicensureRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'sector',
        'profession',
        'takers',
        'passers',
        'passing_rate',
        'submitted_by',
    ];

    protected $casts = [
        'passing_rate' => 'decimal:2',
        'year' => 'integer',
        'takers' => 'integer',
        'passers' => 'integer',
    ];

    /**
     * Get the user who submitted this data
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Scope to filter by year
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to filter by sector
     */
    public function scopeBySector($query, $sector)
    {
        return $query->where('sector', $sector);
    }

    /**
     * Get all years with data
     */
    public static function getAvailableYears()
    {
        return self::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    }

    /**
     * Get all sectors
     */
    public static function getAvailableSectors()
    {
        return self::select('sector')
            ->distinct()
            ->orderBy('sector')
            ->pluck('sector');
    }

    /**
     * Accessor to ensure passing rate is always calculated correctly
     */
    public function getPassingRateAttribute($value)
    {
        // If takers and passers are set, recalculate to ensure accuracy
        if ($this->takers > 0) {
            return round(($this->passers / $this->takers) * 100, 2);
        }
        return $value;
    }
}