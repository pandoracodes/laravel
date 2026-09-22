<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            // Key kita jadikan Primary agar cepat bacanya
            // Contoh Key: 'maintenance_mode', 'max_quota'
            $table->string('key')->primary();

            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
