<?php

namespace App\Models\Module3;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplineGraduate extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year',
        'province',
        'institution_type',
        'agriculture',
        'architecture',
        'business',
        'criminal_justice',
        'education',
        'engineering',
        'arts',
        'humanities',
        'it',
        'law',
        'maritime',
        'mass_comm',
        'mathematics',
        'medical',
        'natural_science',
        'religion',
        'service_trades',
        'social_sciences',
        'grand_total',
        'submitted_by',
    ];

    protected $casts = [
        'agriculture' => 'integer',
        'architecture' => 'integer',
        'business' => 'integer',
        'criminal_justice' => 'integer',
        'education' => 'integer',
        'engineering' => 'integer',
        'arts' => 'integer',
        'humanities' => 'integer',
        'it' => 'integer',
        'law' => 'integer',
        'maritime' => 'integer',
        'mass_comm' => 'integer',
        'mathematics' => 'integer',
        'medical' => 'integer',
        'natural_science' => 'integer',
        'religion' => 'integer',
        'service_trades' => 'integer',
        'social_sciences' => 'integer',
        'grand_total' => 'integer',
    ];

    /**
     * Get the user who submitted this graduate data
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Scope to filter by academic year
     */
    public function scopeForYear($query, $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    /**
     * Scope to filter by province
     */
    public function scopeForProvince($query, $province)
    {
        return $query->where('province', $province);
    }

    /**
     * Scope to filter by institution type
     */
    public function scopeForInstitutionType($query, $institutionType)
    {
        return $query->where('institution_type', $institutionType);
    }

    /**
     * Get all academic years with data
     */
    public static function getAvailableYears()
    {
        return self::select('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');
    }

    /**
     * Get all provinces with data
     */
    public static function getAvailableProvinces()
    {
        return self::select('province')
            ->distinct()
            ->orderBy('province', 'asc')
            ->pluck('province');
    }

    /**
     * Get all institution types with data
     */
    public static function getAvailableInstitutionTypes()
    {
        return self::select('institution_type')
            ->distinct()
            ->orderBy('institution_type', 'asc')
            ->pluck('institution_type');
    }

    /**
     * Calculate and return the grand total
     */
    public function calculateGrandTotal(): int
    {
        return $this->agriculture +
               $this->architecture +
               $this->business +
               $this->criminal_justice +
               $this->education +
               $this->engineering +
               $this->arts +
               $this->humanities +
               $this->it +
               $this->law +
               $this->maritime +
               $this->mass_comm +
               $this->mathematics +
               $this->medical +
               $this->natural_science +
               $this->religion +
               $this->service_trades +
               $this->social_sciences;
    }

    /**
     * Accessor to ensure grand total is always calculated correctly
     */
    public function getGrandTotalAttribute($value)
    {
        // Recalculate to ensure accuracy
        return $this->calculateGrandTotal();
    }

    /**
     * Automatically calculate and save grand total before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->attributes['grand_total'] = $model->calculateGrandTotal();
        });
    }

    /**
     * Get all discipline data as an array
     */
    public function getDisciplinesArray(): array
    {
        return [
            'Agriculture, Forestry, Fisheries' => $this->agriculture,
            'Architecture and Town Planning' => $this->architecture,
            'Business Administration and Related' => $this->business,
            'Criminal Justice Education' => $this->criminal_justice,
            'Education Science and Teacher Training' => $this->education,
            'Engineering and Technology' => $this->engineering,
            'Fine and Applied Arts' => $this->arts,
            'Humanities' => $this->humanities,
            'IT-Related Disciplines' => $this->it,
            'Law and Jurisprudence' => $this->law,
            'Maritime' => $this->maritime,
            'Mass Communication and Documentation' => $this->mass_comm,
            'Mathematics' => $this->mathematics,
            'Medical and Allied' => $this->medical,
            'Natural Science' => $this->natural_science,
            'Religion and Theology' => $this->religion,
            'Service Trades' => $this->service_trades,
            'Social and Behavioral Sciences' => $this->social_sciences,
        ];
    }

    /**
     * Get discipline names mapping
     */
    public static function getDisciplineNames(): array
    {
        return [
            'agriculture' => 'Agriculture, Forestry, Fisheries',
            'architecture' => 'Architecture and Town Planning',
            'business' => 'Business Administration and Related',
            'criminal_justice' => 'Criminal Justice Education',
            'education' => 'Education Science and Teacher Training',
            'engineering' => 'Engineering and Technology',
            'arts' => 'Fine and Applied Arts',
            'humanities' => 'Humanities',
            'it' => 'IT-Related Disciplines',
            'law' => 'Law and Jurisprudence',
            'maritime' => 'Maritime',
            'mass_comm' => 'Mass Communication and Documentation',
            'mathematics' => 'Mathematics',
            'medical' => 'Medical and Allied',
            'natural_science' => 'Natural Science',
            'religion' => 'Religion and Theology',
            'service_trades' => 'Service Trades',
            'social_sciences' => 'Social and Behavioral Sciences',
        ];
    }

    /**
     * Get a readable identifier for the record
     */
    public function getIdentifierAttribute(): string
    {
        return "{$this->academic_year} - {$this->province} - {$this->institution_type}";
    }
}