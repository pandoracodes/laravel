<?php

namespace App\Imports;

use App\Models\Unit;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. Skip jika unit sudah ada (agar tidak error duplikat)
        if (Unit::where('unit_number', $row['unit_number'])->exists()) {
            return null;
        }

        // 2. Ambil PIN dengan aman (Mencegah masalah typo di header Excel)
        // Kita cek: Apakah ada 'access_code'? Kalau ga ada cek 'acces_code', kalau ga ada cek 'pin'.
        $excelPin = $row['access_code'] ?? $row['acces_code'] ?? $row['pin'] ?? null;

        // 3. Konversi ke String & Set Default
        // Jika kosong/null, pakai '123456'
        $rawPin = $excelPin ? (string) $excelPin : '123456';

        return new Unit([
            'unit_number' => strtoupper($row['unit_number']),
            'owner_name' => $row['owner_name'] ?? null,

            // HASH PIN (PENTING)
            'access_code' => Hash::make($rawPin),

            'quota_usage' => 0,
            'is_active' => true,
        ]);
    }
}
