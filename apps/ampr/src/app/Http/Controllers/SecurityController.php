<?php

namespace App\Http\Controllers;

use App\Http\Requests\Security\ValidateTicketRequest;
use App\Http\Resources\SecurityBookingResource;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class SecurityController extends Controller
{
    public function index()
    {
        return Inertia::render('security/Scan');
    }

    // Gunakan Type Hinting class Request buatanmu
    public function validateTicket(ValidateTicketRequest $request): JsonResponse
    {
        $booking = Booking::with('unit')->where('booking_code', $request->booking_code)->first();
        // 2. Logic Status & Waktu (Sebaiknya dipisah ke private method atau Model biar rapi)
        if ($errorResponse = $this->checkBookingConstraints($booking)) {
            return $errorResponse;
        }

        // 3. Jika Lolos Semua, Return pakai Resource
        return response()->json([
            'status' => 'success',
            'message' => 'Tiket VALID. Silakan Masuk.',
            'data' => new SecurityBookingResource($booking), // <--- CLEAN!
        ]);
    }

    public function checkIn(ValidateTicketRequest $request)
    {
        // Tidak perlu validasi manual lagi, 'exists' sudah menanganinya
        $booking = Booking::where('booking_code', $request->booking_code)->first();

        // Tetap perlu validasi status bisnis (apakah sudah dipake?)
        if ($booking->status !== 'booked' && $booking->status !== 'confirmed') {
            return back()->withErrors(['message' => 'Gagal: Status tiket tidak valid untuk Check-in.']);
        }

        $booking->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
            'checked_in_by' => auth()->id(),
        ]);

        return back()->with('success', 'Check-In Berhasil.');
    }

    /**
     * Logic bisnis dipisah biar controller utama bersih
     */
    private function checkBookingConstraints(Booking $booking): ?JsonResponse
    {
        // A. Cek Status
        if ($booking->status === 'cancelled') {
            return response()->json(['status' => 'error', 'message' => 'Tiket BATAL.'], 400);
        }
        if ($booking->status === 'checked_in') {
            return response()->json([
                'status' => 'warning',
                'message' => 'Tiket SUDAH DIGUNAKAN.',
                'data' => new SecurityBookingResource($booking),
            ], 200); // 200 OK karena kita mau tampilkan datanya
        }
        if ($booking->status === 'no_show') {
            return response()->json(['status' => 'error', 'message' => 'Tiket HANGUS (No Show).'], 400);
        }

        // B. Cek Waktu
        $now = Carbon::now('Asia/Jakarta');
        $startTime = Carbon::parse($booking->start_time)->timezone('Asia/Jakarta');
        $lateLimit = $startTime->copy()->addMinutes(15);

        if (! $now->isSameDay($startTime)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Salah Hari! Jadwal: '.$startTime->isoFormat('dddd, D MMMM'),
            ], 400);
        }

        // C. Cek Sanksi
        if ($now->greaterThan($lateLimit)) {
            $booking->update(['status' => 'no_show']);

            if ($booking->unit) {
                $booking->unit->update(['is_banned_until' => $now->addDays(7)]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'TELAT > 15 MENIT. Sanksi Banned Diterapkan.',
            ], 400);
        }

        return null; // Tidak ada error
    }
}
