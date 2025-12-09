<?php

namespace App\Http\Controllers;

use App\Models\JenisTanaman;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BudidayaDynamicExport;
use Illuminate\Support\Facades\Validator;


class BudidayaController extends Controller
{
    /**
     * Tampilkan halaman peta budidaya.
     * Default: tidak ada komoditi terpilih (awal kosong).
     */
    public function index(Request $request)
    {
        // 1) Ambil daftar komoditi dari DB
        $tanaman = JenisTanaman::orderBy('nama_tanaman')->get();

        // 2) Siapkan opsi (name + slug) untuk dropdown
        $komoditiOptions = $tanaman->map(function ($jt) {
            return [
                'name' => $jt->nama_tanaman,
                // contoh: "Kopi" => "kopi", "Kelapa Sawit" => "kelapa_sawit"
                'slug' => Str::slug($jt->nama_tanaman, '_'),
            ];
        })->values()->all();

        // 3) Folder aset shapefile (gunakan 'mapdata' agar tidak bentrok dengan route /peta)
        $assetDir = 'mapdata';

        // 4) Pemetaan slug => URL shapefile (deteksi otomatis berdasar pola nama file)
        //    FINAL_POTENSI_BUDIDAYA_{SLUG-UPPER}_OK_KAB.zip
        $shpFiles = [];
        foreach ($komoditiOptions as $opt) {
            $slug = $opt['slug'];
            $candidate = 'FINAL_POTENSI_BUDIDAYA_' . strtoupper($slug) . '_OK_KAB.zip';
            $absPath   = public_path("$assetDir/$candidate");
            if (file_exists($absPath)) {
                $shpFiles[$slug] = asset("$assetDir/$candidate");
            }
        }

        // (Opsional) mapping manual jika ada nama file yang tidak mengikuti pola di atas.
        // $manual = [
        //     'kopi'  => 'FINAL_POTENSI_BUDIDAYA_KOPI_OK_KAB.zip',
        //     'kakao' => 'NAMA_FILE_KAKAO.zip',
        // ];
        // foreach ($manual as $slug => $file) {
        //     $abs = public_path("$assetDir/$file");
        //     if (file_exists($abs)) {
        //         $shpFiles[$slug] = asset("$assetDir/$file");
        //     }
        // }

        // 5) Default kosong: hanya pakai nilai dari query (?komoditi=slug) jika valid.
        $selectedSlug = $request->query('komoditi');         // bisa null
        if (!$selectedSlug || !array_key_exists($selectedSlug, $shpFiles)) {
            $selectedSlug = null; // paksa kosong jika tidak ada atau tidak valid
        }

        // 6) nameBySlug untuk dipakai di view (label dropdown / judul)
        $nameBySlug = [];
        foreach ($komoditiOptions as $opt) {
            $nameBySlug[$opt['slug']] = $opt['name'];
        }
        $selectedName = $selectedSlug ? ($nameBySlug[$selectedSlug] ?? null) : null;

        // 7) Kirim ke view
        return view('budidaya.index', [
            'komoditiOptions' => $komoditiOptions, // [{name, slug}, ...]
            'shpFiles'        => $shpFiles,        // { slug => url zip }
            'selectedSlug'    => $selectedSlug,    // null saat awal
            'selectedName'    => $selectedName,    // null saat awal
            'assetDir'        => $assetDir,        // 'mapdata'
            'nameBySlug'      => $nameBySlug,      // { slug => name }
        ]);
    }

    public function exportFromJs(Request $request)
{
    // data dikirim dari form hidden sebagai JSON string
    $rowsJson = $request->input('rows_json');

    if (!$rowsJson) {
        return redirect()->back()->with('error', 'Data export kosong.');
    }

    $decoded = json_decode($rowsJson, true);

    if (!is_array($decoded) || empty($decoded)) {
        return redirect()->back()->with('error', 'Format data export tidak valid.');
    }

    // validasi simpel
    foreach ($decoded as $row) {
        if (
            !isset($row['no'], $row['komoditi'], $row['kabupaten'], $row['luas'])
        ) {
            return redirect()->back()->with('error', 'Struktur data export tidak lengkap.');
        }
    }

    // susun array sesuai urutan kolom di Excel
    $rowsForExcel = [];
    foreach ($decoded as $row) {
        $rowsForExcel[] = [
            (int) $row['no'],
            (string) $row['komoditi'],
            (string) $row['kabupaten'],
            (float) $row['luas'],
        ];
    }

    $fileName = 'budidaya_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(new BudidayaDynamicExport($rowsForExcel), $fileName);
}



}
