<?php

namespace App\Models\Module1;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingLaborMarketData extends Model
{
    use HasFactory;

    protected $table = 'pending_labor_market_data';

    protected $fillable = [
        'year',
        'month',
        'household_population',
        'labor_force',
        'employed',
        'underemployed',
        'unemployed',
        'labor_force_participation_rate',
        'employment_rate',
        'underemployment_rate',
        'unemployment_rate',
        'submitted_by',
    ];

    /**
     * Relationship to User who submitted this data
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}