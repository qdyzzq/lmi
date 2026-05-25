<?php

namespace App\Models\Module6;

use Illuminate\Database\Eloquent\Model;

class PublicationIssue extends Model
{
    protected $fillable = [
        'group_id',
        'title',
        'description',
        'year',
        'drive_file_id',
        'sort_order',
    ];

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Extract the Drive file ID from a full Drive URL or return as-is if already an ID.
     * Supports formats:
     *   https://drive.google.com/file/d/{ID}/view...
     *   https://drive.google.com/open?id={ID}
     *   https://docs.google.com/...d/{ID}/...
     */
    public static function extractDriveId(?string $input): ?string
    {
        if (!$input) return null;

        $input = trim($input);

        // Already just an ID (no slashes or dots)
        if (preg_match('/^[a-zA-Z0-9_\-]{10,}$/', $input)) {
            return $input;
        }

        // /file/d/{ID}/ or /d/{ID}/
        if (preg_match('#[/=]d[/=]([a-zA-Z0-9_\-]{10,})#', $input, $m)) {
            return $m[1];
        }

        // ?id={ID} or &id={ID}
        if (preg_match('#[?&]id=([a-zA-Z0-9_\-]{10,})#', $input, $m)) {
            return $m[1];
        }

        return null;
    }

    public function driveThumbnailUrl(): ?string
    {
        if (!$this->drive_file_id) return null;
        return "https://drive.google.com/thumbnail?id={$this->drive_file_id}&sz=s500";
    }

    public function driveViewUrl(): ?string
    {
        if (!$this->drive_file_id) return null;
        return "https://drive.google.com/file/d/{$this->drive_file_id}/view?usp=sharing";
    }
}