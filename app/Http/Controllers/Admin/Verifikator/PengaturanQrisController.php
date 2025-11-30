<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\PengaturanQris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanQrisController extends Controller
{
    /**
     * Tampilkan daftar QRIS.
     */
    public function index()
    {
        $data = PengaturanQris::orderByDesc('id')->get();
        return view('admin.verifikator.qris.index', compact('data'));
    }

    /**
     * Simpan QRIS baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_qris' => 'nullable|string|max:100',
            'gambar_qris' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'aktif' => 'nullable|boolean',
        ]);

        // Nonaktifkan QRIS lain jika baru diaktifkan
        if ($request->aktif) {
            PengaturanQris::query()->update(['aktif' => false]);
        }

        // Upload file
        $validated['gambar_qris'] = $request->file('gambar_qris')->store('uploads/qris', 'public');
        $validated['aktif'] = $request->aktif ? 1 : 0;

        PengaturanQris::create($validated);

        return redirect()->back()->with('success', 'QRIS baru berhasil ditambahkan.');
    }

    /**
     * Update QRIS.
     */
    public function update(Request $request, $id)
    {
        $qris = PengaturanQris::findOrFail($id);

        $validated = $request->validate([
            'nama_qris' => 'nullable|string|max:100',
            'gambar_qris' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'aktif' => 'nullable|boolean',
        ]);

        // Ganti gambar jika diupload baru
        if ($request->hasFile('gambar_qris')) {
            if ($qris->gambar_qris && Storage::disk('public')->exists($qris->gambar_qris)) {
                Storage::disk('public')->delete($qris->gambar_qris);
            }
            $validated['gambar_qris'] = $request->file('gambar_qris')->store('uploads/qris', 'public');
        }

        // Kalau diaktifkan, nonaktifkan yang lain
        if ($request->aktif) {
            PengaturanQris::query()->update(['aktif' => false]);
        }

        $validated['aktif'] = $request->aktif ? 1 : 0;

        $qris->update($validated);

        return redirect()->back()->with('success', 'QRIS berhasil diperbarui.');
    }

    /**
     * Hapus QRIS.
     */
    public function destroy($id)
    {
        $qris = PengaturanQris::findOrFail($id);

        if ($qris->gambar_qris && Storage::disk('public')->exists($qris->gambar_qris)) {
            Storage::disk('public')->delete($qris->gambar_qris);
        }

        $qris->delete();

        return redirect()->back()->with('success', 'QRIS berhasil dihapus.');
    }
}
