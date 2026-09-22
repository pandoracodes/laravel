<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();

            $table->string('unit_number', 50)->unique();
            $table->string('access_code');
            $table->string('owner_name')->nullable();

            $table->dateTime('is_banned_until')->nullable();
            $table->integer('quota_usage')->default(0);

            // TAMBAHKAN BARIS INI (KEMARIN MUNGKIN TERLEWAT)
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
