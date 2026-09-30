<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('artikels', function (Blueprint $table) {
        // 1. Tambah foreign key kategori (Letakkan setelah id_artikel)
        $table->foreignId('id_kategori')->after('id_artikel')->constrained('kategoris', 'id_kategori')->onDelete('cascade');
        
        // 2. Tambah kolom gambar (Letakkan setelah isi_artikel)
        $table->string('gambar')->nullable()->after('isi_artikel');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
