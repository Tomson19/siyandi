<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanBenih;
use App\Models\JenisTanaman;
use App\Models\KeteranganPermohonan;
use App\Models\PengaturanQris;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PermohonanBenihExport;
use App\Exports\LaporanPenjualanExport;
use Illuminate\Database\Eloquent\Builder;


class VerifikatorPermohonanController extends Controller
{
    protected function buildFilteredPermohonanQuery(Request $request): Builder
{
    $query = PermohonanBenih::with(['user', 'jenisTanaman', 'benih'])
        ->orderByDesc('created_at');

    // Pencarian nama / NIK
    if ($request->filled('search')) {
        $search = trim($request->input('search'));
        $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%");
        });
    }

    // Filter status utama
    if ($request->filled('status')) {
        $query->where('status', $request->input('status'));
    }

    // Filter tipe permohonan (Gratis / Berbayar)
    if ($request->filled('tipe_pembayaran')) {
        $query->where('tipe_pembayaran', $request->input('tipe_pembayaran'));
    }

    // Filter status pembayaran
    if ($request->filled('status_pembayaran')) {
        $sp = $request->input('status_pembayaran');
        if ($sp === 'null') {
            $query->whereNull('status_pembayaran');
        } else {
            $query->where('status_pembayaran', $sp);
        }
    }

    // Filter status pengambilan
    if ($request->filled('status_pengambilan')) {
        $query->where('status_pengambilan', $request->input('status_pengambilan'));
    }

    // Filter jenis tanaman
    if ($request->filled('jenis_tanaman_id')) {
        $query->where('jenis_tanaman_id', $request->input('jenis_tanaman_id'));
    }

    // Filter tanggal diajukan (range)
    if ($request->filled('tanggal_dari')) {
        $query->whereDate('tanggal_diajukan', '>=', $request->input('tanggal_dari'));
    }

    if ($request->filled('tanggal_sampai')) {
        $query->whereDate('tanggal_diajukan', '<=', $request->input('tanggal_sampai'));
    }

    return $query;
}



public function dashboard(Request $request)
{
    // query utama dengan filter + relasi + ORDER utk tabel
    $query = $this->buildFilteredPermohonanQuery($request)
        ->with(['user', 'jenisTanaman', 'benih'])
        ->orderByDesc('created_at');

    // clone utk summary & count
    $summaryQuery = clone $query;
    $countQuery   = clone $query;

    // HAPUS ORDER BY di summaryQuery supaya nggak bentrok sama GROUP BY
    $summaryQuery->getQuery()->orders = null;

    // data tabel dengan pagination
    $permohonan = $query->paginate(10)->withQueryString();

    // ringkasan status (berdasar data ter-filter, bukan semua)
    $statusCounts = $summaryQuery
        ->selectRaw('status, COUNT(*) as total')
        ->groupBy('status')
        ->pluck('total', 'status');

    $totalFiltered = $countQuery->count();

    // untuk dropdown filter jenis tanaman
    $jenisTanaman = JenisTanaman::orderBy('nama_tanaman')->get();

    $filters = $request->only([
        'search',
        'status',
        'tipe_pembayaran',
        'status_pembayaran',
        'status_pengambilan',
        'jenis_tanaman_id',
        'tanggal_dari',
        'tanggal_sampai',
    ]);

    return view('admin.verifikator.index', compact(
        'permohonan',
        'statusCounts',
        'totalFiltered',
        'jenisTanaman',
        'filters'
    ));
}



    public function index(Request $request)
{
    return $this->dashboard($request);
}

public function exportExcel(Request $request)
{
    // pakai filter yang sama dengan index
    $query = $this->buildFilteredPermohonanQuery($request)
        ->with(['user', 'jenisTanaman', 'benih'])
        ->orderByDesc('created_at');

    $rows = $query->get();

    if ($rows->isEmpty()) {
        return back()->with('info', 'Tidak ada data yang bisa diexport berdasarkan filter saat ini.');
    }

    $fileName = 'permohonan_benih_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(new PermohonanBenihExport($rows), $fileName);
}

public function exportPdf(Request $request)
{
    $query = $this->buildFilteredPermohonanQuery($request)
        ->with([
            'user',
            'jenisTanaman',
            'benih',
            'keterangan', // <-- penting buat ambil catatan admin
        ])
        ->orderByDesc('created_at');

    $permohonan = $query->get();

    if ($permohonan->isEmpty()) {
        return back()->with('info', 'Tidak ada data yang bisa diexport berdasarkan filter saat ini.');
    }

    $filters = $request->only([
        'search',
        'status',
        'tipe_pembayaran',
        'status_pembayaran',
        'status_pengambilan',
        'jenis_tanaman_id',
        'tanggal_dari',
        'tanggal_sampai',
    ]);

    $pdf = Pdf::loadView('admin.verifikator.permohonan.export_pdf', [
        'permohonan'  => $permohonan,
        'filters'     => $filters,
        'generatedAt' => now(),
    ])->setPaper('A4', 'landscape');

    $fileName = 'permohonan_benih_' . now()->format('Ymd_His') . '.pdf';

    return $pdf->download($fileName);
}

    public function show($id)
    {
        $permohonan = PermohonanBenih::with(['user', 'jenisTanaman', 'benih'])
            ->findOrFail($id);

        $keterangan = KeteranganPermohonan::where('permohonan_id', $id)
            ->orderByDesc('tanggal_keterangan')
            ->get();

        return view('admin.verifikator.show', compact('permohonan', 'keterangan'));
    }

    /**
     * Permohonan diminta perbaikan.
     */
    public function perbaiki(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string|max:500']);

        $permohonan = PermohonanBenih::findOrFail($id);

        if (in_array($permohonan->status, ['Disetujui', 'Ditolak', 'Dibatalkan'])) {
            return back()->with('error', 'Permohonan tidak dapat diminta perbaikan karena sudah diverifikasi atau dibatalkan.');
        }

        $permohonan->update([
            'status' => 'Perbaikan',
        ]);

        KeteranganPermohonan::create([
            'permohonan_id'      => $permohonan->id,
            'admin_id'           => Auth::id(),
            'jenis_keterangan'   => 'Perlu Diperbaiki',
            'isi_keterangan'     => $request->alasan,
            'tanggal_keterangan' => now(),
        ]);

        return redirect()->route('admin.verifikator.permohonan.show', $id)
            ->with('info', 'Permohonan diminta perbaikan.');
    }

    /**
     * Setujui → isi jumlah disetujui + generate surat Word (berbayar / gratis) + set QRIS & batas pembayaran (jika berbayar).
     */
    public function approve(Request $request, $id)
    {
        $permohonan = PermohonanBenih::with(['jenisTanaman', 'benih'])->findOrFail($id);

        if (in_array($permohonan->status, ['Ditolak', 'Dibatalkan'])) {
            return back()->with('error', 'Permohonan ini sudah ditolak atau dibatalkan.');
        }

        $request->validate([
            'alasan'           => 'required|string|max:500',
            'jumlah_disetujui' => 'required|integer|min:1|max:' . $permohonan->jumlah_tanaman,
        ]);

        $jumlahDisetujui = (int) $request->jumlah_disetujui;

        /**
         * ======================================
         *  VALIDASI BENIH & HARGA
         * ======================================
         */
        if (! $permohonan->benih) {
            // coba cari ulang berdasarkan jenis tanaman, benih, dan tipe pembayaran
            $benih = \App\Models\Benih::where('jenis_tanaman_id', $permohonan->jenis_tanaman_id)
                ->where('jenis_benih', $permohonan->jenis_benih)
                ->where('tipe_pembayaran', $permohonan->tipe_pembayaran)
                ->first();

            if (! $benih) {
                return back()->with('error', 'Data benih tidak ditemukan. Pastikan master data benih tersedia.');
            }

            $permohonan->update(['benih_id' => $benih->id]);
            $permohonan->load('benih'); // refresh relasi
        }

        $hargaSatuan = $permohonan->benih->harga ?? 0;
        $nominalPembayaran = null;

        if ($permohonan->tipe_pembayaran === 'Berbayar') {
            if ($hargaSatuan <= 0) {
                return back()->with('error', 'Harga benih belum diatur. Silakan periksa master data benih terlebih dahulu.');
            }

            $nominalPembayaran = $hargaSatuan * $jumlahDisetujui;
        }

        /**
         * ======================================
         *  BUAT SURAT PERSETUJUAN (Word)
         * ======================================
         */
        $templatePath = $permohonan->tipe_pembayaran === 'Berbayar'
            ? storage_path('app/templates/SURAT_Persetujuan_Permohonan_benih_bayar.docx')
            : storage_path('app/templates/SURAT_Persetujuan_Permohonan_benih_gratis (1).docx');

        if (! file_exists($templatePath)) {
            return back()->with('error', 'Template surat tidak ditemukan: ' . basename($templatePath));
        }

        $template = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        $template->setValue('nama', $permohonan->nama);
        $template->setValue('tanggal', now()->translatedFormat('d F Y'));
        $template->setValue(
            'tanggal_permohonan',
            $permohonan->tanggal_diajukan
                ? Carbon::parse($permohonan->tanggal_diajukan)->translatedFormat('d F Y')
                : '-'
        );
        $template->setValue('jenis_tanaman', $permohonan->jenisTanaman->nama_tanaman ?? '-');
        $template->setValue('jenis_benih', $permohonan->jenis_benih ?? '-');
        $template->setValue('jumlah_tanaman', $permohonan->jumlah_tanaman);
        $template->setValue('jumlah_disetujui', $jumlahDisetujui);
        $template->setValue('harga_satuan', number_format($hargaSatuan, 0, ',', '.'));
        $template->setValue('total_bayar', $nominalPembayaran ? number_format($nominalPembayaran, 0, ',', '.') : 'Gratis');

        // Simpan surat ke storage/public
        $filename     = 'surat_persetujuan_' . Str::slug($permohonan->nama) . '.docx';
        $relativePath = 'surat_persetujuan/' . $filename;
        $outputPath   = storage_path('app/public/' . $relativePath);

        if (! is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0775, true);
        }

        $template->saveAs($outputPath);

        /**
         * ======================================
         *  QRIS AKTIF (kalau berbayar)
         * ======================================
         */
        $qris = null;
        if ($permohonan->tipe_pembayaran === 'Berbayar') {
            $qris = PengaturanQris::where('aktif', true)->first();
        }

        /**
         * ======================================
         *  UPDATE DATA PERMOHONAN
         * ======================================
         */
        $updateData = [
            'status'               => 'Disetujui',
            'tanggal_disetujui'    => now(),
            'tanggal_surat_keluar' => now(),
            'status_pengambilan'   => 'Belum Diambil',
            'scan_surat_pengambilan' => $relativePath,
            'jumlah_disetujui'     => $jumlahDisetujui,
            'nominal_pembayaran'   => $nominalPembayaran,
        ];

        if ($permohonan->tipe_pembayaran === 'Berbayar') {
            $updateData['status_pembayaran'] = 'Menunggu';
            $updateData['batas_pembayaran']  = now()->addDays(7);
            if ($qris) {
                $updateData['qris_image'] = $qris->gambar_qris;
            }
        } else {
            $updateData['status_pembayaran'] = null;
            $updateData['batas_pembayaran']  = null;
        }

        $permohonan->update($updateData);

        /**
         * ======================================
         *  CATAT LOG KETERANGAN ADMIN
         * ======================================
         */
        KeteranganPermohonan::create([
            'permohonan_id'      => $permohonan->id,
            'admin_id'           => Auth::id(),
            'jenis_keterangan'   => 'Disetujui',
            'isi_keterangan'     => $request->alasan,
            'tanggal_keterangan' => now(),
        ]);

        return redirect()->route('admin.verifikator.permohonan.show', $id)
            ->with('success', 'Permohonan disetujui. Nominal pembayaran otomatis dari master benih dan surat persetujuan berhasil dibuat.');
    }



    /**
     * Tolak → generate surat Word.
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['alasan' => 'required|string|max:500']);

        $permohonan = PermohonanBenih::with('jenisTanaman')->findOrFail($id);

        if (in_array($permohonan->status, ['Disetujui', 'Dibatalkan'])) {
            return back()->with('error', 'Permohonan ini sudah disetujui atau dibatalkan.');
        }

        // Path template DOCX penolakan
        $templatePath = storage_path('app/templates/Surat Penolakan.docx');
        if (! file_exists($templatePath)) {
            return back()->with('error', 'Template surat penolakan tidak ditemukan.');
        }

        $template = new TemplateProcessor($templatePath);

        $template->setValue('tanggal', now()->translatedFormat('d F Y'));
        $template->setValue(
            'tanggal_permohonan',
            $permohonan->tanggal_diajukan
                ? Carbon::parse($permohonan->tanggal_diajukan)->translatedFormat('d F Y')
                : '-'
        );

        $template->setValue('nama', $permohonan->nama ?? '-');
        $template->setValue('nik', $permohonan->nik ?? '-');
        $template->setValue('alamat', $permohonan->alamat ?? '-');
        $template->setValue('no_telp', $permohonan->no_telp ?? '-');

        $template->setValue('jenis_tanaman', $permohonan->jenisTanaman->nama_tanaman ?? '-');
        $template->setValue('jenis_benih', $permohonan->jenis_benih ?? '-');
        $template->setValue('jumlah_tanaman', $permohonan->jumlah_tanaman ?? '-');
        $template->setValue('luas_area', $permohonan->luas_area ?? '-');

        // Alasan penolakan dari form
        $template->setValue('alasan_penolakan', $request->alasan);

        $filename     = 'surat_penolakan_' . Str::slug($permohonan->nama ?: 'pemohon') . '.docx';
        $relativePath = 'surat_penolakan/' . $filename;
        $outputPath   = storage_path('app/public/' . $relativePath);

        if (! is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0775, true);
        }

        $template->saveAs($outputPath);

        // Update status di database
        $permohonan->update([
            'status'                 => 'Ditolak',
            'tanggal_ditolak'        => now(),
            // Masih pakai kolom ini untuk menyimpan file penolakan
            'scan_surat_pengambilan' => $relativePath,
            'status_pembayaran'      => null,
            'batas_pembayaran'       => null,
        ]);

        // Catat keterangan verifikator
        KeteranganPermohonan::create([
            'permohonan_id'      => $permohonan->id,
            'admin_id'           => Auth::id(),
            'jenis_keterangan'   => 'Ditolak',
            'isi_keterangan'     => $request->alasan,
            'tanggal_keterangan' => now(),
        ]);

        return redirect()->route('admin.verifikator.permohonan.show', $id)
            ->with('error', 'Permohonan ditolak. Surat penolakan (DOCX) telah dibuat.');
    }

    /**
     * Upload ulang surat hasil tanda tangan (PDF).
     */
    public function uploadKeputusan(Request $request, $id)
    {
        $request->validate(['surat_pdf' => 'required|file|mimes:pdf|max:4096']);

        $permohonan = PermohonanBenih::findOrFail($id);

        if (! in_array($permohonan->status, ['Disetujui', 'Ditolak'])) {
            return back()->with('error', 'Upload surat hanya untuk permohonan yang sudah disetujui/ditolak.');
        }

        $folder = $permohonan->status === 'Disetujui'
            ? 'surat_persetujuan'
            : 'surat_penolakan';

        Storage::disk('public')->makeDirectory($folder);

        if ($permohonan->scan_surat_pengambilan && Storage::disk('public')->exists($permohonan->scan_surat_pengambilan)) {
            Storage::disk('public')->delete($permohonan->scan_surat_pengambilan);
        }

        $path = $request->file('surat_pdf')->store($folder, 'public');

        $permohonan->update([
            'scan_surat_pengambilan' => $path,
        ]);

        return back()->with('success', 'Surat hasil tanda tangan (PDF) berhasil diunggah.');
    }

    /**
     * Update manual status & tanggal pengambilan.
     * Di sini juga atur stok benih & deadline bukti tanam.
     */


    public function updatePengambilan(Request $request, $id)
    {
        $request->validate([
            'status_pengambilan'  => 'required|in:Belum Diambil,Selesai,Dibatalkan',
            'tanggal_pengambilan' => 'nullable|date',
            'bukti_pengambilan'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $permohonan = \App\Models\PermohonanBenih::with('benih')->findOrFail($id);

        $dataUpdate = [
            'status_pengambilan'  => $request->status_pengambilan,
            'tanggal_pengambilan' => $request->tanggal_pengambilan,
        ];

        // 📎 Ganti file bukti pengambilan kalau ada file baru
        if ($request->hasFile('bukti_pengambilan')) {
            if ($permohonan->bukti_pengambilan && \Storage::disk('public')->exists($permohonan->bukti_pengambilan)) {
                \Storage::disk('public')->delete($permohonan->bukti_pengambilan);
            }

            $path = $request->file('bukti_pengambilan')->store('bukti_pengambilan', 'public');
            $dataUpdate['bukti_pengambilan'] = $path;
        }

        // ============================================
        // 🌱 LOGIKA UPDATE STOK BENIH
        // ============================================

        $benih = $permohonan->benih;

        // 1️⃣ Jika status baru = "Selesai" dan sebelumnya belum "Selesai"
        if (
            $request->status_pengambilan === 'Selesai' &&
            $permohonan->status_pengambilan !== 'Selesai' &&
            $benih
        ) {
            if ($benih->stok < $permohonan->jumlah_disetujui) {
                return back()->with('error', 'Stok benih tidak mencukupi untuk diserahkan.');
            }

            // Simpan stok lama sebelum perubahan
            $stokLama = $benih->stok;

            // Kurangi stok
            $benih->stok -= $permohonan->jumlah_disetujui;
            $benih->save();

            // Catat ke riwayat stok (stok keluar)
            \App\Models\RiwayatStok::create([
                'benih_id'   => $benih->id,
                'tipe'       => 'Keluar',
                'jumlah'     => $permohonan->jumlah_disetujui,
                'stok_awal'  => $stokLama,
                'stok_akhir' => $benih->stok,
                'keterangan' => 'Pengambilan benih oleh ' . ($permohonan->nama ?? 'Pemohon'),
                'admin_id'   => \Auth::id(),
            ]);

            $dataUpdate['tanggal_selesai'] = now();
            $dataUpdate['tanggal_dibatalkan'] = null;
        }

        // 2️⃣ Jika status baru = "Dibatalkan" dan sebelumnya belum "Dibatalkan"
        elseif (
            $request->status_pengambilan === 'Dibatalkan' &&
            $permohonan->status_pengambilan !== 'Dibatalkan' &&
            $benih
        ) {
            // Simpan stok lama sebelum perubahan
            $stokLama = $benih->stok;

            // Kembalikan stok (jika sebelumnya sudah disetujui)
            if ($permohonan->jumlah_disetujui > 0) {
                $benih->stok += $permohonan->jumlah_disetujui;
                $benih->save();

                // Catat riwayat stok masuk
                \App\Models\RiwayatStok::create([
                    'benih_id'   => $benih->id,
                    'tipe'       => 'Masuk',
                    'jumlah'     => $permohonan->jumlah_disetujui,
                    'stok_awal'  => $stokLama,
                    'stok_akhir' => $benih->stok,
                    'keterangan' => 'Stok dikembalikan karena pembatalan permohonan #' . $permohonan->id,
                    'admin_id'   => \Auth::id(),
                ]);
            }

            $dataUpdate['tanggal_dibatalkan'] = now();
            $dataUpdate['tanggal_selesai'] = null;
        }

        // 3️⃣ Jika status baru = "Belum Diambil" (reset manual)
        elseif ($request->status_pengambilan === 'Belum Diambil') {
            $dataUpdate['tanggal_selesai'] = null;
            $dataUpdate['tanggal_dibatalkan'] = null;
        }

        // ============================================
        // 🔄 Update ke database
        // ============================================

        $permohonan->update($dataUpdate);

        return back()->with('success', 'Status pengambilan dan stok benih berhasil diperbarui.');
    }





    /**
     * Auto cancel pembayaran setelah lewat batas_pembayaran (7 hari) dari disetujui.
     * - Hanya untuk permohonan berbayar
     * - status masih Disetujui
     * - status_pembayaran masih Menunggu / Menunggu Verifikasi
     */
    public function autoCancel()
    {
        $now = Carbon::now()->startOfDay();

        $list = PermohonanBenih::where('status', 'Disetujui')
            ->where('tipe_pembayaran', 'Berbayar')
            ->whereIn('status_pembayaran', ['Menunggu', 'Menunggu Verifikasi'])
            ->whereDate('batas_pembayaran', '<', $now)
            ->get();

        foreach ($list as $permohonan) {
            $permohonan->update([
                'status'             => 'Dibatalkan',
                'status_pembayaran'  => 'Gagal',
                'tanggal_dibatalkan' => now(),
            ]);

            KeteranganPermohonan::create([
                'permohonan_id'      => $permohonan->id,
                'admin_id'           => Auth::id() ?? null,
                'jenis_keterangan'   => 'Ditolak', // enum tidak punya "Dibatalkan", jadi pakai Ditolak + alasan jelas
                'isi_keterangan'     => 'Permohonan dibatalkan otomatis karena pembayaran tidak dilakukan dalam 7 hari sejak surat persetujuan.',
                'tanggal_keterangan' => now(),
            ]);
        }

        return back()->with('info', 'Pemeriksaan otomatis selesai. Permohonan berbayar yang melewati batas 7 hari tanpa pembayaran telah dibatalkan.');
    }
    /**
     * Verifikasi pembayaran oleh admin:
     * - set status_pembayaran = Berhasil / Gagal
     * - isi catatan_pembayaran_admin
     */
    public function verifikasiPembayaran(Request $request, $id)
    {
        $permohonan = PermohonanBenih::findOrFail($id);

        if ($permohonan->tipe_pembayaran !== 'Berbayar') {
            return back()->with('error', 'Permohonan ini tidak memerlukan verifikasi pembayaran.');
        }

        if ($permohonan->status !== 'Disetujui') {
            return back()->with('error', 'Verifikasi pembayaran hanya dapat dilakukan untuk permohonan yang sudah disetujui.');
        }

        if (! $permohonan->bukti_pembayaran) {
            return back()->with('error', 'Belum ada bukti pembayaran yang diunggah oleh pemohon.');
        }

        $request->validate([
            'status_pembayaran'       => 'required|in:Berhasil,Gagal',
            'catatan_pembayaran_admin' => 'nullable|string|max:500',
        ]);

        $permohonan->update([
            'status_pembayaran'        => $request->status_pembayaran,
            'catatan_pembayaran_admin' => $request->catatan_pembayaran_admin,
            'tanggal_verifikasi_pembayaran' => now(),
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
// ===================== GRAFIK PENJUALAN =====================
public function laporanStok(Request $request)
{
    $stokBenih = \App\Models\Benih::with('jenisTanaman')->get();

    $startDate = $request->input('start_date');
    $endDate   = $request->input('end_date');

    // Query dasar riwayat
    $riwayatQuery = \App\Models\RiwayatStok::query();

    if ($startDate && $endDate) {
        $riwayatQuery->whereBetween('created_at', [
            \Carbon\Carbon::parse($startDate)->startOfDay(),
            \Carbon\Carbon::parse($endDate)->endOfDay(),
        ]);
    }

    // Default: urut terbaru & paginate 10 per halaman
    $riwayat = $riwayatQuery
        ->orderByDesc('created_at')
        ->paginate(10)
        ->withQueryString(); // supaya start_date & end_date ikut di URL

    // 🔹 Kalau request dari AJAX, kirim HTML tbody & pagination sebagai JSON
    if ($request->ajax()) {
        $tbody = view('admin.verifikator.partials.tabel_riwayat_stok', compact('riwayat'))->render();
        $pagination = view('admin.verifikator.partials.riwayat_stok_pagination', compact('riwayat'))->render();

        return response()->json([
            'tbody'      => $tbody,
            'pagination' => $pagination,
        ]);
    }

    // 🔹 Request normal (bukan AJAX) → kirim view utama
    return view('admin.verifikator.laporan_stok', compact(
        'stokBenih',
        'riwayat',
        'startDate',
        'endDate'
    ));
}




public function exportStokExcel(Request $request)
{
    $data = $this->getFilteredRiwayatStok($request);

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\LaporanStokExport(
            $data['riwayat'],
            $data['startDate'] ?? null,
            $data['endDate'] ?? null
        ),
        'laporan_riwayat_stok.xlsx'
    );
}



public function exportStokPdf(Request $request)
{
    $data = $this->getFilteredRiwayatStok($request);
    $pdf = Pdf::loadView('admin.verifikator.pdf.laporan_stok', $data);
    return $pdf->download('laporan_riwayat_stok.pdf');
}

/**
 * Ambil data riwayat stok sesuai filter tanggal
 */
private function getFilteredRiwayatStok(Request $request)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    if ($startDate && $endDate) {
        $riwayat = \App\Models\RiwayatStok::whereBetween('created_at', [
                \Carbon\Carbon::parse($startDate)->startOfDay(),
                \Carbon\Carbon::parse($endDate)->endOfDay()
            ])
            ->orderByDesc('created_at')
            ->get();
    } else {
        $riwayat = \App\Models\RiwayatStok::latest()->limit(10)->get();
    }

    return compact('riwayat', 'startDate', 'endDate');
}





// ===================== LAPORAN PENJUALAN =====================


private function buildLaporanPenjualanData(Request $request): array
{
    $mode  = $request->get('mode', 'hari');
    $year  = (int) $request->get('year', now()->year);
    $month = (int) $request->get('month', now()->month);
    $tipe  = $request->get('tipe'); // filter Gratis / Berbayar / Semua

    // ===================== QUERY DASAR =====================
    $baseQuery = \App\Models\PermohonanBenih::with(['benih.jenisTanaman', 'user']);

    // Logika filter tipe & status
    if ($tipe === 'Gratis') {
        $baseQuery->where('tipe_pembayaran', 'Gratis');
    } elseif ($tipe === 'Berbayar') {
        $baseQuery->where('tipe_pembayaran', 'Berbayar')
                  ->where('status_pembayaran', 'Berhasil');
    } else {
        $baseQuery->where(function ($q) {
            $q->where('tipe_pembayaran', 'Gratis')
              ->orWhere(function ($q2) {
                  $q2->where('tipe_pembayaran', 'Berbayar')
                     ->where('status_pembayaran', 'Berhasil');
              });
        });
    }

    // ===================== GRAFIK (chartQuery) =====================
    $chartQuery = (clone $baseQuery);

    if ($mode === 'tahun') {
        $penjualanChart = $chartQuery
            ->selectRaw('YEAR(created_at) as label, SUM(nominal_pembayaran) as total')
            ->groupByRaw('YEAR(created_at)')
            ->orderByRaw('YEAR(created_at)')
            ->get();
    } elseif ($mode === 'bulan') {
        $penjualanChart = $chartQuery
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as label, SUM(nominal_pembayaran) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get()
            ->map(fn($r) => tap($r, fn($row) =>
                $row->label = \Carbon\Carbon::create()->month($row->label)->translatedFormat('F')
            ));
    } elseif ($mode === 'hari') {
        $penjualanChart = $chartQuery
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DAY(created_at) as label, SUM(nominal_pembayaran) as total')
            ->groupByRaw('DAY(created_at)')
            ->orderByRaw('DAY(created_at)')
            ->get()
            ->map(fn($r) => tap($r, fn($row) =>
                $row->label = str_pad($row->label, 2, '0', STR_PAD_LEFT)
                    . ' ' . \Carbon\Carbon::create()->month($month)->translatedFormat('M')
            ));
    } else {
        $penjualanChart = collect();
    }

    // ===================== DATA PENJUALAN =====================
    $dataQuery = (clone $baseQuery);

    // Range waktu untuk data & tabel (supaya match sama grafik)
    if ($mode === 'bulan') {
        $dataQuery->whereYear('created_at', $year);
    } elseif ($mode === 'hari') {
        $dataQuery->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
    }

    // Untuk ringkasan & grafik per benih → group by benih_id
    $penjualan = (clone $dataQuery)->get()->groupBy('benih_id');

    $totalPendapatan = (clone $dataQuery)->sum('nominal_pembayaran');
    $totalTransaksi  = (clone $dataQuery)->count();

    // ===================== DATA TABEL (TRANSaksi) + PAGINATION =====================
    // 10 transaksi terakhir (sesuai filter) per halaman
    $penjualanTable = (clone $dataQuery)
        ->orderByDesc('created_at')
        ->paginate(10)
        ->withQueryString();

    // ===================== TOP 5 BENIH TERLARIS =====================
    $topBenih = $penjualan
        ->map(function ($group, $benihId) {
            $benih         = $group->first()->benih ?? null;
            $jumlahTerjual = $group->sum('jumlah_disetujui');
            $pendapatan    = $group->sum('nominal_pembayaran');

            return (object) [
                'benih_id'       => $benihId,
                'benih'          => $benih,
                'jumlah_terjual' => $jumlahTerjual,
                'pendapatan'     => $pendapatan,
            ];
        })
        ->sortByDesc('pendapatan')
        ->take(5)
        ->values();

    return compact(
        'penjualanChart',
        'penjualan',        // group per benih (untuk ringkasan & grafik per benih & export)
        'penjualanTable',   // transaksi per permohonan (untuk tabel + pagination)
        'totalPendapatan',
        'totalTransaksi',
        'mode',
        'year',
        'month',
        'tipe',
        'topBenih',
    );
}

public function laporanPenjualan(Request $request)
{
    $data = $this->buildLaporanPenjualanData($request);

    return view('admin.verifikator.laporan_penjualan', $data);
}

public function exportPenjualanExcel(Request $request)
{
    // Ambil semua data yang sama dengan tampilan di layar
    $data = $this->buildLaporanPenjualanData($request);

    return Excel::download(
        new LaporanPenjualanExport($data),
        'laporan_penjualan_benih.xlsx'
    );
}


public function exportPenjualanPdf(Request $request)
{
    $data = $this->buildLaporanPenjualanData($request);

    // bisa tambahkan batas aman misal kalau row terlalu banyak
    if ($data['penjualan']->count() > 1000) {
        return back()->with('error', 'Data terlalu banyak untuk PDF, gunakan export Excel.');
    }

    $pdf = Pdf::loadView('admin.verifikator.pdf.laporan_penjualan', $data)
        ->setPaper('a4', 'landscape');

    return $pdf->download('laporan_penjualan_benih.pdf');
}








   
}
