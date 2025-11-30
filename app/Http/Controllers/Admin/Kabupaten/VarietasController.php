<?php

namespace App\Http\Controllers\Admin\Kabupaten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Varietas;
use App\Models\Tanaman;
use Illuminate\Support\Facades\Auth;

class VarietasController extends Controller
{
    /**
     * Menampilkan daftar varietas milik kabupaten login.
     */
    public function index(Request $request)
{
    $user = Auth::user();

    // List tanaman untuk dropdown filter
    $tanamanList = Tanaman::orderBy('nama_tanaman')->get();

    $query = Varietas::with(['tanaman', 'kabupaten'])
        ->where('kabupaten_id', $user->kabupaten_id);

    // 🔍 Filter by tanaman (opsional)
    if ($request->filled('tanaman_id')) {
        $query->where('tanaman_id', $request->tanaman_id);
    }

    // 🔍 Filter by status (draft / published) (opsional)
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 🔍 Filter pencarian umum (nama varietas, nomor SK, pemilik)
    if ($request->filled('q')) {
        $search = $request->q;
        $query->where(function ($q2) use ($search) {
            $q2->where('nama_varietas', 'like', "%{$search}%")
               ->orWhere('nomor_tanggal_sk', 'like', "%{$search}%")
               ->orWhere('pemilik_varietas', 'like', "%{$search}%");
        });
    }

    // 📄 Pagination 10 data per halaman + keep query string filter
    $varietas = $query->orderBy('id', 'desc')
        ->paginate(10)
        ->appends($request->query());

    return view('admin.kabupaten.varietas.index', compact('varietas', 'tanamanList'));
}


    /**
     * Form tambah varietas baru.
     */
    public function create()
    {
        $tanamanList = Tanaman::all();
        return view('admin.kabupaten.varietas.create', compact('tanamanList'));
    }

    /**
     * Simpan varietas baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanaman_id' => 'required|exists:tanaman,id',
            'nama_varietas' => 'required|string|max:255',
            'nomor_tanggal_sk' => 'nullable|string|max:255',
            'jenis_benih' => 'nullable|string|max:255',
            'pemilik_varietas' => 'nullable|string|max:255',
            'jumlah_materi_genetik' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $user = Auth::user();

        Varietas::create([
            'tanaman_id' => $request->tanaman_id,
            'kabupaten_id' => $user->kabupaten_id, // penting
            'user_id' => $user->id,
            'nomor_tanggal_sk' => $request->nomor_tanggal_sk,
            'nama_varietas' => $request->nama_varietas,
            'jenis_benih' => $request->jenis_benih,
            'pemilik_varietas' => $request->pemilik_varietas,
            'jumlah_materi_genetik' => $request->jumlah_materi_genetik,
            'keterangan' => $request->keterangan,
            'status' => 'Published',
        ]);

        return redirect()->route('admin.varietas.index')
            ->with('success', 'Varietas berhasil ditambahkan.');
    }

  public function edit($id)
{
    $varietas = Varietas::with('kabupaten')->findOrFail($id);
    $this->authorizeVarietas($varietas);

    $tanamanList = Tanaman::all();
    return view('admin.kabupaten.varietas.edit', compact('varietas', 'tanamanList'));
}

public function update(Request $request, $id)
{
    $varietas = Varietas::findOrFail($id);
    $this->authorizeVarietas($varietas);

    $request->validate([
        'tanaman_id' => 'required|exists:tanaman,id',
        'nama_varietas' => 'required|string|max:255',
        'nomor_tanggal_sk' => 'nullable|string|max:255',
        'jenis_benih' => 'nullable|string|max:255',
        'pemilik_varietas' => 'nullable|string|max:255',
        'jumlah_materi_genetik' => 'nullable|numeric',
        'keterangan' => 'nullable|string',
    ]);

    $varietas->update($request->only([
        'tanaman_id',
        'nomor_tanggal_sk',
        'nama_varietas',
        'jenis_benih',
        'pemilik_varietas',
        'jumlah_materi_genetik',
        'keterangan',
    ]));

    return redirect()->route('admin.varietas.index')->with('success', 'Varietas berhasil diperbarui.');
}

public function destroy($id)
{
    $varietas = Varietas::findOrFail($id);
    $this->authorizeVarietas($varietas);

    $varietas->delete();
    return redirect()->route('admin.varietas.index')->with('success', 'Varietas berhasil dihapus.');
}

public function show($id)
{
    // Ambil varietas beserta relasi utama (tanpa materiGenetik agar tidak dobel)
    $varietas = Varietas::with(['tanaman', 'kabupaten', 'deskripsi'])
        ->findOrFail($id);

    // Ambil data materi genetik dengan pagination (misalnya 5 per halaman)
    $materiGenetik = \App\Models\MateriGenetik::where('varietas_id', $id)
        ->orderBy('id', 'desc')
        ->paginate(48);

    // Kirim dua variabel ke view
    return view('admin.kabupaten.varietas.detail', compact('varietas', 'materiGenetik'));
}




    /**
     * Pastikan user hanya bisa ubah varietas miliknya.
     */
    protected function authorizeVarietas(Varietas $varietas)
    {
        $user = Auth::user();

        // 🟢 Jika super admin, izinkan semua
        if (method_exists($user, 'hasRole') && $user->hasRole('admin_super')) {
            return;
        }

        // 🟡 Jika varietas belum punya kabupaten, tolak
        if (!$varietas->kabupaten_id) {
            abort(403, 'Data varietas belum terhubung dengan kabupaten mana pun.');
        }

        // 🔴 Jika varietas bukan milik kabupaten user yang login, tolak
        if ((int) $varietas->kabupaten_id !== (int) $user->kabupaten_id) {
            abort(403, 'Anda tidak berhak mengakses data ini.');
        }

        // ✅ Lolos semua pengecekan
    }
}
