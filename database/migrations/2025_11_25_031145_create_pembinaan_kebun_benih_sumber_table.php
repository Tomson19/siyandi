<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembinaan_kebun_benih_sumber', function (Blueprint $table) {
            $table->id();

            // pemohon (user) yang mengajukan pembinaan
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // relasi ke sesi pembinaan (boleh null kalau belum dijadwalkan)
            $table->foreignId('pembinaan_sesi_id')
                ->nullable()
                ->constrained('pembinaan_sesi')
                ->nullOnDelete();

            // DATA KEBUN BENIH SUMBER
            $table->string('nama');                            // nama pemohon / penanggung jawab
            $table->string('nik', 25)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 25)->nullable();           // no HP / WA

            // 👉 relasi ke jenis_tanaman (komoditas)
            $table->foreignId('jenis_tanaman_id')
                ->constrained('jenis_tanaman') // nama tabel dari migration jenis_tanaman
                ->cascadeOnDelete();

            $table->string('lokasi_kebun')->nullable();        // alamat / desa / kecamatan

            // ✅ koordinat pakai latitude & longitude terpisah
            $table->decimal('latitude_kebun', 10, 7)->nullable();
            $table->decimal('longitude_kebun', 10, 7)->nullable();

            $table->unsignedInteger('jumlah_pohon_induk')->nullable();

            // STATUS PEMBINAAN
            // menunggu_jadwal / dijadwalkan / selesai / batal
            $table->string('status')->default('menunggu_jadwal');
            $table->text('alasan_status')->nullable();         // alasan batal / catatan setelah selesai

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembinaan_kebun_benih_sumber');
    }
};
