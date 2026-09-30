<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            // Kita tambahkan kolom deskripsi setelah kolom nama_paket
            // Gunakan tipe 'text' agar bisa menampung banyak karakter
            $table->text('deskripsi')->nullable()->after('nama_paket');
        });
    }

    public function down(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            $table->dropColumn('deskripsi');
        });
    }
};