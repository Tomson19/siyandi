<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();

            $table->string('nama');
            $table->string('nik', 20)->nullable();      // bisa kosong kalau mau, tapi nanti divalidasi 16 digit
            $table->text('alamat')->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->text('pengaduan');

            // file gambar opsional
            $table->string('gambar_path')->nullable();

            // info teknis
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
