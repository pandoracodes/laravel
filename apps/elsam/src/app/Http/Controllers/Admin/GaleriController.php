<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GaleriRequest;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Galeri/Index', [
            'filters' => $request->only(['search', 'jenis']),
            'galeries' => Galeri::query()
                ->filter($request->only(['search', 'jenis']))
                ->latest()
                ->paginate(10)
                ->withQueryString()
        ]);
    }
 public function create()
    {
        return Inertia::render('Admin/Galeri/Create');
    }
    public function store(GaleriRequest $request)
    {
        $data = $request->validated();
        
        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('galeri', 'public');
        }
        
        $data['id_user'] = auth()->id();
        
        Galeri::create($data);

        return redirect()->route('admin.galeri.index')->with('success', 'Data berhasil disimpan');
    }

    public function edit(Galeri $galeri)
    {
        return Inertia::render('Admin/Galeri/Edit', [
            'galeri' => $galeri
        ]);
    }

    public function update(GaleriRequest $request, Galeri $galeri)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            // Hapus file lama
            if ($galeri->file_path) {
                Storage::disk('public')->delete($galeri->file_path);
            }
            // Simpan file baru
            $data['file_path'] = $request->file('file')->store('galeri', 'public');
        }

        $galeri->update($data);

        return redirect()->route('admin.galeri.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(Galeri $galeri)
    {
        // File fisik akan dihapus oleh Observer
        $galeri->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
