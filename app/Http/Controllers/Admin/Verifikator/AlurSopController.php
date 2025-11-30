<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\AlurSop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlurSopController extends Controller
{
    /**
     * Halaman admin (Verifikator) - list + search + filter + pagination
     */
    public function index(Request $request)
    {
        $search = $request->query('q');
        $year   = $request->query('year');

        $query = AlurSop::query();

        if ($search) {
            $query->where('judul', 'like', "%{$search}%");
        }

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        $alurList = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // daftar tahun untuk filter
        $years = AlurSop::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.verifikator.alur_sop.index', compact(
            'alurList',
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
                'judul' => ['required', 'string', 'max:255'],
                'file'  => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB
            ],
            [
                'judul.required' => 'Judul wajib diisi.',
                'file.required'  => 'File PDF wajib diupload.',
                'file.mimes'     => 'File harus berupa PDF.',
                'file.max'       => 'Ukuran file maksimal 10MB.',
            ]
        );

        $path = $request->file('file')->store('alur_sop', 'public');

        AlurSop::create([
            'judul'     => $validated['judul'],
            'file_path' => $path,
        ]);

        return redirect()
            ->route('admin.verifikator.alur_sop.index')
            ->with('success', 'Data Alur & SOP berhasil ditambahkan.');
    }

    /**
     * Update data
     */
    public function update(Request $request, AlurSop $alur_sop)
    {
        $validated = $request->validate(
            [
                'judul' => ['required', 'string', 'max:255'],
                'file'  => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            ],
            [
                'judul.required' => 'Judul wajib diisi.',
                'file.mimes'     => 'File harus berupa PDF.',
                'file.max'       => 'Ukuran file maksimal 10MB.',
            ]
        );

        $alur_sop->judul = $validated['judul'];

        if ($request->hasFile('file')) {
            // hapus file lama
            if ($alur_sop->file_path && Storage::disk('public')->exists($alur_sop->file_path)) {
                Storage::disk('public')->delete($alur_sop->file_path);
            }

            $path = $request->file('file')->store('alur_sop', 'public');
            $alur_sop->file_path = $path;
        }

        $alur_sop->save();

        return redirect()
            ->route('admin.verifikator.alur_sop.index')
            ->with('success', 'Data Alur & SOP berhasil diperbarui.');
    }

    /**
     * Hapus data
     */
    public function destroy(AlurSop $alur_sop)
    {
        if ($alur_sop->file_path && Storage::disk('public')->exists($alur_sop->file_path)) {
            Storage::disk('public')->delete($alur_sop->file_path);
        }

        $alur_sop->delete();

        return redirect()
            ->route('admin.verifikator.alur_sop.index')
            ->with('success', 'Data Alur & SOP berhasil dihapus.');
    }

    /**
     * Halaman publik (tanpa login)
     */
    public function publicIndex(Request $request)
    {
        $search = $request->query('q');
        $year   = $request->query('year');

        $query = AlurSop::query();

        if ($search) {
            $query->where('judul', 'like', "%{$search}%");
        }

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        $alurList = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $years = AlurSop::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('alur_sop.index', compact(
            'alurList',
            'search',
            'year',
            'years'
        ));
    }
}
