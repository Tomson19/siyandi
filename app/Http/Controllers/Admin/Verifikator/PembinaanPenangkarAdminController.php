<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\PembinaanPenangkar;
use App\Models\PembinaanSesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembinaanPenangkarAdminController extends Controller
{
    /**
     * Halaman utama admin:
     * - Daftar pengajuan pembinaan
     * - Daftar sesi pembinaan
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('q');

        // Daftar pengajuan pemohon
        $query = PembinaanPenangkar::with(['user', 'sesi'])
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_penangkar', 'like', "%{$search}%")
                    ->orWhere('nama_penanggung_jawab', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $pembinaanList = $query->paginate(10)->withQueryString();

        // Daftar sesi:
        // - dijadwalkan → paling atas
        // - selesai
        // - batal
        $sesiList = PembinaanSesi::withCount('peserta')
            ->where('jenis', 'penangkar')
            ->orderByRaw("
                CASE 
                    WHEN status = 'dijadwalkan' THEN 1
                    WHEN status = 'selesai'     THEN 2
                    WHEN status = 'batal'       THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->paginate(10, ['*'], 'sesi_page')
            ->withQueryString();

        return view('admin.verifikator.pembinaan.index', compact(
            'pembinaanList',
            'sesiList',
            'status',
            'search'
        ));
    }

    /**
     * Buat sesi pembinaan baru + pasang beberapa pengajuan ke sesi tersebut.
     */
    public function storeSesi(Request $request)
    {
        $data = $request->validate([
            'nama_sesi'        => 'nullable|string|max:255',
            'tanggal'          => 'required|date',
            'jam_mulai'        => 'required',
            'jam_selesai'      => 'required',
            'meet_link'        => 'required|string|max:255',
            'pembinaan_ids'    => 'required|array|min:1',
            'pembinaan_ids.*'  => 'integer|exists:pembinaan_penangkar,id',
            'materi'           => 'nullable|file|max:20480', // 20MB
        ], [
            'pembinaan_ids.required' => 'Pilih minimal satu pengajuan untuk dijadwalkan.',
        ]);

        $materiPath = null;
        if ($request->hasFile('materi')) {
            $materiPath = $request->file('materi')
                ->store('pembinaan/materi', 'public');
        }

        $sesi = PembinaanSesi::create([
            'jenis'       => 'penangkar',
            'nama_sesi'   => $data['nama_sesi'] ?? null,
            'tanggal'     => $data['tanggal'],
            'jam_mulai'   => $data['jam_mulai'],
            'jam_selesai' => $data['jam_selesai'],
            'meet_link'   => $data['meet_link'],
            'materi_path' => $materiPath,
            'status'      => 'dijadwalkan',
            'created_by'  => Auth::id(),
        ]);

        // Update semua pengajuan yang dipilih → dipasang ke sesi & status jadi dijadwalkan
        PembinaanPenangkar::whereIn('id', $data['pembinaan_ids'])
            ->update([
                'pembinaan_sesi_id' => $sesi->id,
                'status'            => 'dijadwalkan',
                'alasan_status'     => null,
            ]);

        return redirect()
            ->route('admin.verifikator.pembinaan.index')
            ->with('success', 'Sesi pembinaan berhasil dibuat dan pengajuan sudah dijadwalkan.');
    }

    /**
     * Detail satu sesi (lihat peserta, dll).
     */
    public function showSesi(PembinaanSesi $sesi)
    {
        $sesi->load([
            'peserta.user',
            'creator',
        ])->loadCount('peserta');

        return view('admin.verifikator.pembinaan.show_sesi', compact('sesi'));
    }

    /**
     * Update data sesi:
     * - Bisa dipakai untuk edit jadwal (nama_sesi, tanggal, jam, meet_link, materi)
     * - Bisa juga dipakai ubah status sesi + bukti pembinaan
     *
     * CATATAN:
     * Di sini TIDAK mengubah status peserta lagi.
     * Status per peserta diatur di method updatePesertaStatus().
     */
    public function updateSesi(Request $request, PembinaanSesi $sesi)
    {
        $rules = [
            'status'          => 'required|in:dijadwalkan,selesai,batal',
            'alasan'          => 'nullable|string',
            'bukti_pembinaan' => 'nullable|file|max:20480',
            'nama_sesi'       => 'nullable|string|max:255',
            'tanggal'         => 'nullable|date',
            'jam_mulai'       => 'nullable',
            'jam_selesai'     => 'nullable',
            'meet_link'       => 'nullable|string|max:255',
            'materi'          => 'nullable|file|max:20480',
        ];

        $data = $request->validate($rules);

        // upload / ganti materi (kalau ada)
        if ($request->hasFile('materi')) {
            if ($sesi->materi_path && Storage::disk('public')->exists($sesi->materi_path)) {
                Storage::disk('public')->delete($sesi->materi_path);
            }
            $data['materi_path'] = $request->file('materi')
                ->store('pembinaan/materi', 'public');
        }

        // upload / ganti bukti pembinaan (kalau ada)
        if ($request->hasFile('bukti_pembinaan')) {
            if ($sesi->bukti_pembinaan_path && Storage::disk('public')->exists($sesi->bukti_pembinaan_path)) {
                Storage::disk('public')->delete($sesi->bukti_pembinaan_path);
            }

            $data['bukti_pembinaan_path'] = $request->file('bukti_pembinaan')
                ->store('pembinaan/bukti', 'public');
        }

        $sesi->update($data);

        // ⚠️ Tidak ada sinkron status ke peserta di sini lagi

        return redirect()
            ->route('admin.verifikator.pembinaan.sesi.show', $sesi)
            ->with('success', 'Data sesi pembinaan berhasil diperbarui.');
    }

    /**
     * Ubah status perizinan satu pengajuan (per pemohon).
     */
    public function updatePerizinan(Request $request, PembinaanPenangkar $pembinaan)
    {
        $data = $request->validate([
            'status_perizinan'  => 'required|in:menunggu,berhasil,dibatalkan',
            'alasan_perizinan'  => 'nullable|string',
        ]);

        // Kalau diset berhasil → pastikan NIB & sertifikat sudah diisi pemohon
        if (
            $data['status_perizinan'] === 'berhasil' &&
            (!$pembinaan->nib || !$pembinaan->no_sertifikat_standar)
        ) {
            return back()
                ->withInput()
                ->with('error', 'Tidak dapat menyetujui perizinan karena data OSS (NIB dan Sertifikat) belum lengkap.');
        }

        // Kalau dibatalkan → sebaiknya ada alasan
        if ($data['status_perizinan'] === 'dibatalkan' && empty($data['alasan_perizinan'])) {
            return back()
                ->withInput()
                ->with('error', 'Harap isi alasan pembatalan perizinan.');
        }

        $pembinaan->update($data);

        return back()->with('success', 'Status perizinan berhasil diperbarui.');
    }

    /**
     * Ubah status pembinaan untuk satu peserta (satu pengajuan).
     * Route: admin.verifikator.pembinaan.peserta.status
     */
    public function updatePesertaStatus(Request $request, PembinaanPenangkar $pembinaan)
    {
        $data = $request->validate([
            'status'         => 'required|in:menunggu_jadwal,dijadwalkan,selesai,batal',
            'alasan_status'  => 'nullable|string',
        ]);

        // Kalau pembinaan dibatalkan
        if ($data['status'] === 'batal') {
            // status_perizinan TIDAK NULL => pakai 'dibatalkan'
            $pembinaan->status_perizinan      = 'dibatalkan';
            $pembinaan->alasan_perizinan      = null;

            // kosongkan data OSS (boleh null karena biasanya nullable)
            $pembinaan->nib                   = null;
            $pembinaan->no_sertifikat_standar = null;
        }

        // Update status pembinaan + alasannya
        $pembinaan->status        = $data['status'];
        $pembinaan->alasan_status = $data['alasan_status'] ?? null;

        $pembinaan->save();

        return back()->with('success', 'Status pembinaan peserta berhasil diperbarui.');
    }
}
