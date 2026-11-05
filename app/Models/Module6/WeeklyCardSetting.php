<?php

namespace App\Models\Module6;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WeeklyCardSetting extends Model
{
    protected $table = 'weekly_card_settings';

    protected $fillable = [
        'area',
        'image_path',
        'link_url',
        'title',
        'subtitle',
        'description',
        'is_published',
        'has_draft_changes',
        'published_at',
        'published_snapshot',
    ];

    protected $attributes = [
        'title'             => 'REGIONAL LMI WEEKLY',
        'subtitle'          => 'WEEKLY TRENDS BULLETIN',
        'description'       => 'Direct insights on weekly hiring trends and vacancy fluctuations in the Davao region. (Based on PhilJobNet)',
        'is_published'      => false,
        'has_draft_changes' => false,
    ];

    protected $casts = [
        'is_published'       => 'boolean',
        'has_draft_changes'  => 'boolean',
        'published_snapshot' => 'array',
        'published_at'       => 'datetime',
    ];

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * One card per area: 'Regional' (the original card) or 'National'.
     * The National card is created on first use; the admin enters its text.
     */
    public static function instance(string $area = 'Regional'): static
    {
        $area = $area === 'National' ? 'National' : 'Regional';

        return static::firstOrCreate(
            ['area' => $area],
            [
                'title'             => $area === 'National' ? 'NATIONAL LMI WEEKLY' : 'REGIONAL LMI WEEKLY',
                'subtitle'          => 'WEEKLY TRENDS BULLETIN',
                'description'       => $area === 'National'
                    ? ''
                    : 'Direct insights on weekly hiring trends and vacancy fluctuations in the Davao region. (Based on PhilJobNet)',
                'is_published'      => false,
                'has_draft_changes' => false,
                'published_snapshot'=> [],
            ]
        );
    }

    public function imageUrl(): ?string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : null;
    }

    public function toFrontendArray(): array
    {
        return [
            'area'              => $this->area ?? 'Regional',
            'image_url'         => $this->imageUrl(),
            'link_url'          => $this->link_url,
            'title'             => $this->title,
            'subtitle'          => $this->subtitle,
            'description'       => $this->description,
            'is_published'      => $this->is_published,
            'has_draft_changes' => $this->has_draft_changes,
            'published_at'      => $this->published_at?->toIso8601String(),
        ];
    }
}
