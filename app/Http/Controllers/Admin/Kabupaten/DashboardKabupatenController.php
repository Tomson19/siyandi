<?php

namespace App\Http\Controllers\Admin\Kabupaten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Varietas;
use App\Models\DeskripsiVarietas;
use App\Models\MateriGenetik;
use App\Models\KebunBenihSumber;
use App\Models\Penangkar;

class DashboardKabupatenController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $kabupatenId = $user->kabupaten_id;

        // 🔢 STATISTIK UTAMA
        $jumlahVarietas = Varietas::where('kabupaten_id', $kabupatenId)->count();

        $jumlahVarietasPublished = Varietas::where('kabupaten_id', $kabupatenId)
            ->where('status', 'published')
            ->count();

        $jumlahDeskripsi = DeskripsiVarietas::whereHas('varietas', function ($q) use ($kabupatenId) {
                $q->where('kabupaten_id', $kabupatenId);
            })
            ->count();

        $jumlahMateri = MateriGenetik::whereHas('varietas', function ($q) use ($kabupatenId) {
                $q->where('kabupaten_id', $kabupatenId);
            })
            ->count();

        $jumlahKbs = KebunBenihSumber::where('kabupaten_id', $kabupatenId)->count();

        $jumlahPenangkar = Penangkar::where('kabupaten_id', $kabupatenId)->count();

        // 🆕 DATA TERBARU (untuk tabel kecil di dashboard)
        $varietasTerbaru = Varietas::with('tanaman')
            ->where('kabupaten_id', $kabupatenId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $kbsTerbaru = KebunBenihSumber::with(['tanaman'])
            ->where('kabupaten_id', $kabupatenId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $penangkarTerbaru = Penangkar::with(['tanaman'])
            ->where('kabupaten_id', $kabupatenId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.kabupaten.dashboard', compact(
            'user',
            'jumlahVarietas',
            'jumlahVarietasPublished',
            'jumlahDeskripsi',
            'jumlahMateri',
            'jumlahKbs',
            'jumlahPenangkar',
            'varietasTerbaru',
            'kbsTerbaru',
            'penangkarTerbaru'
        ));
    }
}
