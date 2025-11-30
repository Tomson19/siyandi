<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tanaman;
use App\Models\Kabupaten;
use App\Models\Varietas;

class PetaController extends Controller
{
    /**
     * Halaman utama peta publik
     */
   public function index(Request $request)
{
    $tanamanList   = Tanaman::orderBy('nama_tanaman')->get();
    $kabupatenList = Kabupaten::orderBy('nama_kabupaten')->get();

    // default: kosong
    $varietas       = collect(); // untuk tabel (tanpa pagination saat belum filter)
    $varietasMap    = collect(); // untuk peta (selalu all data hasil filter)

    // kalau user sudah pilih minimal 1 filter baru tampilkan
    if ($request->filled('tanaman_id') || $request->filled('kabupaten_id')) {

        $filteredQuery = Varietas::with(['kabupaten', 'tanaman', 'materiGenetik']);

        if ($request->filled('tanaman_id')) {
            $filteredQuery->where('tanaman_id', $request->tanaman_id);
        }

        if ($request->filled('kabupaten_id')) {
            $filteredQuery->where('kabupaten_id', $request->kabupaten_id);
        }

        // 🔹 untuk MAP → semua data hasil filter (tidak paginated)
        $varietasMap = (clone $filteredQuery)->get();

        // 🔹 untuk TABEL → pagination 10 baris
        $varietas = (clone $filteredQuery)
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->appends($request->query()); // supaya filter tetap nempel saat pindah halaman
    }

    return view('peta.index', [
        'tanamanList'   => $tanamanList,
        'kabupatenList' => $kabupatenList,
        'varietas'      => $varietas,     // untuk tabel
        'varietasMap'   => $varietasMap,  // untuk map
    ]);
}



    /**
     * Detail varietas (ajax atau page)
     */
    public function detail($id)
    {
        $varietas = Varietas::with(['kabupaten', 'deskripsi', 'materiGenetik'])->findOrFail($id);
        return response()->json($varietas);
    }
}
