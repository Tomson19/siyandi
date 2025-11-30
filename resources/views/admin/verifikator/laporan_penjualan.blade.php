@extends('layouts.bootstrap')

@section('title', 'Laporan Penjualan Benih')

@section('content')
    <style>
        body {
            margin-top: -70px;
        }
    </style>
    <div class="container py-4">

        {{-- ===================== HEADER ===================== --}}
        <div class="text-center mb-4">
            <h3 class="fw-bold text-uppercase mb-2" style="font-size: 1.2rem;">
                <i class="bi bi-bar-chart-line me-2 text-primary"></i> Laporan Penjualan Benih
            </h3>
            <div class="mx-auto" style="width: 200px; height: 2px; background: linear-gradient(90deg,#0d6efd,#17a2b8);">
            </div>
        </div>

        {{-- ===================== RINGKASAN PENJUALAN ===================== --}}
        @php
            $avgTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;
            $totalJenisTerjual = $penjualan->count(); // group per benih
        @endphp

        <div class="row g-3 mb-4 text-center">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body py-3">
                        <div class="small text-muted mb-1">Total Pendapatan</div>
                        <h5 class="fw-bold text-success mb-0">
                            Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
                        </h5>
                        <div class="small text-muted mt-1">
                            Mode: {{ ucfirst($mode) }}
                            @if ($tipe)
                                · Tipe: {{ $tipe }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body py-3">
                        <div class="small text-muted mb-1">Total Transaksi Berhasil</div>
                        <h5 class="fw-bold mb-0">
                            {{ number_format($totalTransaksi) }}
                        </h5>
                        <div class="small text-muted mt-1">
                            Transaksi berhasil
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body py-3">
                        <div class="small text-muted mb-1">Rata-rata Nilai Transaksi</div>
                        <h5 class="fw-bold mb-0">
                            Rp{{ number_format($avgTransaksi, 0, ',', '.') }}
                        </h5>
                        <div class="small text-muted mt-1">
                            Per transaksi berhasil
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body py-3">
                        <div class="small text-muted mb-1">Jenis Benih Terjual</div>
                        <h5 class="fw-bold mb-0">
                            {{ $totalJenisTerjual }}
                        </h5>
                        <div class="small text-muted mt-1">
                            === <span class="text-lowercase">===</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== GRAFIK PENJUALAN (LINE – PER WAKTU) ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center py-3">
                <div>
                    <div class="text-uppercase small text-secondary fw-semibold">
                        Grafik Penjualan Benih
                    </div>
                    <small class="text-muted">
                        Mode: {{ ucfirst($mode) }}
                        @if ($mode === 'bulan' || $mode === 'hari')
                            · Tahun: {{ $year }}
                        @endif
                        @if ($mode === 'hari')
                            · Bulan: {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                        @endif
                        @if ($tipe)
                            · Tipe: {{ $tipe }}
                        @endif
                    </small>
                </div>
            </div>
            <div class="card-body pt-0">
                <canvas id="penjualanChart" height="100"></canvas>
            </div>
        </div>

        {{-- ===================== GRAFIK PENJUALAN PER JENIS BENIH (BAR) ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent d-flex justify-content-between align-items-center py-3">
                <div>
                    <div class="text-uppercase small text-secondary fw-semibold">
                        Grafik Penjualan per Jenis Benih
                    </div>
                    <small class="text-muted">
                        Total jumlah benih terjual (Gratis &amp; Berbayar) berdasarkan filter saat ini
                    </small>
                </div>
                <span class="badge bg-info-subtle text-info-emphasis rounded-pill small">
                    <i class="bi bi-bar-chart-fill me-1"></i> Per Benih
                </span>
            </div>
            <div class="card-body pt-0">
                <canvas id="penjualanPerBenihChart" height="110"></canvas>
            </div>
        </div>

        {{-- ===================== TABEL PENJUALAN (TRANSaksi + PAGINATION) ===================== --}}
        <div id="penjualan-table" class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent py-3">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase small text-secondary fw-semibold mb-1">
                            Data Penjualan Benih (Transaksi Berhasil)
                        </div>
                        <small class="text-muted">
                            Menampilkan transaksi per permohonan (10 terakhir per halaman, mengikuti filter)
                        </small>
                    </div>

                    {{-- FILTER + EXPORT UNTUK PENJUALAN --}}
                    <form method="GET" action="{{ route('admin.verifikator.laporan_penjualan') }}#penjualan-table"
                        class="d-flex flex-wrap align-items-center gap-2">


                        {{-- Mode --}}
                        <div class="d-flex align-items-center gap-1">
                            <label for="mode" class="small fw-semibold text-muted">Mode:</label>
                            <select name="mode" id="mode" class="form-select form-select-sm"
                                onchange="this.form.submit()">
                                <option value="tahun" {{ $mode === 'tahun' ? 'selected' : '' }}>Per Tahun</option>
                                <option value="bulan" {{ $mode === 'bulan' ? 'selected' : '' }}>Per Bulan</option>
                                <option value="hari" {{ $mode === 'hari' ? 'selected' : '' }}>Per Hari</option>
                            </select>
                        </div>

                        {{-- Tahun --}}
                        @if (in_array($mode, ['bulan', 'hari']))
                            <div class="d-flex align-items-center gap-1">
                                <label for="year" class="small fw-semibold text-muted">Tahun:</label>
                                <select name="year" id="year" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        @endif

                        {{-- Bulan --}}
                        @if ($mode === 'hari')
                            <div class="d-flex align-items-center gap-1">
                                <label for="month" class="small fw-semibold text-muted">Bulan:</label>
                                <select name="month" id="month" class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        @endif

                        {{-- Tipe --}}
                        <div class="d-flex align-items-center gap-1">
                            <label for="tipe" class="small fw-semibold text-muted">Tipe:</label>
                            <select name="tipe" id="tipe" class="form-select form-select-sm"
                                onchange="this.form.submit()">
                                <option value="">Semua</option>
                                <option value="Gratis" {{ $tipe == 'Gratis' ? 'selected' : '' }}>Gratis</option>
                                <option value="Berbayar" {{ $tipe == 'Berbayar' ? 'selected' : '' }}>Berbayar</option>
                            </select>
                        </div>

                        {{-- Export --}}
                        <div class="d-flex gap-1 ms-0 ms-md-2">
                            <a href="{{ route('admin.verifikator.laporan_penjualan.export.excel', request()->query()) }}"
                                class="btn btn-sm btn-success">
                                <i class="bi bi-file-earmark-excel"></i>
                                <span class="d-none d-md-inline">Excel</span>
                            </a>

                            <a href="{{ route('admin.verifikator.laporan_penjualan.export.pdf', request()->query()) }}"
                                class="btn btn-sm btn-danger">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span class="d-none d-md-inline">PDF</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body pt-0 table-responsive">
                <table class="table table-hover align-middle small">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Nama Pemohon</th>
                            <th>Tanaman</th>
                            <th>Jenis Benih</th>
                            <th>Harga Satuan (Rp)</th>
                            <th>Jumlah Terjual</th>
                            <th>Total Pendapatan (Rp)</th>
                            <th>Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp

                        @forelse ($penjualanTable as $row)
                            @php
                                $benih = $row->benih ?? null;
                                $grandTotal += $row->nominal_pembayaran;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    {{ ($penjualanTable->currentPage() - 1) * $penjualanTable->perPage() + $loop->iteration }}
                                </td>

                                <td>
                                    {{ $row->nama ?? ($row->user->name ?? '-') }}
                                </td>

                                <td>{{ $benih->jenisTanaman->nama_tanaman ?? '-' }}</td>
                                <td>{{ $benih->jenis_benih ?? '-' }}</td>
                                <td class="text-end">
                                    {{ number_format($benih->harga ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-semibold">
                                    {{ number_format($row->jumlah_disetujui ?? 0) }}
                                </td>
                                <td class="text-end text-success fw-bold">
                                    Rp{{ number_format($row->nominal_pembayaran ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-semibold">
                                    {{ number_format($benih->stok ?? 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted fst-italic">
                                    Belum ada penjualan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end fw-bold">
                                Total Pendapatan (halaman ini):
                            </td>
                            <td class="text-end fw-bold text-danger">
                                Rp{{ number_format($grandTotal, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                {{-- PAGINATION --}}
                <div class="mt-3 d-flex justify-content-end">
                    {{ $penjualanTable->links() }}
                </div>
            </div>
        </div>

        {{-- ===================== TOP 5 BENIH TERLARIS ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header border-0 bg-transparent py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <div class="text-uppercase small text-secondary fw-semibold mb-1">
                            Top 5 Benih Terlaris
                        </div>
                        <small class="text-muted">
                            Berdasarkan total pendapatan pada periode &amp; filter yang dipilih
                        </small>
                    </div>
                    @if ($topBenih->count() > 0)
                        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill small">
                            <i class="bi bi-trophy me-1"></i> Best Seller
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body pt-0 table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Tanaman</th>
                            <th>Jenis Benih</th>
                            <th>Jumlah Terjual</th>
                            <th>Total Pendapatan (Rp)</th>
                            <th>Perkiraan Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPendapatanSafe = $totalPendapatan > 0 ? $totalPendapatan : 1;
                        @endphp

                        @forelse ($topBenih as $index => $item)
                            @php
                                $benih = $item->benih;
                                $persentase = ($item->pendapatan / $totalPendapatanSafe) * 100;
                            @endphp
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td>{{ $benih->jenisTanaman->nama_tanaman ?? '-' }}</td>
                                <td>{{ $benih->jenis_benih ?? '-' }}</td>
                                <td class="text-end fw-semibold">
                                    {{ number_format($item->jumlah_terjual) }}
                                </td>
                                <td class="text-end text-success fw-bold">
                                    Rp{{ number_format($item->pendapatan, 0, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($persentase, 1, ',', '.') }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted fst-italic py-3">
                                    Belum ada data untuk ditampilkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div> {{-- penutup .container --}}

    @php
        // Disiapkan untuk grafik batang per benih (total jumlah terjual per benih)
        $benihChart = $penjualan
            ->map(function ($group) {
                $benih = $group->first()->benih ?? null;

                $label = ($benih->jenisTanaman->nama_tanaman ?? '-') . ' - ' . ($benih->jenis_benih ?? '-');

                return [
                    'label' => $label,
                    'jumlah' => $group->sum('jumlah_disetujui'),
                ];
            })
            ->values();
    @endphp

    {{-- ===================== CHART.JS ===================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ===== GRAFIK LINE (PENJUALAN PER WAKTU) =====
            const ctxLine = document.getElementById('penjualanChart');
            const labelsLine = @json($penjualanChart->pluck('label'));
            const dataLine = @json($penjualanChart->pluck('total'));

            if (ctxLine && labelsLine.length) {
                new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: labelsLine,
                        datasets: [{
                            label: 'Total Penjualan (Rp)',
                            data: dataLine,
                            fill: true,
                            tension: 0.3,
                            borderWidth: 2,
                            borderColor: 'rgba(13,110,253,0.8)',
                            backgroundColor: 'rgba(13,110,253,0.2)',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) =>
                                        'Rp' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (value) =>
                                        'Rp' + new Intl.NumberFormat('id-ID').format(value)
                                }
                            }
                        }
                    }
                });
            }

            // ===== GRAFIK BATANG PER JENIS BENIH =====
            const ctxBar = document.getElementById('penjualanPerBenihChart');
            const benihLabels = @json($benihChart->pluck('label'));
            const benihJumlah = @json($benihChart->pluck('jumlah'));

            if (ctxBar && benihLabels.length) {
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: benihLabels,
                        datasets: [{
                            label: 'Jumlah Terjual',
                            data: benihJumlah,
                            backgroundColor: 'rgba(13,110,253,0.7)',
                            borderColor: 'rgba(13,110,253,1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) =>
                                        new Intl.NumberFormat('id-ID').format(ctx.parsed.y) + ' unit'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Terjual'
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
        });
    </script>
@endsection
