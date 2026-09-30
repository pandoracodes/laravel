<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGaleriRequest extends FormRequest
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
     public function edit(Galeri $galeri)
    {
        return Inertia::render('Admin/Galeri/Edit', [
            'galeri' => $galeri
        ]);
    }

    public function update(GaleriRequest $request, Galeri $galeri)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            // Hapus file lama
            if ($galeri->file_path) {
                Storage::disk('public')->delete($galeri->file_path);
            }
            // Simpan file baru
            $data['file_path'] = $request->file('file')->store('galeri', 'public');
        }

        $galeri->update($data);

        return redirect()->route('admin.galeri.index')->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(Galeri $galeri)
    {
        // File fisik akan dihapus oleh Observer
        $galeri->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}

