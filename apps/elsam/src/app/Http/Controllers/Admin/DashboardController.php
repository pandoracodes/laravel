<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;    
use Illuminate\Http\Request; 
use App\Models\Program;
use App\Models\Peserta;
use App\Models\Artikel;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $recentPeserta = Peserta::query()
        ->with(['paket.program'])
        ->when($request->search, function($query, $search) {
            $query->where('nama_peserta', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%");
        })
        ->latest()
        ->take(10)
        ->get()
        ->map(function ($p) {
            return [
                'id_peserta'     => $p->id_peserta, // WAJIB ADA
                'nama_peserta'   => $p->nama_peserta,
                'no_pendaftaran' => $p->no_pendaftaran,
                'status_peserta' => $p->status_peserta,
                'program_info'   => ($p->paket->program->nama_program ?? 'N/A') . ' - ' . ($p->paket->nama_paket ?? 'N/A'),
            ];
        });

    return Inertia::render('Admin/Dashboard', [
        'totalProgram'  => Program::count(),
        'totalPeserta'  => Peserta::count(),
        'totalArtikel'  => Artikel::count(),
        'recentPeserta' => $recentPeserta,
        'filters'       => $request->only(['search']),
    ]);
}
}