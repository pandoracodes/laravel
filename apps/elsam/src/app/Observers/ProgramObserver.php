<?php

namespace App\Observers;

use App\Models\Program;
use Illuminate\Support\Str;

class ProgramObserver
{
    public function creating(Program $program)
    {
        // Otomatis membuat slug dari nama_program sebelum masuk ke database
        $program->slug = Str::slug($program->nama_program);
    }
    public function updating(Program $program): void
    {
        // Update slug hanya jika nama program berubah
        if ($program->isDirty('nama_program')) {
            $program->slug = Str::slug($program->nama_program);
        }
    }
}