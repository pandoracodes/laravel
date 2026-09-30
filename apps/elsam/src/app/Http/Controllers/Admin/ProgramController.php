<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;

use Illuminate\Http\Request;
use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia; 


class ProgramController extends Controller
{
    public function index(Request $request)
{
    $programs = Program::query()
        ->with('pakets') 
        ->when($request->search, function ($query, $search) {
            $query->where('nama_program', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10) 
        ->withQueryString();

    return Inertia::render('Admin/Program/Index', [
        'programs' => $programs,
        'filters'  => $request->only(['search'])
    ]);
}

    public function create()
    {
        return Inertia::render('Admin/Program/Create');
    }

   public function store(StoreProgramRequest $request)
{
    try {
        DB::beginTransaction();

        $path = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('programs', 'public');
        }


        $program = Program::create([
            'nama_program' => $request->nama_program,
            'deskripsi'    => $request->deskripsi,
            'status'       => $request->status,
            'gambar'       => $path,
            // Slug otomatis diurus oleh Observer
        ]);

        foreach ($request->pakets as $paketData) {
            $program->pakets()->create([
                'nama_paket'      => $paketData['nama_paket'],
                'deskripsi'        => $paketData['deskripsi'],
                'total_pertemuan' => $paketData['total_pertemuan'],
                'harga'           => $paketData['harga'],
            ]);
        }

        DB::commit();
        return redirect('/admin/program')->with('success', 'Program dan Paket berhasil disimpan');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}

public function edit($id)
{
    // Load program beserta relasi pakets
    $program = Program::with('pakets')->findOrFail($id);

    return Inertia::render('Admin/Program/Edit', [
        'program' => $program
    ]);
}

public function update(UpdateProgramRequest $request, $id)
{
    $program = Program::findOrFail($id);

    try {
        DB::beginTransaction();

        $data = $request->validated();

        // Handle Upload Gambar
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($program->gambar) {
                Storage::disk('public')->delete($program->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('programs', 'public');
        } else {
            // Jika tidak upload baru, tetap gunakan yang lama
            unset($data['gambar']);
        }

        // Update Data Program (Slug otomatis dihandle Observer)
        $program->update($data);

        // Update Paket (Hapus yang lama, simpan yang baru)
        $program->pakets()->delete();
        foreach ($request->pakets as $paketData) {
            $program->pakets()->create([
                'nama_paket'      => $paketData['nama_paket'],
                'deskripsi'        => $paketData['deskripsi'],
                'total_pertemuan' => $paketData['total_pertemuan'],
                'harga'           => $paketData['harga'],
            ]);
        }

        DB::commit();
        return redirect('/admin/program')->with('success', 'Program berhasil diperbarui');
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
    }
}
    public function destroy($id)
{
   
    $program = Program::findOrFail($id);

    if ($program->gambar) {
        \Storage::disk('public')->delete($program->gambar);
    }

    $program->pakets()->delete();

    $program->delete();

    
    return redirect('/admin/program')->with('success', 'Program dan paket berhasil dihapus!');
}
public function show(Peserta $peserta)
{
   
    return Inertia::render('Admin/Peserta/Show', [
        'peserta' => $peserta->load(['paket.program'])
    ]);
}
}