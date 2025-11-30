<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembinaan_sesi', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 20)->default('penangkar');

            // Info sesi
            $table->string('nama_sesi')->nullable(); // contoh: "Batch 1 - 2025"

            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');

            // Link & materi
            $table->string('meet_link')->nullable();           // link Google Meet / Zoom / dll
            $table->string('materi_path')->nullable();         // file materi (ppt/pdf/dll)
            $table->string('bukti_pembinaan_path')->nullable(); // bukti setelah selesai (foto, berita acara, dll)

            // Status sesi: dijadwalkan / selesai / batal
            $table->string('status')->default('dijadwalkan');
            $table->text('alasan')->nullable(); // alasan batal / catatan tambahan

            // admin pembuat sesi
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembinaan_sesi');
    }
};
