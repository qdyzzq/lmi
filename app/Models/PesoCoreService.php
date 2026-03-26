<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class PesoCoreService extends Model
{
    protected $table    = 'peso_core_services';
    protected $fillable = ['name', 'description', 'sort_order', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];
 
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}