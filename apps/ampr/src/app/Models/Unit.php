<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Daftar kolom yang boleh diisi via Unit::create()
     */
    protected $fillable = [
        'unit_number',
        'owner_name',
        'access_code',
        'is_banned_until',
        'quota_usage',
        'last_quota_reset',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * PENTING: Sembunyikan PIN agar tidak ikut terkirim ke Frontend/Vue
     */
    protected $hidden = [
        'access_code',
    ];

    /**
     * The attributes that should be cast.
     * Agar kolom tanggal otomatis jadi object Carbon (bisa pakai .format(), .diffForHumans())
     */
    protected $casts = [
        'is_banned_until' => 'datetime',
        'last_quota_reset' => 'date',
        'is_active' => 'boolean',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Satu Unit bisa punya banyak Booking history
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ==========================================
    // SCOPES (Best Practice Query)
    // ==========================================

    /**
     * Mencari unit berdasarkan nomor (Case Insensitive user friendly)
     * Cara pakai: Unit::byNumber('TWR-A-0101')->first();
     */
    public function scopeByNumber($query, $number)
    {
        return $query->where('unit_number', $number);
    }

    /**
     * Filter unit yang statusnya Active (Tidak non-aktif oleh admin)
     * Cara pakai: Unit::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
