<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesoDirectoryPublish extends Model
{
    protected $table = 'peso_directory_publish';

    protected $fillable = [
        'published_snapshot',
        'published_at',
        'has_draft_changes',
    ];

    protected $casts = [
        'published_snapshot' => 'array',
        'published_at'       => 'datetime',
        'has_draft_changes'  => 'boolean',
    ];

    /**
     * Always return the single row for this table, creating it if it doesn't exist.
     * This model acts as a singleton config record.
     */
    public static function singleton(): static
    {
        return static::firstOrCreate([], [
            'published_snapshot' => null,
            'published_at'       => null,
            'has_draft_changes'  => false,
        ]);
    }
}