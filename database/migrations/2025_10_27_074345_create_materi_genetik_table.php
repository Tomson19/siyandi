<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materi_genetik', function (Blueprint $table) {
            $table->id();

            // 🟢 relasi ke varietas (INI WAJIB ADA)
            $table->foreignId('varietas_id')->constrained('varietas')->onDelete('cascade');

            $table->string('no_sk')->nullable();
            $table->string('tanggal_sk')->nullable();
            $table->integer('nomor_pohon')->nullable();

            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materi_genetik');
    }
};
