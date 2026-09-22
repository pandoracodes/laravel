<?php

namespace App\Observers;

use App\Models\Unit;
use Illuminate\Support\Facades\Hash;

class UnitObserver
{
    // Dijalankan SEBELUM data disimpan (Creating atau Updating)
    public function saving(Unit $unit): void
    {
        // Jika kolom access_code berubah (atau baru dibuat), otomatis Hash
        if ($unit->isDirty('access_code')) {
            // Cek apakah belum di-hash (mencegah double hash)
            if (! Hash::info($unit->access_code)['algo']) {
                $unit->access_code = Hash::make($unit->access_code);
            }
        }
    }
}
