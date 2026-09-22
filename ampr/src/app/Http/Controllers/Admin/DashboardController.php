<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Tambahkan ini untuk Transaction
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. SET TIMEZONE JAKARTA (PENTING)
        $nowJakarta = Carbon::now('Asia/Jakarta');
        $startDate = $nowJakarta->copy()->startOfDay();
        $endDate = $nowJakarta->copy()->addDays(29)->endOfDay();

        // 2. Generate Header Tanggal
        $days = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $days[] = [
                'date' => $current->format('Y-m-d'),
                'day_name' => $current->locale('id')->isoFormat('dddd'),
                'formatted' => $current->format('d M'),
                'is_today' => $current->isToday(),
            ];
            $current->addDay();
        }

        // 3. Ambil Booking (Filter yang AKTIF saja)
        // cancelled tidak perlu diambil supaya tidak muncul di jadwal
        $bookings = Booking::with('unit')
            ->whereBetween('start_time', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled') // <--- Pastikan yang dicancel GAK DIAMBIL
            ->get()
            ->map(function ($book) {

                // Logika Status untuk Frontend
                $status = $book->status;

                // Cek No Show (Jika booked tapi jamnya sudah lewat)
                if ($status === 'booked' && Carbon::parse($book->start_time)->lt(now())) {
                    $status = 'no_show'; // Hanya label visual, db tetap booked
                }

                // Parsing Deskripsi Maintenance
                $description = null;
                if ($book->status === 'maintenance') {
                    $raw = $book->player_names;
                    if (is_array($raw)) {
                        $description = $raw[0] ?? 'Maintenance';
                    } else {
                        $decoded = json_decode($raw, true);
                        $description = is_array($decoded) ? ($decoded[0] ?? 'Maintenance') : ($raw ?? 'Maintenance');
                    }
                }

                return [
                    'id' => $book->id,
                    'unit' => $book->unit->unit_number ?? 'Unknown',
                    'date' => Carbon::parse($book->start_time)->format('Y-m-d'),
                    'hour' => (int) Carbon::parse($book->start_time)->format('H'),
                    'status' => $status,
                    'description' => $description,
                ];
            });

        // 4. Stats (HITUNGAN YANG BENAR)
        // Kita pakai $nowJakarta agar sesuai jam WIB
        $stats = [
            'total_units' => Unit::count(),
            'banned_units' => Unit::where('is_banned_until', '>', now())->count(),

            // [FIX] Hitung Booking Hari Ini berdasarkan Tanggal Jakarta
            'today_bookings' => Booking::whereDate('start_time', $nowJakarta->format('Y-m-d'))
                ->whereIn('status', ['booked', 'checked_in'])
                ->count(),
        ];

        $units = Unit::select('id', 'unit_number', 'owner_name')->orderBy('unit_number')->get();

        // [FIX] Hitung Check-in Hari Ini berdasarkan Tanggal Jakarta
        $checkInCount = Booking::whereDate('start_time', $nowJakarta->format('Y-m-d'))
            ->where('status', 'checked_in')
            ->count();

        return Inertia::render('admin/Dashboard', [
            'days' => $days,
            'bookings' => $bookings,
            'stats' => $stats,
            'units' => $units,
            'checkInCount' => $checkInCount,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'date' => 'required|date',
            'hour' => 'required|integer|min:6|max:22',
        ]);

        $startTime = Carbon::parse($request->date)->setHour($request->hour)->setMinute(0)->setSecond(0);
        $endTime = $startTime->copy()->addHour();

        // 1. Validasi Waktu Lampau
        if ($startTime->lt(now())) {
            return back()->withErrors(['modal' => 'Gagal! Tidak bisa booking waktu yang sudah lewat.']);
        }

        // 2. Cek Ketersediaan
        $exists = Booking::where('start_time', $startTime)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            return back()->withErrors(['modal' => 'Slot waktu ini sudah terisi!']);
        }

        // [FIX] Gunakan Transaction agar Booking & Update Kuota atomik
        DB::transaction(function () use ($request, $startTime, $endTime) {

            // Simpan Booking
            Booking::create([
                'unit_id' => $request->unit_id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'booked',
                'booking_code' => 'TN-'.strtoupper(Str::random(8)), // [FIX] Tambah prefix biar rapi
                'player_names' => ['Admin Booking'], // [FIX] Kirim Array langsung (karena Model casts)
            ]);

            // [FIX PENTING] Tambah Kuota Unit
            // Karena kalau nanti di-cancel kuotanya dikembalikan, maka saat buat harus dikurangi.
            Unit::where('id', $request->unit_id)->increment('quota_usage');
        });

        return back()->with('success', 'Booking berhasil ditambahkan.');
    }

    public function cancel(Booking $booking)
    {
        // Debugging: Cek apakah ID masuk
        // dd($booking->id);

        try {
            \DB::transaction(function () use ($booking) {
                // 1. Ubah Status
                // Kita paksa update tanpa validasi ribet dulu untuk tes
                $booking->update(['status' => 'cancelled']);

                // 2. Kembalikan Kuota
                if ($booking->unit_id) {
                    // Pastikan relasi unit terload
                    $unit = \App\Models\Unit::find($booking->unit_id);
                    if ($unit && $unit->quota_usage > 0) {
                        $unit->decrement('quota_usage');
                    }
                }
            });

            // 3. Pesan Sukses
            return back()->with('success', 'Booking berhasil dibatalkan (Slot Kosong Kembali).');

        } catch (\Exception $e) {
            // 4. Pesan Gagal (Biar ketahuan errornya apa)
            return back()->with('error', 'Gagal membatalkan: '.$e->getMessage());
        }
    }
}
