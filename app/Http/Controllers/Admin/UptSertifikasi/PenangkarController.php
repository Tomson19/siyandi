<?php

namespace App\Http\Controllers\Admin\UptSertifikasi;

use App\Http\Controllers\Controller;
use App\Models\Penangkar;
use App\Models\Tanaman;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenangkarController extends Controller
{
    /**
     * Helper untuk ambil kabupaten_id user (kalau ada).
     * Untuk admin_upt_sertifikasi biasanya NULL (bisa lihat semua kabupaten).
     */
    protected function kabupatenId(): ?int
    {
        return Auth::user()->kabupaten_id;
    }

    /**
     * Batasi akses hanya ke data kabupaten miliknya
     * KECUALI kalau user tidak punya kabupaten_id (misal admin_upt_sertifikasi).
     */
    protected function ensureOwned(Penangkar $penangkar): void
    {
        if ($this->kabupatenId() && $penangkar->kabupaten_id != $this->kabupatenId()) {
            abort(403, 'Anda tidak boleh mengakses data kabupaten lain.');
        }
    }

    /**
     * LIST DATA PENANGKAR + FILTER
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // List komoditas untuk filter & form
        $tanamanList = Tanaman::orderBy('nama_tanaman')->get();

        // List kabupaten – untuk admin_upt_sertifikasi bisa pilih semua
        $kabupatenList = Kabupaten::orderBy('nama_kabupaten')->get();

        // Query dasar
        $query = Penangkar::with(['tanaman', 'kabupaten']);

        // 🔹 Batasi data hanya untuk kabupaten user (kalau dia punya kabupaten_id dan BUKAN admin_upt_sertifikasi)
        if (!$user->hasRole('admin_upt_sertifikasi') && $this->kabupatenId()) {
            $query->where('kabupaten_id', $this->kabupatenId());
        }

        // 🔍 Filter komoditas
        if ($request->filled('tanaman_id')) {
            $query->where('tanaman_id', $request->tanaman_id);
        }

        // 🔍 Filter kabupaten (untuk admin_upt_sertifikasi)
        if ($request->filled('kabupaten_id')) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }

        // 🔍 Filter keyword (nama penangkar / desa / kecamatan / jalan)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_penangkar', 'like', "%{$q}%")
                    ->orWhere('desa', 'like', "%{$q}%")
                    ->orWhere('kecamatan', 'like', "%{$q}%")
                    ->orWhere('jalan', 'like', "%{$q}%");
            });
        }

        // 🔢 Pagination 10 baris
        $penangkarList = $query->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query()); // supaya filter tetap kebawa di pagination

        return view('admin.upt_sertifikasi.penangkar.index', [ // kalau view-mu sudah dipindah, ganti path ini saja
            'penangkarList'      => $penangkarList,
            'tanamanList'        => $tanamanList,
            'kabupatenList'      => $kabupatenList,
            'selectedTanamanId'  => $request->tanaman_id,
            'selectedKabupatenId' => $request->kabupaten_id,
            'search'             => $request->q,
        ]);
    }

    /**
     * FORM TAMBAH (kalau kamu pakai halaman terpisah, bukan modal di index)
     */
    public function create()
    {
        $tanamanList   = Tanaman::orderBy('nama_tanaman')->get();
        $kabupatenList = Kabupaten::orderBy('nama_kabupaten')->get();

        return view('admin.upt_sertifikasi.penangkar.create', [ // atau pakai view lain sesuai strukturmu
            'tanamanList'   => $tanamanList,
            'kabupatenList' => $kabupatenList,
        ]);
    }

    /**
     * SIMPAN DATA BARU
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'tanaman_id'     => 'required|exists:tanaman,id',
            'kabupaten_id'   => 'required|exists:kabupaten,id',
            'nama_penangkar' => 'required|string|max:255',

            // field baru
            'nib_dan_tanggal'                         => 'nullable|string|max:255',
            'sertifikat_izin_usaha_nomor_dan_tanggal' => 'nullable|string|max:255',
            'luas_areal_ha'                           => 'nullable|numeric',
            'jumlah_sertifikasi'                      => ['nullable', 'integer'],

            'jalan'          => 'nullable|string|max:255',
            'desa'           => 'nullable|string|max:255',
            'kecamatan'      => 'nullable|string|max:255',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
        ]);

        // Kalau masih ada user admin_kabupaten lama, paksa kabupaten_id = miliknya
        if (!$user->hasRole('admin_upt_sertifikasi') && $this->kabupatenId()) {
            $validated['kabupaten_id'] = $this->kabupatenId();
        }

        Penangkar::create($validated);

        return redirect()->route('admin.upt_sertifikasi.penangkar.index')
            ->with('success', 'Data penangkar berhasil ditambahkan.');
    }

    /**
     * FORM EDIT
     */
    public function edit(Penangkar $penangkar)
    {
        $this->ensureOwned($penangkar);

        $tanamanList   = Tanaman::orderBy('nama_tanaman')->get();
        $kabupatenList = Kabupaten::orderBy('nama_kabupaten')->get();

        return view('admin.upt_sertifikasi.penangkar.edit', [ // atau tetap index kalau edit pakai modal
            'row'           => $penangkar,
            'tanamanList'   => $tanamanList,
            'kabupatenList' => $kabupatenList,
        ]);
    }

    /**
     * UPDATE DATA
     */
    public function update(Request $request, Penangkar $penangkar)
    {
        $this->ensureOwned($penangkar);

        $user = Auth::user();

        $validated = $request->validate([
            'tanaman_id'     => 'required|exists:tanaman,id',
            'kabupaten_id'   => 'required|exists:kabupaten,id',
            'nama_penangkar' => 'required|string|max:255',

            'nib_dan_tanggal'                         => 'nullable|string|max:255',
            'sertifikat_izin_usaha_nomor_dan_tanggal' => 'nullable|string|max:255',
            'luas_areal_ha'                           => 'nullable|numeric',
            'jumlah_sertifikasi'                      => ['nullable', 'integer'],

            'jalan'          => 'nullable|string|max:255',
            'desa'           => 'nullable|string|max:255',
            'kecamatan'      => 'nullable|string|max:255',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
        ]);

        // Lagi-lagi, kalau admin kabupaten biasa, jangan boleh pindah kabupaten lain
        if (!$user->hasRole('admin_upt_sertifikasi') && $this->kabupatenId()) {
            $validated['kabupaten_id'] = $this->kabupatenId();
        }

        $penangkar->update($validated);

        return redirect()->route('admin.upt_sertifikasi.penangkar.index')
            ->with('success', 'Data penangkar berhasil diupdate.');
    }

    /**
     * HAPUS DATA
     */
    public function destroy(Penangkar $penangkar)
    {
        $this->ensureOwned($penangkar);

        $penangkar->delete();

        return redirect()->route('admin.upt_sertifikasi.penangkar.index')
            ->with('success', 'Data penangkar berhasil dihapus.');
    }
}
