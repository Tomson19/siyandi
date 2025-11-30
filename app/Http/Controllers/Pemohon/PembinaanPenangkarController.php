<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\PembinaanPenangkar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembinaanPenangkarController extends Controller
{
    /**
     * Daftar pengajuan pembinaan milik pemohon.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $status = $request->query('status');

        $query = PembinaanPenangkar::with(['sesi'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $pembinaanList = $query->paginate(10)->withQueryString();

        return view('pemohon.pembinaan.index', compact(
            'pembinaanList',
            'status',
        ));
    }

    /**
     * Simpan pengajuan pembinaan baru (via modal).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'nama_penangkar'            => 'required|string|max:255',
            'nama_penanggung_jawab'     => 'required|string|max:255',
            'nik'                       => 'nullable|digits:16',
            'alamat_penanggung_jawab'   => 'nullable|string|max:255',
            'npwp'                      => 'nullable|string|max:32',
            'lokasi_usaha'              => 'nullable|string|max:255',

            'jenis_benih_diusahakan'    => 'required|in:Biji,Siap Tanam',

            'status_kepemilikan_lahan'  => 'nullable|string|max:100',
            'no_hp'                     => 'nullable|string|max:30',
        ], [
            'jenis_benih_diusahakan.required' => 'Jenis benih wajib dipilih.',
            'jenis_benih_diusahakan.in'       => 'Jenis benih harus Biji atau Siap Tanam.',
        ]);

        $data['user_id'] = $user->id;
        $data['status']  = 'menunggu_jadwal';
        // status_perizinan akan default 'menunggu' dari migration

        PembinaanPenangkar::create($data);

        return redirect()
            ->route('pemohon.pembinaan.index')
            ->with('success', 'Pengajuan pembinaan berhasil dikirim. Silakan menunggu penjadwalan dari admin.');
    }

    /**
     * Update pengajuan pembinaan (hanya boleh kalau status = menunggu_jadwal).
     */
    public function update(Request $request, PembinaanPenangkar $pembinaan)
    {
        $user = Auth::user();

        // pastikan pemiliknya
        if ($pembinaan->user_id !== $user->id) {
            abort(403);
        }

        // hanya boleh edit kalau masih menunggu_jadwal
        if ($pembinaan->status !== 'menunggu_jadwal') {
            return redirect()
                ->route('pemohon.pembinaan.index')
                ->with('error', 'Data pengajuan tidak dapat diubah karena sudah diproses.');
        }

        $data = $request->validate([
            'nama_penangkar'            => 'required|string|max:255',
            'nama_penanggung_jawab'     => 'required|string|max:255',
            'nik'                       => 'nullable|digits:16',
            'alamat_penanggung_jawab'   => 'nullable|string|max:255',
            'npwp'                      => 'nullable|string|max:32',
            'lokasi_usaha'              => 'nullable|string|max:255',

            'jenis_benih_diusahakan'    => 'required|in:Biji,Siap Tanam',

            'status_kepemilikan_lahan'  => 'nullable|string|max:100',
            'no_hp'                     => 'nullable|string|max:30',
        ], [
            'jenis_benih_diusahakan.required' => 'Jenis benih wajib dipilih.',
            'jenis_benih_diusahakan.in'       => 'Jenis benih harus Biji atau Siap Tanam.',
        ]);

        // user_id, status, status_perizinan tidak diubah di sini
        $pembinaan->update($data);

        return redirect()
            ->route('pemohon.pembinaan.index')
            ->with('success', 'Data pengajuan berhasil diperbarui.');
    }

    /**
     * Simpan data OSS (NIB & No Sertifikat Standar) setelah pembinaan selesai.
     * Sekaligus mengubah status_perizinan menjadi "berhasil".
     */
    public function storeOssData(Request $request, PembinaanPenangkar $pembinaan)
{
    $user = Auth::user();

    // Pastikan ini pengajuan milik pemohon yang login
    if ($pembinaan->user_id !== $user->id) {
        abort(403);
    }

    // Hanya boleh isi kalau status pembinaan SELESAI
    if ($pembinaan->status !== 'selesai') {
        return redirect()
            ->route('pemohon.pembinaan.index')
            ->with('error', 'Data OSS hanya dapat diisi setelah pembinaan dinyatakan selesai.');
    }

    // Kalau status perizinan sudah DIBATALKAN oleh admin => pemohon tidak boleh ubah lagi
    if ($pembinaan->status_perizinan === 'dibatalkan') {
        return redirect()
            ->route('pemohon.pembinaan.index')
            ->with('error', 'Status perizinan Anda telah dibatalkan oleh admin. Data OSS tidak dapat diubah.');
    }

    $data = $request->validate([
        'nib'                   => 'required|string|max:100',
        'no_sertifikat_standar' => 'required|string|max:100',
    ], [
        'nib.required'                   => 'NIB wajib diisi.',
        'no_sertifikat_standar.required' => 'Nomor Sertifikat Standar wajib diisi.',
    ]);

    // Isi / update OSS + set status perizinan menjadi BERHASIL
    $pembinaan->update([
        'nib'                   => $data['nib'],
        'no_sertifikat_standar' => $data['no_sertifikat_standar'],
        'status_perizinan'      => 'berhasil',
        'alasan_perizinan'      => null, // reset alasan jika sebelumnya ada
]);

    return redirect()
        ->route('pemohon.pembinaan.index')
        ->with('success', 'Data OSS berhasil disimpan / diperbarui dan status perizinan dinyatakan berhasil.');
}


    public function show(PembinaanPenangkar $pembinaan)
    {
        $user = Auth::user();

        if ($pembinaan->user_id !== $user->id) {
            abort(403);
        }

        $pembinaan->load('sesi');

        return view('pemohon.pembinaan.show', compact('pembinaan'));
    }
}
