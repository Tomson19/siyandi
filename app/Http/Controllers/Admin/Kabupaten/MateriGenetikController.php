<?php

namespace App\Http\Controllers\Admin\Kabupaten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MateriGenetik;
use App\Models\Varietas;

class MateriGenetikController extends Controller
{
    /**
     * Simpan data materi genetik baru.
     */
    public function store(Request $request, $varietas_id)
    {
        $varietas = Varietas::findOrFail($varietas_id);

        $data = $request->validate([
            'no_sk' => 'nullable|string|max:255',
            'tanggal_sk' => 'nullable|string|max:255',
            'nomor_pohon' => 'required|integer|min:1',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $data['varietas_id'] = $varietas->id;

        MateriGenetik::create($data);

        return redirect()
            ->route('admin.varietas.show', $varietas->id)
            ->with('success', 'Data materi genetik berhasil ditambahkan.');
    }

    /**
     * Update data materi genetik.
     */
    public function update(Request $request, $varietas_id, $id)
    {
        $materi = MateriGenetik::findOrFail($id);

        $data = $request->validate([
            'no_sk' => 'nullable|string|max:255',
            'tanggal_sk' => 'nullable|string|max:255',
            'nomor_pohon' => 'required|integer|min:1',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $materi->update($data);

        return redirect()
            ->route('admin.varietas.show', $materi->varietas_id)
            ->with('success', 'Data materi genetik berhasil diperbarui.');
    }

    /**
     * Hapus data materi genetik.
     */
    public function destroy($varietas_id, $id)
    {
        $materi = MateriGenetik::findOrFail($id);
        $materi->delete();

        return redirect()
            ->route('admin.varietas.show', $materi->varietas_id)
            ->with('success', 'Data materi genetik berhasil dihapus.');
    }
}
