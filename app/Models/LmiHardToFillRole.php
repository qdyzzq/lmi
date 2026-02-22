<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmiHardToFillRole extends Model
{
    use HasFactory;

    protected $fillable = [
    'lmi_submission_id',
    'job_title',
    'job_classification',
    'salary_range',
    'vacancies',              // ← ADD THIS LINE
    'vacancy_duration',
    'difficulty_reasons',
    'technical_skills_missing',
    'soft_skills_missing'
];

        protected $casts = [
            'difficulty_reasons' => 'array',
            'technical_skills_missing' => 'array',
            'soft_skills_missing' => 'array',
        ];

    public function submission()
    {
        return $this->belongsTo(LmiSubmission::class);
    }
    protected $appends = ['formatted_job_title'];

    protected static function boot()
    {
        parent::boot();
        
        // Auto-normalize on create/update
        static::saving(function ($role) {
            $role->job_title_normalized = self::normalizeJobTitle($role->job_title);
        });
    }
    
    /**
     * Normalize job title for grouping and sorting
     */
    public static function normalizeJobTitle($title)
    {
        if (empty($title)) return '';
        
        // Convert to lowercase
        $normalized = strtolower(trim($title));
        
        // Replace multiple spaces with single space
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        
        // Remove special characters (keep letters, numbers, spaces)
        $normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
        
        return $normalized;
    }
    
    /**
     * Get formatted title for display
     */
    public function getFormattedJobTitleAttribute()
    {
        return mb_strtoupper(trim($this->job_title), 'UTF-8');
    }
    
    /**
     * Scope for ordering by normalized title
     */
    public function scopeOrderByNormalizedTitle($query)
    {
        return $query->orderBy('job_title_normalized');
    }
}