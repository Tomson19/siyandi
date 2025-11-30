<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom stok_awal dan stok_akhir ke tabel riwayat_stok.
     */
    public function up(): void
    {
        Schema::table('riwayat_stok', function (Blueprint $table) {
            if (!Schema::hasColumn('riwayat_stok', 'stok_awal')) {
                $table->integer('stok_awal')->default(0)->after('jumlah');
            }
            if (!Schema::hasColumn('riwayat_stok', 'stok_akhir')) {
                $table->integer('stok_akhir')->default(0)->after('stok_awal');
            }
        });
    }

    /**
     * Rollback perubahan.
     */
    public function down(): void
    {
        Schema::table('riwayat_stok', function (Blueprint $table) {
            $table->dropColumn(['stok_awal', 'stok_akhir']);
        });
    }
};
