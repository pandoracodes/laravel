<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessNoShow extends Command
{
    /**
     * Nama perintah yang akan diketik di terminal
     */
    protected $signature = 'bookings:check-noshow';

    /**
     * Deskripsi perintah
     */
    protected $description = 'Cek booking yang telat check-in > 15 menit dan berikan sanksi.';

    /**
     * Eksekusi Logic
     */
    public function handle()
    {
        $this->info('Memulai pengecekan No-Show...');

        // 1. Tentukan batas waktu (Sekarang - 15 Menit)
        // Artinya: Jika jadwal main jam 10:00, maka jam 10:16 dianggap telat.
        $limitTime = Carbon::now('Asia/Jakarta')->subMinutes(15);

        // 2. Cari Booking yang:
        // - Statusnya masih 'booked' (Belum check-in)
        // - Jam mainnya sudah lewat dari batas toleransi 15 menit
        $lateBookings = Booking::with('unit')
            ->where('status', 'booked')
            ->where('start_time', '<', $limitTime)
            ->get();

        if ($lateBookings->isEmpty()) {
            $this->info('Tidak ada booking yang telat.');

            return;
        }

        foreach ($lateBookings as $booking) {
            DB::transaction(function () use ($booking) {
                // A. Ubah Status Booking jadi No-Show
                $booking->update(['status' => 'no_show']);

                // B. Berikan Sanksi Banned ke Unit (7 Hari)
                $bannedUntil = Carbon::now('Asia/Jakarta')->addDays(7);

                $booking->unit->update([
                    'is_banned_until' => $bannedUntil,
                ]);

                // Log untuk history
                $message = "Unit {$booking->unit->unit_number} telat check-in (Jadwal: {$booking->start_time}). Sanksi Banned s/d {$bannedUntil}.";
                Log::warning($message);
                $this->error($message); // Tampilkan merah di terminal
            });
        }

        $this->info('Selesai. '.$lateBookings->count().' booking diproses.');
    }
}
