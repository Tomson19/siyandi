<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penangkar', function (Blueprint $table) {
            $table->id();

            // Relasi ke master
            $table->foreignId('tanaman_id')->constrained('tanaman');      // Komoditas
            $table->foreignId('kabupaten_id')->constrained('kabupaten');  // Kabupaten (admin login / lokasi)

            // Data penangkar
            $table->string('nama_penangkar');    // Nama Produsen Benih Perorangan/Perusahaan

            // ➕ NIB dan tanggal (digabung)
            $table->string('nib_dan_tanggal')
                  ->nullable()
                  ->comment('NIB dan Tanggal'); // label untuk referensi

            // ➕ Sertifikat standar / izin usaha prod. benih Nomor dan tanggal (digabung)
            $table->string('sertifikat_izin_usaha_nomor_dan_tanggal')
                  ->nullable()
                  ->comment('Sertifikat standar/izin usaha prod. benih Nomor dan Tanggal');

            $table->integer('jumlah_sertifikasi')->nullable();

            // ➕ Luas areal (HA)
            $table->decimal('luas_areal_ha', 10, 2)
                  ->nullable()
                  ->comment('Luas areal (HA)');

            // Lokasi pembibitan
            $table->string('jalan')->nullable();      // Jalan/Tempat
            $table->string('desa')->nullable();       // Desa/Kelurahan
            $table->string('kecamatan')->nullable();  // Kecamatan

            // Koordinat
            $table->decimal('latitude', 10, 6)->nullable();   // LU/LS
            $table->decimal('longitude', 10, 6)->nullable();  // BT

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penangkar');
    }
};

