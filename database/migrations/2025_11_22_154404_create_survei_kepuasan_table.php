<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('survei_kepuasan', function (Blueprint $table) {
            $table->id();

            // Rating disimpan sebagai string: 'sangat_puas', 'puas', dst
            $table->string('q1_tampilan');      // Bagaimana tampilan situs web
            $table->string('q2_fitur');         // Bagaimana fitur pada web
            $table->text('q3_informasi')->nullable();    // Apakah Anda menemukan informasi ...
            $table->text('q4_sukai')->nullable();        // Apa yang paling Anda sukai ...
            $table->string('q5_kinerja');       // Bagaimana menilai kinerja situs web
            $table->text('q6_rekomendasi')->nullable();  // Rekomendasi perbaikan

            // Info teknis (opsional)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survei_kepuasan');
    }
};
