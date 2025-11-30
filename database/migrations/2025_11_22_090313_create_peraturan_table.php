<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('peraturan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tahun');          // Nomor & tahun (string bebas)
            $table->date('tanggal_penetapan');      // Tanggal penetapan
            $table->text('tentang');                // Tentang / judul
            $table->string('file_path');            // path file pdf di storage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peraturan');
    }
};
