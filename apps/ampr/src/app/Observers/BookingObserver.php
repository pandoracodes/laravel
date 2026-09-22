<?php

namespace App\Observers;

use App\Models\Booking;
use Illuminate\Support\Str;

class BookingObserver
{
    public function creating(Booking $booking): void
    {
        // 1. Generate UUID otomatis untuk booking_code
        $booking->booking_code = (string) Str::upper(Str::random(10)); // Cth: TN-X7Y2-A1

        // 2. Default Status
        $booking->status = $booking->status ?? 'booked';
    }
}
