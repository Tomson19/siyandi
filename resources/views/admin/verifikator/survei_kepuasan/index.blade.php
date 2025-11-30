@extends('layouts.bootstrap')

@push('styles')
    <style>
        .survey-wrapper {
            background: linear-gradient(135deg, #f5f7ff 0%, #ffffff 55%);
            min-height: 100%;
        }

        .card-survey {
            border-radius: 1.25rem;
            border: 1px solid rgba(15, 76, 129, 0.06);
        }

        .card-survey-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            background: radial-gradient(circle at top left, #e8f1ff 0, #ffffff 55%);
            border-radius: 1.25rem 1.25rem 0 0 !important;
        }

        .filter-pill {
            border-radius: 999px !important;
        }

        .table-survey thead {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .table-survey tbody tr:hover {
            background-color: #f8fbff;
        }

        .badge-rating {
            border-radius: 999px;
            font-size: 0.7rem;
            padding: 0.25rem 0.6rem;
            font-weight: 600;
        }

        .badge-rating.rating-sangat-puas {
            background: rgba(25, 135, 84, 0.12);
            color: #198754;
        }

        .badge-rating.rating-puas {
            background: rgba(13, 110, 253, 0.12);
            color: #0d6efd;
        }

        .badge-rating.rating-cukup {
            background: rgba(255, 193, 7, 0.15);
            color: #b88600;
        }

        .badge-rating.rating-kurang-puas {
            background: rgba(220, 53, 69, 0.08);
            color: #b02a37;
        }

        .badge-rating.rating-tidak-puas {
            background: rgba(108, 117, 125, 0.12);
            color: #495057;
        }

        .badge-soft-info {
            background-color: rgba(13, 202, 240, 0.12);
            color: #0b8ca2;
        }

        @media (max-width: 576px) {
            .survey-header-title {
                font-size: 1.1rem;
            }
        }
    </style>
@endpush

@section('content')
<style>
    body{
            margin-top: -70px;
        }
</style>
    <div class="container-fluid py-3 survey-wrapper">

        {{-- HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h1 class="h5 mb-1 survey-header-title d-flex align-items-center gap-2">
                    <i class="bi bi-emoji-smile text-warning"></i>
                    <span>Survei Kepuasan</span>
                </h1>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="badge badge-soft-info px-3 py-2">
                    <i class="bi bi-people-fill me-1"></i>
                    Total respon: <strong>{{ $total }}</strong>
                </span>
            </div>
        </div>

        <div class="card card-survey shadow-sm">

            {{-- FILTER --}}
            <div class="card-body card-survey-header pb-3">
                <form method="GET" action="{{ route('admin.verifikator.survei_kepuasan.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">
                                Rating (Tampilan/Fitur/Kinerja)
                            </label>
                            <div class="input-group input-group-sm filter-pill shadow-sm">
                                <span class="input-group-text bg-white border-0">
                                    <i class="bi bi-bar-chart-line"></i>
                                </span>
                                <select name="rating" class="form-select border-0">
                                    <option value="">Semua</option>
                                    @foreach ($ratingOptions as $key => $label)
                                        <option value="{{ $key }}" {{ $rating === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">
                                Cari di komentar (Q3/Q4/Q6)
                            </label>
                            <div class="input-group input-group-sm filter-pill shadow-sm">
                                <span class="input-group-text bg-white border-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text"
                                       name="q"
                                       class="form-control border-0"
                                       placeholder="Cari kata di jawaban teks..."
                                       value="{{ $search }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Dari tanggal</label>
                            <div class="input-group input-group-sm filter-pill shadow-sm">
                                <span class="input-group-text bg-white border-0">
                                    <i class="bi bi-calendar-event"></i>
                                </span>
                                <input type="date"
                                       name="from"
                                       class="form-control border-0"
                                       value="{{ $from }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-1">Sampai tanggal</label>
                            <div class="input-group input-group-sm filter-pill shadow-sm">
                                <span class="input-group-text bg-white border-0">
                                    <i class="bi bi-calendar-check"></i>
                                </span>
                                <input type="date"
                                       name="to"
                                       class="form-control border-0"
                                       value="{{ $to }}">
                            </div>
                        </div>

                        <div class="col-md-2 d-flex flex-column gap-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-funnel me-1"></i> Terapkan
                            </button>
                            <a href="{{ route('admin.verifikator.survei_kepuasan.index') }}"
                               class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                {{-- INFO FILTER AKTIF --}}
                @if ($rating || $search || $from || $to)
                    <div class="mt-3 d-flex flex-wrap gap-2 small align-items-center">
                        <span class="text-muted me-1">Filter aktif:</span>

                        @if ($rating)
                            <span class="badge rounded-pill bg-light text-muted border">
                                <i class="bi bi-bar-chart-line me-1"></i>
                                Rating: {{ $ratingOptions[$rating] ?? $rating }}
                            </span>
                        @endif

                        @if ($search)
                            <span class="badge rounded-pill bg-light text-muted border">
                                <i class="bi bi-search me-1"></i> "{{ $search }}"
                            </span>
                        @endif

                        @if ($from)
                            <span class="badge rounded-pill bg-light text-muted border">
                                <i class="bi bi-calendar-event me-1"></i>
                                Dari: {{ $from }}
                            </span>
                        @endif

                        @if ($to)
                            <span class="badge rounded-pill bg-light text-muted border">
                                <i class="bi bi-calendar-check me-1"></i>
                                Sampai: {{ $to }}
                            </span>
                        @endif

                        <a href="{{ route('admin.verifikator.survei_kepuasan.index') }}"
                           class="ms-auto small text-decoration-none text-primary">
                            Bersihkan filter
                        </a>
                    </div>
                @endif
            </div>

            {{-- TABEL --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-sm mb-0 align-middle table-survey">
                        <thead class="table-light text-center small">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th style="width: 140px;">Waktu</th>
                                <th>Tampilan</th>
                                <th>Fitur</th>
                                <th>Kinerja</th>
                                <th style="width: 260px;">Informasi (Q3)</th>
                                <th style="width: 260px;">Yang disukai (Q4)</th>
                                <th style="width: 260px;">Rekomendasi (Q6)</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @php
                                $labelRating = [
                                    'sangat_puas' => 'Sangat Puas',
                                    'puas'        => 'Puas',
                                    'cukup'       => 'Cukup',
                                    'kurang_puas' => 'Kurang Puas',
                                    'tidak_puas'  => 'Tidak Puas',
                                ];

                                $ratingClass = [
                                    'sangat_puas' => 'rating-sangat-puas',
                                    'puas'        => 'rating-puas',
                                    'cukup'       => 'rating-cukup',
                                    'kurang_puas' => 'rating-kurang-puas',
                                    'tidak_puas'  => 'rating-tidak-puas',
                                ];
                            @endphp

                            @forelse ($surveiList as $index => $row)
                                <tr>
                                    <td class="text-center">
                                        {{ $surveiList->firstItem() + $index }}
                                    </td>
                                    <td class="text-center">
                                        {{ $row->created_at?->format('d-m-Y H:i') ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        @php $val = $row->q1_tampilan; @endphp
                                        <span class="badge badge-rating {{ $ratingClass[$val] ?? '' }}">
                                            {{ $labelRating[$val] ?? $val }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php $val = $row->q2_fitur; @endphp
                                        <span class="badge badge-rating {{ $ratingClass[$val] ?? '' }}">
                                            {{ $labelRating[$val] ?? $val }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php $val = $row->q5_kinerja; @endphp
                                        <span class="badge badge-rating {{ $ratingClass[$val] ?? '' }}">
                                            {{ $labelRating[$val] ?? $val }}
                                        </span>
                                    </td>
                                    <td class="text-center" style="white-space: normal; max-width: 190px;">
                                        {{ $row->q3_informasi ?: '-' }}
                                    </td>
                                    <td class="text-center" style="white-space: normal; max-width: 190px;">
                                        {{ $row->q4_sukai ?: '-' }}
                                    </td>
                                    <td class="text-center" style="white-space: normal; max-width: 190px;">
                                        {{ $row->q6_rekomendasi ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="text-center text-muted py-4">
                                            <div class="mb-2">
                                                <i class="bi bi-clipboard-x" style="font-size: 2rem;"></i>
                                            </div>
                                            <div class="fw-semibold">
                                                Belum ada jawaban survei.
                                            </div>
                                            <div class="small">
                                                Survei belum diisi oleh pengguna.
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if ($surveiList->hasPages())
                    <div class="border-top px-3 py-2 d-flex justify-content-end align-items-center small">
                        <div class="mb-0">
                            {{ $surveiList->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
