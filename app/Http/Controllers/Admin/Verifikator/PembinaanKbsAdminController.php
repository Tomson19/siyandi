<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\PembinaanKebunBenihSumber;
use App\Models\PembinaanSesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembinaanKbsAdminController extends Controller
{
    /**
     * Halaman utama admin verifikator untuk KBS:
     * - Daftar pengajuan pembinaan KBS
     * - Daftar sesi pembinaan (dipakai untuk KBS)
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('q');

        // Daftar pengajuan KBS dari pemohon
        $query = PembinaanKebunBenihSumber::with(['user', 'sesi', 'jenisTanaman'])
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('lokasi_kebun', 'like', "%{$search}%")
                    ->orWhereHas('jenisTanaman', function ($jt) use ($search) {
                        $jt->where('nama_tanaman', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $pembinaanList = $query->paginate(10)->withQueryString();

        // Daftar sesi untuk KBS
        // Urutkan: dijadwalkan → selesai → batal
        $sesiList = PembinaanSesi::withCount('pesertaKbs')
            ->where('jenis', 'kbs')
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

        return view('admin.verifikator.pembinaan_kbs.index', compact(
            'pembinaanList',
            'sesiList',
            'status',
            'search'
        ));
    }

    /**
     * Buat sesi pembinaan baru + pasang beberapa pengajuan KBS ke sesi tersebut.
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
            'pembinaan_ids.*'  => 'integer|exists:pembinaan_kebun_benih_sumber,id',
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
            'jenis'       => 'kbs',
            'nama_sesi'   => $data['nama_sesi'] ?? null,
            'tanggal'     => $data['tanggal'],
            'jam_mulai'   => $data['jam_mulai'],
            'jam_selesai' => $data['jam_selesai'],
            'meet_link'   => $data['meet_link'],
            'materi_path' => $materiPath,
            'status'      => 'dijadwalkan',
            'created_by'  => Auth::id(),
        ]);

        // Update semua pengajuan KBS yang dipilih → pasang ke sesi & status = dijadwalkan
        PembinaanKebunBenihSumber::whereIn('id', $data['pembinaan_ids'])
            ->update([
                'pembinaan_sesi_id' => $sesi->id,
                'status'            => 'dijadwalkan',
                'alasan_status'     => null,
            ]);

        return redirect()
            ->route('admin.verifikator.pembinaan_kbs.index')
            ->with('success', 'Sesi pembinaan KBS berhasil dibuat dan pengajuan sudah dijadwalkan.');
    }

    /**
     * Detail satu sesi (khusus tampilan peserta KBS).
     */
    public function showSesi(PembinaanSesi $sesi)
    {
        $sesi->load([
            'pesertaKbs.user',  // peserta kebun benih sumber
            'creator',
        ])->loadCount('pesertaKbs');

        return view('admin.verifikator.pembinaan_kbs.show_sesi', compact('sesi'));
    }

    /**
     * Update data sesi KBS:
     * - Edit jadwal (nama_sesi, tanggal, jam, meet_link, materi)
     * - Ubah status sesi + bukti pembinaan
     *
     * Tidak mengubah status peserta di sini.
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

        return redirect()
            ->route('admin.verifikator.pembinaan_kbs.sesi.show', $sesi)
            ->with('success', 'Data sesi pembinaan KBS berhasil diperbarui.');
    }

    /**
     * Ubah status pembinaan untuk satu peserta KBS (satu pengajuan).
     * Route: admin.verifikator.pembinaan-kbs.peserta.status
     */
    public function updatePesertaStatus(Request $request, PembinaanKebunBenihSumber $pembinaanKbs)
    {
        $data = $request->validate([
            'status'        => 'required|in:menunggu_jadwal,dijadwalkan,selesai,batal',
            'alasan_status' => 'nullable|string',
        ]);

        $pembinaanKbs->status        = $data['status'];
        $pembinaanKbs->alasan_status = $data['alasan_status'] ?? null;
        $pembinaanKbs->save();

        return back()->with('success', 'Status pembinaan kebun benih sumber berhasil diperbarui.');
    }
}
