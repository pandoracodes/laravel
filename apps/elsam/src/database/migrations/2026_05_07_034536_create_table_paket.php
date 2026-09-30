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
    Schema::create('pakets', function (Blueprint $table) {
        $table->id('id_paket'); 
        $table->foreignId('id_program')
              ->constrained('programs', 'id_program') 
              ->onDelete('cascade');
        $table->string('nama_paket');
        $table->string('total_pertemuan');
        $table->decimal('harga', 12, 2); // Tambahan harga agar lebih fungsional
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pakets');
    }
};
