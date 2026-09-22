<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SecurityBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'booking_code' => $this->booking_code,

            // SALAH (Ini penyebab error, mencari user di dalam unit)
            // 'resident' => $this->unit->user->name,

            // BENAR (Ambil langsung dari kolom owner_name di tabel unit)
            'resident' => $this->unit->owner_name ?? 'Penghuni',

            'unit' => $this->unit->unit_number,
            'time' => Carbon::parse($this->start_time)->format('H:i'),
            'status' => $this->status,
        ];
    }
}
