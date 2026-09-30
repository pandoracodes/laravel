<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Peserta;
use Carbon\Carbon;

class PesertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Peserta::create([
            'no_pendaftaran' => 'REG-20260624-001',
            'nama_peserta' => 'Dewi Saputri',
            'nama_orang_tua' => 'Slamet',
            'no_telepon' => '081234567890',
            'no_wali' => '081298765432',
            'email' => 'dewi@gmail.com',
            'tanggal_pendaftaran' => Carbon::now(),
            'no_ktp' => '3305010401040001',

            // pastikan id_paket ini sudah ada di tabel pakets
            'id_paket' => 1,

            'status_peserta' => 'Calon Siswa',
            'tanggal_daftar_ulang' => null,
            'alamat_peserta' => 'Kebumen, Jawa Tengah',
            'jenis_kelamin' => 'P',
        ]);


        Peserta::create([
            'no_pendaftaran' => 'REG-20260624-002',
            'nama_peserta' => 'Budi Santoso',
            'nama_orang_tua' => 'Joko Santoso',
            'no_telepon' => '082345678901',
            'no_wali' => '082345678902',
            'email' => 'budi@gmail.com',
            'tanggal_pendaftaran' => Carbon::now(),
            'no_ktp' => '3305020202020002',

            'id_paket' => 2,

            'status_peserta' => 'Siswa Aktif',
            'tanggal_daftar_ulang' => Carbon::now(),
            'alamat_peserta' => 'Yogyakarta',
            'jenis_kelamin' => 'L',
        ]);
    }
}