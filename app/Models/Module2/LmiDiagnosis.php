<?php

namespace App\Models\Module2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmiDiagnosis extends Model
{
    use HasFactory;

    protected $table = 'lmi_diagnosis';

    protected $fillable = [
        'lmi_submission_id',
        'lmi_hard_to_fill_role_id',
        'impact_level',
        'rejection_reasons',
        'rejection_reasons_other',
        'coordination_frequency',
        'coordination_frequency_other',
    ];

    protected $casts = [
        'rejection_reasons' => 'array',
    ];

    public function submission()
    {
        return $this->belongsTo(LmiSubmission::class, 'lmi_submission_id');
    }

    public function hardToFillRole()
    {
        return $this->belongsTo(LmiHardToFillRole::class, 'lmi_hard_to_fill_role_id');
    }
}