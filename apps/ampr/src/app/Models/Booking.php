<?php

namespace App\Models;

use App\Observers\BookingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(BookingObserver::class)] // <-- Observer ditempel disini (Laravel 10/11+)
class Booking extends Model
{
    protected $fillable = [
        'booking_code', 'unit_id', 'start_time', 'end_time',
        'status', 'player_names', 'checked_in_at',
        'checked_in_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'checked_in_at' => 'datetime', // Casting ini juga penting
        'player_names' => 'array',
    ];

    // --- RELATIONSHIPS ---
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // --- SCOPES (Best Practice) ---

    // Ambil booking yang aktif (tidak cancel)
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    // Ambil booking pada tanggal tertentu untuk cek bentrok
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('start_time', $date);
    }

    public function scopeUpcomingForUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId)
            ->active() // Menggunakan scopeActive yang sudah ada
            ->where('start_time', '>=', now()) // Hanya yang belum lewat
            ->orderBy('start_time', 'asc');
    }

    /**
     * Scope: Cari booking berdasarkan kode (Case Insensitive)
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('booking_code', $code);
    }
}
