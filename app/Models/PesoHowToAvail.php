<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class PesoHowToAvail extends Model
{
    protected $table    = 'peso_how_to_avail';
    protected $fillable = ['content', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];
 
    public static function getContent(string $default = ''): string
    {
        return static::where('is_active', true)->value('content') ?? $default;
    }
}