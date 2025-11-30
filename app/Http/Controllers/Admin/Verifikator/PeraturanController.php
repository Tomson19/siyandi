<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Peraturan;
use App\Models\JenisTanaman; // ga kepake, kalau ga perlu boleh dihapus
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PeraturanController extends Controller
{
    /**
     * INDEX ADMIN (login, role: admin_verifikator)
     */
    public function index(Request $request)
    {
        $search      = $request->query('q');
        $selectedYear = $request->query('tahun');
        $perPage     = 10;

        $query = Peraturan::query();

        // 🔍 FILTER PENCARIAN (nomor_tahun / tentang)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tahun', 'like', "%{$search}%")
                    ->orWhere('tentang', 'like', "%{$search}%");
            });
        }

        // 📅 FILTER TAHUN (berdasarkan tanggal_penetapan)
        if (!empty($selectedYear)) {
            $query->whereYear('tanggal_penetapan', $selectedYear);
        }

        $peraturanList = $query
            ->orderBy('tanggal_penetapan', 'desc')
            ->paginate($perPage)
            ->withQueryString(); // supaya query ?q= & ?tahun= ikut di pagination

        // list tahun untuk dropdown (distinct year dari tanggal_penetapan)
        $tahunList = Peraturan::selectRaw('YEAR(tanggal_penetapan) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('admin.verifikator.peraturan.index', compact(
            'peraturanList',
            'search',
            'selectedYear',
            'tahunList'
        ));
    }

    /**
     * SIMPAN DATA BARU
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_tahun'       => ['required', 'string', 'max:255'],
            'tanggal_penetapan' => ['required', 'date'],
            'tentang'           => ['required', 'string'],
            'file'              => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB
        ], [
            'file.mimes' => 'File harus berupa PDF.',
            'file.max'   => 'Ukuran file maksimal 10MB.',
        ]);

        // Simpan file
        $path = $request->file('file')->store('peraturan', 'public');

        Peraturan::create([
            'nomor_tahun'       => $validated['nomor_tahun'],
            'tanggal_penetapan' => $validated['tanggal_penetapan'],
            'tentang'           => $validated['tentang'],
            'file_path'         => $path,
        ]);

        return redirect()
            ->route('admin.verifikator.peraturan.index')
            ->with('success', 'Peraturan berhasil ditambahkan.');
    }

    /**
     * UPDATE DATA
     */
    public function update(Request $request, Peraturan $peraturan)
    {
        $validated = $request->validate([
            'nomor_tahun'       => ['required', 'string', 'max:255'],
            'tanggal_penetapan' => ['required', 'date'],
            'tentang'           => ['required', 'string'],
            'file'              => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'file.mimes' => 'File harus berupa PDF.',
            'file.max'   => 'Ukuran file maksimal 10MB.',
        ]);

        $dataUpdate = [
            'nomor_tahun'       => $validated['nomor_tahun'],
            'tanggal_penetapan' => $validated['tanggal_penetapan'],
            'tentang'           => $validated['tentang'],
        ];

        if ($request->hasFile('file')) {
            // hapus file lama kalau ada
            if ($peraturan->file_path && Storage::disk('public')->exists($peraturan->file_path)) {
                Storage::disk('public')->delete($peraturan->file_path);
            }

            $path = $request->file('file')->store('peraturan', 'public');
            $dataUpdate['file_path'] = $path;
        }

        $peraturan->update($dataUpdate);

        return redirect()
            ->route('admin.verifikator.peraturan.index')
            ->with('success', 'Peraturan berhasil diupdate.');
    }

    /**
     * HAPUS DATA
     */
    public function destroy(Peraturan $peraturan)
    {
        if ($peraturan->file_path && Storage::disk('public')->exists($peraturan->file_path)) {
            Storage::disk('public')->delete($peraturan->file_path);
        }

        $peraturan->delete();

        return redirect()
            ->route('admin.verifikator.peraturan.index')
            ->with('success', 'Peraturan berhasil dihapus.');
    }

    /**
     * INDEX PUBLIC (tanpa login)
     */
    public function publicIndex(Request $request)
{
    $search       = $request->query('q');
    $selectedYear = $request->query('tahun');
    $perPage      = 10;

    $query = Peraturan::query();

    // 🔍 Search nomor & tahun / tentang
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('nomor_tahun', 'like', "%{$search}%")
              ->orWhere('tentang', 'like', "%{$search}%");
        });
    }

    // 📅 Filter tahun dari tanggal_penetapan
    if (!empty($selectedYear)) {
        $query->whereYear('tanggal_penetapan', $selectedYear);
    }

    $peraturanList = $query
        ->orderBy('tanggal_penetapan', 'desc')
        ->paginate($perPage)
        ->withQueryString();

    // List tahun untuk dropdown
    $tahunList = Peraturan::selectRaw('YEAR(tanggal_penetapan) as tahun')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    return view('peraturan.index', compact(
        'peraturanList',
        'search',
        'selectedYear',
        'tahunList'
    ));
}
}
