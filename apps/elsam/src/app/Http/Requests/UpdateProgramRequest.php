<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgramRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
         'nama_program' => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'status'       => 'required|in:Aktif,Non-Aktif',
            'gambar'       => 'nullable|image|max:2048', // Opsional saat update
            'pakets'       => 'required|array|min:1',
            'pakets.*.nama_paket'      => 'required|string',
            'pakets.*.deskripsi'       => 'nullable|string',
            'pakets.*.total_pertemuan' => 'required|string',
            'pakets.*.harga'           => 'required|numeric',
    ];
}
}
