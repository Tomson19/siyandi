<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembinaanPenangkar;
use App\Models\PembinaanKebunBenihSumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPembinaanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->query('tahun'); // bisa null
        $bulan = $request->query('bulan'); // bisa null

        // ====== RANGE TAHUN UNTUK DROPDOWN ======
        $firstPenangkar = PembinaanPenangkar::min('created_at');
        $firstKbs       = PembinaanKebunBenihSumber::min('created_at');

        $years = [];

        if ($firstPenangkar || $firstKbs) {
            $year1   = $firstPenangkar ? Carbon::parse($firstPenangkar)->year : null;
            $year2   = $firstKbs ? Carbon::parse($firstKbs)->year : null;
            $minYear = min(array_filter([$year1, $year2]));
            $maxYear = now()->year;

            for ($y = $minYear; $y <= $maxYear; $y++) {
                $years[] = $y;
            }
        } else {
            $years = [now()->year];
        }

        // ====== BASE QUERY (default: all-time, kecuali difilter) ======
        $penangkarBase = PembinaanPenangkar::query();
        $kbsBase       = PembinaanKebunBenihSumber::query();

        if ($tahun) {
            $penangkarBase->whereYear('pembinaan_penangkar.created_at', $tahun);
            $kbsBase->whereYear('pembinaan_kebun_benih_sumber.created_at', $tahun);
        }

        if ($bulan) {
            $penangkarBase->whereMonth('pembinaan_penangkar.created_at', $bulan);
            $kbsBase->whereMonth('pembinaan_kebun_benih_sumber.created_at', $bulan);
        }

        // ====== STATISTIK PENANGKAR ======
        $penangkarTotal          = (clone $penangkarBase)->count();
        $penangkarMenungguJadwal = (clone $penangkarBase)->where('status', 'menunggu_jadwal')->count();
        $penangkarDijadwalkan    = (clone $penangkarBase)->where('status', 'dijadwalkan')->count();
        $penangkarSelesai        = (clone $penangkarBase)->where('status', 'selesai')->count();
        $penangkarBatal          = (clone $penangkarBase)->where('status', 'batal')->count();

        $perizinanBerhasil   = (clone $penangkarBase)->where('status_perizinan', 'berhasil')->count();
        $perizinanDibatalkan = (clone $penangkarBase)->where('status_perizinan', 'dibatalkan')->count();

        $ossLengkap = (clone $penangkarBase)
            ->whereNotNull('nib')
            ->whereNotNull('no_sertifikat_standar')
            ->count();

        $persentasePembinaanSelesai = $penangkarTotal > 0
            ? round(($penangkarSelesai / $penangkarTotal) * 100, 1)
            : 0;

        $persentasePerizinanBerhasil = $penangkarSelesai > 0
            ? round(($perizinanBerhasil / $penangkarSelesai) * 100, 1)
            : 0;

        // ====== STATISTIK KBS ======
        $kbsTotal          = (clone $kbsBase)->count();
        $kbsMenungguJadwal = (clone $kbsBase)->where('status', 'menunggu_jadwal')->count();
        $kbsDijadwalkan    = (clone $kbsBase)->where('status', 'dijadwalkan')->count();
        $kbsSelesai        = (clone $kbsBase)->where('status', 'selesai')->count();
        $kbsBatal          = (clone $kbsBase)->where('status', 'batal')->count();

        // ====== KOMODITAS TERPOPULER PENANGKAR (TOP 5) ======
        $topKomoditasPenangkar = (clone $penangkarBase)
            ->select('jenis_benih_diusahakan', DB::raw('COUNT(*) as total'))
            ->whereNotNull('jenis_benih_diusahakan')
            ->groupBy('jenis_benih_diusahakan')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ====== KOMODITAS TERPOPULER KBS (TOP 5) ======
        $topKomoditasKbs = (clone $kbsBase)
            ->join('jenis_tanaman', 'pembinaan_kebun_benih_sumber.jenis_tanaman_id', '=', 'jenis_tanaman.id')
            ->select('jenis_tanaman.nama_tanaman', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_tanaman.nama_tanaman')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $bulanList = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return view('admin.laporan.pembinaan', compact(
            'tahun',
            'bulan',
            'years',
            'bulanList',

            'penangkarTotal',
            'penangkarMenungguJadwal',
            'penangkarDijadwalkan',
            'penangkarSelesai',
            'penangkarBatal',
            'perizinanBerhasil',
            'perizinanDibatalkan',
            'ossLengkap',
            'persentasePembinaanSelesai',
            'persentasePerizinanBerhasil',

            'kbsTotal',
            'kbsMenungguJadwal',
            'kbsDijadwalkan',
            'kbsSelesai',
            'kbsBatal',

            'topKomoditasPenangkar',
            'topKomoditasKbs',
        ));
    }
}

