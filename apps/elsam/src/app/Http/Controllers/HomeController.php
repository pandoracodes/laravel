<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Artikel;
use App\Models\Galeri;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class HomeController extends Controller
{
    public function index()
{
    return Inertia::render('Home', [
        'canRegister' => Features::enabled(Features::registration()),

        'courses' => Program::aktif()
            ->with('pakets')
            ->latest()
            ->take(3)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id_program, 
                'name' => $p->nama_program,
                'slug' => $p->slug,
                'price' => number_format($p->pakets->min('harga') ?? 0, 0, ',', '.'),
                'image' => $p->gambar ? asset('storage/' . $p->gambar) : '/images/default.jpg',
                'short_description' => $p->deskripsi,
                'features' => $p->pakets->pluck('nama_paket')->take(4),
                'duration' => $p->pakets->first()->total_pertemuan ?? '-',
            ]),

        // PERBAIKAN 1: Ubah 'artikels' menjadi 'articles' (pakai 'c')
        'articles' => Artikel::with('kategori')
            ->latest()
            ->take(3)
            ->get(),


        'galleries' => \App\Models\Galeri::latest()
            ->take(6) 
            ->get()
            ->map(fn($item) => [
                'id_galeri' => $item->id_galeri,
                'judul' => $item->judul,
                'jenis_media' => $item->jenis_media,
                // Pastikan ini menggunakan asset('storage/...')
                'file_url' => asset('storage/' . $item->file_path), 
            ]),
    ]);
}
}