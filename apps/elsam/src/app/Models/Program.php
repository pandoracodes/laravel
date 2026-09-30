<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';
    protected $primaryKey = 'id_program'; // Sesuai ERD

    protected $fillable = [
        'nama_program',
        'slug',
        'deskripsi',
        'status',
        'gambar'
    ];

    // Relasi ke Paket
    public function pakets(): HasMany
    {
        // foreign_key: id_program, local_key: id_program
        return $this->hasMany(Paket::class, 'id_program', 'id_program');
    }

    // Scoping untuk mencari berdasarkan nama (digunakan di Controller)
    public function scopeSearch(Builder $query, $search)
    {
        return $query->when($search, function ($q, $search) {
            $q->where('nama_program', 'like', "%{$search}%");
        });
    }

    public function scopeAktif(Builder $query)
    {
        return $query->where('status', 'Aktif');
    }
}