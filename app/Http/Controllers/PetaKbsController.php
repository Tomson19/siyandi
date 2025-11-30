<?php

namespace App\Http\Controllers;

use App\Models\KebunBenihSumber;
use App\Models\Tanaman;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use App\Exports\KbsListExport;
use App\Exports\KbsDetailExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class PetaKbsController extends Controller
{
   public function index(Request $request)
{
    $tanamanList   = Tanaman::orderBy('nama_tanaman')->get();
    $kabupatenList = Kabupaten::orderBy('nama_kabupaten')->get();

    $hasFilter = $request->filled('tanaman_id') || $request->filled('kabupaten_id');

    if ($hasFilter) {
        // Query dasar
        $baseQuery = KebunBenihSumber::with([
            'tanaman',
            'kabupaten',
            'pemilik.pohon',
        ]);

        if ($request->filled('tanaman_id')) {
            $baseQuery->where('tanaman_id', $request->tanaman_id);
        }

        if ($request->filled('kabupaten_id')) {
            $baseQuery->where('kabupaten_id', $request->kabupaten_id);
        }

        // 🔹 Semua data untuk marker & export (TIDAK paginate)
        $markerData = (clone $baseQuery)
            ->orderBy('id', 'asc')
            ->get();

        // 🔹 Data untuk tabel (paginate 10)
        $kbsList = $baseQuery
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->appends($request->query());
    } else {
        // Belum ada filter → kosong
        $markerData = collect();
        $kbsList    = collect();
    }

    return view('peta.kbs', [
        'kbsList'       => $kbsList,       // untuk tabel (paginate)
        'markerData'    => $markerData,    // untuk peta (semua KBS hasil filter)
        'tanamanList'   => $tanamanList,
        'kabupatenList' => $kabupatenList,
        'hasFilter'     => $hasFilter,
    ]);
}



    public function show(KebunBenihSumber $kbs)
    {
        // Load relasi lengkap untuk detail
        $kbs->load([
            'tanaman',
            'kabupaten',
            'pemilik.pohon',
        ]);

        // Bentuk JSON rapi untuk dipakai di JS
        $result = [
            'id'           => $kbs->id,
            'komoditas'    => $kbs->tanaman->nama_tanaman ?? null,
            'varietas'     => $kbs->nama_varietas,
            'nomor_sk'     => $kbs->nomor_sk,
            'tanggal_sk'   => $kbs->tanggal_sk,
            'kabupaten'    => $kbs->kabupaten->nama_kabupaten ?? null,
            'pemilik'      => [],
        ];

        foreach ($kbs->pemilik as $p) {
            $pemilikData = [
                'id'                 => $p->id,
                'no_pemilik'         => $p->no_pemilik,
                'nama_pemilik'       => $p->nama_pemilik,
                'luas_ha'            => $p->luas_ha,
                'jumlah_pohon_induk' => $p->jumlah_pohon_induk,
                'kecamatan'          => $p->kecamatan,
                'desa'               => $p->desa,
                'tahun_tanam'        => $p->tahun_tanam,
                'jumlah_pit'         => $p->jumlah_pit,
                'pohon'              => [],
            ];

            foreach ($p->pohon as $ph) {
                $pemilikData['pohon'][] = [
                    'id'                => $ph->id,
                    'no_pohon'          => $ph->no_pohon,
                    'nomor_pohon_induk' => $ph->nomor_pohon_induk,
                    'latitude'          => $ph->latitude,
                    'longitude'         => $ph->longitude,
                ];
            }

            $result['pemilik'][] = $pemilikData;
        }

        return response()->json($result);
    }

    public function exportExcel(Request $request)
{
    $hasFilter = $request->filled('tanaman_id') || $request->filled('kabupaten_id');

    if (!$hasFilter) {
        return back()->with('error', 'Silakan pilih komoditi dan/atau kabupaten terlebih dahulu sebelum export.');
    }

    $query = KebunBenihSumber::with(['tanaman', 'kabupaten', 'pemilik']);

    if ($request->filled('tanaman_id')) {
        $query->where('tanaman_id', $request->tanaman_id);
    }

    if ($request->filled('kabupaten_id')) {
        $query->where('kabupaten_id', $request->kabupaten_id);
    }

    $kbsList = $query->orderBy('id', 'asc')->get();

    $fileName = 'kebun_benih_sumber_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(new KbsListExport($kbsList), $fileName);
}

public function exportPdf(Request $request)
{
    $hasFilter = $request->filled('tanaman_id') || $request->filled('kabupaten_id');

    if (!$hasFilter) {
        return back()->with('error', 'Silakan pilih komoditi dan/atau kabupaten terlebih dahulu sebelum export.');
    }

    $query = KebunBenihSumber::with(['tanaman', 'kabupaten', 'pemilik']);

    if ($request->filled('tanaman_id')) {
        $query->where('tanaman_id', $request->tanaman_id);
    }

    if ($request->filled('kabupaten_id')) {
        $query->where('kabupaten_id', $request->kabupaten_id);
    }

    $kbsList = $query->orderBy('id', 'asc')->get();

    $pdf = Pdf::loadView('peta.kbs_pdf', [
        'kbsList' => $kbsList,
        'generatedAt' => now(),
    ])->setPaper('a4', 'landscape');

    $fileName = 'kebun_benih_sumber_' . now()->format('Ymd_His') . '.pdf';

    return $pdf->download($fileName);
}

public function exportDetailExcel(KebunBenihSumber $kbs)
{
    // Load relasi lengkap
    $kbs->load(['tanaman', 'kabupaten', 'pemilik.pohon']);

    $rows = collect();
    $rowNumber = 1;

    foreach ($kbs->pemilik as $p) {
        if ($p->pohon->count()) {
            foreach ($p->pohon as $ph) {
                $rows->push([
                    'No'                  => $rowNumber++,
                    'Komoditas'           => $kbs->tanaman->nama_tanaman ?? '-',
                    'Varietas'            => $kbs->nama_varietas,
                    'Nomor SK'            => $kbs->nomor_sk ?? '-',
                    'Tanggal SK'          => $kbs->tanggal_sk ?? '-',
                    'Kabupaten'           => $kbs->kabupaten->nama_kabupaten ?? '-',
                    'Kecamatan'           => $p->kecamatan ?? '-',
                    'Desa'                => $p->desa ?? '-',
                    'Tahun Tanam'         => $p->tahun_tanam ?? '-',
                    'Jumlah PIT'          => $p->jumlah_pit ?? '-',
                    'No Pemilik'          => $p->no_pemilik ?? '-',
                    'Nama Pemilik'        => $p->nama_pemilik ?? '-',
                    'Luas (Ha)'           => $p->luas_ha ?? '-',
                    'Jumlah Pohon Induk'  => $p->jumlah_pohon_induk ?? '-',
                    'No Pohon'            => $ph->no_pohon ?? '-',
                    'No Pohon Induk'      => $ph->nomor_pohon_induk ?? '-',
                    'Latitude'            => $ph->latitude ?? '-',
                    'Longitude'           => $ph->longitude ?? '-',
                ]);
            }
        } else {
            // Pemilik tanpa pohon → tetap 1 baris
            $rows->push([
                'No'                  => $rowNumber++,
                'Komoditas'           => $kbs->tanaman->nama_tanaman ?? '-',
                'Varietas'            => $kbs->nama_varietas,
                'Nomor SK'            => $kbs->nomor_sk ?? '-',
                'Tanggal SK'          => $kbs->tanggal_sk ?? '-',
                'Kabupaten'           => $kbs->kabupaten->nama_kabupaten ?? '-',
                'Kecamatan'           => $p->kecamatan ?? '-',
                'Desa'                => $p->desa ?? '-',
                'Tahun Tanam'         => $p->tahun_tanam ?? '-',
                'Jumlah PIT'          => $p->jumlah_pit ?? '-',
                'No Pemilik'          => $p->no_pemilik ?? '-',
                'Nama Pemilik'        => $p->nama_pemilik ?? '-',
                'Luas (Ha)'           => $p->luas_ha ?? '-',
                'Jumlah Pohon Induk'  => $p->jumlah_pohon_induk ?? '-',
                'No Pohon'            => '-',
                'No Pohon Induk'      => '-',
                'Latitude'            => '-',
                'Longitude'           => '-',
            ]);
        }
    }

    // 🔢 hitung total dari relasi pemilik
    $totalLuas = $kbs->pemilik->sum('luas_ha');
    $totalPohonInduk = $kbs->pemilik->sum('jumlah_pohon_induk');

    // Baris jumlah di bawah
    $rows->push([
        'No'                  => '',
        'Komoditas'           => '',
        'Varietas'            => '',
        'Nomor SK'            => '',
        'Tanggal SK'          => '',
        'Kabupaten'           => '',
        'Kecamatan'           => '',
        'Desa'                => '',
        'Tahun Tanam'         => '',
        'Jumlah PIT'          => '',
        'No Pemilik'          => '',
        'Nama Pemilik'        => 'Jumlah',
        'Luas (Ha)'           => $totalLuas,
        'Jumlah Pohon Induk'  => $totalPohonInduk,
        'No Pohon'            => '',
        'No Pohon Induk'      => '',
        'Latitude'            => '',
        'Longitude'           => '',
    ]);

    $fileName = 'detail_kbs_' . ($kbs->nama_varietas ?? 'varietas') . '_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(
        new KbsDetailExport($rows, $kbs->nama_varietas ?? 'Detail KBS'),
        $fileName
    );
}


public function exportDetailPdf(KebunBenihSumber $kbs)
{
    $kbs->load(['tanaman', 'kabupaten', 'pemilik.pohon']);

    $pdf = Pdf::loadView('peta.kbs_detail_pdf', [
        'kbs'         => $kbs,
        'generatedAt' => now(),
    ])->setPaper('a4', 'landscape');

    $fileName = 'detail_kbs_' . ($kbs->nama_varietas ?? 'varietas') . '_' . now()->format('Ymd_His') . '.pdf';

    return $pdf->download($fileName);
}


}
