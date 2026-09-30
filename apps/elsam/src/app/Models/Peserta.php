<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peserta extends Model
{
    use HasFactory;

    // 1. Deklarasi Tabel & Primary Key (WAJIB karena custom name)
    protected $table = 'pesertas';
    protected $primaryKey = 'id_peserta'; 
    public $incrementing = true;
    protected $keyType = 'int';

    // 2. Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'no_pendaftaran', 
        'nama_peserta', 
        'nama_orang_tua', 
        'no_telepon', 
        'no_wali', 
        'email', 
        'tanggal_pendaftaran', 
        'no_ktp', 
        'id_paket', // FK ke tabel pakets
        'status_peserta', 
        'tanggal_daftar_ulang', 
        'alamat_peserta', 
        'jenis_kelamin',
        'input_via'
    ];

    // 3. Casting Tipe Data
    protected $casts = [
        'tanggal_pendaftaran' => 'date', 
        'tanggal_daftar_ulang' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * RELASI: Peserta terdaftar pada satu Paket
     */
    public function paket(): BelongsTo
    {
        // Parameter 2: Foreign Key di tabel pesertas
        // Parameter 3: Primary Key di tabel pakets
        return $this->belongsTo(Paket::class, 'id_paket', 'id_paket')->withDefault([
            'nama_paket' => 'Paket Terhapus'
        ]);
    }

    /**
     * AKSESOR: Mendapatkan data Program lewat Paket
     * Gunakan ini sebagai properti: $peserta->program_name
     */
    public function getProgramNameAttribute()
    {
        return $this->paket && $this->paket->program 
            ? $this->paket->program->nama_program 
            : 'Tanpa Program';
    }

    /**
     * SCOPE FILTER: Untuk Pencarian & Filter di Tabel Admin
     */
   public function scopeFilter(Builder $query, array $filters)
{
    // Filter Search
    $query->when($filters['search'] ?? null, function ($query, $search) {
        $query->where(function($q) use ($search) {
            $q->where('nama_peserta', 'like', '%'.$search.'%')
              ->orWhere('no_pendaftaran', 'like', '%'.$search.'%')
              ->orWhere('email', 'like', '%'.$search.'%');
        });
    });

    // Filter Berdasarkan Program (Relasi: Peserta -> Paket -> Program)
    $query->when($filters['id_program'] ?? null, function ($query, $programId) {
        $query->whereHas('paket', function ($q) use ($programId) {
            $q->where('id_program', $programId);
        });
    });

    // Filter Bulan & Tahun (Gunakan kolom tanggal_pendaftaran sesuai DB)
    $query->when($filters['month'] ?? null, function ($query, $month) {
        $query->whereMonth('tanggal_pendaftaran', $month);
    });

    $query->when($filters['year'] ?? null, function ($query, $year) {
        $query->whereYear('tanggal_pendaftaran', $year);
    });
}
    public function scopeFromUser($query) {
    return $query->where('input_via', 'user');
}

public function scopeFromAdmin($query) {
    return $query->where('input_via', 'admin');
}

}