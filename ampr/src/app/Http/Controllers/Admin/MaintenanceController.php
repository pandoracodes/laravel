<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query()
            ->where('status', 'maintenance')
            ->orderBy('start_time', 'desc');

        if ($request->search) {
            $query->where('booking_code', 'like', '%'.$request->search.'%')
                ->orWhere('player_names', 'like', '%'.$request->search.'%');
        }

        $maintenances = $query->paginate(10)->withQueryString();

        return Inertia::render('admin/maintenance/Index', [
            'maintenances' => $maintenances,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_hour' => 'required|integer|min:6|max:22',
            'end_hour' => 'required|integer|gt:start_hour',
            'reason' => 'nullable|string|max:50',
        ]);

        $adminUnit = Unit::first();
        if (! $adminUnit) {
            return back()->withErrors(['slot' => 'Error: Tidak ada data Unit di database.']);
        }

        // --- PERBAIKAN DI SINI (Ganti titik jadi panah) ---
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        // --------------------------------------------------

        $reason = $request->reason ?? 'Maintenance';

        $conflictCount = 0;
        $successCount = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            for ($hour = $request->start_hour; $hour < $request->end_hour; $hour++) {

                $startTime = $date->copy()->setHour($hour)->setMinute(0)->setSecond(0);
                $endTime = $startTime->copy()->addHour();

                $exists = Booking::where('start_time', $startTime)
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if ($exists) {
                    $conflictCount++;

                    continue;
                }

                Booking::create([
                    'unit_id' => $adminUnit->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'maintenance',
                    'player_names' => json_encode([$reason]),
                    'booking_code' => strtoupper(Str::random(10)),
                ]);
                $successCount++;
            }
        }

        if ($successCount === 0 && $conflictCount > 0) {
            return back()->withErrors(['slot' => 'Gagal! Slot sudah terisi semua.']);
        }

        return back()->with('success', "Berhasil blokir $successCount slot.");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:50',
        ]);

        $booking = Booking::findOrFail($id);

        // Update alasan di kolom player_names (format JSON)
        $booking->update([
            'player_names' => json_encode([$request->reason]),
        ]);

        return back()->with('success', 'Alasan maintenance berhasil diperbarui.');
    }

    /**
     * Hapus Jadwal Maintenance
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // Validasi agar tidak menghapus booking user biasa
        if ($booking->status !== 'maintenance') {
            return back()->withErrors(['error' => 'Data ini bukan data maintenance.']);
        }

        $booking->delete();

        return back()->with('success', 'Jadwal maintenance berhasil dihapus.');
    }
}
