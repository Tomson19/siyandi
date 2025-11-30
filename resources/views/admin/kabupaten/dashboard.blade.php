@extends('layouts.bootstrap')

@section('content')
<style>
    body{
        margin-top: -70px;
    }
</style>
    <div class="container py-5">

        {{-- HEADER --}}
        <div class="text-center mb-5">
            <h2 class="fw-bold text-primary">🌾 Selamat Datang, {{ $user->name }}!</h2>
        </div>

        {{-- INFORMASI USER --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 h-100 hover-grow">
                    <div class="card-body text-center py-4">
                        <div class="text-success fs-1 mb-2"><i class="bi bi-geo-alt-fill"></i></div>
                        <h6 class="fw-bold text-secondary mb-1">Kabupaten</h6>
                        <h5 class="fw-bold mb-0">
                            {{ optional($user->kabupaten)->nama_kabupaten ?? '-' }}
                        </h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 h-100 hover-grow">
                    <div class="card-body text-center py-4">
                        <div class="text-primary fs-1 mb-2"><i class="bi bi-person-badge-fill"></i></div>
                        <h6 class="fw-bold text-secondary mb-1">Role</h6>
                        <h5 class="fw-bold mb-0 text-capitalize">
                            {{ str_replace('_', ' ', $user->getRoleNames()->first() ?? 'User') }}
                        </h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 h-100 hover-grow">
                    <div class="card-body text-center py-4">
                        <div class="text-info fs-1 mb-2"><i class="bi bi-calendar-check-fill"></i></div>
                        <h6 class="fw-bold text-secondary mb-1">Login Terakhir</h6>
                        <h5 class="fw-bold mb-0">
                            {{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : '-' }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATISTIK UTAMA --}}
        <div class="row g-4 mb-4">
            {{-- Varietas --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center py-3 stat-card h-100">
                    <div class="text-primary fs-2 mb-2"><i class="bi bi-journal-text"></i></div>
                    <h3 class="fw-bold mb-0">{{ $jumlahVarietas ?? 0 }}</h3>
                    <p class="text-muted mb-1">Total Varietas</p>
                    <small class="text-success">
                        ✅ {{ $jumlahVarietasPublished ?? 0 }} sudah dipublish
                    </small>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center py-3 stat-card h-100">
                    <div class="text-success fs-2 mb-2"><i class="bi bi-file-earmark-text"></i></div>
                    <h3 class="fw-bold mb-0">{{ $jumlahDeskripsi ?? 0 }}</h3>
                    <p class="text-muted mb-1">Deskripsi Terdata</p>
                    <small class="text-muted">
                        Form deskripsi varietas yang sudah terisi
                    </small>
                </div>
            </div>

            {{-- Materi Genetik --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center py-3 stat-card h-100">
                    <div class="text-info fs-2 mb-2"><i class="bi bi-diagram-3"></i></div>
                    <h3 class="fw-bold mb-0">{{ $jumlahMateri ?? 0 }}</h3>
                    <p class="text-muted mb-1">Materi Genetik</p>
                    <small class="text-muted">
                        Pohon / rumpun yang tercatat
                    </small>
                </div>
            </div>

            {{-- KBS --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 text-center py-3 stat-card h-100">
                    <div class="text-warning fs-2 mb-2"><i class="bi bi-tree"></i></div>
                    <h3 class="fw-bold mb-0">{{ $jumlahKbs ?? 0 }}</h3>
                    <p class="text-muted mb-1">Kebun Benih Sumber</p>
                    <small class="text-muted">
                        Unit kebun benih di kabupaten ini
                    </small>
                </div>
            </div>
        </div>

        {{-- INFO / TIPS (tanpa statistik penangkar) --}}
        <div class="row g-4 mb-5">
            <div class="col-md-12">
                <div class="alert alert-info border-0 shadow-sm rounded-4 h-100 d-flex align-items-center mb-0">
                    <div>
                        <h6 class="fw-bold mb-1">Tips Pengelolaan Data</h6>
                        <ul class="mb-0 small">
                            <li>Pastikan data varietas yang sudah siap publikasi berstatus <strong>“publish”</strong>.</li>
                            <li>Lengkapi deskripsi varietas agar tampil lengkap di peta publik.</li>
                            <li>Update koordinat materi genetik dan KBS untuk mendukung peta sebaran.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL DATA TERBARU (tanpa penangkar) --}}
        <div class="row g-4">
            {{-- Varietas Terbaru --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                        <span>🆕 Varietas Terbaru</span>
                        <a href="{{ route('admin.varietas.index') }}" class="small text-decoration-none">
                            Lihat semua
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr class="small text-muted">
                                    <th class="ps-3">Nama Varietas</th>
                                    <th>Tanaman</th>
                                    <th class="text-end pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($varietasTerbaru as $v)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="small fw-semibold">{{ $v->nama_varietas }}</div>
                                            <div class="text-muted small">
                                                {{ $v->nomor_tanggal_sk ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="small">
                                            {{ $v->tanaman->nama_tanaman ?? '-' }}
                                        </td>
                                        <td class="text-end pe-3">
                                            @if($v->status === 'published')
                                                <span class="badge bg-success-subtle text-success small">Publish</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary small">Draft</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted small py-3">
                                            Belum ada data varietas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- KBS Terbaru --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                        <span>🌳 KBS Terbaru</span>
                        <a href="{{ route('admin.kabupaten.kbs.index') }}" class="small text-decoration-none">
                            Lihat semua
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr class="small text-muted">
                                    <th class="ps-3">Varietas</th>
                                    <th>Tanaman</th>
                                    <th class="text-center">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kbsTerbaru as $k)
                                    <tr>
                                        <td class="ps-3 small fw-semibold">
                                            {{ $k->nama_varietas }}
                                            <div class="text-muted small">
                                                {{ $k->nomor_sk ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="small">
                                            {{ $k->tanaman->nama_tanaman ?? '-' }}
                                        </td>
                                        <td class="small text-center">
                                            {{ $k->kabupaten->nama_kabupaten ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted small py-3">
                                            Belum ada data KBS.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

    @push('styles')
        <style>
            .stat-card {
                transition: all 0.2s ease-in-out;
            }
            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 0.75rem 1.25rem rgba(0,0,0,0.1);
            }
            .hover-grow {
                transition: all 0.2s ease-in-out;
            }
            .hover-grow:hover {
                transform: scale(1.03);
            }
        </style>
    @endpush
@endsection
