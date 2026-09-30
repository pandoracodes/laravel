<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Artikel extends Model
{
    protected $primaryKey = 'id_artikel';
    protected $guarded = [];

   
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    
    public function scopeFilter($query, array $filters)
{
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where('judul_artikel', 'like', '%' . $search . '%');
    })->when($filters['category'] ?? null, function ($query, $category) {
        $query->whereHas('kategori', function ($query) use ($category) {
            $query->where('slug', $category);
        });
    });
}
}