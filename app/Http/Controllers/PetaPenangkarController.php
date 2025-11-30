<?php

namespace App\Http\Controllers;

use App\Models\Penangkar;
use App\Models\Tanaman;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PenangkarExport;
use Barryvdh\DomPDF\Facade\Pdf;

class PetaPenangkarController extends Controller
{
    /**
     * Bangun query dasar penangkar + relasi, dengan filter dari request.
     */
    protected function buildFilteredQuery(Request $request)
    {
        $query = Penangkar::with(['tanaman', 'kabupaten']);

        if ($request->filled('tanaman_id')) {
            $query->where('tanaman_id', $request->tanaman_id);
        }

        if ($request->filled('kabupaten_id')) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }

        return $query;
    }

    protected function hasFilter(Request $request): bool
    {
        return $request->filled('tanaman_id') || $request->filled('kabupaten_id');
    }

    public function index(Request $request)
    {
        $tanamanList   = Tanaman::orderBy('nama_tanaman')->get();
        $kabupatenList = Kabupaten::orderBy('nama_kabupaten')->get();

        $hasFilter = $this->hasFilter($request);

        if ($hasFilter) {
            $query = $this->buildFilteredQuery($request);

            // 🔹 Data lengkap untuk peta (SEMUA titik sesuai filter)
            $markerData = (clone $query)->get();

            // 🔹 Data paginate 10 baris untuk tabel
            $penangkarList = $query->paginate(10)->appends($request->query());
        } else {
            $markerData    = collect();
            $penangkarList = collect();
        }

        return view('peta.penangkar', [
            'tanamanList'   => $tanamanList,
            'kabupatenList' => $kabupatenList,
            'penangkarList' => $penangkarList, // untuk tabel (paginate)
            'markerData'    => $markerData,    // untuk peta (semua data filter)
            'hasFilter'     => $hasFilter,
        ]);
    }

    /**
     * Export Excel: semua data sesuai filter (tanpa pagination).
     */
    public function exportExcel(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $data  = $query->get(); // 🔹 semua baris sesuai filter

        if ($data->isEmpty()) {
            return redirect()
                ->route('peta.penangkar.index', $request->query())
                ->with('error', 'Tidak ada data untuk diexport.');
        }

        $fileName = 'penangkar_benih_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PenangkarExport($data), $fileName);
    }

    /**
     * Export PDF: semua data sesuai filter (tanpa pagination).
     */
    public function exportPdf(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $data  = $query->get(); // 🔹 semua baris sesuai filter

        if ($data->isEmpty()) {
            return redirect()
                ->route('peta.penangkar.index', $request->query())
                ->with('error', 'Tidak ada data untuk diexport.');
        }

        // Untuk judul / keterangan di PDF
        $tanaman   = $request->filled('tanaman_id') ? Tanaman::find($request->tanaman_id) : null;
        $kabupaten = $request->filled('kabupaten_id') ? Kabupaten::find($request->kabupaten_id) : null;

        $pdf = Pdf::loadView('peta.penangkar_pdf', [
            'data'      => $data,
            'tanaman'   => $tanaman,
            'kabupaten' => $kabupaten,
        ])->setPaper('a4', 'landscape');

        $fileName = 'penangkar_benih_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}
