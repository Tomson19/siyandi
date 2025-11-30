<?php

namespace App\Http\Controllers\Admin\Super;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ringkasan jumlah
        $totalUsers   = User::count();
        $totalPemohon = User::role('pemohon')->count();
        $totalAdmin   = User::whereHas('roles', function ($q) {
            $q->where('name', '!=', 'pemohon');
        })->count();

        // Pemohon yang belum verifikasi email
        $unverifiedPemohon = User::role('pemohon')
            ->whereNull('email_verified_at')
            ->count();

        // User terbaru
        $latestUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get();

        // Aktivitas login terakhir (diambil dari tabel sessions)
        $recentSessions = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->select(
                'sessions.id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->orderByDesc('sessions.last_activity')
            ->limit(5)
            ->get();

        return view('admin.super.index', compact(
            'totalUsers',
            'totalPemohon',
            'totalAdmin',
            'unverifiedPemohon',
            'latestUsers',
            'recentSessions'
        ));
    }
}
