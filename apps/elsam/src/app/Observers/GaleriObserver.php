<?php

namespace App\Observers;

use App\Models\Galeri;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GaleriObserver
{
    public function creating(Galeri $galeri)
    {
    
        $galeri->tanggal_upload = now();
        $galeri->id_user = Auth::id();
    }

    public function deleted(Galeri $galeri)
    {
        // Hapus file fisik saat record dihapus
        $path = $galeri->getRawOriginal('file_path');
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}