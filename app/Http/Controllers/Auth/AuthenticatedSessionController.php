<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login user.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Proses autentikasi default Laravel Breeze
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();
        $role = $user->getRoleNames()->first();

        // === Mapping untuk role utama ===
        $roleRoutes = [
            'admin_super' => 'admin.super.dashboard',
            'admin_operator' => 'admin.verifikator.laporan_penjualan',
            'admin_verifikator' => 'admin.verifikator.laporan_penjualan',
            'admin_keuangan' => 'admin.keuangan.dashboard',
            'admin_upt_sertifikasi' => 'admin.upt_sertifikasi.penangkar.index',
            'admin_manager' => 'admin.verifikator.laporan_penjualan',
            'pemohon' => 'pemohon.dashboard',
            'admin_bidang_produksi'  => 'admin.program_kegiatan.index',
        ];

        // Jika role ada di daftar utama → redirect langsung
        if (array_key_exists($role, $roleRoutes)) {
            return redirect()->route($roleRoutes[$role]);
        }

        // === Role admin kabupaten (dinamis) ===
        if (str_starts_with($role, 'admin_')) {
            // Contoh: admin_kampar → admin.kampar.dashboard
            $routeName = str_replace('_', '.', $role) . '.dashboard';

            // Jika route tersebut memang ada
            if (app('router')->has($routeName)) {
                return redirect()->route($routeName);
            }

            // fallback jika route kabupaten belum ada
            return redirect()->route('admin.kabupaten.dashboard');
        }

        // === Jika role tidak dikenali ===
        return redirect('/login')->withErrors([
            'email' => 'Role user tidak dikenali, hubungi admin sistem.',
        ]);
    }

    /**
     * Logout user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
