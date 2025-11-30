<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\BukuPanduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuPanduanController extends Controller
{
    /**
     * Halaman admin (Verifikator) - list + search + filter + pagination
     */
    public function index(Request $request)
    {
        $search = $request->query('q');
        $year   = $request->query('year');

        $query = BukuPanduan::query();

        if ($search) {
            $query->where('nama_buku', 'like', "%{$search}%");
        }

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        $bukuList = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // daftar tahun untuk filter
        $years = BukuPanduan::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.verifikator.buku_panduan.index', compact(
            'bukuList',
            'search',
            'year',
            'years'
        ));
    }

    /**
     * Simpan data baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'nama_buku' => ['required', 'string', 'max:255'],
                'file'      => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB
            ],
            [
                'nama_buku.required' => 'Nama buku panduan wajib diisi.',
                'file.required'      => 'File PDF wajib diupload.',
                'file.mimes'         => 'File harus berupa PDF.',
                'file.max'           => 'Ukuran file maksimal 10MB.',
            ]
        );

        $path = $request->file('file')->store('buku_panduan', 'public');

        BukuPanduan::create([
            'nama_buku' => $validated['nama_buku'],
            'file_path' => $path,
        ]);

        return redirect()
            ->route('admin.verifikator.buku_panduan.index')
            ->with('success', 'Buku panduan berhasil ditambahkan.');
    }

    /**
     * Update data
     */
    public function update(Request $request, BukuPanduan $buku_panduan)
    {
        $validated = $request->validate(
            [
                'nama_buku' => ['required', 'string', 'max:255'],
                'file'      => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            ],
            [
                'nama_buku.required' => 'Nama buku panduan wajib diisi.',
                'file.mimes'         => 'File harus berupa PDF.',
                'file.max'           => 'Ukuran file maksimal 10MB.',
            ]
        );

        $buku_panduan->nama_buku = $validated['nama_buku'];

        if ($request->hasFile('file')) {
            if ($buku_panduan->file_path && Storage::disk('public')->exists($buku_panduan->file_path)) {
                Storage::disk('public')->delete($buku_panduan->file_path);
            }

            $path = $request->file('file')->store('buku_panduan', 'public');
            $buku_panduan->file_path = $path;
        }

        $buku_panduan->save();

        return redirect()
            ->route('admin.verifikator.buku_panduan.index')
            ->with('success', 'Buku panduan berhasil diperbarui.');
    }

    /**
     * Hapus data
     */
    public function destroy(BukuPanduan $buku_panduan)
    {
        if ($buku_panduan->file_path && Storage::disk('public')->exists($buku_panduan->file_path)) {
            Storage::disk('public')->delete($buku_panduan->file_path);
        }

        $buku_panduan->delete();

        return redirect()
            ->route('admin.verifikator.buku_panduan.index')
            ->with('success', 'Buku panduan berhasil dihapus.');
    }

    /**
     * Halaman publik (tanpa login)
     */
    public function publicIndex(Request $request)
    {
        $search = $request->query('q');
        $year   = $request->query('year');

        $query = BukuPanduan::query();

        if ($search) {
            $query->where('nama_buku', 'like', "%{$search}%");
        }

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        $bukuList = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $years = BukuPanduan::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('buku_panduan.index', compact(
            'bukuList',
            'search',
            'year',
            'years'
        ));
    }
}
