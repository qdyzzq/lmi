<?php

namespace App\Models\Module5;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FieldOffice extends Model
{
    use HasFactory;

    protected $table = 'field_offices';

    protected $fillable = [
        'province',
        'name',
        'office_type',
        'position_title_id',  // FK → position_titles.id
        'persons_name',        // the actual person's name
        'email',
        'address',
        'sort_order',
    ];

    protected $casts = [
        'sort_order'        => 'integer',
        'position_title_id' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function positionTitle()
    {
        return $this->belongsTo(PositionTitle::class, 'position_title_id');
    }

    // ── Scopes ────────────────────────────────────────────────
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
    public function officeType()
{
    return $this->belongsTo(OfficeType::class, 'office_type_id');
}
}