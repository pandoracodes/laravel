<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Galeri extends Model
{
    protected $table = 'galeris';
    protected $primaryKey = 'id_galeri';

    protected $fillable = [
        'judul', 
        'jenis_media', 
        'tanggal_upload', 
        'file_path', 
        'id_user'
    ];

    // Gunakan 'file_url' sebagai virtual attribute agar tidak merusak path asli
    protected $appends = ['file_url'];

    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file_path ? asset('storage/' . $this->file_path) : null,
        );
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('judul', 'like', '%' . $search . '%');
        });

        $query->when($filters['jenis'] ?? null, function ($query, $jenis) {
            $query->where('jenis_media', $jenis);
        });
    }

    public function scopeLatestUpload(Builder $query)
    {
        return $query->latest('tanggal_upload');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}