<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('program_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program');
            $table->string('nama_kegiatan');

            // relasi ke jenis_tanaman
            $table->foreignId('jenis_tanaman_id')
                  ->constrained('jenis_tanaman')
                  ->cascadeOnDelete();

            // khusus admin_verifikator
            $table->decimal('jumlah_produksi', 15, 2)->nullable();
            $table->string('jenis_benih')->nullable();

            // khusus admin_bidang_produksi
            $table->decimal('kebutuhan_benih', 15, 2)->nullable();

            // bidang otomatis: "UPT Benih Tanaman Perkebunan" / "Bidang Produksi"
            $table->string('bidang');

            $table->integer('tahun');

            // opsional: simpan siapa yang input
            $table->foreignId('user_id')->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_kegiatan');
    }
};

