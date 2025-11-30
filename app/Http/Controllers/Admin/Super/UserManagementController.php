<?php

namespace App\Http\Controllers\Admin\Super;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Hitung prioritas urutan user di list.
     * 1 = admin_super
     * 2 = admin lain (bukan pemohon)
     * 3 = pemohon
     * 4 = lain-lain (fallback)
     */
    protected function getUserPriority(User $user): int
    {
        if ($user->hasRole('admin_super')) {
            return 1;
        }

        if ($user->hasAnyRole([
            'admin_operator',
            'admin_verifikator',
            'admin_keuangan',
            'admin_manager',
            'admin_upt_sertifikasi',
            'admin_pekanbaru',
            'admin_kampar',
            'admin_bengkalis',
            'admin_indragiri_hulu',
            'admin_indragiri_hilir',
            'admin_kuantan_singingi',
            'admin_pelalawan',
            'admin_rokan_hilir',
            'admin_rokan_hulu',
            'admin_siak',
            'admin_kepulauan_meranti',
            'admin_dumai',
        ])) {
            return 2;
        }

        if ($user->hasRole('pemohon')) {
            return 3;
        }

        return 4;
    }

    /**
     * Daftar semua user + ringkasan (dengan sorting custom + pagination).
     */
   public function index(Request $request)
{
    $search      = $request->query('q');
    $type        = $request->query('type');         // null | 'admin' | 'pemohon'
    $emailStatus = $request->query('email_status'); // null | 'verified' | 'unverified'

    $perPage = 10;
    $page    = LengthAwarePaginator::resolveCurrentPage();

    // Ambil semua user yang cocok dengan pencarian
    $usersQuery = User::with('roles');

    if ($search) {
        $usersQuery->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $allUsers = $usersQuery->get();

    // ==== FILTER TYPE (ADMIN / PEMOHON) ====
    $filtered = $allUsers->filter(function (User $user) use ($type) {
        if ($type === 'admin') {
            // semua yang BUKAN pemohon
            return ! $user->hasRole('pemohon');
        }

        if ($type === 'pemohon') {
            return $user->hasRole('pemohon');
        }

        // default: semua
        return true;
    });

    // ==== FILTER STATUS EMAIL (VERIFIED / UNVERIFIED) ====
    $filtered = $filtered->filter(function (User $user) use ($emailStatus) {
        if ($emailStatus === 'verified') {
            return ! is_null($user->email_verified_at);
        }

        if ($emailStatus === 'unverified') {
            return is_null($user->email_verified_at);
        }

        // default: semua
        return true;
    });

    // Sort by priority + nama
    $sorted = $filtered
        ->sortBy(function (User $user) {
            return [
                $this->getUserPriority($user),
                strtolower($user->name),
            ];
        })
        ->values(); // reset index

    // Manual pagination di Collection
    $pagedUsers = new LengthAwarePaginator(
        $sorted->forPage($page, $perPage),
        $sorted->count(),
        $perPage,
        $page,
        [
            'path'  => $request->url(),
            'query' => $request->query(),
        ]
    );

    $users = $pagedUsers;

    // Ringkasan jumlah
    $totalUsers   = User::count();
    $totalPemohon = User::role('pemohon')->count();
    $totalAdmin   = User::whereHas('roles', function ($q) {
        $q->where('name', '!=', 'pemohon');
    })->count();

    return view('admin.super.users.index', compact(
        'users',
        'totalUsers',
        'totalPemohon',
        'totalAdmin',
        'search',
        'type',
        'emailStatus',
    ));
}


    /**
     * Update data user (nama + email).
     */
    public function update(Request $request, User $user)
    {
        $request->validate(
            [
                'name'  => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],
            ],
            [
                'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            ]
        );

        $emailChanged = $user->email !== $request->email;

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($user->hasRole('pemohon') && $emailChanged) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }

        $user->save();

        return redirect()
            ->route('admin.super.users.index')
            ->with('status', 'user-updated');
    }
}
