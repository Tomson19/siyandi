<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kbs_pemilik', function (Blueprint $table) {
            $table->id();

            // Relasi ke header KBS (kebun_benih_sumber)
            $table->foreignId('kbs_id')
                  ->constrained('kebun_benih_sumber')
                  ->onDelete('cascade');

            // Lokasi & umum (boleh beda-beda per baris)
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->string('tahun_tanam')->nullable();    // contoh: 1990-2000
            $table->integer('jumlah_pit')->nullable();    // contoh: 217

            // Pemilik
            $table->integer('no_pemilik')->nullable();    // No. (1,2,3,...)
            $table->string('nama_pemilik')->nullable();   // Atan, Masbukhin, dll
            $table->decimal('luas_ha', 8, 2)->nullable(); // 0.5, 1, 2, ...
            $table->integer('jumlah_pohon_induk')->nullable(); // 21, 1, 5, ...

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kbs_pemilik');
    }
};
