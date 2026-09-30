<?php
namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Peserta;
use App\Http\Requests\StorePesertaRequest;
use App\Http\Requests\StorePendaftaranRequest;
use Inertia\Inertia;

class PendaftaranController extends Controller
{

    public function create()
{
    return Inertia::render('Pendaftaran/Create', [
        'programs' => Program::where('status', 'Aktif')
            ->with('pakets') // Agar bisa pilih paket berdasarkan program
            ->get(),
        'submit_url' => url('/daftar/simpan')
    ]);
}

public function store(StorePendaftaranRequest $request)
{
    $data = $request->validated();
    unset($data['id_program']);

    $peserta = Peserta::create(array_merge($data, [
        'input_via' => 'user'
    ]));

    $peserta->load('paket.program');

    $noAdmin = '6281249899550';
    $pesan = "Halo Admin LKP Elsam,\n\n" .
             "Saya ingin konfirmasi pendaftaran kursus:\n" .
             "Nama: {$peserta->nama_peserta}\n" .
             "Program: " . ($peserta->paket->program->nama_program ?? '-') . "\n" .
             "No. Pendaftaran: {$peserta->no_pendaftaran}\n\n" .
             "Mohon informasi langkah selanjutnya. Terima kasih.";

    $urlWA = "https://wa.me/{$noAdmin}?text=" . urlencode($pesan);

    // ✅ BENAR - redirect langsung ke WA setelah simpan
    return Inertia::location($urlWA);
}
}
