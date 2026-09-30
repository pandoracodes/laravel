<?php
namespace App\Observers;

use App\Models\Artikel;
use Illuminate\Support\Str;

class ArtikelObserver
{
    public function creating(Artikel $artikel)
    {
        // Otomatis buat slug dari judul sebelum simpan
        $artikel->slug = Str::slug($artikel->judul_artikel);
        
        // Otomatis isi id_user dengan ID orang yang sedang login
        $artikel->id_user = auth()->id();
    }

    public function updating(Artikel $artikel)
    {
        // Update slug jika judul berubah
        if ($artikel->isDirty('judul_artikel')) {
            $artikel->slug = Str::slug($artikel->judul_artikel);
        }
    }
}