<?php

namespace App\Models\Sprs;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Office extends Model
{
    protected $table = 'offices';

    protected $fillable = [
        'name',
        'parent_office_id',
        'office_type',
        'province_id',
        'class_type',
    ];

    // ---- relationships ----

    public function officeType()
    {
        return $this->belongsTo(RoleType::class, 'office_type');
    }

    /**
     * FK is province_id. The previous version passed 'province_type',
     * which is not a column — the relation always resolved to null.
     */
    public function province()
    {
        return $this->belongsTo(ProvinceType::class, 'province_id');
    }

    public function classType()
    {
        return $this->belongsTo(ClassType::class, 'class_type');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'office_id');
    }

    public function submissions()
    {
        return $this->hasMany(SprsSubmission::class, 'office_id');
    }

    // ---- reporting tree ----

    public function parent()
    {
        return $this->belongsTo(Office::class, 'parent_office_id');
    }

    public function children()
    {
        return $this->hasMany(Office::class, 'parent_office_id');
    }

    /**
     * Every office at or below this one, to any depth.
     *
     * One recursive CTE rather than a query per level. This is what
     * answers "PESO Province sees all submissions under it" and "FO sees
     * everything below it".
     *
     * Requires MySQL 8.0+ / MariaDB 10.2+.
     *
     * @return array<int, int>
     */
    public function descendantIds(bool $includeSelf = true): array
    {
        $rows = DB::select(
            'WITH RECURSIVE office_tree AS (
                 SELECT id, parent_office_id FROM offices WHERE id = ?
                 UNION ALL
                 SELECT o.id, o.parent_office_id
                 FROM offices o
                 INNER JOIN office_tree t ON o.parent_office_id = t.id
             )
             SELECT id FROM office_tree',
            [$this->id]
        );

        $ids = array_map(static fn ($row) => (int) $row->id, $rows);

        return $includeSelf ? $ids : array_values(array_diff($ids, [$this->id]));
    }

    public function hierarchyLevel(): ?int
    {
        return $this->officeType?->hierarchy_level;
    }
}