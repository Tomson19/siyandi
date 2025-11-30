<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PermohonanBenih;
use App\Models\KeteranganPermohonan;
use Carbon\Carbon;

class AutoCancelPermohonan extends Command
{
    protected $signature = 'auto:cancel';
    protected $description = 'Batalkan otomatis permohonan yang disetujui tapi tidak diambil dalam 7 hari';

    public function handle()
    {
        $today = Carbon::today();

        // Ambil semua permohonan yang disetujui tapi belum diambil
        $permohonanList = PermohonanBenih::where('status', 'Disetujui')
            ->where('status_pengambilan', 'Belum Diambil')
            ->whereNotNull('tanggal_surat_keluar')
            ->get();

        $total = 0;

        foreach ($permohonanList as $permohonan) {
            $tanggalSuratKeluar = Carbon::parse($permohonan->tanggal_surat_keluar);

            // Cek kalau sudah lebih dari 7 hari sejak surat keluar
            if ($tanggalSuratKeluar->addDays(7)->lt($today)) {
                $permohonan->update([
                    'status_pengambilan' => 'Dibatalkan Setelah Disetujui',
                    'tanggal_dibatalkan' => $today->toDateString(),
                ]);

                // Simpan alasan otomatis ke tabel keterangan
                KeteranganPermohonan::create([
                    'permohonan_benih_id' => $permohonan->id,
                    'tanggal' => $today->toDateString(),
                    'keterangan' => 'Permohonan dibatalkan otomatis karena tidak diambil dalam waktu 7 hari setelah disetujui.',
                    'admin_id' => null, // karena sistem, bukan admin
                ]);

                $total++;
            }
        }

        $this->info("Auto-cancel selesai. Total dibatalkan: {$total}");
        return 0;
    }
}
