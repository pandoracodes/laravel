<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun SECURITY (Login pakai Email)
        User::create([
            'name' => 'Pak Security',
            'email' => 'security@ampr.com',
            'password' => Hash::make('12345678'),
            'role' => 'security',
            'is_active' => true,
        ]);

        // 2. Buat Akun ADMIN (Login pakai Email)
        User::create([
            'name' => 'Admin Pengelola',
            'email' => 'admin@ampr.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 3. Buat Akun UNIT / PENGHUNI (Login pakai Unit & PIN)
        Unit::create([
            'unit_number' => 'TWR-A-0101', // Ini Username-nya
            'access_code' => Hash::make('123456'), // Ini PIN-nya
            'owner_name' => 'Budi Santoso',
            'is_active' => true,
            'quota_usage' => 0,
        ]);
    }
}
