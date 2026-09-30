<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePesertaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array 
    {

         $id = $this->segment(3); 

        return [
            'nama_peserta'   => 'required|string|max:255',
            'nama_orang_tua' => 'nullable|string|max:255',
            
            'email' => [
                'required', 
                'email', 
                Rule::unique('pesertas', 'email')->ignore($id, 'id_peserta')
            ],

            'no_ktp' => [
                'required', 
                'digits:16', 
                Rule::unique('pesertas', 'no_ktp')->ignore($id, 'id_peserta')
            ],

            'no_telepon'     => 'required|string|max:20',
            'no_wali'        => 'nullable|string|max:20',
            'id_paket'       => 'required|exists:pakets,id_paket',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'alamat_peserta' => 'required|string',
            'status_peserta' => 'required|string',
            'id_program'     => 'required',
            'tanggal_daftar_ulang' => 'nullable|date', 
        ];
    }
}