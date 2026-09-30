<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class KategoriController extends Controller
{
    protected $table = 'kategoris';
    protected $primaryKey = 'id_kategori'; 

    protected $fillable = [
        'nama_kategori',
        'slug'
    ];
    public function index()
    {
        return Inertia::render('Admin/Kategori/Index', [
            'kategoris' => Kategori::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori),
        ]);

        return back()->with('message', 'Kategori baru berhasil ditambahkan!');
    }

    public function destroy(Kategori $kategori)
    {
        $kategori->delete();
        return back()->with('message', 'Kategori berhasil dihapus!');
    }
}