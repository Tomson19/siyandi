<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PermohonanBenih;
use App\Models\JenisTanaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Benih;

class PermohonanController extends Controller
{
    /**
     * Menampilkan daftar permohonan milik pemohon
     */
    public function index()
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())
            ->with([
                'jenisTanaman',
                'benih',
                'keterangan' => fn ($q) => $q->latest(),
            ])
            ->latest()
            ->get();

            $jenisTanaman = JenisTanaman::all();

        return view('pemohon.permohonan.index', compact('permohonan', 'jenisTanaman'));
    }

    /**
     * Form tambah permohonan baru
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->is_locked ?? false) {
            return redirect()->route('pemohon.permohonan.index')
                ->with('error', 'Akun Anda telah dikunci karena belum mengunggah bukti tanam. Anda tidak dapat membuat permohonan baru.');
        }

        $jenisTanaman = JenisTanaman::all();
        return view('pemohon.permohonan.create', compact('jenisTanaman'));
    }

    /**
     * Simpan permohonan baru + generate surat PDF otomatis
     */


public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->is_locked ?? false) {
            return redirect()->route('pemohon.permohonan.index')
                ->with('error', 'Akun Anda telah dikunci karena belum mengunggah bukti tanam. Anda tidak dapat membuat permohonan baru.');
        }

        $request->validate([
            'nama'              => 'required|string|max:255',
            'nik'               => 'required|string|max:20',
            'alamat'            => 'required|string',
            'no_telp'           => 'required|string|max:20',
            'jenis_tanaman_id'  => 'required|exists:jenis_tanaman,id',
            'jenis_benih'       => 'required|in:Biji,Siap Tanam',
            'tipe_pembayaran'   => 'required|in:Berbayar,Gratis',
            'jumlah_tanaman'    => 'required|integer|min:1',
            'luas_area'         => 'required|numeric|min:0.1',
            'latitude'          => 'nullable|string',
            'longitude'         => 'nullable|string',
        ]);

        // 🧩 Cari data benih yang cocok otomatis
        $benih = Benih::where('jenis_tanaman_id', $request->jenis_tanaman_id)
            ->where('jenis_benih', $request->jenis_benih)
            ->where('tipe_pembayaran', $request->tipe_pembayaran)
            ->first();

        if (! $benih) {
            return back()->with('error', 'Data benih tidak ditemukan. Pastikan master data benih untuk kombinasi ini sudah dibuat.');
        }

        // 🧾 Buat permohonan baru
        $permohonan = PermohonanBenih::create([
            'user_id'            => $user->id,
            'benih_id'           => $benih->id, // ✅ otomatis
            'nama'               => $request->nama,
            'nik'                => $request->nik,
            'alamat'             => $request->alamat,
            'no_telp'            => $request->no_telp,
            'jenis_tanaman_id'   => $request->jenis_tanaman_id,
            'jenis_benih'        => $request->jenis_benih,
            'tipe_pembayaran'    => $request->tipe_pembayaran,
            'jumlah_tanaman'     => $request->jumlah_tanaman,
            'luas_area'          => $request->luas_area,
            'latitude'           => $request->latitude,
            'longitude'          => $request->longitude,
            'nominal_pembayaran' => $benih->harga, // 💰 ambil harga dari tabel benih
            'status'             => 'Menunggu Dokumen',
            'status_pengambilan' => 'Belum Diambil',
            'tanggal_diajukan'   => Carbon::now(),
        ]);

        // 🧾 Generate PDF
        Storage::disk('public')->makeDirectory('surat_permohonan');
        Storage::disk('public')->makeDirectory('surat_pernyataan');

        $viewPermohonan = $permohonan->tipe_pembayaran === 'Berbayar'
            ? 'pdf.surat_permohonan_berbayar'
            : 'pdf.surat_permohonan_gratis';

        $pdfPermohonan = Pdf::loadView($viewPermohonan, compact('permohonan'));
        $filenamePermohonan = 'surat_permohonan_' . $permohonan->id . '.pdf';
        Storage::disk('public')->put("surat_permohonan/{$filenamePermohonan}", $pdfPermohonan->output());

        $pdfPernyataan = Pdf::loadView('pdf.surat_pernyataan', compact('permohonan'));
        $filenamePernyataan = 'surat_pernyataan_' . $permohonan->id . '.pdf';
        Storage::disk('public')->put("surat_pernyataan/{$filenamePernyataan}", $pdfPernyataan->output());

        $permohonan->update([
            'scan_surat_permohonan' => "surat_permohonan/{$filenamePermohonan}",
            'scan_surat_pernyataan' => "surat_pernyataan/{$filenamePernyataan}",
        ]);

        return redirect()->route('pemohon.permohonan.show', $permohonan->id)
            ->with('success', 'Permohonan berhasil dibuat. Surat permohonan dan surat pernyataan telah dihasilkan.');
    }



    /**
     * Form upload dokumen
     */
    public function uploadForm($id)
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())->findOrFail($id);

        if (in_array($permohonan->status, ['Disetujui', 'Ditolak', 'Dibatalkan'])) {
            return back()->with('error', 'Dokumen tidak dapat diunggah karena permohonan sudah diverifikasi atau dibatalkan.');
        }

        return view('pemohon.permohonan.upload', compact('permohonan'));
    }

    /**
     * Simpan / perbarui dokumen yang diupload pemohon
     */
    public function uploadStore(Request $request, $id)
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())->findOrFail($id);

        if (in_array($permohonan->status, ['Disetujui', 'Ditolak', 'Dibatalkan'])) {
            return back()->with('error', 'Dokumen tidak dapat diunggah karena permohonan sudah diverifikasi atau dibatalkan.');
        }

        $request->validate([
            'scan_surat_permohonan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'scan_surat_pernyataan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'scan_kk'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'scan_ktp'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'scan_surat_tanah'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'mimes' => 'Format file tidak valid (gunakan PDF, JPG, JPEG, atau PNG).',
            'max'   => 'Ukuran file maksimal 2 MB per dokumen.',
        ]);

        Storage::disk('public')->makeDirectory('dokumen');
        $data = [];

        foreach (['scan_surat_permohonan', 'scan_surat_pernyataan', 'scan_kk', 'scan_ktp', 'scan_surat_tanah'] as $field) {
            if ($request->hasFile($field)) {
                if ($permohonan->$field && Storage::disk('public')->exists($permohonan->$field)) {
                    Storage::disk('public')->delete($permohonan->$field);
                }
                $data[$field] = $request->file($field)->store('dokumen', 'public');
            }
        }

        if (!empty($data)) {
            $data['status'] = 'Sedang Diverifikasi';
        }

        $permohonan->update($data);

        return redirect()->route('pemohon.permohonan.index')
            ->with('success', 'Dokumen berhasil diunggah. Menunggu verifikasi admin.');
    }

    /**
     * Detail permohonan
     */
    public function show($id)
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())
            ->with(['jenisTanaman', 'benih', 'keterangan' => fn ($q) => $q->latest()])
            ->findOrFail($id);

        return view('pemohon.permohonan.show', compact('permohonan'));
    }

    /**
     * Form edit data permohonan
     */
    public function edit($id)
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())->findOrFail($id);

        if (in_array($permohonan->status, ['Disetujui', 'Ditolak', 'Dibatalkan'])) {
            return back()->with('error', 'Data tidak dapat diubah karena sudah diverifikasi atau dibatalkan.');
        }

        $jenisTanaman = JenisTanaman::all();
        return view('pemohon.permohonan.edit', compact('permohonan', 'jenisTanaman'));
    }

    /**
     * Update data permohonan + regenerasi surat permohonan PDF
     */
     public function update(Request $request, $id)
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())->findOrFail($id);

        if (in_array($permohonan->status, ['Disetujui', 'Ditolak', 'Dibatalkan'])) {
            return back()->with('error', 'Data tidak dapat diubah karena sudah diverifikasi atau dibatalkan.');
        }

        $request->validate([
            'nama'              => 'nullable|string|max:255',
            'nik'               => 'nullable|string|max:20',
            'alamat'            => 'nullable|string',
            'no_telp'           => 'nullable|string|max:20',
            'jenis_tanaman_id'  => 'nullable|exists:jenis_tanaman,id',
            'jenis_benih'       => 'nullable|in:Biji,Siap Tanam',
            'tipe_pembayaran'   => 'nullable|in:Berbayar,Gratis',
            'jumlah_tanaman'    => 'nullable|integer|min:1',
            'luas_area'         => 'nullable|numeric|min:0.1',
            'latitude'          => 'nullable|string',
            'longitude'         => 'nullable|string',
        ]);

        // 🧩 Update otomatis benih_id dan nominal_pembayaran kalau kombinasi berubah
        $benih = Benih::where('jenis_tanaman_id', $request->jenis_tanaman_id)
            ->where('jenis_benih', $request->jenis_benih)
            ->where('tipe_pembayaran', $request->tipe_pembayaran)
            ->first();

        if (! $benih) {
            return back()->with('error', 'Data benih tidak ditemukan. Pastikan kombinasi jenis tanaman, jenis benih, dan tipe pembayaran tersedia.');
        }

        $permohonan->update([
            'nama'               => $request->nama,
            'nik'                => $request->nik,
            'alamat'             => $request->alamat,
            'no_telp'            => $request->no_telp,
            'jenis_tanaman_id'   => $request->jenis_tanaman_id,
            'jenis_benih'        => $request->jenis_benih,
            'tipe_pembayaran'    => $request->tipe_pembayaran,
            'jumlah_tanaman'     => $request->jumlah_tanaman,
            'luas_area'          => $request->luas_area,
            'latitude'           => $request->latitude,
            'longitude'          => $request->longitude,
            'benih_id'           => $benih->id, // ✅ update otomatis
            'nominal_pembayaran' => $benih->harga, // 💰 ambil harga baru
            'status'             => 'Menunggu Dokumen',
            'status_pengambilan' => 'Belum Diambil',
        ]);

        // 🔄 Regenerasi surat PDF revisi
        Storage::disk('public')->makeDirectory('surat_permohonan');
        Storage::disk('public')->makeDirectory('surat_pernyataan');

        if ($permohonan->scan_surat_permohonan && Storage::disk('public')->exists($permohonan->scan_surat_permohonan)) {
            Storage::disk('public')->delete($permohonan->scan_surat_permohonan);
        }
        if ($permohonan->scan_surat_pernyataan && Storage::disk('public')->exists($permohonan->scan_surat_pernyataan)) {
            Storage::disk('public')->delete($permohonan->scan_surat_pernyataan);
        }

        $viewPermohonan = $permohonan->tipe_pembayaran === 'Berbayar'
            ? 'pdf.surat_permohonan_berbayar'
            : 'pdf.surat_permohonan_gratis';

        $pdfPermohonan = Pdf::loadView($viewPermohonan, compact('permohonan'));
        $filenamePermohonan = 'surat_permohonan_' . $permohonan->id . '_revisi.pdf';
        Storage::disk('public')->put("surat_permohonan/{$filenamePermohonan}", $pdfPermohonan->output());

        $pdfPernyataan = Pdf::loadView('pdf.surat_pernyataan', compact('permohonan'));
        $filenamePernyataan = 'surat_pernyataan_' . $permohonan->id . '_revisi.pdf';
        Storage::disk('public')->put("surat_pernyataan/{$filenamePernyataan}", $pdfPernyataan->output());

        $permohonan->update([
            'scan_surat_permohonan' => "surat_permohonan/{$filenamePermohonan}",
            'scan_surat_pernyataan' => "surat_pernyataan/{$filenamePernyataan}",
        ]);

        return redirect()->route('pemohon.permohonan.show', $permohonan->id)
            ->with('warning', 'Data permohonan telah diperbarui. Silakan unduh ulang surat dan upload ulang dokumen pendukung.');
    }

    /**
     * Hapus permohonan
     */
    public function destroy($id)
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())->findOrFail($id);

        if (in_array($permohonan->status, ['Disetujui', 'Ditolak', 'Dibatalkan'])) {
            return back()->with('error', 'Data tidak dapat dihapus karena sudah diverifikasi atau dibatalkan.');
        }

        foreach ([
            'scan_surat_permohonan',
            'scan_surat_pernyataan',
            'scan_kk',
            'scan_ktp',
            'scan_surat_tanah',
        ] as $field) {
            if ($permohonan->$field && Storage::disk('public')->exists($permohonan->$field)) {
                Storage::disk('public')->delete($permohonan->$field);
            }
        }

        $permohonan->delete();

        return redirect()->route('pemohon.permohonan.index')
            ->with('success', 'Permohonan berhasil dihapus.');
    }
        /**
     * Form upload bukti pembayaran oleh pemohon.
     */
    public function pembayaranForm($id)
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($permohonan->tipe_pembayaran !== 'Berbayar') {
            return back()->with('error', 'Permohonan ini tidak memerlukan pembayaran.');
        }

        if ($permohonan->status !== 'Disetujui') {
            return back()->with('error', 'Pembayaran hanya dapat dilakukan untuk permohonan yang sudah disetujui.');
        }

        if (! in_array($permohonan->status_pembayaran, ['Menunggu', 'Menunggu Verifikasi', 'Gagal', null])) {
            return back()->with('error', 'Status pembayaran tidak valid untuk diunggah.');
        }

        if ($permohonan->batas_pembayaran && now()->startOfDay()->gt($permohonan->batas_pembayaran)) {
            return back()->with('error', 'Batas waktu pembayaran telah berakhir. Permohonan Anda mungkin telah dibatalkan.');
        }

        return view('pemohon.permohonan.pembayaran', compact('permohonan'));
    }

        /**
     * Simpan / perbarui bukti pembayaran oleh pemohon.
     */
    public function pembayaranStore(Request $request, $id)
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($permohonan->tipe_pembayaran !== 'Berbayar') {
            return back()->with('error', 'Permohonan ini tidak memerlukan pembayaran.');
        }

        if ($permohonan->status !== 'Disetujui') {
            return back()->with('error', 'Pembayaran hanya dapat dilakukan untuk permohonan yang sudah disetujui.');
        }

        if ($permohonan->batas_pembayaran && now()->startOfDay()->gt($permohonan->batas_pembayaran)) {
            return back()->with('error', 'Batas waktu pembayaran telah berakhir. Permohonan Anda mungkin telah dibatalkan.');
        }

        $request->validate([
            'bukti_pembayaran'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'pesan_pemohon_pembayaran'  => 'nullable|string|max:500',
        ], [
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diunggah.',
            'bukti_pembayaran.mimes'    => 'Format bukti pembayaran harus JPG, JPEG, PNG, atau PDF.',
            'bukti_pembayaran.max'      => 'Ukuran maksimal bukti pembayaran 4 MB.',
        ]);

        Storage::disk('public')->makeDirectory('pembayaran');

        // Hapus bukti lama jika ada
        if ($permohonan->bukti_pembayaran && Storage::disk('public')->exists($permohonan->bukti_pembayaran)) {
            Storage::disk('public')->delete($permohonan->bukti_pembayaran);
        }

        $path = $request->file('bukti_pembayaran')->store('pembayaran', 'public');

        $permohonan->update([
            'bukti_pembayaran'         => $path,
            'pesan_pemohon_pembayaran' => $request->pesan_pemohon_pembayaran,
            'status_pembayaran'        => 'Menunggu Verifikasi',
            // reset catatan admin agar verifikasi berikutnya jelas
            'catatan_pembayaran_admin' => null,
        ]);

        return redirect()->route('pemohon.permohonan.show', $permohonan->id)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }

        /**
     * Form upload bukti tanam bibit di lahan.
     */
    public function buktiTanamForm($id)
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($permohonan->status_pengambilan !== 'Selesai') {
            return back()->with('error', 'Bukti tanam hanya dapat diunggah setelah bibit diambil.');
        }

        return view('pemohon.permohonan.bukti_tanam', compact('permohonan'));
    }
    /**
     * Simpan bukti tanam oleh pemohon.
     */
    public function buktiTanamStore(Request $request, $id)
    {
        $permohonan = PermohonanBenih::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($permohonan->status_pengambilan !== 'Selesai') {
            return back()->with('error', 'Bukti tanam hanya dapat diunggah setelah bibit diambil.');
        }

        $request->validate([
            'bukti_tanam' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ], [
            'bukti_tanam.required' => 'Bukti tanam wajib diunggah.',
            'bukti_tanam.mimes'    => 'Format bukti tanam harus JPG, JPEG, PNG, atau PDF.',
            'bukti_tanam.max'      => 'Ukuran maksimal bukti tanam 4 MB.',
        ]);

        Storage::disk('public')->makeDirectory('bukti_tanam');

        if ($permohonan->bukti_tanam && Storage::disk('public')->exists($permohonan->bukti_tanam)) {
            Storage::disk('public')->delete($permohonan->bukti_tanam);
        }

        $path = $request->file('bukti_tanam')->store('bukti_tanam', 'public');

        $permohonan->update([
            'bukti_tanam'   => $path,
            'tanggal_tanam' => now(),
        ]);

        return redirect()->route('pemohon.permohonan.show', $permohonan->id)
            ->with('success', 'Bukti tanam berhasil diunggah. Terima kasih telah menanam bibit.');
    }


}
