<?php

namespace App\Observers;

use App\Models\Peserta;
use Carbon\Carbon;

class PesertaObserver
{
    public function creating(Peserta $peserta)
{
    // Generate: REG-202405-001
    $count = Peserta::count() + 1;
    $peserta->no_pendaftaran = 'REG-' . now()->format('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    
    $peserta->tanggal_pendaftaran = now();
    
    if (!$peserta->status_peserta) {
        $peserta->status_peserta = 'Pending'; // Sesuai kolom varchar di DB kamu
    }
}
 public function updating(Peserta $peserta)
    {
        // Jika status diubah ke 'aktif' dan tanggal_daftar_ulang masih kosong
        if ($peserta->isDirty('status_peserta') && $peserta->status_peserta === 'aktif') {
            if (empty($peserta->tanggal_daftar_ulang)) {
                $peserta->tanggal_daftar_ulang = now();
            }
        }
    }
public function scopeSudahDaftarUlang($query) {
    return $query->whereNotNull('tanggal_daftar_ulang');
}

// Menampilkan yang baru daftar (pendaftar baru)
public function scopePendaftarBaru($query) {
    return $query->whereNull('tanggal_daftar_ulang');
}
}