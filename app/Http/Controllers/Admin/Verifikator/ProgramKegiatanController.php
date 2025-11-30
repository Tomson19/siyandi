<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\ProgramKegiatan;
use App\Models\JenisTanaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramKegiatanController extends Controller
{
    // ====== ADMIN (BUTUH LOGIN) ======
    public function index(Request $request)
{
    $user = Auth::user();

    // Ambil parameter filter dari query string
    $tahun          = $request->query('tahun');
    $jenisTanamanId = $request->query('jenis_tanaman_id');

    $query = ProgramKegiatan::with('jenisTanaman');

    // Filter data berdasarkan user (kecuali admin_super, kalau ada dan boleh lihat semua)
    if (! $user->hasRole('admin_super')) {
        $query->where('user_id', $user->id);
    }

    // Filter tahun (opsional)
    if ($tahun) {
        $query->where('tahun', $tahun);
    }

    // Filter jenis tanaman (opsional)
    if ($jenisTanamanId) {
        $query->where('jenis_tanaman_id', $jenisTanamanId);
    }

    // Pagination + urutkan
    $programs = $query
        ->orderBy('tahun', 'desc')
        ->orderBy('nama_program')
        ->paginate(10)              // per halaman 10
        ->withQueryString();        // supaya filter tetap nempel saat pindah halaman

    // Untuk isi dropdown komoditas di filter + modal
    $jenisTanaman = JenisTanaman::orderBy('nama_tanaman')->get();

    // Untuk isi dropdown tahun di filter (hanya tahun yang ada datanya)
    $listTahun = ProgramKegiatan::select('tahun')
        ->when(! $user->hasRole('admin_super'), function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    return view('admin.program_kegiatan.index', compact(
        'programs',
        'jenisTanaman',
        'listTahun',
        'tahun',
        'jenisTanamanId'
    ));
}



    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'nama_program'      => 'required|string|max:255',
            'nama_kegiatan'     => 'required|string|max:255',
            'jenis_tanaman_id'  => 'required|exists:jenis_tanaman,id',
            'tahun'             => 'required|integer',

            'jumlah_produksi'   => 'nullable|numeric',
            'jenis_benih'       => 'nullable|string|max:255',
            'kebutuhan_benih'   => 'nullable|numeric',
        ]);

        // Set bidang & field sesuai role
        if ($user->hasRole('admin_verifikator')) {
            $data['bidang'] = 'UPT Benih Tanaman Perkebunan';

            // Untuk verifikator: kebutuhan_benih tidak dipakai
            $data['kebutuhan_benih'] = null;
        } elseif ($user->hasRole('admin_bidang_produksi')) {
            $data['bidang'] = 'Bidang Produksi';

            // Untuk bidang produksi: jumlah_produksi & jenis_benih tidak dipakai
            $data['jumlah_produksi'] = null;
            $data['jenis_benih']     = null;
        } else {
            abort(403, 'Role tidak diperbolehkan.');
        }

        $data['user_id'] = $user->id;

        ProgramKegiatan::create($data);

        // 🔹 Nama route pakai underscore, sesuai resource()->names('program_kegiatan')
        return redirect()->route('admin.program_kegiatan.index')
            ->with('success', 'Data program & kegiatan berhasil disimpan.');
    }

    public function update(Request $request, ProgramKegiatan $programKegiatan)
    {
        $user = Auth::user();

        // Larang edit data milik user lain (kecuali admin_super kalau mau)
        if (! $user->hasRole('admin_super') && $programKegiatan->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengubah data ini.');
        }

        $data = $request->validate([
            'nama_program'      => 'required|string|max:255',
            'nama_kegiatan'     => 'required|string|max:255',
            'jenis_tanaman_id'  => 'required|exists:jenis_tanaman,id',
            'tahun'             => 'required|integer',
            'jumlah_produksi'   => 'nullable|numeric',
            'jenis_benih'       => 'nullable|string|max:255',
            'kebutuhan_benih'   => 'nullable|numeric',
        ]);

        if ($user->hasRole('admin_verifikator')) {
            $data['bidang'] = 'UPT Benih Tanaman Perkebunan';
            $data['kebutuhan_benih'] = null;
        } elseif ($user->hasRole('admin_bidang_produksi')) {
            $data['bidang']         = 'Bidang Produksi';
            $data['jumlah_produksi'] = null;
            $data['jenis_benih']     = null;
        } else {
            abort(403, 'Role tidak diperbolehkan.');
        }

        $programKegiatan->update($data);

        return redirect()->route('admin.program_kegiatan.index')
            ->with('success', 'Data program & kegiatan berhasil diupdate.');
    }

    public function destroy(ProgramKegiatan $programKegiatan)
    {
        $user = Auth::user();

        if (! $user->hasRole('admin_super') && $programKegiatan->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak menghapus data ini.');
        }

        $programKegiatan->delete();

        return redirect()->route('admin.program_kegiatan.index')
            ->with('success', 'Data program & kegiatan berhasil dihapus.');
    }

    // ====== VIEW PUBLIK (TANPA LOGIN) ======
    public function publicIndex(Request $request)
{
    $currentYear     = date('Y');

    // Ambil dari query string (boleh kosong)
    $tahun           = $request->query('tahun');
    $jenisTanamanId  = $request->query('jenis_tanaman_id');

    // ❗Jika tahun tidak dipilih, pakai tahun sekarang sebagai default
    if (empty($tahun)) {
        $tahun = $currentYear;
    }

    $query = ProgramKegiatan::with('jenisTanaman')
        ->where('tahun', $tahun); // selalu filter per tahun (default: tahun sekarang)

    if (!empty($jenisTanamanId)) {
        $query->where('jenis_tanaman_id', $jenisTanamanId);
    }

    // Di public, biasanya tidak perlu pagination (boleh ditambah kalau mau)
    $programs = $query
        ->orderBy('nama_program')
        ->get();

    // Dropdown tahun (ambil semua tahun yang ada di DB)
    $listTahun = ProgramKegiatan::select('tahun')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun');

    // Dropdown komoditas
    $jenisTanamanList = JenisTanaman::orderBy('nama_tanaman')->get();

    return view('program_kegiatan.index', compact(
        'programs',
        'listTahun',
        'jenisTanamanList',
        'tahun',
        'jenisTanamanId',
        'currentYear'
    ));
}

}
