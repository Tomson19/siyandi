<?php

namespace App\Http\Controllers\Admin\Kabupaten;

use App\Http\Controllers\Controller;
use App\Models\KebunBenihSumber;
use App\Models\Tanaman;
use App\Models\KbsDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KbsPemilik;
use App\Models\KbsPohon;

class KebunBenihSumberController extends Controller
{
    /**
     * INDEX
     * Tampilkan daftar KBS (header)
     * No | Komoditas | No & Tgl SK | Varietas | Kabupaten
     */
    public function index(Request $request)
{
    $user = Auth::user();

    $tanamanList = Tanaman::orderBy('nama_tanaman')->get();

    $query = KebunBenihSumber::with(['tanaman', 'kabupaten']);

    // Batasi ke kabupaten admin yang login
    if ($user->kabupaten_id) {
        $query->where('kabupaten_id', $user->kabupaten_id);
    }

    // Filter komoditas (tanaman)
    if ($request->filled('tanaman_id')) {
        $query->where('tanaman_id', $request->tanaman_id);
    }

    // 🔹 Pagination 10 baris + bawa query string (supaya filter tidak hilang)
    $kbs = $query->orderBy('id', 'desc')
        ->paginate(10)
        ->appends($request->query());

    return view('admin.kabupaten.kbs.index', [
        'kbs'         => $kbs,
        'tanamanList' => $tanamanList,
        'user'        => $user,
    ]);
}


    /**
     * STORE HEADER
     * Simpan data KBS di tabel header:
     * - tanaman_id
     * - nama_varietas
     * - nomor_sk
     * - tanggal_sk
     * - kabupaten_id (otomatis dari user)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'tanaman_id'    => ['required', 'exists:tanaman,id'],
            'nama_varietas' => ['required', 'string', 'max:255'],
            'nomor_sk'      => ['nullable', 'string', 'max:255'],
            'tanggal_sk'    => ['nullable', 'string', 'max:255'],
        ]);

        // Kabupatennya ambil dari user yang login
        $data['kabupaten_id'] = $user->kabupaten_id;

        KebunBenihSumber::create($data);

        return redirect()
            ->route('admin.kabupaten.kbs.index')
            ->with('success', 'Data Kebun Benih Sumber berhasil ditambahkan.');
    }

    /**
     * UPDATE HEADER
     */
    public function update(Request $request, KebunBenihSumber $kbs)
    {
        $user = Auth::user();

        // Pastikan data ini milik kabupaten yang sama
        if ($user->kabupaten_id && $kbs->kabupaten_id !== $user->kabupaten_id) {
            abort(403);
        }

        $data = $request->validate([
            'tanaman_id'    => ['required', 'exists:tanaman,id'],
            'nama_varietas' => ['required', 'string', 'max:255'],
            'nomor_sk'      => ['nullable', 'string', 'max:255'],
            'tanggal_sk'    => ['nullable', 'string', 'max:255'],
        ]);

        // Kabupatennya tetap kabupaten user (supaya tidak bisa dipindah)
        if ($user->kabupaten_id) {
            $data['kabupaten_id'] = $user->kabupaten_id;
        }

        $kbs->update($data);

        return redirect()
            ->route('admin.kabupaten.kbs.index')
            ->with('success', 'Data Kebun Benih Sumber berhasil diperbarui.');
    }

    /**
     * DELETE HEADER
     */
    public function destroy(KebunBenihSumber $kbs)
    {
        $user = Auth::user();

        if ($user->kabupaten_id && $kbs->kabupaten_id !== $user->kabupaten_id) {
            abort(403);
        }

        $kbs->delete();

        return redirect()
            ->route('admin.kabupaten.kbs.index')
            ->with('success', 'Data Kebun Benih Sumber berhasil dihapus.');
    }

    /**
     * SHOW
     * Tampilkan 1 header + semua baris detail (tabel panjang)
     */
   public function show(KebunBenihSumber $kbs)
{
    $user = Auth::user();

    // Pastikan admin kabupaten hanya lihat KBS kabupatennya sendiri
    if ($user->kabupaten_id && $kbs->kabupaten_id !== $user->kabupaten_id) {
        abort(403);
    }

    // Load relasi yang dibutuhkan
    $kbs->load([
        'tanaman',
        'kabupaten',
        'pemilik.pohon', // pemilik dan pohonnya
    ]);

    return view('admin.kabupaten.kbs.show', [
        'kbs'     => $kbs,
        'pemilik' => $kbs->pemilik,
        'user'    => $user,
    ]);
}

/**
 * Simpan baris pemilik baru + lokasi + umum.
 */
public function storePemilik(Request $request, KebunBenihSumber $kbs)
{
    $user = Auth::user();
    if ($user->kabupaten_id && $kbs->kabupaten_id !== $user->kabupaten_id) {
        abort(403);
    }

    $data = $request->validate([
        'kecamatan'           => ['nullable', 'string', 'max:255'],
        'desa'                => ['nullable', 'string', 'max:255'],
        'tahun_tanam'         => ['nullable', 'string', 'max:50'],
        'jumlah_pit'          => ['nullable', 'integer'],
        'no_pemilik'          => ['nullable', 'integer'],
        'nama_pemilik'        => ['required', 'string', 'max:255'],
        'luas_ha'             => ['nullable', 'numeric'],
        'jumlah_pohon_induk'  => ['nullable', 'integer'],
    ]);

    $data['kbs_id'] = $kbs->id;

    KbsPemilik::create($data);

    return redirect()
        ->route('admin.kabupaten.kbs.show', $kbs->id)
        ->with('success', 'Data pemilik berhasil ditambahkan.');
}

public function updatePemilik(Request $request, KebunBenihSumber $kbs, KbsPemilik $pemilik)
{
    $user = Auth::user();
    // Pastikan pemilik ini benar-benar milik KBS ini & kabupaten-nya cocok
    if ($user->kabupaten_id && $kbs->kabupaten_id !== $user->kabupaten_id) {
        abort(403);
    }
    if ($pemilik->kbs_id !== $kbs->id) {
        abort(404);
    }

    $data = $request->validate([
        'kecamatan'           => ['nullable', 'string', 'max:255'],
        'desa'                => ['nullable', 'string', 'max:255'],
        'tahun_tanam'         => ['nullable', 'string', 'max:50'],
        'jumlah_pit'          => ['nullable', 'integer'],
        'no_pemilik'          => ['nullable', 'integer'],
        'nama_pemilik'        => ['required', 'string', 'max:255'],
        'luas_ha'             => ['nullable', 'numeric'],
        'jumlah_pohon_induk'  => ['nullable', 'integer'],
    ]);

    $pemilik->update($data);

    return redirect()
        ->route('admin.kabupaten.kbs.show', $kbs->id)
        ->with('success', 'Data pemilik berhasil diperbarui.');
}


public function destroyPemilik(KebunBenihSumber $kbs, KbsPemilik $pemilik)
{
    $user = Auth::user();
    if ($user->kabupaten_id && $kbs->kabupaten_id !== $user->kabupaten_id) {
        abort(403);
    }

    if ($pemilik->kbs_id !== $kbs->id) {
        abort(404);
    }

    $pemilik->delete(); // otomatis hapus pohon karena onDelete('cascade') di relasi DB

    return redirect()
        ->route('admin.kabupaten.kbs.show', $kbs->id)
        ->with('success', 'Data pemilik beserta pohonnya berhasil dihapus.');
}

/**
 * Simpan pohon + koordinat baru untuk salah satu pemilik
 */
public function storePohon(Request $request, KebunBenihSumber $kbs)
{
    $user = Auth::user();
    if ($user->kabupaten_id && $kbs->kabupaten_id !== $user->kabupaten_id) {
        abort(403);
    }

    $data = $request->validate([
        'kbs_pemilik_id'    => ['required', 'exists:kbs_pemilik,id'],
        'no_pohon'          => ['nullable', 'integer'],
        'nomor_pohon_induk' => ['nullable', 'string', 'max:50'],
        'latitude'          => ['nullable', 'numeric'],
        'longitude'         => ['nullable', 'numeric'],
    ]);

    $pemilik = KbsPemilik::where('id', $data['kbs_pemilik_id'])
        ->where('kbs_id', $kbs->id)
        ->firstOrFail();

    KbsPohon::create([
        'kbs_pemilik_id'    => $pemilik->id,
        'no_pohon'          => $data['no_pohon'] ?? null,
        'nomor_pohon_induk' => $data['nomor_pohon_induk'] ?? null,
        'latitude'          => $data['latitude'] ?? null,
        'longitude'         => $data['longitude'] ?? null,
    ]);

    return redirect()
        ->route('admin.kabupaten.kbs.show', $kbs->id)
        ->with('success', 'Data pohon & koordinat berhasil ditambahkan.');
}

public function updatePohon(Request $request, KebunBenihSumber $kbs, KbsPohon $pohon)
{
    $user = Auth::user();
    if ($user->kabupaten_id && $kbs->kabupaten_id !== $user->kabupaten_id) {
        abort(403);
    }

    // Pastikan pohon ini milik KBS yang sama melalui relasi pemilik
    if (!$pohon->pemilik || $pohon->pemilik->kbs_id !== $kbs->id) {
        abort(404);
    }

    $data = $request->validate([
        'kbs_pemilik_id'    => ['required', 'exists:kbs_pemilik,id'],
        'no_pohon'          => ['nullable', 'integer'],
        'nomor_pohon_induk' => ['nullable', 'string', 'max:50'],
        'latitude'          => ['nullable', 'numeric'],
        'longitude'         => ['nullable', 'numeric'],
    ]);

    // Pastikan pemilik yang dipilih juga milik KBS ini
    $pemilikBaru = KbsPemilik::where('id', $data['kbs_pemilik_id'])
        ->where('kbs_id', $kbs->id)
        ->firstOrFail();

    $pohon->update([
        'kbs_pemilik_id'    => $pemilikBaru->id,
        'no_pohon'          => $data['no_pohon'] ?? null,
        'nomor_pohon_induk' => $data['nomor_pohon_induk'] ?? null,
        'latitude'          => $data['latitude'] ?? null,
        'longitude'         => $data['longitude'] ?? null,
    ]);

    return redirect()
        ->route('admin.kabupaten.kbs.show', $kbs->id)
        ->with('success', 'Data pohon & koordinat berhasil diperbarui.');
}


public function destroyPohon(KebunBenihSumber $kbs, KbsPohon $pohon)
{
    $user = Auth::user();
    if ($user->kabupaten_id && $kbs->kabupaten_id !== $user->kabupaten_id) {
        abort(403);
    }

    if (!$pohon->pemilik || $pohon->pemilik->kbs_id !== $kbs->id) {
        abort(404);
    }

    $pohon->delete();

    return redirect()
        ->route('admin.kabupaten.kbs.show', $kbs->id)
        ->with('success', 'Data pohon & koordinat berhasil dihapus.');
}

}
