<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('pesertas', function (Blueprint $table) {
        $table->id('id_peserta');
        $table->string('no_pendaftaran')->unique(); // Akan diisi otomatis via Observer
        $table->string('nama_peserta');
        $table->string('nama_orang_tua')->nullable();
        $table->string('no_telepon');
        $table->string('no_wali')->nullable();
        $table->string('email')->unique();
        $table->date('tanggal_pendaftaran');
        $table->string('no_ktp')->nullable();
        
        // Relasi ke PAKET (Sesuai Diagram)
       $table->foreignId('id_paket')->constrained('pakets', 'id_paket')->onDelete('cascade');
        
        $table->string('status_peserta'); // Misal: Calon Siswa, Siswa Aktif, Lulus
        $table->date('tanggal_daftar_ulang')->nullable();
        $table->text('alamat_peserta');
        $table->string('jenis_kelamin'); // L / P
        $table->enum('input_via', ['user', 'admin'])->default('user');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_pesertas');
    }
};
