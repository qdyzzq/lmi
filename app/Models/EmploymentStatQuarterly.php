<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentStatQuarterly extends Model
{
    protected $table ='employment_stats_quarterly';

    protected $fillable= [
        'year',
        'quarter',
        'labor_force_thousands',
        'employed_thousands',
        'unemployed_thousands',
        'employment_rate'
    ];

    public $timestamps = false;
}
