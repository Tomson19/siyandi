<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kebun_benih_sumber', function (Blueprint $table) {
            $table->id();

            // Relasi ke master
            $table->foreignId('tanaman_id')->constrained('tanaman');      // Komoditas
            $table->foreignId('kabupaten_id')->constrained('kabupaten');  // Kabupaten

            // Data utama
            $table->string('nama_varietas');          // Varietas
            $table->string('nomor_sk')->nullable();   // No SK
            $table->string('tanggal_sk')->nullable(); // Tanggal SK (boleh string dulu, simple)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebun_benih_sumber');
    }
};
