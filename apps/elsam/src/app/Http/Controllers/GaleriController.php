<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GaleriController extends Controller
{
    public function index(Request $request)
{
    // Ambil data untuk slider hero (3-6 gambar terbaru)
    $heroGalleries = Galeri::where('jenis_media', 'gambar')
        ->latest()
        ->take(6)
        ->get()
        ->map(fn($item) => [
            'id_galeri' => $item->id_galeri,
            'judul' => $item->judul,
            'file_url' => $item->file_url, // Memastikan menggunakan accessor yang sama
        ]);

    return Inertia::render('Galeri/Index', [ 
        'filters' => $request->only(['search']),
        'galleries' => Galeri::query()
            ->latestUpload()
            ->filter($request->only(['search']))
            ->paginate(12)
            ->through(fn($item) => [
                'id_galeri' => $item->id_galeri,
                'judul' => $item->judul,
                'file_url' => $item->file_url,
                'jenis_media' => $item->jenis_media,
                'tanggal' => $item->created_at->format('d M Y'),
            ]),
        'heroGalleries' => Galeri::latest()->take(5)->get()->map(fn($item) => [
    'id_galeri' => $item->id_galeri,
    'judul' => $item->judul,
    'file_url' => $item->file_url,
])->values(),
    ]);
}
}