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
    Schema::create('artikels', function (Blueprint $table) {
        $table->id('id_artikel'); 
        $table->string('judul_artikel');
        $table->string('slug')->unique();
        $table->text('isi_artikel');
        $table->string('penulis');
        $table->date('tanggal_publish');
        $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_artikels');
    }
};
