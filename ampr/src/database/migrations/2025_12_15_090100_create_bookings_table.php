<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Booking Code (UUID) untuk keperluan scan QR nanti
            $table->string('booking_code')->unique();

            // Relasi ke Unit (Jika Unit dihapus, history booking tetap ada atau ikut hapus?
            // Best practice booking: Cascade atau Set Null. Kita pakai Cascade biar bersih dulu).
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');

            $table->dateTime('start_time');
            $table->dateTime('end_time');

            // Status: booked, checked_in, no_show, cancelled
            $table->string('status', 20)->default('booked');

            // Simpan nama pemain dalam JSON (Array)
            $table->json('player_names')->nullable();

            // Data Check-in (diisi Satpam nanti)
            $table->dateTime('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users');

            $table->timestamps();

            // PERFORMANCE INDEX (Sangat Penting!)
            // Index ini mempercepat query saat sistem mengecek "Apakah jam ini kosong?"
            // Tanpa ini, aplikasi bakal lemot saat data booking sudah ribuan.
            $table->index(['start_time', 'end_time', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
