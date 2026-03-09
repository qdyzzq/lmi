<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FieldOffice extends Model
{
    use HasFactory;

    protected $table = 'field_offices';

    protected $fillable = [
        'province',
        'name',
        'office_type',   // e.g. PESO, JPO, DOLE, TESDA, etc. — not limited
        'manager_name',
        'email',
        'address',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // ── Scopes ──────────────────────────────────────────────

    public function scopeByProvince($query, string $province)
    {
        return $query->where('province', $province);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('office_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('province')->orderBy('sort_order')->orderBy('name');
    }
}