<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Tambahan untuk PDF & Excel
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengaduanExport;

class PengaduanController extends Controller
{
    /**
     * Public: simpan pengaduan (tanpa login).
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'nama'      => ['required', 'string', 'max:255'],
                'nik'       => ['nullable', 'digits:16'],
                'alamat'    => ['nullable', 'string', 'max:1000'],
                'no_hp'     => ['nullable', 'string', 'max:50'],
                'pengaduan' => ['required', 'string', 'max:2000'],
                'gambar'    => [
                    'nullable',
                    'image',
                    'mimes:jpeg,jpg,png',
                    'max:2048', // 2MB
                ],
            ],
            [
                'nama.required'      => 'Nama wajib diisi.',
                'nik.digits'         => 'NIK harus 16 digit.',
                'pengaduan.required' => 'Isi pengaduan wajib diisi.',
                'gambar.image'       => 'File harus berupa gambar.',
                'gambar.mimes'       => 'Gambar harus bertipe JPG, JPEG, atau PNG.',
                'gambar.max'         => 'Ukuran gambar maksimal 2MB.',
            ]
        );

        // Simpan file jika ada
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('pengaduan', 'public');
        }

        $validated['gambar_path'] = $gambarPath;
        $validated['ip_address']  = $request->ip();
        $validated['user_agent']  = $request->userAgent();

        Pengaduan::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaduan berhasil dikirim.',
            ]);
        }

        return back()->with('status', 'pengaduan-sent');
    }

    /**
     * Admin verifikator: lihat daftar pengaduan.
     */
    public function index(Request $request)
    {
        $search = $request->query('q');
        $from   = $request->query('from');
        $to     = $request->query('to');

        $query = $this->buildFilteredQuery($search, $from, $to);

        $pengaduanList = $query
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        $total = Pengaduan::count();

        return view('admin.verifikator.pengaduan.index', compact(
            'pengaduanList',
            'search',
            'from',
            'to',
            'total'
        ));
    }

    /**
     * Export PDF daftar pengaduan (dengan filter yang sama seperti index).
     */
    public function exportPdf(Request $request)
    {
        $search = $request->query('q');
        $from   = $request->query('from');
        $to     = $request->query('to');

        $query = $this->buildFilteredQuery($search, $from, $to);

        // Ambil semua data (tanpa pagination)
        $pengaduanList = $query
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.verifikator.pengaduan.export_pdf', [
            'pengaduanList' => $pengaduanList,
            'search'        => $search,
            'from'          => $from,
            'to'            => $to,
        ])->setPaper('a4', 'portrait'); // bisa diubah ke landscape kalau perlu

        $filename = 'pengaduan_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export Excel daftar pengaduan (dengan filter yang sama seperti index).
     */
    public function exportExcel(Request $request)
    {
        $search = $request->query('q');
        $from   = $request->query('from');
        $to     = $request->query('to');

        $filename = 'pengaduan_' . now()->format('Ymd_His') . '.xlsx';

        // PengaduanExport akan kita buat di app/Exports/PengaduanExport.php
        return Excel::download(new PengaduanExport($search, $from, $to), $filename);
    }

    /**
     * (Opsional) Hapus pengaduan kalau mau.
     */
    public function destroy(Pengaduan $pengaduan)
    {
        if ($pengaduan->gambar_path && Storage::disk('public')->exists($pengaduan->gambar_path)) {
            Storage::disk('public')->delete($pengaduan->gambar_path);
        }

        $pengaduan->delete();

        return redirect()
            ->route('admin.verifikator.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }

    /**
     * Helper: build query dengan filter yang sama (dipakai index, PDF, Excel).
     */
    protected function buildFilteredQuery(?string $search, ?string $from, ?string $to)
    {
        $query = Pengaduan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('pengaduan', 'like', "%{$search}%");
            });
        }

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }
}
