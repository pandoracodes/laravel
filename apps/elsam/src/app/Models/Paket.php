<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paket extends Model
{
    protected $table = 'pakets';
    protected $primaryKey = 'id_paket';


    protected $fillable = [
        'program_id', 
        'nama_paket',
        'total_pertemuan',
        'harga',
        'deskripsi'
    ];

    protected $casts = [
        'harga' => 'integer',
    ];

    /**
     * RELASI: Paket ini milik Program apa
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'id_program', 'id_program');
    }

    
    public function pesertas(): HasMany
    {
        return $this->hasMany(Peserta::class, 'id_paket', 'id_paket');
    }
}