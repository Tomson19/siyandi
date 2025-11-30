<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Benih;
use App\Models\JenisTanaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BenihController extends Controller
{
    /**
     * Tampilkan daftar benih (stok) untuk verifikator.
     */
    public function index()
    {
        $benih = Benih::with('jenisTanaman')
            ->orderBy('jenis_tanaman_id')
            ->orderBy('jenis_benih')
            ->get();

        return view('admin.verifikator.stok_benih.index', [
            'benih' => $benih,
            'jenisTanaman' => JenisTanaman::orderBy('nama_tanaman')->get(),
        ]);
    }

    /**
     * Simpan data benih baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_tanaman_id' => 'required|exists:jenis_tanaman,id',
            'jenis_benih'      => 'required|in:Biji,Siap Tanam',
            'tipe_pembayaran'  => 'required|in:Gratis,Berbayar',
            'stok'             => 'required|integer|min:0',
            'harga'            => 'nullable|integer|min:0', // <-- Tambahan
            'gambar'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('uploads/benih', 'public');
        }

        // Jika tipe Gratis, harga otomatis null
        if ($data['tipe_pembayaran'] === 'Gratis') {
            $data['harga'] = null;
        }

        Benih::create($data);

        return redirect()
            ->route('admin.verifikator.benih.index')
            ->with('success', 'Data benih berhasil ditambahkan.');
    }

    /**
     * Perbarui data benih.
     */
   public function update(Request $request, $id)
{
    $benih = Benih::findOrFail($id);

    $data = $request->validate([
        'jenis_tanaman_id' => 'required|exists:jenis_tanaman,id',
        'jenis_benih'      => 'required|in:Biji,Siap Tanam',
        'tipe_pembayaran'  => 'required|in:Gratis,Berbayar',
        'stok'             => 'required|integer|min:0',
        'harga'            => 'nullable|integer|min:0',
        'gambar'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Simpan stok lama dulu
    $stokLama = $benih->stok;

    // Jika ada upload gambar baru
    if ($request->hasFile('gambar')) {
        if ($benih->gambar && file_exists(storage_path('app/public/' . $benih->gambar))) {
            unlink(storage_path('app/public/' . $benih->gambar));
        }
        $data['gambar'] = $request->file('gambar')->store('uploads/benih', 'public');
    }

    // Jika Gratis, harga dihapus
    if ($data['tipe_pembayaran'] === 'Gratis') {
        $data['harga'] = null;
    }

    // Update data utama
    $benih->update($data);

    // ==========================================================
    // LOGIKA TAMBAHAN UNTUK RIWAYAT STOK
    // ==========================================================
    if ($stokLama != $benih->stok) {
    $selisih = $benih->stok - $stokLama;
    $tipe = $selisih > 0 ? 'Masuk' : 'Keluar';

    \App\Models\RiwayatStok::create([
        'benih_id'   => $benih->id,
        'tipe'       => $tipe,
        'jumlah'     => abs($selisih),
        'stok_awal'  => $stokLama,
        'stok_akhir' => $benih->stok,
        'keterangan' => 'Perubahan stok manual oleh admin (edit data)',
        'admin_id'   => Auth::id(),
    ]);
}


    return redirect()
        ->route('admin.verifikator.benih.index')
        ->with('success', 'Data benih berhasil diperbarui.');
}

    /**
     * Hapus data benih.
     */
    public function destroy($id)
    {
        $benih = Benih::findOrFail($id);

        if ($benih->gambar && file_exists(storage_path('app/public/' . $benih->gambar))) {
            unlink(storage_path('app/public/' . $benih->gambar));
        }

        $benih->delete();

        return redirect()
            ->route('admin.verifikator.benih.index')
            ->with('success', 'Data benih berhasil dihapus.');
    }

    /**
     * Halaman publik: menampilkan stok benih untuk semua orang (tanpa login)
     */
    public function publicIndex()
    {
        $benih = Benih::with('jenisTanaman')
            ->orderBy('jenis_tanaman_id')
            ->orderBy('jenis_benih')
            ->get();

        return view('benih.index', compact('benih'));
    }
}
