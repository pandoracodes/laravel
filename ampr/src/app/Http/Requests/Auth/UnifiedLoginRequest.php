<?php

namespace App\Http\Requests\Auth;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UnifiedLoginRequest extends FormRequest
{
    /**
     * Tentukan siapa yang boleh akses (True = semua orang/guest)
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan Validasi Form
     */
    public function rules(): array
    {
        return [
            'login_id' => ['required', 'string'], // Bisa Email atau No. Unit
            'credential' => ['required', 'string'], // Bisa Password atau PIN
        ];
    }

    /**
     * FUNGSI UTAMA: Mencoba Login
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginId = $this->input('login_id');
        $credential = $this->input('credential'); // Password / PIN

        // --- SKENARIO 1: LOGIN SEBAGAI STAFF (Admin/Security) ---
        // Cek tabel 'users' menggunakan Auth::attempt bawaan Laravel
        if (Auth::attempt(['email' => $loginId, 'password' => $credential], $this->boolean('remember'))) {

            // Cek status aktif user (Opsional, sesuai migrasi kamu)
            if (! Auth::user()->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'login_id' => 'Akun Anda telah dinonaktifkan.',
                ]);
            }

            RateLimiter::clear($this->throttleKey());

            return; // Sukses, keluar dari fungsi
        }

        // --- SKENARIO 2: LOGIN SEBAGAI PENGHUNI (Unit) ---
        // Cek tabel 'units' manual
        $unit = Unit::where('unit_number', $loginId)->first();

        if ($unit && Hash::check($credential, $unit->access_code)) {

            // Validasi Status Unit
            if (! $unit->is_active) {
                throw ValidationException::withMessages([
                    'login_id' => 'Unit ini dinonaktifkan.',
                ]);
            }

            if ($unit->is_banned_until && now() < $unit->is_banned_until) {
                $date = $unit->is_banned_until->format('d M Y');
                throw ValidationException::withMessages([
                    'login_id' => "Unit sedang disanksi (Banned) hingga $date.",
                ]);
            }

            // Set Session Penghuni
            session(['resident_unit_id' => $unit->id]);
            session(['resident_unit_number' => $unit->unit_number]);

            RateLimiter::clear($this->throttleKey());

            return; // Sukses
        }

        // --- JIKA GAGAL KEDUANYA ---
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'login_id' => 'Email/Unit tidak ditemukan atau Password/PIN salah.',
        ]);
    }

    /**
     * Proteksi Brute Force (Maksimal 5x salah dalam 1 menit)
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login_id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Kunci Rate Limiter berdasarkan IP dan Login ID
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login_id')).'|'.$this->ip());
    }
}
