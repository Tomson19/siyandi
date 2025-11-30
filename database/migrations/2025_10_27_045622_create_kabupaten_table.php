<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kabupaten', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kabupaten');
            $table->timestamps();
        });

        // Tambahkan kolom kabupaten_id ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('kabupaten_id')->nullable()->constrained('kabupaten');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kabupaten_id');
        });

        Schema::dropIfExists('kabupaten');
    }
};
