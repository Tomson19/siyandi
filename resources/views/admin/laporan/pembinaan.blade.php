@extends('layouts.bootstrap')

@section('content')
<style>
    body{
        margin-top: -70px;
    }

    .page-header-badge {
        font-size: .75rem;
        border-radius: 999px;
        padding-inline: .75rem;
    }

    .card-header-soft {
        background: #f8f9fa;
        border-bottom: 0;
    }

    .icon-circle {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .stat-card {
        border-radius: 1rem;
        padding: 1rem 1.1rem;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: "";
        position: absolute;
        right: -30px;
        top: -30px;
        width: 80px;
        height: 80px;
        border-radius: 999px;
        opacity: .15;
    }

    .stat-card-primary::before {
        background: #0d6efd;
    }
    .stat-card-success::before {
        background: #198754;
    }
    .stat-card-warning::before {
        background: #ffc107;
    }
    .stat-card-danger::before {
        background: #dc3545;
    }
    .stat-card-info::before {
        background: #0dcaf0;
    }
    .stat-card-secondary::before {
        background: #6c757d;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .stat-label {
        font-size: .8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .section-title {
        font-size: .85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6c757d;
    }

    .badge-pill-soft {
        border-radius: 999px;
        padding-inline: .65rem;
        font-size: .75rem;
    }

    .card-gradient-header {
        background: linear-gradient(135deg, #0d6efd, #6610f2);
        color: #fff;
        border: none;
    }
</style>

<div class="container-fluid py-4">
    {{-- HEADER --}}
    <div class="row mb-3 align-items-center">
        <div class="col">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="icon-circle bg-primary text-white">
                    <i class="bi bi-graph-up-arrow"></i>
                </span>
                <div>
                    <h1 class="h5 mb-0">Laporan Pembinaan Penangkar & Kebun Benih Sumber</h1>
                </div>
            </div>
        </div>
        <div class="col-auto d-none d-md-block">
            <span class="badge bg-success-subtle text-success border border-success-subtle page-header-badge">
                <i class="bi bi-shield-check me-1"></i> Laporan Admin
            </span>
        </div>
    </div>

    {{-- FILTER PERIODE --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="section-title">
                    Filter Periode
                </span>
                <span class="badge-pill-soft bg-light border text-muted">
                    <i class="bi bi-clock-history me-1"></i>
                    Periode:
                    @if($tahun)
                        Tahun <strong>{{ $tahun }}</strong>
                    @else
                        <strong>Semua Tahun</strong>
                    @endif
                    @if($bulan)
                        , Bulan <strong>{{ $bulanList[(int)$bulan] ?? $bulan }}</strong>
                    @else
                        , <strong>Semua Bulan</strong>
                    @endif
                </span>
            </div>

            <form method="GET"
                  action="{{ route('admin.laporan.pembinaan.index') }}"
                  class="row g-2 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small mb-0 text-muted">
                        <i class="bi bi-calendar3 me-1"></i>Tahun
                    </label>
                    <select name="tahun" class="form-select form-select-sm">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ (string)$tahun === (string)$y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small mb-0 text-muted">
                        <i class="bi bi-calendar2-month me-1"></i>Bulan
                    </label>
                    <select name="bulan" class="form-select form-select-sm">
                        <option value="">Semua Bulan</option>
                        @foreach($bulanList as $num => $label)
                            <option value="{{ $num }}" {{ (string)$bulan === (string)$num ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto mt-2 mt-md-4">
                    <button class="btn btn-sm btn-primary" type="submit">
                        <i class="bi bi-filter me-1"></i>Terapkan
                    </button>
                    @if(request()->hasAny(['tahun','bulan']) && (request('tahun') || request('bulan')))
                        <a href="{{ route('admin.laporan.pembinaan.index') }}"
                           class="btn btn-sm btn-outline-secondary ms-1">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- RINGKASAN ATAS (PENANGKAR & KBS TOTAL) --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="section-title">Ringkasan Singkat</span>
                        <span class="icon-circle bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-people"></i>
                        </span>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="stat-card stat-card-primary bg-primary bg-opacity-10 border-0">
                                <div class="stat-label">Total Pengajuan Penangkar</div>
                                <div class="stat-value mt-1">{{ $penangkarTotal }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="stat-card stat-card-success bg-success bg-opacity-10 border-0">
                                <div class="stat-label">Total Pengajuan KBS</div>
                                <div class="stat-value mt-1">{{ $kbsTotal }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">
                                Persentase Pembinaan Penangkar Selesai
                            </small>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold">{{ $persentasePembinaanSelesai }}%</span>
                                <span class="small text-muted">
                                    {{ $penangkarSelesai }}/{{ $penangkarTotal ?: 1 }}
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success"
                                     role="progressbar"
                                     style="width: {{ $persentasePembinaanSelesai }}%;"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">
                                Persentase Perizinan Berhasil (dari yang selesai)
                            </small>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold">{{ $persentasePerizinanBerhasil }}%</span>
                                <span class="small text-muted">
                                    {{ $perizinanBerhasil }}/{{ $penangkarSelesai ?: 1 }}
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-info"
                                     role="progressbar"
                                     style="width: {{ $persentasePerizinanBerhasil }}%;"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- kecilkan highlight OSS --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="section-title">Pipeline OSS & Perizinan</span>
                        <span class="icon-circle bg-info bg-opacity-10 text-info">
                            <i class="bi bi-shield-lock"></i>
                        </span>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="stat-card stat-card-info bg-info bg-opacity-10 border-0">
                                <div class="stat-label">OSS Lengkap</div>
                                <div class="stat-value mt-1">{{ $ossLengkap }}</div>
                                <div class="small text-muted mt-1">
                                    NIB & Sertifikat terisi
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="stat-card stat-card-success bg-success bg-opacity-10 border-0">
                                <div class="stat-label">Perizinan Berhasil</div>
                                <div class="stat-value mt-1">{{ $perizinanBerhasil }}</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="stat-card stat-card-danger bg-danger bg-opacity-10 border-0">
                                <div class="stat-label">Perizinan Dibatalkan</div>
                                <div class="stat-value mt-1">{{ $perizinanDibatalkan }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Data OSS diinput oleh pemohon, admin hanya mengatur status perizinan.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RINGKASAN DETAIL PENANGKAR --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header card-gradient-header d-flex justify-content-between align-items-center">
            <div>
                <span class="small fw-semibold text-uppercase">Pembinaan Calon Penangkar</span>
                <div class="small">
                    Status pembinaan & perizinan untuk calon penangkar.
                </div>
            </div>
            <span class="badge-pill-soft bg-light text-dark">
                <i class="bi bi-people-fill me-1"></i>
                Penangkar
            </span>
        </div>
        <div class="card-body bg-light bg-opacity-50">
            <div class="row g-3">
                <div class="col-md-2 col-6">
                    <div class="stat-card stat-card-secondary bg-white border">
                        <div class="stat-label">Total</div>
                        <div class="stat-value mt-1">{{ $penangkarTotal }}</div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card stat-card-warning bg-warning bg-opacity-10 border-0">
                        <div class="stat-label">Menunggu Jadwal</div>
                        <div class="stat-value mt-1">{{ $penangkarMenungguJadwal }}</div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card stat-card-info bg-info bg-opacity-10 border-0">
                        <div class="stat-label">Dijadwalkan</div>
                        <div class="stat-value mt-1">{{ $penangkarDijadwalkan }}</div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card stat-card-success bg-success bg-opacity-10 border-0">
                        <div class="stat-label">Selesai</div>
                        <div class="stat-value mt-1">{{ $penangkarSelesai }}</div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card stat-card-danger bg-danger bg-opacity-10 border-0">
                        <div class="stat-label">Dibatalkan</div>
                        <div class="stat-value mt-1">{{ $penangkarBatal }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RINGKASAN KBS --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header card-header-soft d-flex justify-content-between align-items-center">
            <div>
                <span class="small fw-semibold">Pembinaan Kebun Benih Sumber</span>
                <div class="small text-muted">
                    Rekap status pembinaan KBS.
                </div>
            </div>
            <span class="icon-circle bg-success bg-opacity-10 text-success">
                <i class="bi bi-tree-fill"></i>
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2 col-6">
                    <div class="stat-card stat-card-secondary bg-light border-0">
                        <div class="stat-label">Total KBS</div>
                        <div class="stat-value mt-1">{{ $kbsTotal }}</div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card stat-card-warning bg-warning bg-opacity-10 border-0">
                        <div class="stat-label">Menunggu Jadwal</div>
                        <div class="stat-value mt-1">{{ $kbsMenungguJadwal }}</div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card stat-card-info bg-info bg-opacity-10 border-0">
                        <div class="stat-label">Dijadwalkan</div>
                        <div class="stat-value mt-1">{{ $kbsDijadwalkan }}</div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card stat-card-success bg-success bg-opacity-10 border-0">
                        <div class="stat-label">Selesai</div>
                        <div class="stat-value mt-1">{{ $kbsSelesai }}</div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="stat-card stat-card-danger bg-danger bg-opacity-10 border-0">
                        <div class="stat-label">Dibatalkan</div>
                        <div class="stat-value mt-1">{{ $kbsBatal }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KOMODITAS TERPOPULER --}}
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header card-header-soft d-flex justify-content-between align-items-center">
                    <span class="small fw-semibold">Komoditas Terbanyak - Penangkar</span>
                    <span class="icon-circle bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-list-ul"></i>
                    </span>
                </div>
                <div class="card-body small">
                    @if($topKomoditasPenangkar->isEmpty())
                        <div class="text-muted">
                            Belum ada data komoditas untuk periode ini.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Jenis Benih</th>
                                        <th class="text-end">Jumlah Pengajuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topKomoditasPenangkar as $i => $row)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td style="white-space: normal;">
                                                {{ $row->jenis_benih_diusahakan ?? '-' }}
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-primary-subtle text-primary">
                                                    {{ $row->total }}
                                                </span>
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

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header card-header-soft d-flex justify-content-between align-items-center">
                    <span class="small fw-semibold">Komoditas Terbanyak - Kebun Benih Sumber</span>
                    <span class="icon-circle bg-success bg-opacity-10 text-success">
                        <i class="bi bi-list-ul"></i>
                    </span>
                </div>
                <div class="card-body small">
                    @if($topKomoditasKbs->isEmpty())
                        <div class="text-muted">
                            Belum ada data komoditas KBS untuk periode ini.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Komoditas</th>
                                        <th class="text-end">Jumlah Pengajuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topKomoditasKbs as $i => $row)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td style="white-space: normal;">
                                                {{ $row->nama_tanaman ?? '-' }}
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-success-subtle text-success">
                                                    {{ $row->total }}
                                                </span>
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
