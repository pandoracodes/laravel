<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtikelController extends Controller
{
    
   public function index(Request $request)
{
    // Ambil filter, tapi buang yang nilainya null
    $filters = $request->only(['search', 'category']);

    return Inertia::render('Artikel/Index', [
        'artikels' => Artikel::with('kategori')
            ->filter($filters) 
            ->latest()
            ->paginate(9)
            ->withQueryString(),
        'kategoris' => \App\Models\Kategori::all(),
        'filters'   => $filters, // Kirim balik agar input search tidak hilang
    ]);
}

    // Fungsi untuk menampilkan DETAIL ARTIKEL (saat diklik)
    public function show($slug)
    {
        $artikel = Artikel::with('kategori')
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedArtikels = Artikel::with('kategori')
            ->where('id_kategori', $artikel->id_kategori)
            ->where('id_artikel', '!=', $artikel->id_artikel)
            ->latest()
            ->take(3)
            ->get();

        return Inertia::render('Artikel/Show', [
            'artikel' => $artikel,
            'relatedArtikels' => $relatedArtikels
        ]);
    }
}