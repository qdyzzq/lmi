<?php
 
namespace App\Models\Module5;
 
use Illuminate\Database\Eloquent\Model;
 
class PesoObjective extends Model
{
    protected $table    = 'peso_objectives';
    protected $fillable = ['content', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];
 
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }
 
    public static function getContent(string $default = ''): string
    {
        return static::where('is_active', true)->value('content') ?? $default;
    }
}