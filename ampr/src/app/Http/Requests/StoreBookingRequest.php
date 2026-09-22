<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public boleh akses
    }

    public function rules(): array
    {
        return [

            'player_names' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'hour' => ['required', 'integer', 'min:6', 'max:22'],

            // PENTING: Validasi Checkbox
            'agree_terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'unit_number.exists' => 'Nomor Unit tidak dikenali.',
            'agree_terms.accepted' => 'Anda wajib menyetujui tata tertib.',
        ];
    }
}
