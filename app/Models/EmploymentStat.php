<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentStat extends Model
{
    protected $table = 'employment_stats';

    protected $fillable = [
        'year',
        'labor_force_thousands',
        'employed_thousands',
        'unemployed_thousands',
        'employment_rate'
    ];

    public $timestamps = false;
}
