<?php

namespace App\Http\Controllers\Pemohon;

use App\Http\Controllers\Controller;
use App\Models\PembinaanKebunBenihSumber;
use App\Models\JenisTanaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembinaanKebunBenihSumberController extends Controller
{
    /**
     * Daftar pengajuan pembinaan KBS milik pemohon.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $status = $request->query('status');

        $query = PembinaanKebunBenihSumber::with(['sesi', 'jenisTanaman'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $pembinaanList = $query->paginate(10)->withQueryString();

        // data jenis tanaman untuk dropdown di modal create & edit
        $jenisTanaman = JenisTanaman::orderBy('nama_tanaman')->get();

        return view('pemohon.pembinaan_kbs.index', compact(
            'pembinaanList',
            'status',
            'jenisTanaman',
        ));
    }

    /**
     * Simpan pengajuan pembinaan KBS baru (via form / modal).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'nama'               => 'required|string|max:255',
            'nik'                => 'nullable|digits:16',
            'alamat'             => 'nullable|string|max:255',
            'no_hp'              => 'nullable|string|max:30',

            'jenis_tanaman_id'   => 'required|exists:jenis_tanaman,id',

            'lokasi_kebun'       => 'nullable|string|max:255',
            'latitude_kebun'     => 'nullable|numeric',
            'longitude_kebun'    => 'nullable|numeric',
            'jumlah_pohon_induk' => 'nullable|integer|min:0',
        ], [
            'jenis_tanaman_id.required' => 'Komoditas (jenis tanaman) wajib dipilih.',
            'jenis_tanaman_id.exists'   => 'Komoditas yang dipilih tidak valid.',
        ]);

        $data['user_id'] = $user->id;
        $data['status']  = 'menunggu_jadwal';

        PembinaanKebunBenihSumber::create($data);

        return redirect()
            ->route('pemohon.pembinaan-kbs.index')
            ->with('success', 'Pengajuan pembinaan kebun benih sumber berhasil dikirim. Silakan menunggu penjadwalan dari admin.');
    }

    /**
     * Update pengajuan pembinaan KBS (hanya boleh kalau status = menunggu_jadwal).
     */
    public function update(Request $request, PembinaanKebunBenihSumber $pembinaanKbs)
    {
        $user = Auth::user();

        // pastikan pemiliknya
        if ($pembinaanKbs->user_id !== $user->id) {
            abort(403);
        }

        // hanya boleh edit kalau masih menunggu_jadwal
        if ($pembinaanKbs->status !== 'menunggu_jadwal') {
            return redirect()
                ->route('pemohon.pembinaan-kbs.index')
                ->with('error', 'Data pengajuan tidak dapat diubah karena sudah diproses.');
        }

        $data = $request->validate([
            'nama'               => 'required|string|max:255',
            'nik'                => 'nullable|digits:16',
            'alamat'             => 'nullable|string|max:255',
            'no_hp'              => 'nullable|string|max:30',

            'jenis_tanaman_id'   => 'required|exists:jenis_tanaman,id',

            'lokasi_kebun'       => 'nullable|string|max:255',
            'latitude_kebun'     => 'nullable|numeric',
            'longitude_kebun'    => 'nullable|numeric',
            'jumlah_pohon_induk' => 'nullable|integer|min:0',
        ], [
            'jenis_tanaman_id.required' => 'Komoditas (jenis tanaman) wajib dipilih.',
            'jenis_tanaman_id.exists'   => 'Komoditas yang dipilih tidak valid.',
        ]);

        $pembinaanKbs->update($data);

        return redirect()
            ->route('pemohon.pembinaan-kbs.index')
            ->with('success', 'Data pengajuan pembinaan kebun benih sumber berhasil diperbarui.');
    }

    /**
     * Detail pengajuan pembinaan KBS milik pemohon.
     */
    public function show(PembinaanKebunBenihSumber $pembinaanKbs)
    {
        $user = Auth::user();

        if ($pembinaanKbs->user_id !== $user->id) {
            abort(403);
        }

        $pembinaanKbs->load(['sesi', 'jenisTanaman']);

        // dropdown komoditas di modal edit di halaman show
        $jenisTanaman = JenisTanaman::orderBy('nama_tanaman')->get();

        return view('pemohon.pembinaan_kbs.show', compact('pembinaanKbs', 'jenisTanaman'));
    }
}
