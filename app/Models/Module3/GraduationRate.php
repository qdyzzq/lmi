<?php

namespace App\Models\Module3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraduationRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'graduate_year',
        'enrollment_year',
        'graduation_rate',
        'base_enrollees',
        'projected_graduates',
        'notes',
        'description'
    ];

    protected $casts = [
        'graduation_rate' => 'decimal:2',
        'base_enrollees' => 'integer',
        'projected_graduates' => 'integer',
    ];

    /**
     * Calculate enrollment year (4 years before graduate year)
     */
    public static function calculateEnrollmentYear($graduateYear)
    {
        // Parse graduate year (e.g., "2024-2025")
        $years = explode('-', $graduateYear);
        if (count($years) !== 2) {
            return null;
        }

        $firstYear = (int)$years[0] - 4;
        $secondYear = (int)$years[1] - 4;

        return "{$firstYear}-{$secondYear}";
    }

    /**
     * Calculate projected graduates based on enrollment and rate
     */
    public function calculateProjectedGraduates()
    {
        if ($this->base_enrollees && $this->graduation_rate) {
            $this->projected_graduates = round($this->base_enrollees * ($this->graduation_rate / 100));
            return $this->projected_graduates;
        }
        return null;
    }

    /**
     * Get graduation rate for a specific year
     */
    public static function getRateForYear($graduateYear)
    {
        $rate = self::where('graduate_year', $graduateYear)->first();
        return $rate ? $rate->graduation_rate : 60.00; // Default 60%
    }

    /**
     * Get projected graduates for a specific year
     */
    public static function getProjectedForYear($graduateYear)
    {
        $rate = self::where('graduate_year', $graduateYear)->first();
        return $rate ? $rate->projected_graduates : 0;
    }
}