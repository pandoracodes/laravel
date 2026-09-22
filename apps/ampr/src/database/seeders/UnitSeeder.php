<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [];
        $towers = ['A', 'B']; // Tower A dan Tower B

        // Kita buat total 50 unit
        // Tower A: Lantai 1-5, per lantai 5 kamar
        // Tower B: Lantai 1-5, per lantai 5 kamar

        foreach ($towers as $tower) {
            for ($floor = 1; $floor <= 5; $floor++) {
                for ($room = 1; $room <= 5; $room++) {

                    // Format Unit: TWR-A-0101 (Tower-Lantai-Nomor)
                    $unitNumber = sprintf('TWR-%s-%02d%02d', $tower, $floor, $room);

                    // PIN Default untuk semua unit: 123456 (Biar gampang ngetesnya)
                    // Nanti di production harus random
                    $pin = '123456';

                    $units[] = [
                        'unit_number' => $unitNumber,
                        'access_code' => Hash::make($pin), // PIN di-hash biar aman
                        'owner_name' => 'Pemilik '.$unitNumber,
                        'quota_usage' => 0,
                        'is_active' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }

        // Masukkan ke database
        DB::table('units')->insert($units);

        // Tambahkan 1 unit yang sedang di-BANNED untuk tes validasi sanksi
        DB::table('units')->insert([
            'unit_number' => 'TWR-X-9999',
            'access_code' => Hash::make('123456'),
            'owner_name' => 'Bad User',
            'is_banned_until' => Carbon::now()->addDays(3), // Banned sampai 3 hari ke depan
            'quota_usage' => 0,
            'is_active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
