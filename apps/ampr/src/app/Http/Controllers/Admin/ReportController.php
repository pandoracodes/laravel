<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    // === 1. HALAMAN LAPORAN & SEARCH ENGINE ===
    public function index(Request $request)
    {
        $search = $request->query('search');

        $bookings = DB::table('bookings')
            ->leftJoin('units', 'bookings.unit_id', '=', 'units.id')
            ->select('bookings.*', 'units.unit_number')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {

                    // 1. Cari berdasarkan kode tiket, status, atau nama/alasan
                    $q->where('bookings.booking_code', 'LIKE', "%{$search}%")
                        ->orWhere('bookings.status', 'LIKE', "%{$search}%")
                        ->orWhere('bookings.player_names', 'LIKE', "%{$search}%")

                      // 2. Cari berdasarkan unit (TAPI KECUALIKAN MAINTENANCE)
                        ->orWhere(function ($subQ) use ($search) {
                            $subQ->where('units.unit_number', 'LIKE', "%{$search}%")
                                ->where('bookings.status', '!=', 'maintenance');
                        });

                    // 3. Fitur Ajaib: Kalau admin ngetik "ADMIN", munculkan semua maintenance
                    if (strtoupper($search) === 'ADMIN') {
                        $q->orWhere('bookings.status', 'maintenance');
                    }

                });
            })
            ->orderBy('bookings.start_time', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/report/Index', [
            'bookings' => $bookings,
            'filters' => ['search' => $search],
        ]);
    }

    // === 2. EXPORT EXCEL ===
    public function export()
    {
        $bookings = DB::table('bookings')
            ->leftJoin('units', 'bookings.unit_id', '=', 'units.id')
            ->select('bookings.*', 'units.unit_number')
            ->orderBy('bookings.start_time', 'desc')
            ->get();

        $filename = 'Laporan_Reservasi_'.date('Y-m-d').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Tanggal', 'Jam', 'Unit', 'Pemain / Keterangan', 'Status', 'Kode Tiket'];

        $callback = function () use ($bookings, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($bookings as $booking) {
                // Menghapus tanda kurung siku dan kutip dari JSON
                $playersOrReason = str_replace(['"', '[', ']'], '', $booking->player_names);

                $date = date('d-m-Y', strtotime($booking->start_time));
                $time = date('H:i', strtotime($booking->start_time)).' - '.date('H:i', strtotime($booking->end_time));

                // Jika maintenance, tulis ADMIN di excel
                $unit = ($booking->status === 'maintenance') ? 'ADMIN' : ($booking->unit_number ?? '-');

                $row = [
                    $date,
                    $time,
                    $unit,
                    $playersOrReason, // Menampilkan data asli (misal: Pengecatan Ulang)
                    strtoupper($booking->status),
                    $booking->booking_code,
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
