<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;

class ValidateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan role user benar (ini sudah bagus)
        return in_array($this->user()->role, ['security', 'admin']);
    }

    protected function prepareForValidation()
    {
        // BEST PRACTICE: Rapikan data sebelum divalidasi
        // Jadi di controller tidak perlu strtoupper() lagi
        $this->merge([
            'booking_code' => strtoupper(trim($this->booking_code)),
        ]);
    }

    public function rules(): array
    {
        return [
            'booking_code' => ['required', 'string', 'exists:bookings,booking_code'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_code.exists' => 'Tiket tidak ditemukan dalam sistem (Kode Salah).',
        ];
    }
}
