@extends('layouts.bootstrap')

@section('title', 'Laporan Stok Benih')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/verifikator/laporan-stock.css') }}">
@endpush
<style>
    body{
        margin-top: -70px;
    }
</style>
    <div class="container py-4">

        {{-- ===================== HEADER ===================== --}}
        <div class="mb-4 text-center">
            <div
                class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-light border border-primary-subtle mb-2">
                <i class="bi bi-box-seam text-primary"></i>
                <span class="text-uppercase fw-semibold small text-primary">Laporan Stok Benih</span>
            </div>
            <h3 class="fw-bold mb-1" style="font-size: 1.35rem;">
                Ringkasan & Riwayat Persediaan
            </h3>
            <div class="page-header-line"></div>
        </div>

        {{-- ===================== RINGKASAN STOK ===================== --}}
        <div class="row g-3 mb-4 text-center">
            <div class="col-6 col-md-3">
                <div class="card summary-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body py-3">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-center">
                                <div class="summary-icon">
                                    <i class="bi bi-grid-3x3-gap text-primary"></i>
                                </div>
                            </div>
                            <div class="fw-semibold text-muted small">Total Jenis Benih</div>
                            <h4 class="fw-bold text-primary mb-0">
                                {{ $stokBenih->count() }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card summary-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body py-3">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-center">
                                <div class="summary-icon">
                                    <i class="bi bi-stack text-success"></i>
                                </div>
                            </div>
                            <div class="fw-semibold text-muted small">Total Stok Keseluruhan</div>
                            <h4 class="fw-bold text-success mb-0">
                                {{ number_format($stokBenih->sum('stok')) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card summary-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body py-3">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-center">
                                <div class="summary-icon">
                                    <i class="bi bi-gift text-success"></i>
                                </div>
                            </div>
                            <div class="fw-semibold text-muted small">Stok Gratis</div>
                            <h4 class="fw-bold text-success mb-0">
                                {{ number_format($stokBenih->where('tipe_pembayaran', 'Gratis')->sum('stok')) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card summary-card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body py-3">
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-center">
                                <div class="summary-icon">
                                    <i class="bi bi-cash-coin text-danger"></i>
                                </div>
                            </div>
                            <div class="fw-semibold text-muted small">Stok Berbayar</div>
                            <h4 class="fw-bold text-danger mb-0">
                                {{ number_format($stokBenih->where('tipe_pembayaran', 'Berbayar')->sum('stok')) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== GRAFIK STOK BENIH ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center py-3">
                <div>
                    <div class="card-section-header text-secondary fw-semibold">
                        Grafik Stok per Jenis Benih
                    </div>
                    <small class="text-muted">
                        Visualisasi komposisi stok berdasarkan jenis & tipe pembayaran
                    </small>
                </div>
                <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill small">
                    <i class="bi bi-bar-chart-line me-1"></i> Chart
                </span>
            </div>
            <div class="card-body pt-0">
                <div class="bg-light rounded-4 p-3">
                    <canvas id="stokChart" style="max-height: 320px;"></canvas>
                </div>
            </div>
        </div>

        {{-- ===================== TABEL STOK BENIH ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center py-3">
                <div>
                    <div class="card-section-header text-secondary fw-semibold">
                        Rekapitulasi Stok Benih
                    </div>
                    <small class="text-muted">
                        Daftar stok terkini per jenis tanaman & benih
                    </small>
                </div>
                <span class="badge bg-info-subtle text-info-emphasis rounded-pill small">
                    <i class="bi bi-box me-1"></i> Stok Saat Ini
                </span>
            </div>

            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover align-middle mb-0">
                        <thead class="table-primary text-center">
                            <tr class="align-middle">
                                <th>NO</th>
                                <th>Tanaman</th>
                                <th>Jenis Benih</th>
                                <th>Tipe</th>
                                <th>Harga (Rp)</th>
                                <th>Stok Saat Ini</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stokBenih as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">{{ $item->jenisTanaman->nama_tanaman ?? '-' }}</td>
                                    <td class="text-center">{{ $item->jenis_benih }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill {{ $item->tipe_pembayaran === 'Berbayar' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-success-subtle text-success-emphasis' }}">
                                            {{ $item->tipe_pembayaran }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{ $item->harga ? number_format($item->harga, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($item->stok) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted fst-italic py-3">
                                        Belum ada data stok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===================== RIWAYAT STOK ===================== --}}
        <div id="riwayat-stok" class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent py-3">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <div>
                        <div class="card-section-header text-secondary fw-semibold mb-1">
                            Riwayat Perubahan Stok
                        </div>
                        <small class="text-muted">
                            Lihat histori penambahan / pengurangan stok berdasarkan rentang tanggal
                        </small>
                    </div>
                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill small">
                        <i class="bi bi-clock-history me-1"></i> Riwayat
                    </span>
                </div>
            </div>

            <div class="card-body pt-0">

                {{-- FILTER BAR --}}
                <div class="border rounded-4 px-3 py-2 mb-3 bg-light-subtle">
                    <form id="filterForm" class="row g-2 align-items-end" onsubmit="return false;">

                        {{-- Rentang Tanggal --}}
                        <div class="col-md-5 d-flex flex-wrap gap-2 align-items-center">
                            <span class="filter-chip-label text-muted me-1">
                                Filter Tanggal
                            </span>

                            <div class="d-flex flex-grow-1 gap-2">
                                <div class="flex-fill">
                                    <label for="start_date" class="small fw-semibold text-muted mb-1">Dari</label>
                                    <input type="date" id="start_date" class="form-control form-control-sm"
                                        value="{{ $startDate ?? '' }}">
                                </div>
                                <div class="flex-fill">
                                    <label for="end_date" class="small fw-semibold text-muted mb-1">Sampai</label>
                                    <input type="date" id="end_date" class="form-control form-control-sm"
                                        value="{{ $endDate ?? '' }}">
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Filter --}}
                        <div class="col-md-4 d-flex flex-wrap gap-2">
                            <div>
                                <label class="small fw-semibold text-muted d-block mb-1 invisible">Aksi</label>
                                <button type="button" id="filterBtn" class="btn btn-sm btn-primary">
                                    <i class="bi bi-search"></i> Terapkan
                                </button>
                                <button type="button" id="resetBtn" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Reset
                                </button>
                            </div>
                        </div>

                        {{-- Export --}}
                        <div class="col-md-3 d-flex flex-wrap gap-2 justify-content-md-end">
                            <div>
                                <label class="small fw-semibold text-muted d-block mb-1">Export</label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="#" id="btnExcel" class="btn btn-success">
                                        <i class="bi bi-file-earmark-excel"></i> Excel
                                    </a>
                                    <a href="#" id="btnPdf" class="btn btn-danger">
                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                {{-- TABEL RIWAYAT --}}
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light text-center">
                            <tr class="align-middle">
                                <th style="width: 110px;">Tanggal</th>
                                <th>Benih</th>
                                <th style="width: 110px;">Tipe</th>
                                <th style="width: 90px;">Jumlah</th>
                                <th style="width: 100px;">Stok Awal</th>
                                <th style="width: 100px;">Stok Akhir</th>
                                <th>Keterangan</th>
                                <th style="width: 110px;">Admin</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat-body">
                            @include('admin.verifikator.partials.tabel_riwayat_stok', [
                                'riwayat' => $riwayat,
                            ])
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION RIWAYAT --}}
                <div id="riwayat-pagination" class="mt-2">
                    @include('admin.verifikator.partials.riwayat_stok_pagination', ['riwayat' => $riwayat])
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== CHART.JS ===================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // === GRAFIK STOK ===
            const ctx = document.getElementById('stokChart');
            const labels = @json($stokBenih->map(fn($b) => ($b->jenisTanaman->nama_tanaman ?? '-') . ' (' . $b->jenis_benih . ')'));
            const data = @json($stokBenih->pluck('stok'));
            const tipe = @json($stokBenih->pluck('tipe_pembayaran'));

            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Stok',
                            data: data,
                            backgroundColor: tipe.map(t =>
                                t === 'Berbayar' ?
                                'rgba(220,53,69,0.7)' :
                                'rgba(25,135,84,0.7)'
                            ),
                            borderColor: tipe.map(t =>
                                t === 'Berbayar' ?
                                'rgba(220,53,69,1)' :
                                'rgba(25,135,84,1)'
                            ),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Stok'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Jenis Benih'
                                }
                            }
                        }
                    }
                });
            }

            // === FILTER TANGGAL (AJAX TANPA REFRESH) & PAGINATION ===
            const filterBtn = document.getElementById("filterBtn");
            const resetBtn = document.getElementById("resetBtn");
            const tbody = document.getElementById("riwayat-body");
            const paginationEl = document.getElementById("riwayat-pagination");
            const btnExcel = document.getElementById("btnExcel");
            const btnPdf = document.getElementById("btnPdf");

            async function loadRiwayat(url) {
                tbody.innerHTML =
                    '<tr><td colspan="8" class="text-center text-muted fst-italic py-3">Memuat data...</td></tr>';

                try {
                    const res = await fetch(url, {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    });
                    const data = await res.json();

                    tbody.innerHTML = data.tbody;
                    if (paginationEl) {
                        paginationEl.innerHTML = data.pagination;
                    }
                } catch (err) {
                    tbody.innerHTML =
                        '<tr><td colspan="8" class="text-center text-danger fst-italic py-3">Gagal memuat data.</td></tr>';
                }
            }

            function buildFilterUrl(page = 1) {
                const start = document.getElementById("start_date").value;
                const end = document.getElementById("end_date").value;
                const params = new URLSearchParams();

                if (start) params.append("start_date", start);
                if (end) params.append("end_date", end);
                if (page) params.append("page", page);

                return `{{ route('admin.verifikator.laporan_stok') }}?` + params.toString();
            }

            // tombol filter
            filterBtn.addEventListener("click", function() {
                const start = document.getElementById("start_date").value;
                const end = document.getElementById("end_date").value;

                if (start && end && new Date(end) < new Date(start)) {
                    alert("Tanggal akhir tidak boleh sebelum tanggal awal.");
                    return;
                }

                // selalu mulai dari halaman 1 setelah filter
                loadRiwayat(buildFilterUrl(1));
            });

            // tombol reset
            resetBtn.addEventListener("click", () => {
                document.getElementById("start_date").value = "";
                document.getElementById("end_date").value = "";
                loadRiwayat(buildFilterUrl(1));
            });

            // === PAGINATION CLICK (AJAX) ===
            if (paginationEl) {
                paginationEl.addEventListener("click", function(e) {
                    const link = e.target.closest("a");
                    if (!link) return;

                    // hanya intercept link pagination, bukan link lain
                    if (!link.classList.contains("page-link")) return;

                    e.preventDefault();
                    const url = link.getAttribute("href");
                    if (url) {
                        loadRiwayat(url);
                    }
                });
            }

            // === EXPORT PDF / EXCEL SESUAI FILTER ===
            function getDateParams() {
                const start = document.getElementById('start_date').value;
                const end = document.getElementById('end_date').value;
                const params = new URLSearchParams();
                if (start) params.append('start_date', start);
                if (end) params.append('end_date', end);
                return params.toString();
            }

            btnExcel.addEventListener('click', function(e) {
                e.preventDefault();
                const params = getDateParams();
                const url = `{{ route('admin.verifikator.laporan_stok.export.excel') }}?${params}`;
                window.open(url, '_blank');
            });

            btnPdf.addEventListener('click', function(e) {
                e.preventDefault();
                const params = getDateParams();
                const url = `{{ route('admin.verifikator.laporan_stok.export.pdf') }}?${params}`;
                window.open(url, '_blank');
            });
        });
    </script>
@endsection
