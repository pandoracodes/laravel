<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan Halaman Login
     */
    public function create(): Response
    {
        return Inertia::render('auth/UnifiedLogin', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Proses Login Gabungan (Admin, Security, Resident)
     */
    public function store(Request $request): RedirectResponse
    {
        // Kita ubah validasi agar menerima 'login_id' (bisa email atau unit number)
        // Di frontend nanti name inputnya tetap 'email' biar gampang, atau kita sesuaikan.
        // Asumsi input dari frontend name="email" (bawaan breeze)
        $loginId = $request->input('email');
        $password = $request->input('password');

        // ---------------------------------------------------------
        // 1. CEK LOGIN SEBAGAI ADMIN / SECURITY (Tabel Users)
        // ---------------------------------------------------------
        if (Auth::attempt(['email' => $loginId, 'password' => $password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'security') {
                return redirect()->route('security.scan');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/'); // Default fallback
        }

        // ---------------------------------------------------------
        // 2. CEK LOGIN SEBAGAI PENGHUNI (Tabel Units)
        // ---------------------------------------------------------
        // Cek apakah inputnya berupa Unit Number
        $unit = Unit::where('unit_number', $loginId)->first();

        if ($unit && Hash::check($password, $unit->access_code)) {

            // Cek Status Banned (Opsional, tapi bagus ada)
            // if ($unit->is_banned_until && now() < $unit->is_banned_until) {
            //     throw ValidationException::withMessages([
            //         'email' => 'Unit Anda sedang dibanned.',
            //     ]);
            // }

            // Login Manual pakai Session Custom
            $request->session()->regenerate();
            session([
                'resident_unit_id' => $unit->id,
                'resident_unit_number' => $unit->unit_number,
                'resident_logged_in' => true,
            ]);

            return redirect()->route('booking.home');
        }

        // ---------------------------------------------------------
        // 3. GAGAL SEMUA
        // ---------------------------------------------------------
        throw ValidationException::withMessages([
            'email' => 'Kombinasi akun dan password tidak ditemukan.',
        ]);
    }

    /**
     * Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout Admin/Security
        Auth::guard('web')->logout();

        // Logout Penghuni (Hapus session custom)
        $request->session()->forget(['resident_unit_id', 'resident_unit_number', 'resident_logged_in']);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
