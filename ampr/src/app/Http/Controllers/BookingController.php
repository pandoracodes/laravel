<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\TicketResource;
use App\Models\Booking;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * PAGE: Halaman Utama (Jadwal)
     */
    public function index()
    {
        $today = Carbon::now('Asia/Jakarta');

        // 1. Dynamic Date (1 Bulan)
        $endDate = $today->copy()->addMonth();
        $daysDifference = $today->diffInDays($endDate);

        $dates = collect(range(0, $daysDifference))->map(function ($days) use ($today) {
            $date = $today->copy()->addDays($days);

            return [
                'full_date' => $date->format('Y-m-d'),
                'day_name' => $date->locale('en')->isoFormat('ddd'), // MON, TUE, WED
                'date_num' => $date->format('d'),
                'month_name' => $date->locale('en')->isoFormat('MMM'), // Jan, Feb
                'is_today' => $date->isToday(),
            ];

        });

        // 2. [REVISI] Ambil Booking + Relasi Unit
        // Kita butuh 'unit:id,unit_number' agar frontend bisa menampilkan "Booked By TWR-A..."
        $rawBookings = Booking::active()
            ->with('unit:id,unit_number') // <--- PENTING: Eager load unit
            ->whereDate('start_time', '>=', $today->format('Y-m-d'))
            ->whereDate('start_time', '<=', $endDate->format('Y-m-d'))
            ->get(['id', 'unit_id', 'start_time', 'status', 'player_names']);

        $bookedSlots = $rawBookings->map(function ($b) {
            // Logic Info Maintenance
            $info = '-';
            if ($b->status === 'maintenance') {
                $info = is_array($b->player_names) ? ($b->player_names[0] ?? 'Perbaikan') : 'Maintenance';
            }

            return [
                'date' => $b->start_time->format('Y-m-d'),
                'hour' => (int) $b->start_time->format('H'),
                'status' => $b->status,
                'info' => $info,
                // [REVISI] Kirim Nomor Unit ke Frontend
                'unit_number' => $b->unit ? $b->unit->unit_number : 'Occupied',
            ];
        });

        // 3. Logic Tanggal Penuh
        $bookingsPerDate = $bookedSlots->groupBy('date')->map->count();
        $fullyBookedDates = $bookingsPerDate->filter(fn ($count) => $count >= 16)->keys()->toArray();

        // 4. [REVISI] Cek Status Banned Unit yang sedang Login
        $unitId = session('resident_unit_id');
        $userUnitNumber = session('resident_unit_number');

        $currentUnit = Unit::find($unitId);

        // Default values
        $isBanned = false;
        $banMessage = '';

        if ($currentUnit && $currentUnit->is_banned_until) {
            // Cek apakah waktu saat ini SUDAH MELEWATI waktu banned
            if (now() >= $currentUnit->is_banned_until) {
                // AUTO-UNBAN: Hapus sanksinya di database
                $currentUnit->update(['is_banned_until' => null]);
            } else {
                // Jika belum lewat, tampilkan banner merah
                $isBanned = true;
                $dateEnd = Carbon::parse($currentUnit->is_banned_until)->format('d M Y');
                $banMessage = "Unit Anda sedang disanksi hingga {$dateEnd} dikarenakan pelanggaran aturan lapangan.";
            }
        }

        return Inertia::render('booking/Home', [
            'dates' => $dates,
            'bookedSlots' => $bookedSlots,
            'fullyBookedDates' => $fullyBookedDates,
            'userUnit' => $userUnitNumber,

            // [REVISI] Kirim Props Banned ke Frontend
            'isUserBanned' => $isBanned,
            'banMessage' => $banMessage,
        ]);
    }

    /**
     * ACTION: Proses Booking (Logic Validasi Lengkap)
     */
    public function store(StoreBookingRequest $request)
    {
        // 1. PERSIAPAN WAKTU (Dari Input Frontend: date & hour)
        // Kita harus ubah input "2023-10-25" dan jam "8" menjadi format Tanggal yang dimengerti database
        $startTime = Carbon::createFromFormat('Y-m-d H', "$request->date $request->hour", 'Asia/Jakarta');
        $endTime = $startTime->copy()->addHour();

        // 2. ATOMIC LOCK (Mencegah Double Booking)
        // Supaya tidak ada 2 orang yang booking di detik yang sama persis
        $lockKey = "booking_lock_{$startTime->timestamp}";
        $lock = Cache::lock($lockKey, 5);

        if (! $lock->get()) {
            throw ValidationException::withMessages(['slot' => 'Slot sedang diproses oleh penghuni lain.']);
        }

        try {
            return DB::transaction(function () use ($request, $startTime, $endTime) {

                // 3. CARI UNIT DARI SESSION (Siapa yang login?)
                // Kita tidak mengambil dari input, tapi dari session login biar aman
                $unitId = session('resident_unit_id');
                $unit = Unit::find($unitId);

                // Cek apakah session valid
                if (! $unit) {
                    throw ValidationException::withMessages(['player_names' => 'Sesi Anda habis. Silakan login ulang.']);
                }

                // 4. VALIDASI: APAKAH UNIT KENA BANNED?
                if ($unit->is_banned_until && now() < $unit->is_banned_until) {
                    throw ValidationException::withMessages([
                        'player_names' => 'Unit Anda sedang disanksi (Banned).',
                    ]);
                }

                // 5. VALIDASI: KUOTA MINGGUAN (Max 2 Jam)
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                $weeklyUsage = Booking::active()
                    ->where('unit_id', $unit->id)
                    ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
                    ->count();

                if ($weeklyUsage >= 2) {
                    throw ValidationException::withMessages([
                        'player_names' => 'Kuota mingguan habis (Maks 2 jam/minggu).',
                    ]);
                }

                // 6. VALIDASI: CEK APAKAH SLOT MASIH KOSONG?
                // Penting! Cek database sekali lagi sebelum simpan
                $isBooked = Booking::active()
                    ->where('start_time', $startTime->format('Y-m-d H:i:s'))
                    ->exists();

                if ($isBooked) {
                    throw ValidationException::withMessages(['slot' => 'Maaf, slot ini baru saja diambil orang lain.']);
                }

                // 7. SIMPAN BOOKING (INI BAGIAN YG KAMU TANYAKAN)
                // Disini kita TIDAK LAGI mengecek Access Code / PIN
                $booking = Booking::create([
                    'unit_id' => $unit->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'player_names' => [$request->player_names], // Array nama pemain
                    'status' => 'booked',
                    'booking_code' => 'TN-'.strtoupper(uniqid()), // Generate Kode Tiket
                ]);

                // 8. UPDATE STATISTIK UNIT
                $unit->increment('quota_usage');

                // 9. REDIRECT KE TIKET
                return to_route('booking.ticket', $booking->booking_code);
            });

        } finally {
            if (isset($lock)) {
                $lock->release();
            }
        }
    }

    public function showTicket(Booking $booking)
    {
        $booking->load('unit');

        // Pastikan tiket milik unit yang sedang login (Security Check)
        if (session('resident_unit_id') && $booking->unit_id != session('resident_unit_id')) {
            abort(403, 'Anda tidak berhak melihat tiket ini.');
        }

        $qrCode = (new QRCode)->render($booking->booking_code);

        return Inertia::render('booking/Success', [
            'booking' => $booking,
            'qrCode' => $qrCode,
        ]);
    }

    public function downloadPdf(Booking $booking)
    {
        // Security check sama seperti showTicket
        if (session('resident_unit_id') && $booking->unit_id != session('resident_unit_id')) {
            abort(403);
        }

        $booking->load('unit');

        $options = new \chillerlan\QRCode\QROptions([
            'version' => 5,
            'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
            'scale' => 5,
            'imageBase64' => true,
        ]);

        $qrCode = (new QRCode($options))->render($booking->booking_code);

        $pdf = Pdf::loadView('pdf.ticket', [
            'booking' => $booking,
            'qrCode' => $qrCode,
        ]);

        $pdf->setPaper('A5', 'portrait');
        $pdf->setWarnings(false);

        return $pdf->download('e-ticket-'.$booking->booking_code.'.pdf');
    }

    public function myTickets()
    {
        $unitId = session('resident_unit_id');
        $userUnit = session('resident_unit_number');

        // 1. Tiket AKAN DATANG
        $activeData = Booking::where('unit_id', $unitId)
            ->where('status', '!=', 'maintenance') // <--- FILTER MAINTENANCE
            ->where('start_time', '>=', now())
            ->whereIn('status', ['booked'])
            ->orderBy('start_time', 'asc')
            ->get();

        // 2. Tiket RIWAYAT
        $historyData = Booking::where('unit_id', $unitId)
            ->where('status', '!=', 'maintenance') // <--- FILTER MAINTENANCE
            ->where(function ($q) {
                $q->where('start_time', '<', now())
                    ->orWhereIn('status', ['checked_in', 'cancelled', 'no_show']);
            })
            ->orderBy('start_time', 'desc')
            ->limit(20)
            ->get();

        // RETURN MENGGUNAKAN RESOURCE
        // TicketResource::collection(...) otomatis mengolah data sesuai format di file Resource tadi
        return Inertia::render('booking/MyTickets', [
            'activeTickets' => TicketResource::collection($activeData),
            'historyTickets' => TicketResource::collection($historyData),
            'userUnit' => $userUnit,
        ]);
    }
}
