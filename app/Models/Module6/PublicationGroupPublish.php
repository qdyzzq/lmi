<?php

namespace App\Models\Module6;

use Illuminate\Database\Eloquent\Model;

class PublicationGroupPublish extends Model
{
    protected $primaryKey = 'group_id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'group_id',
        'is_published',
        'has_draft_changes',
        'published_at',
        'published_snapshot',
    ];

    protected $casts = [
        'is_published'       => 'boolean',
        'has_draft_changes'  => 'boolean',
        'published_at'       => 'datetime',
        'published_snapshot' => 'array',
    ];

    /**
     * Get or create the publish record for a group.
     */
    public static function forGroup(string $groupId): self
    {
        return static::firstOrCreate(
            ['group_id' => $groupId],
            [
                'is_published'      => false,
                'has_draft_changes' => false,
            ]
        );
    }
}