<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GaleriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'jenis_media' => 'required|in:FOTO,VIDEO', // sesuaikan dengan opsi anda
            'tanggal_upload' => 'required|date',
            'file' => $this->isMethod('POST') 
                ? 'required|file|mimes:jpg,jpeg,png,mp4|max:20480' // Max 20MB
                : 'nullable|file|mimes:jpg,jpeg,png,mp4|max:20480',
        ];
    }
}