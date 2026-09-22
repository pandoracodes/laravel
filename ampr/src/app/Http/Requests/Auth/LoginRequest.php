<?php

namespace App\Http\Requests\Auth;

use App\Models\Unit;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Kita ubah nama field 'email' jadi 'login_id' biar umum
            // Tapi kalau di Vue kamu masih pake 'email', di sini sesuaikan
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Logic Authentication Kita "Upgrade" Disini
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Ambil inputan (Breeze defaultnya kirim 'email' dan 'password')
        // Kita anggap field 'email' ini bisa diisi Nomor Unit juga
        $loginId = $this->input('email');
        $credential = $this->input('password');

        // 1. CEK KE TABLE USERS (Security/Admin) - Bawaan Breeze
        if (Auth::attempt(['email' => $loginId, 'password' => $credential], $this->boolean('remember'))) {

            // Opsional: Cek status aktif
            if (! Auth::user()->is_active) {
                Auth::logout();
                throw ValidationException::withMessages(['email' => 'Akun dinonaktifkan via User Table.']);
            }

            RateLimiter::clear($this->throttleKey());

            return; // Login User Sukses
        }

        // 2. CEK KE TABLE UNITS (Penghuni) - Tambahan Kita
        $unit = Unit::where('unit_number', $loginId)->first();

        if ($unit && Hash::check($credential, $unit->access_code)) {

            // Validasi Unit
            if (! $unit->is_active) {
                throw ValidationException::withMessages(['email' => 'Unit ini dinonaktifkan.']);
            }
            if ($unit->is_banned_until && now() < $unit->is_banned_until) {
                throw ValidationException::withMessages(['email' => 'Unit sedang di-BANNED.']);
            }

            // Set Session Penghuni
            session(['resident_unit_id' => $unit->id]);
            session(['resident_unit_number' => $unit->unit_number]);

            RateLimiter::clear($this->throttleKey());

            return; // Login Penghuni Sukses
        }

        // 3. GAGAL KEDUANYA
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Email/Unit tidak ditemukan atau Password/PIN salah.',
        ]);
    }

    // ... function ensureIsNotRateLimited & throttleKey biarkan default Breeze ...
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }
        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
