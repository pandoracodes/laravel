<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Peserta;
use App\Models\Program;
use App\Http\Requests\StorePesertaRequest; 
use App\Http\Requests\UpdatePesertaRequest; 
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Exports\PesertaExport;
use Maatwebsite\Excel\Facades\Excel;

class PesertaController extends Controller
{
    public function store(StorePesertaRequest $request)
{
    $data = $request->validated();
    unset($data['id_program']); 
    
    // Set input_via ke admin
    $data['input_via'] = 'admin';
    
    Peserta::create($data);

    return redirect()->route('admin.peserta.index')
        ->with('success', 'Peserta berhasil ditambahkan oleh Admin');
}

// Untuk Index Admin, jika ingin membedakan tampilan
public function index(Request $request)
{
    $peserta = Peserta::query()
        ->with(['paket.program'])
        ->filter($request->only(['search', 'id_program']))
        ->when($request->source, function($q, $source) {
            return $q->where('input_via', $source); // Filter berdasarkan admin/user
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return inertia('Admin/Peserta/Index', [
        'peserta' => $peserta,
        'filters' => $request->all(),
    ]);
}
    public function create()
    {
        return Inertia::render('Admin/Peserta/Create', [
            // Pastikan relasi di model Program adalah 'pakets'
            'programs' => Program::with('pakets')->get(),
            'isAdmin' => true 
        ]);
    }

    public function edit($id_peserta)
{
    
    $peserta = Peserta::with('paket')->findOrFail($id_peserta);

    $peserta->id_program = $peserta->paket ? $peserta->paket->id_program : null;

    return Inertia::render('Admin/Peserta/Edit', [
        'peserta' => $peserta,
        'programs' => Program::with('pakets')->get(),
    ]);
}

   public function update(UpdatePesertaRequest $request, $id) // Ubah parameter kedua menjadi $id
{
    // 1. Cari data berdasarkan id_peserta secara manual
    $peserta = Peserta::findOrFail($id);

    // 2. Ambil data yang sudah divalidasi
    $data = $request->validated();

    // 3. Buang id_program karena tidak ada di tabel pesertas
    unset($data['id_program']); 
    
    // 4. Proses Update
    $peserta->update($data);

    // 5. Kembalikan ke halaman index dengan pesan sukses
    return redirect()->route('admin.peserta.index')
        ->with('success', 'Data ' . $peserta->nama_peserta . ' berhasil diperbarui');
}

public function destroy($id) // Ubah dari (Peserta $peserta) menjadi ($id)
{
    // 1. Cari data berdasarkan id_peserta secara manual
    $peserta = Peserta::findOrFail($id);

    // 2. Hapus data tersebut
    $peserta->delete();

    // 3. Kembali ke halaman sebelumnya dengan pesan sukses
    return redirect()->back()->with('success', 'Data peserta berhasil dihapus');
}
public function show($id_peserta)
{
    
    $peserta = Peserta::with(['paket.program'])->findOrFail($id_peserta);

    return Inertia::render('Admin/Peserta/Show', [
        'peserta' => $peserta
    ]);
}


    public function exportExcel(Request $request)
{
    $filters = $request->only(['search', 'id_program', 'month', 'year']);
    
    // Logika penamaan file yang lebih aman
    $monthName = 'Semua_Bulan';
    if ($request->filled('month')) {
        $monthName = date('F', mktime(0, 0, 0, $request->month, 10));
    }
    
    $yearName = $request->year ?? 'Semua_Tahun';
    $nama_file = "Laporan_Peserta_{$monthName}_{$yearName}.xlsx";

    return Excel::download(new PesertaExport($filters), $nama_file);
}

}