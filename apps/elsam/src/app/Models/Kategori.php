<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoris'; // Nama tabel kamu
    protected $primaryKey = 'id_kategori'; // Sesuaikan jika PK kamu bukan 'id'

    // DAFTARKAN kolom yang boleh diisi manual di sini:
    protected $fillable = [
        'nama_kategori',
        'slug'
    ];
}