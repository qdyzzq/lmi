<?php
 
namespace App\Models\Module5;
 
use Illuminate\Database\Eloquent\Model;
 
class PesoDoleProgram extends Model
{
    protected $table    = 'peso_dole_programs';
    protected $fillable = ['name', 'acronym', 'description', 'sort_order', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];
 
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}