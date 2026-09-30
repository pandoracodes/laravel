<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Paket;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Program Komputer
        $komputer = Program::create([
            'nama_program' => 'Komputer & Perkantoran',
            'deskripsi' => 'Kuasai Microsoft Office, desain grafis dasar, dan administrasi perkantoran untuk dunia kerja.',
            'status' => 'Aktif',
            'gambar' => null, // Bisa diisi path gambar jika sudah ada di storage
        ]);

        $komputer->pakets()->createMany([
    [
        'nama_paket' => 'Reguler',
        'total_pertemuan' => '24 Jam',
        'harga' => 750000
    ],
]);

        // 2. Program Setir Mobil
        $setir = Program::create([
            'nama_program' => 'Setir Mobil Profesional',
            'deskripsi' => 'Belajar mengemudi dengan instruktur sabar dan berpengalaman hingga mahir di jalan raya.',
            'status' => 'Aktif',
        ]);

        $setir->pakets()->createMany([
            ['nama_paket' => 'Manual (Dasar)', 'total_pertemuan' => '10 Jam', 'harga' => 600000],
            ['nama_paket' => 'Matic (Dasar)', 'total_pertemuan' => '10 Jam', 'harga' => 700000],
            ['nama_paket' => 'Sampai Bisa', 'total_pertemuan' => 'Unlimitied', 'harga' => 1500000],
        ]);

        // 3. Program Otomotif
        $otomotif = Program::create([
            'nama_program' => 'Teknik Otomotif (TSM)',
            'deskripsi' => 'Pelatihan teknisi sepeda motor (TSM) mencakup mesin, kelistrikan, dan pemeliharaan rutin.',
            'status' => 'Aktif',
        ]);

        $otomotif->pakets()->createMany([
            ['nama_paket' => 'Teknisi Dasar', 'total_pertemuan' => '40 Jam', 'harga' => 1500000],
        ]);
    }
}