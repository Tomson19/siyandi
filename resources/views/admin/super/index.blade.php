@extends('layouts.bootstrap')

@section('content')
<style>
  body{
    margin-top: -70px;
  }
</style>
    <div class="container-fluid py-4">
        {{-- HEADER --}}
        <div class="row mb-3">
            <div class="col">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-gradient text-white d-flex align-items-center justify-content-center me-3"
                        style="width: 46px; height: 46px;">
                        <i class="bi bi-speedometer2 fs-4"></i>
                    </div>
                    <div>
                        <h1 class="h5 mb-1">Dashboard Super Admin</h1>
                    </div>
                </div>
            </div>
        </div>

        {{-- RINGKASAN JUMLAH --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total User</div>
                            <div class="h5 mb-0">{{ $totalUsers }}</div>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                            <i class="bi bi-people fs-3 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Pemohon</div>
                            <div class="h5 mb-0">{{ $totalPemohon }}</div>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-2">
                            <i class="bi bi-person-badge fs-3 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Admin</div>
                            <div class="h5 mb-0">{{ $totalAdmin }}</div>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2">
                            <i class="bi bi-shield-lock fs-3 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Pemohon blm verifikasi</div>
                            <div class="h5 mb-0">{{ $unverifiedPemohon }}</div>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2">
                            <i class="bi bi-exclamation-triangle fs-3 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            {{-- USER TERBARU --}}
<div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">User Terbaru</h6>
            <a href="{{ route('admin.super.users.index') }}" class="small text-decoration-none">
                Lihat semua
            </a>
        </div>
        <div class="card-body pt-2 pb-2">
            @if ($latestUsers->isEmpty())
                <p class="text-muted small mb-0">Belum ada user.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0 small">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="text-end">Verif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestUsers as $user)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ \Illuminate\Support\Str::limit($user->name, 22) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted">
                                            {{ \Illuminate\Support\Str::limit($user->email, 26) }}
                                        </div>
                                    </td>
                                    <td>
                                        @forelse ($user->roles as $role)
                                            <span class="badge bg-secondary text-uppercase small me-1">
                                                {{ \Illuminate\Support\Str::limit($role->name, 12) }}
                                            </span>
                                        @empty
                                            <span class="text-muted small">-</span>
                                        @endforelse
                                    </td>
                                    <td class="text-end">
                                        @if ($user->email_verified_at)
                                            <span class="badge bg-success">
                                                <i class="bi bi-shield-check"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted">
                                                <i class="bi bi-exclamation-triangle"></i>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>


            {{-- AKTIVITAS LOGIN TERAKHIR --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Aktivitas Login Terakhir</h6>
                        <small class="text-muted">Berdasarkan session aktif</small>
                    </div>
                    <div class="card-body">
                        @if ($recentSessions->isEmpty())
                            <p class="text-muted small mb-0">Belum ada aktivitas login.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>IP Address</th>
                                            <th class="d-none d-md-table-cell">User Agent</th>
                                            <th class="text-end">Aktivitas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentSessions as $session)
                                            <tr>
                                                <td>
                                                    @if ($session->user_name)
                                                        <strong>{{ $session->user_name }}</strong><br>
                                                        <small class="text-muted">{{ $session->user_email }}</small>
                                                    @else
                                                        <span class="text-muted small">Guest / tidak diketahui</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <code class="small">{{ $session->ip_address ?? '-' }}</code>
                                                </td>
                                                <td class="small d-none d-md-table-cell">
                                                    {{ Str::limit($session->user_agent ?? '-', 40) }}
                                                </td>
                                                <td class="text-end small">
                                                    {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
