<?php

namespace App\Http\Requests;

use App\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePinRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya boleh akses jika sudah login sebagai penghuni
        return session()->has('resident_unit_id');
    }

    public function rules(): array
    {
        return [
            'current_pin' => ['required', 'string'],
            'new_pin' => ['required', 'string', 'min:4', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_pin.required' => 'PIN lama wajib diisi.',
            'new_pin.required' => 'PIN baru wajib diisi.',
            'new_pin.min' => 'PIN minimal 4 karakter.',
            'new_pin.confirmed' => 'Konfirmasi PIN baru tidak cocok.',
        ];
    }

    // Validasi logic PIN lama benar/salah
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $unitId = session('resident_unit_id');
            if ($unitId) {
                $unit = Unit::find($unitId);

                if (! $unit || ! Hash::check($this->current_pin, $unit->access_code)) {
                    $validator->errors()->add('current_pin', 'PIN lama Anda salah.');
                }
            }
        });
    }
}
