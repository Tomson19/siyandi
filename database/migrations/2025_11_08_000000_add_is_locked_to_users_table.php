<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // menandai apakah user diblokir karena tidak upload bukti tanam, dsb.
            $table->boolean('is_locked')
                  ->default(false)
                  ->after('password'); // sesuaikan posisi kalau mau

            $table->dateTime('locked_at')
                  ->nullable()
                  ->after('is_locked');

            $table->text('alasan_lock')
                  ->nullable()
                  ->after('locked_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'locked_at', 'alasan_lock']);
        });
    }
};
