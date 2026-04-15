<?php
 
namespace App\Models\Module5;
 
use Illuminate\Database\Eloquent\Model;
 
class PesoInfoSection extends Model
{
    protected $table    = 'peso_info_sections';
    protected $fillable = ['section_key', 'title', 'content', 'is_active', 'sort_order'];
    protected $casts    = ['is_active' => 'boolean'];
 
    public static function getByKey(string $key): ?self
    {
        return static::where('section_key', $key)->first();
    }
 
    public static function getContent(string $key, string $default = ''): string
    {
        return static::where('section_key', $key)->value('content') ?? $default;
    }
}
 