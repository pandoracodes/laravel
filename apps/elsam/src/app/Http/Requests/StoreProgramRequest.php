<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama_program' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'status' => 'required|string',
            'gambar' => $this->isMethod('POST') ? 'required|image|max:2048' : 'nullable|image|max:2048',
            'pakets' => 'required|array|min:1', 
            'pakets.*.nama_paket' => 'required|string',
            'pakets.*.deskripsi' => 'nullable|string',
            'pakets.*.total_pertemuan' => 'required',
            'pakets.*.harga' => 'required|numeric',
        ];
    }
}