<?php

namespace App\Http\Controllers\Admin\Kabupaten;

use App\Http\Controllers\Controller;
use App\Models\DeskripsiVarietas;
use App\Models\Varietas;
use Illuminate\Http\Request;

class DeskripsiVarietasController extends Controller
{
    /**
     * Form tambah deskripsi untuk varietas tertentu.
     */
    public function create($varietas_id)
    {
        $varietas = Varietas::findOrFail($varietas_id);
        return view('admin.kabupaten.deskripsi.create', compact('varietas'));
    }

    /**
     * Simpan deskripsi baru.
     */
    public function store(Request $request, $varietas_id)
    {
        $varietas = Varietas::findOrFail($varietas_id);

        // ✅ Validasi lengkap semua kolom
        $data = $request->validate([
            'nomor_sk' => 'nullable|string|max:255',
            'tanggal' => 'nullable|string|max:255',
            'tipe_varietas' => 'nullable|string|max:255',
            'asal_usul' => 'nullable|string',

            // Morfologi
            'tipe_pertumbuhan' => 'nullable|string',
            'bentuk_tajuk' => 'nullable|string',

            // Daun
            'daun_ukuran' => 'nullable|string|max:255',
            'daun_warna_muda' => 'nullable|string|max:255',
            'daun_warna_tua' => 'nullable|string|max:255',
            'daun_bentuk_ujung' => 'nullable|string|max:255',
            'daun_tepi' => 'nullable|string|max:255',
            'daun_pangkal' => 'nullable|string|max:255',
            'daun_permukaan' => 'nullable|string|max:255',
            'daun_warna_pucuk' => 'nullable|string|max:255',

            // Bunga
            'bunga_warna_mahkota' => 'nullable|string|max:255',
            'bunga_jumlah_mahkota' => 'nullable|string|max:255',
            'bunga_ukuran' => 'nullable|string|max:255',

            // Buah
            'buah_ukuran' => 'nullable|string|max:255',
            'buah_panjang' => 'nullable|string|max:255',
            'buah_diameter' => 'nullable|string|max:255',
            'buah_bobot' => 'nullable|string|max:255',
            'buah_bentuk' => 'nullable|string|max:255',
            'buah_warna_muda' => 'nullable|string|max:255',
            'buah_warna_masak' => 'nullable|string|max:255',
            'buah_ukuran_discus' => 'nullable|string|max:255',

            // Biji
            'biji_bentuk' => 'nullable|string|max:255',
            'biji_nisbah' => 'nullable|string|max:255',
            'biji_persen_normal' => 'nullable|string|max:255',
            'citarasa' => 'nullable|string',
            'potensi_produksi' => 'nullable|string',

            // Ketahanan & adaptasi
            'penyakit_karat_daun' => 'nullable|string|max:255',
            'penggerek_buah_kopi' => 'nullable|string|max:255',
            'daerah_adaptasi' => 'nullable|string|max:255',

            // Pemuliaan
            'pemulia' => 'nullable|string',
            'peneliti' => 'nullable|string',
            'pemilik_varietas' => 'nullable|string',
        ]);

        $data['varietas_id'] = $varietas->id;
        DeskripsiVarietas::create($data);

        return redirect()
            ->route('admin.varietas.show', $varietas->id)
            ->with('success', 'Deskripsi berhasil ditambahkan.');
    }

    /**
     * Form edit deskripsi.
     */
    public function edit($id)
    {
        $deskripsi = DeskripsiVarietas::with('varietas')->findOrFail($id);
        $varietas = $deskripsi->varietas;

        return view('admin.kabupaten.deskripsi.edit', compact('deskripsi', 'varietas'));
    }

    /**
     * Update deskripsi varietas.
     */
    public function update(Request $request, $id)
    {
        $deskripsi = DeskripsiVarietas::findOrFail($id);

        // ✅ Validasi ulang semua kolom sama seperti store
        $data = $request->validate([
            'nomor_sk' => 'nullable|string|max:255',
            'tanggal' => 'nullable|string|max:255',
            'tipe_varietas' => 'nullable|string|max:255',
            'asal_usul' => 'nullable|string',

            'tipe_pertumbuhan' => 'nullable|string',
            'bentuk_tajuk' => 'nullable|string',

            'daun_ukuran' => 'nullable|string|max:255',
            'daun_warna_muda' => 'nullable|string|max:255',
            'daun_warna_tua' => 'nullable|string|max:255',
            'daun_bentuk_ujung' => 'nullable|string|max:255',
            'daun_tepi' => 'nullable|string|max:255',
            'daun_pangkal' => 'nullable|string|max:255',
            'daun_permukaan' => 'nullable|string|max:255',
            'daun_warna_pucuk' => 'nullable|string|max:255',

            'bunga_warna_mahkota' => 'nullable|string|max:255',
            'bunga_jumlah_mahkota' => 'nullable|string|max:255',
            'bunga_ukuran' => 'nullable|string|max:255',

            'buah_ukuran' => 'nullable|string|max:255',
            'buah_panjang' => 'nullable|string|max:255',
            'buah_diameter' => 'nullable|string|max:255',
            'buah_bobot' => 'nullable|string|max:255',
            'buah_bentuk' => 'nullable|string|max:255',
            'buah_warna_muda' => 'nullable|string|max:255',
            'buah_warna_masak' => 'nullable|string|max:255',
            'buah_ukuran_discus' => 'nullable|string|max:255',

            'biji_bentuk' => 'nullable|string|max:255',
            'biji_nisbah' => 'nullable|string|max:255',
            'biji_persen_normal' => 'nullable|string|max:255',
            'citarasa' => 'nullable|string',
            'potensi_produksi' => 'nullable|string',

            'penyakit_karat_daun' => 'nullable|string|max:255',
            'penggerek_buah_kopi' => 'nullable|string|max:255',
            'daerah_adaptasi' => 'nullable|string|max:255',

            'pemulia' => 'nullable|string',
            'peneliti' => 'nullable|string',
            'pemilik_varietas' => 'nullable|string',
        ]);

        $deskripsi->update($data);

        return redirect()
            ->route('admin.varietas.show', $deskripsi->varietas_id)
            ->with('success', 'Deskripsi berhasil diperbarui.');
    }
}
