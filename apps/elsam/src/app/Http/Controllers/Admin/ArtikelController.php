<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Artikel;
use App\Models\Kategori;
use App\Http\Requests\ArtikelRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage; // <--- TAMBAHKAN INI

class ArtikelController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Artikel/Index', [
            'artikels' => Artikel::with('kategori') // Gunakan eager loading agar kategori muncul
                ->filter($request->only(['search']))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
             'filters' => $request->only(['search']), 
        ]);
    }

    public function create()
    {
       return Inertia::render('Admin/Artikel/Create', [
            'kategoris' => Kategori::all() 
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_kategori'     => 'required|exists:kategoris,id_kategori',
            'judul_artikel'   => 'required',
            'isi_artikel'     => 'required',
            'penulis'         => 'required',
            'tanggal_publish' => 'required|date',
            'gambar'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('artikel', 'public');
        }

        Artikel::create($data); // Gunakan Model langsung karena sudah di-import

        return redirect('/admin/artikel')->with('message', 'Artikel Berhasil Diterbitkan!');
    }

    public function edit(Artikel $artikel)
    {
        return Inertia::render('Admin/Artikel/Edit', [
            'artikel' => $artikel,
            'kategoris' => Kategori::all()
        ]);
    }

    public function update(ArtikelRequest $request, Artikel $artikel)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama dari folder storage jika ada
            if ($artikel->gambar) {
                Storage::disk('public')->delete($artikel->gambar);
            }
            // Simpan gambar baru
            $data['gambar'] = $request->file('gambar')->store('artikel', 'public');
        }

        $artikel->update($data);

        return redirect('/admin/artikel')->with('message', 'Artikel diperbarui!');
    }

    public function destroy(Artikel $artikel)
    {
        // Hapus gambar saat artikel dihapus
        if ($artikel->gambar) {
            Storage::disk('public')->delete($artikel->gambar);
        }
        
        $artikel->delete();
        return back()->with('message', 'Artikel dihapus!');
    }
}