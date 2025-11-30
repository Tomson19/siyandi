<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deskripsi_varietas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('varietas_id')->constrained('varietas')->onDelete('cascade');

            // Identitas dasar
            $table->string('nomor_sk')->nullable();
            $table->string('tanggal')->nullable();
            $table->string('tipe_varietas')->nullable();
            $table->text('asal_usul')->nullable();

            // Morfologi
            $table->text('tipe_pertumbuhan')->nullable();
            $table->text('bentuk_tajuk')->nullable();

            // Daun
            $table->string('daun_ukuran')->nullable();
            $table->string('daun_warna_muda')->nullable();
            $table->string('daun_warna_tua')->nullable();
            $table->string('daun_bentuk_ujung')->nullable();
            $table->string('daun_tepi')->nullable();
            $table->string('daun_pangkal')->nullable();
            $table->string('daun_permukaan')->nullable();
            $table->string('daun_warna_pucuk')->nullable();

            // Bunga
            $table->string('bunga_warna_mahkota')->nullable();
            $table->string('bunga_jumlah_mahkota')->nullable();
            $table->string('bunga_ukuran')->nullable();

            // Buah
            $table->string('buah_ukuran')->nullable();
            $table->string('buah_panjang')->nullable();
            $table->string('buah_diameter')->nullable();
            $table->string('buah_bobot')->nullable();
            $table->string('buah_bentuk')->nullable();
            $table->string('buah_warna_muda')->nullable();
            $table->string('buah_warna_masak')->nullable();
            $table->string('buah_ukuran_discus')->nullable();

            // Biji
            $table->string('biji_bentuk')->nullable();
            $table->string('biji_nisbah')->nullable();
            $table->string('biji_persen_normal')->nullable();

            // Mutu & Produksi
            $table->text('citarasa')->nullable();
            $table->text('potensi_produksi')->nullable();

            // Ketahanan & Adaptasi
            $table->string('penyakit_karat_daun')->nullable();
            $table->string('penggerek_buah_kopi')->nullable();
            $table->string('daerah_adaptasi')->nullable();

            // Pemuliaan
            $table->text('pemulia')->nullable();
            $table->text('peneliti')->nullable();
            $table->text('pemilik_varietas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deskripsi_varietas');
    }
};
