<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePendaftaranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
         'nama_peserta'   => 'required|string|max:255',
            'nama_orang_tua' => 'nullable|string|max:255',
            'email'          => 'required|email|unique:pesertas,email',
            'no_telepon'     => 'required|string|max:20',
            'no_wali'        => 'nullable|string|max:20',
            'no_ktp'         => 'nullable|string|digits:16',
            'id_paket'       => 'required|exists:pakets,id_paket',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'alamat_peserta' => 'required|string',
    ];
}
}
