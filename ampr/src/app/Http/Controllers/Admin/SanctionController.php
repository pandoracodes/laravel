<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SanctionController extends Controller
{
    /**
     * Tampilkan daftar unit yang sedang kena sanksi
     */
    public function index(Request $request)
    {
        $query = Unit::query()
            ->where('is_banned_until', '>', now()); // Hanya yang masih banned

        // Fitur Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('unit_number', 'like', '%'.$request->search.'%')
                    ->orWhere('owner_name', 'like', '%'.$request->search.'%');
            });
        }

        // Urutkan: Yang sanksinya mau habis duluan ditaruh paling atas
        $bannedUnits = $query->orderBy('is_banned_until', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/sanctions/Index', [
            'bannedUnits' => $bannedUnits,
            'filters' => $request->only(['search']),
            'totalBanned' => Unit::where('is_banned_until', '>', now())->count(),
        ]);
    }

    /**
     * Cabut sanksi (Override Manual)
     */
    public function destroy(Unit $unit)
    {
        // Set banned jadi NULL
        $unit->update(['is_banned_until' => null]);

        return back()->with('success', "Sanksi unit {$unit->unit_number} berhasil dicabut. Unit kembali aktif.");
    }
}
