<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        return Inertia::render('Programs/Index', [
            'programs' => Program::where('status', 'Aktif')
                ->latest()
                ->get()
                ->map(fn($item) => [
                    // SESUAIKAN KEY INI DENGAN YANG ADA DI HOME/COURSECARD
                    'id'    => $item->id_program, 
                    'name'  => $item->nama_program, // di Home kemungkinan pakai 'name'
                    'slug'  => $item->slug,
                    'description' => \Str::limit(strip_tags($item->deskripsi), 120),
                    'image' => $item->gambar ? asset('storage/' . $item->gambar) : '/image/hero-elsam 3.png',
                ])
        ]);
    }

    public function show(Program $program)
{
    if ($program->status !== 'Aktif') {
        abort(404);
    }

    // Ambil data paket yang berelasi dengan program ini
    $program->load('pakets');

    return Inertia::render('Programs/Show', [
        'program' => [
            'name'        => $program->nama_program,
            'description' => $program->deskripsi,
            'image'       => $program->gambar ? asset('storage/' . $program->gambar) : '/image/hero-elsam 3.png',
            'pakets'      => $program->pakets->map(fn($p) => [
                'id'         => $p->id_paket,
                'name'       => $p->nama_paket,
                'description' => $p->deskripsi,
                'meetings'   => $p->total_pertemuan, // Sesuai database kamu
                'price'      => 'Rp ' . number_format($p->harga, 0, ',', '.'),
                'price_raw'  => $p->harga,
            ]),
        ],
        'back_url'     => url('/program-kursus'),
        'register_url' => url('/daftar'),
    ]);
}
}