@extends('layouts.halamanutama')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@push('styles')
    <style>
        .section-guide-wrapper {
            background: linear-gradient(135deg, #f3f6ff 0%, #ffffff 60%);
            min-height: 100%;
        }

        .section-guide-card {
            border-radius: 1.25rem;
            border: 1px solid rgba(15, 76, 129, 0.06);
        }

        .section-guide-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            background: radial-gradient(circle at top left, #e8f1ff 0, #ffffff 55%);
            border-radius: 1.25rem 1.25rem 0 0 !important;
        }

        .badge-soft-primary {
            background-color: rgba(13, 110, 253, 0.12);
            color: #0d6efd;
        }

        .filter-pill {
            border-radius: 999px !important;
        }

        .table-guide thead {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .table-guide tbody tr:hover {
            background-color: #f8fbff;
        }

        @media (max-width: 576px) {
            .section-guide-header h2 {
                font-size: 1.25rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="section-guide-wrapper py-4">
        <div class="container">

            {{-- HEADER --}}
            <div class="mb-4 text-center">
                <span class="badge badge-soft-primary px-3 py-1 mb-2">
                    <i class="bi bi-journal-text me-1"></i> Buku Panduan Layanan
                </span>
                <h2 class="fw-bold mb-1">
                    Buku Panduan
                </h2>
            </div>

            <div class="card shadow-sm section-guide-card">
                {{-- FILTER & SEARCH --}}
                <div class="card-body section-guide-header pb-3">
                    <form method="GET"
                          action="{{ route('buku_panduan.public') }}"
                          class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small text-muted mb-1">
                                Cari Buku
                            </label>
                            <div class="input-group input-group-sm filter-pill shadow-sm">
                                <span class="input-group-text bg-white border-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text"
                                       name="q"
                                       class="form-control border-0"
                                       placeholder="Cari nama buku panduan..."
                                       value="{{ $search }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">
                                Tahun Terbit
                            </label>
                            <div class="input-group input-group-sm filter-pill shadow-sm">
                                <span class="input-group-text bg-white border-0">
                                    <i class="bi bi-calendar3"></i>
                                </span>
                                <select name="year" class="form-select border-0">
                                    <option value="">Semua Tahun</option>
                                    @foreach ($years as $y)
                                        <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 d-flex gap-2 justify-content-md-end">
                            <button type="submit" class="btn btn-sm btn-primary px-3 w-100 w-md-auto">
                                <i class="bi bi-funnel me-1"></i> Terapkan Filter
                            </button>
                            <a href="{{ route('buku_panduan.public') }}"
                               class="btn btn-sm btn-outline-secondary px-3 w-100 w-md-auto">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                        </div>
                    </form>

                    {{-- INFO FILTER AKTIF --}}
                    @if ($search || $year)
                        <div class="mt-3 d-flex flex-wrap gap-2 small align-items-center">
                            <span class="text-muted me-1">Filter aktif:</span>

                            @if ($search)
                                <span class="badge rounded-pill bg-light text-muted border">
                                    <i class="bi bi-search me-1"></i> "{{ $search }}"
                                </span>
                            @endif

                            @if ($year)
                                <span class="badge rounded-pill bg-light text-muted border">
                                    <i class="bi bi-calendar3 me-1"></i> Tahun {{ $year }}
                                </span>
                            @endif

                            <a href="{{ route('buku_panduan.public') }}"
                               class="ms-auto small text-decoration-none text-primary">
                                Bersihkan filter
                            </a>
                        </div>
                    @endif
                </div>

                {{-- TABEL --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-sm mb-0 align-middle table-guide">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th class="text-start">Nama Buku Panduan</th>
                                    <th style="width: 160px;">Diterbitkan</th>
                                    <th style="width: 130px;">Dokumen</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @forelse ($bukuList as $index => $row)
                                    @php
                                        $docYear = optional($row->created_at)->format('Y');
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            {{ $bukuList->firstItem() + $index }}
                                        </td>

                                        <td>
                                            <div class="fw-semibold text-dark mb-1">
                                                {{ $row->nama_buku }}
                                            </div>
                                            <div class="text-muted d-flex flex-wrap gap-2 small">
                                                @if ($row->created_at)
                                                    <span>
                                                        <i class="bi bi-clock-history me-1"></i>
                                                        {{ $row->created_at->format('d M Y') }}
                                                    </span>
                                                @endif

                                                @if ($docYear)
                                                    <span class="badge bg-light text-muted border">
                                                        Tahun {{ $docYear }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            {{ $row->created_at?->format('d-m-Y') ?? '-' }}
                                        </td>

                                        <td class="text-center">
                                            @if ($row->file_path && Storage::disk('public')->exists($row->file_path))
                                                <a href="{{ Storage::url($row->file_path) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-primary filter-pill px-3">
                                                    <i class="bi bi-file-earmark-text me-1"></i>
                                                    Lihat File
                                                </a>
                                            @else
                                                <span class="text-muted">
                                                    <i class="bi bi-dash-lg"></i>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center text-muted py-4">
                                                <div class="mb-2">
                                                    <i class="bi bi-folder-x" style="font-size: 2rem;"></i>
                                                </div>
                                                <div class="fw-semibold">
                                                    Belum ada data buku panduan
                                                </div>
                                                <div class="small">
                                                    Silakan kembali beberapa saat lagi.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    @if ($bukuList->hasPages())
                        <div class="border-top px-3 py-2 d-flex justify-content-between align-items-center small">
                            <span class="text-muted">
                                Menampilkan {{ $bukuList->firstItem() }}–{{ $bukuList->lastItem() }}
                                dari {{ $bukuList->total() }} buku panduan
                            </span>
                            <div class="mb-0">
                                {{ $bukuList->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
