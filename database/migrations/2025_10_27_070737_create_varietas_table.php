<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('varietas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanaman_id')->constrained('tanaman');
            $table->foreignId('kabupaten_id')->nullable()->constrained('kabupaten');
            $table->foreignId('user_id')->nullable()->constrained('users');
            
            $table->string('nomor_tanggal_sk')->nullable(); // 🟢 digabung jadi satu
            $table->string('nama_varietas');
            $table->string('jenis_benih')->nullable();
            $table->string('pemilik_varietas')->nullable();
            $table->decimal('jumlah_materi_genetik', 10, 2)->nullable();
            $table->text('keterangan')->nullable();

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('varietas');
    }
};
