<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmiEngagement extends Model
{
    use HasFactory;

    protected $table = 'lmi_engagement';  // ← ADD THIS LINE

    protected $fillable = [
        'lmi_submission_id',
        'lmi_features',
        'specific_inputs'
    ];

    protected $casts = [
        'lmi_features' => 'array',
    ];

    public function submission()
    {
        return $this->belongsTo(LmiSubmission::class);
    }
}