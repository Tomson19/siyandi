<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('buku_panduan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_buku');   // nama buku panduan
            $table->string('file_path');   // path PDF di storage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku_panduan');
    }
};
