<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembinaan_penangkar', function (Blueprint $table) {
            $table->id();

            // pemohon yang mengajukan
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // jenis benih yang diusahakan (Biji / Siap Tanam)
            $table->string('jenis_benih_diusahakan');

            // relasi ke sesi pembinaan (boleh null kalau belum dijadwalkan)
            $table->foreignId('pembinaan_sesi_id')
                ->nullable()
                ->constrained('pembinaan_sesi')
                ->nullOnDelete();

            // data penangkar
            $table->string('nama_penangkar');
            $table->string('nama_penanggung_jawab');
            $table->string('nik', 20)->nullable();
            $table->string('alamat_penanggung_jawab')->nullable();
            $table->string('npwp', 32)->nullable();
            $table->string('lokasi_usaha')->nullable();
            $table->string('status_kepemilikan_lahan')->nullable(); // misal: milik sendiri / sewa
            $table->string('no_hp')->nullable();

            // status pengajuan pembinaan
            // menunggu_jadwal / dijadwalkan / selesai / batal
            $table->string('status')->default('menunggu_jadwal');
            $table->text('alasan_status')->nullable(); // alasan batal / catatan lainnya

            // data OSS (diisi setelah pembinaan selesai)
            $table->string('nib')->nullable();
            $table->string('no_sertifikat_standar')->nullable();

            // status perizinan setelah pembinaan selesai
            // menunggu / berhasil / dibatalkan
            $table->string('status_perizinan')->default('menunggu');
            $table->text('alasan_perizinan')->nullable(); // alasan pembatalan perizinan oleh admin

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembinaan_penangkar');
    }
};
