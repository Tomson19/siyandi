<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Varietas;
use App\Models\Tanaman;
use App\Models\Kabupaten;
use App\Models\User;

class VarietasSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan tanaman "Kopi" sudah ada
        $tanaman = Tanaman::firstOrCreate(['nama_tanaman' => 'Kopi']);

        // Ambil kabupaten & admin-nya (contoh: Kepulauan Meranti)
        $kabupaten = Kabupaten::where('nama_kabupaten', 'Kepulauan Meranti')->first();
        $admin = User::where('email', 'admin_kepulauan_meranti@siyandi.local')->first();

        // === 📋 Data dari tabel kamu ===
        $varietasData = [
            [
                'tanaman_id' => $tanaman->id,
                'kabupaten_id' => $kabupaten?->id,
                'user_id' => $admin?->id,
                'nomor_tanggal_sk' => 'No. 69/Kpts/KB.020/1/2026, tgl. 26 Januari 2025',
                'nama_varietas' => 'Liberoid Meranti 1',
                'jenis_benih' => 'Unggul',
                'pemilik_varietas' => 'Pemerintah Daerah Kabupaten Kepulauan Meranti, Provinsi Riau',
                'jumlah_materi_genetik' => 176,
                'keterangan' => '-',
                'status' => 'published',
            ],
            [
                'tanaman_id' => $tanaman->id,
                'kabupaten_id' => $kabupaten?->id,
                'user_id' => $admin?->id,
                'nomor_tanggal_sk' => 'No. 70/Kpts/KB.020/1/2026, tgl. 26 Januari 2025',
                'nama_varietas' => 'Liberoid Meranti 2',
                'jenis_benih' => 'Unggul',
                'pemilik_varietas' => 'Pemerintah Daerah Kabupaten Kepulauan Meranti, Provinsi Riau',
                'jumlah_materi_genetik' => 8,
                'keterangan' => '-',
                'status' => 'published',
            ],
        ];

        foreach ($varietasData as $data) {
            Varietas::updateOrCreate(
                ['nama_varietas' => $data['nama_varietas']],
                $data
            );
        }
    }
}
