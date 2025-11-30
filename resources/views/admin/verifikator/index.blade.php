@extends('layouts.bootstrap')

@section('title', 'Daftar Permohonan (Verifikator)')

@section('content')
<style>
    body{
        margin-top: -70px;
    }
</style>
    <div class="container py-3">

        {{-- HEADER + RINGKASAN --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h1 class="mb-1 text-uppercase fw-bold" style="font-size: .95rem; letter-spacing: .06em;">
                    <i class="bi bi-file-earmark-check me-2"></i> Daftar Permohonan Benih
                </h1>
                <p class="text-muted small mb-0">
                    Kelola dan verifikasi permohonan benih berdasarkan status dan tipe permohonan.
                </p>
            </div>

            {{-- RINGKASAN STATUS (berdasar data ter-filter) --}}
            <div class="d-flex flex-wrap gap-2 small justify-content-end">
                <span class="badge rounded-pill bg-dark-subtle text-dark-emphasis">
                    Total: <strong>{{ $totalFiltered ?? $permohonan->total() }}</strong>
                </span>
                <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
                    Menunggu: {{ $statusCounts['Menunggu Dokumen'] ?? 0 }}
                </span>
                <span class="badge rounded-pill bg-info-subtle text-info-emphasis">
                    Diverifikasi: {{ $statusCounts['Sedang Diverifikasi'] ?? 0 }}
                </span>
                <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">
                    Perbaikan: {{ $statusCounts['Perbaikan'] ?? 0 }}
                </span>
                <span class="badge rounded-pill bg-success-subtle text-success-emphasis">
                    Disetujui: {{ $statusCounts['Disetujui'] ?? 0 }}
                </span>
                <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis">
                    Ditolak: {{ $statusCounts['Ditolak'] ?? 0 }}
                </span>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $color)
            @if (session($key))
                <div class="alert alert-{{ $color }} shadow-sm py-2 mb-3 small">
                    {!! session($key) !!}
                </div>
            @endif
        @endforeach

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-3">

                {{-- FORM FILTER --}}
                <form method="GET" class="mb-3">
                    <div class="row g-2 align-items-end small">
                        {{-- Kolom 1: Search --}}
                        <div class="col-md-3">
                            <label class="form-label mb-1">Cari (Nama / NIK)</label>
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Nama atau NIK..." value="{{ request('search') }}">
                        </div>

                        {{-- Kolom 2: Status Utama --}}
                        <div class="col-md-2">
                            <label class="form-label mb-1">Status Utama</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">-- Semua --</option>
                                @foreach (['Menunggu Dokumen', 'Sedang Diverifikasi', 'Perbaikan', 'Disetujui', 'Ditolak', 'Dibatalkan'] as $status)
                                    <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kolom 3: Tipe Permohonan --}}
                        <div class="col-md-2">
                            <label class="form-label mb-1">Tipe Permohonan</label>
                            <select name="tipe_pembayaran" class="form-select form-select-sm">
                                <option value="">-- Semua --</option>
                                <option value="Gratis" {{ request('tipe_pembayaran') == 'Gratis' ? 'selected' : '' }}>
                                    Gratis</option>
                                <option value="Berbayar" {{ request('tipe_pembayaran') == 'Berbayar' ? 'selected' : '' }}>
                                    Berbayar</option>
                            </select>
                        </div>

                        {{-- Kolom 4: Status Pembayaran --}}
                        <div class="col-md-2">
                            <label class="form-label mb-1">Status Pembayaran</label>
                            <select name="status_pembayaran" class="form-select form-select-sm">
                                <option value="">-- Semua --</option>
                                <option value="Menunggu"
                                    {{ request('status_pembayaran') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="Menunggu Verifikasi"
                                    {{ request('status_pembayaran') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu
                                    Verifikasi</option>
                                <option value="Berhasil"
                                    {{ request('status_pembayaran') == 'Berhasil' ? 'selected' : '' }}>Berhasil</option>
                                <option value="Gagal" {{ request('status_pembayaran') == 'Gagal' ? 'selected' : '' }}>
                                    Gagal</option>
                                <option value="null" {{ request('status_pembayaran') == 'null' ? 'selected' : '' }}>Tidak
                                    Berlaku (Gratis)</option>
                            </select>
                        </div>

                        {{-- Kolom 5: Status Pengambilan --}}
                        <div class="col-md-3">
                            <label class="form-label mb-1">Status Pengambilan</label>
                            <select name="status_pengambilan" class="form-select form-select-sm">
                                <option value="">-- Semua --</option>
                                <option value="Belum Diambil"
                                    {{ request('status_pengambilan') == 'Belum Diambil' ? 'selected' : '' }}>Belum Diambil
                                </option>
                                <option value="Selesai" {{ request('status_pengambilan') == 'Selesai' ? 'selected' : '' }}>
                                    Selesai</option>
                                <option value="Dibatalkan"
                                    {{ request('status_pengambilan') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan
                                </option>
                            </select>
                        </div>

                        {{-- Row kedua filter --}}
                        <div class="col-md-3">
                            <label class="form-label mb-1">Jenis Tanaman</label>
                            <select name="jenis_tanaman_id" class="form-select form-select-sm">
                                <option value="">-- Semua --</option>
                                @foreach ($jenisTanaman as $tanaman)
                                    <option value="{{ $tanaman->id }}"
                                        {{ request('jenis_tanaman_id') == $tanaman->id ? 'selected' : '' }}>
                                        {{ $tanaman->nama_tanaman }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label mb-1">Tgl Diajukan dari</label>
                            <input type="date" name="tanggal_dari" class="form-control form-control-sm"
                                value="{{ request('tanggal_dari') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label mb-1">Tgl Diajukan sampai</label>
                            <input type="date" name="tanggal_sampai" class="form-control form-control-sm"
                                value="{{ request('tanggal_sampai') }}">
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100 mt-3 mt-md-0">
                                <i class="bi bi-funnel me-1"></i> Terapkan Filter
                            </button>
                            <a href="{{ route('admin.verifikator.permohonan.index') }}"
                                class="btn btn-outline-secondary btn-sm mt-3 mt-md-0">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>
                </form>
                {{-- TOMBOL EXPORT --}}
                <div class="d-flex justify-content-end gap-2 mb-2">
                    <a href="{{ route('admin.verifikator.permohonan.export_excel', request()->query()) }}"
                        class="btn btn-success btn-sm">
                        <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                    </a>
                    <a href="{{ route('admin.verifikator.permohonan.export_pdf', request()->query()) }}"
                        class="btn btn-danger btn-sm ">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                    </a>
                </div>


                {{-- TABEL DATA --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle small mb-0" id="verifikasiTable">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th class="text-center" style="width: 4%;">No</th>
                                <th style="width: 26%;">Pemohon</th>
                                <th style="width: 28%;">Tanaman & Benih</th>
                                <th style="width: 18%;">Tipe & Jumlah</th>
                                <th style="width: 18%;">Status Proses</th>
                                <th class="text-center" style="width: 6%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($permohonan as $index => $item)
                                @php
                                    $isBerbayar = $item->tipe_pembayaran === 'Berbayar';
                                    $sp = $item->status_pembayaran;
                                @endphp
                                <tr>
                                    {{-- NO (sesuai pagination) --}}
                                    <td class="text-center">
                                        {{ $permohonan->firstItem() + $index }}
                                    </td>

                                    {{-- PEMOHON --}}
                                    <td>
                                        <div class="fw-semibold text-uppercase" style="font-size: .8rem;">
                                            {{ $item->nama }}
                                        </div>
                                        <div class="text-muted small">
                                            NIK: <span class="text-monospace">{{ $item->nik }}</span>
                                        </div>
                                        <div class="text-muted small">
                                            Diajukan:
                                            <strong>
                                                {{ $item->tanggal_diajukan ? \Carbon\Carbon::parse($item->tanggal_diajukan)->format('d M Y') : '-' }}
                                            </strong>
                                        </div>
                                    </td>

                                    {{-- TANAMAN & BENIH --}}
                                    <td>
                                        <div class="fw-semibold text-primary text-uppercase" style="font-size: .8rem;">
                                            {{ $item->jenisTanaman->nama_tanaman ?? '-' }}
                                        </div>
                                        <div class="small text-muted">
                                            Jenis Benih:
                                            <strong>{{ $item->jenis_benih ?? '-' }}</strong>
                                        </div>
                                        <div class="small text-muted">
                                            Luas Area:
                                            <strong>{{ $item->luas_area ?? '-' }}</strong> Ha
                                        </div>
                                    </td>

                                    {{-- TIPE & JUMLAH --}}
                                    <td>
                                        <div class="small mb-1">
                                            Jumlah Diajukan:
                                            <strong>{{ $item->jumlah_tanaman }}</strong> tanaman
                                        </div>
                                        <div class="small mb-1">
                                            Jumlah Disetujui:
                                            <strong>{{ $item->jumlah_disetujui ?? '-' }}</strong>
                                        </div>
                                        <div class="small">
                                            Tipe Permohonan:
                                            @if ($isBerbayar)
                                                <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis">
                                                    Berbayar
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-success-subtle text-success-emphasis">
                                                    Gratis
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- STATUS PROSES --}}
                                    <td>
                                        {{-- Status utama --}}
                                        <div class="mb-1">
                                            @switch($item->status)
                                                @case('Menunggu Dokumen')
                                                    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
                                                        Menunggu Dokumen
                                                    </span>
                                                @break

                                                @case('Sedang Diverifikasi')
                                                    <span class="badge rounded-pill bg-info-subtle text-info-emphasis">
                                                        Sedang Diverifikasi
                                                    </span>
                                                @break

                                                @case('Perbaikan')
                                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">
                                                        Perlu Perbaikan
                                                    </span>
                                                @break

                                                @case('Disetujui')
                                                    <span class="badge rounded-pill bg-success-subtle text-success-emphasis">
                                                        Disetujui
                                                    </span>
                                                @break

                                                @case('Ditolak')
                                                    <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis">
                                                        Ditolak
                                                    </span>
                                                @break

                                                @case('Dibatalkan')
                                                    <span class="badge rounded-pill bg-dark-subtle text-dark-emphasis">
                                                        Dibatalkan
                                                    </span>
                                                @break

                                                @default
                                                    <span class="badge rounded-pill bg-light text-dark">
                                                        {{ $item->status ?? '-' }}
                                                    </span>
                                            @endswitch
                                        </div>

                                        {{-- Status pembayaran --}}
                                        @if ($isBerbayar)
                                            <div class="small mb-1">
                                                <span class="text-muted">Pembayaran:</span>
                                                <span
                                                    class="badge rounded-pill
                                                @switch($sp)
                                                    @case('Menunggu')             bg-secondary-subtle text-secondary-emphasis @break
                                                    @case('Menunggu Verifikasi')  bg-info-subtle text-info-emphasis         @break
                                                    @case('Berhasil')             bg-success-subtle text-success-emphasis   @break
                                                    @case('Gagal')                bg-danger-subtle text-danger-emphasis     @break
                                                    @default                      bg-light text-dark
                                                @endswitch
                                            ">
                                                    {{ $sp ?? 'Menunggu' }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Status pengambilan --}}
                                        <div class="small">
                                            <span class="text-muted">Pengambilan:</span>
                                            @switch($item->status_pengambilan)
                                                @case('Belum Diambil')
                                                    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
                                                        Belum Diambil
                                                    </span>
                                                @break

                                                @case('Selesai')
                                                    <span class="badge rounded-pill bg-success-subtle text-success-emphasis">
                                                        Selesai
                                                    </span>
                                                @break

                                                @case('Dibatalkan')
                                                    <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis">
                                                        Dibatalkan
                                                    </span>
                                                @break

                                                @default
                                                    <span class="badge rounded-pill bg-light text-dark">
                                                        {{ $item->status_pengambilan ?? '-' }}
                                                    </span>
                                            @endswitch
                                        </div>
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center">
                                        <a href="{{ route('admin.verifikator.permohonan.show', $item->id) }}"
                                            class="btn btn-sm btn-outline-primary icon-btn" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            Belum ada data permohonan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="small text-muted">
                            Menampilkan
                            <strong>{{ $permohonan->firstItem() ?? 0 }}</strong>
                            -
                            <strong>{{ $permohonan->lastItem() ?? 0 }}</strong>
                            dari
                            <strong>{{ $permohonan->total() }}</strong> data
                        </div>
                        <div>
                            {{ $permohonan->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            #verifikasiTable {
                font-size: 0.8rem;
            }

            #verifikasiTable th,
            #verifikasiTable td {
                vertical-align: top !important;
                padding: 6px 8px !important;
            }

            #verifikasiTable thead th {
                border-bottom-width: 1px;
                font-size: 0.75rem;
                letter-spacing: .06em;
            }

            .icon-btn {
                width: 30px;
                height: 30px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 0.8rem;
                border-radius: 999px;
            }
        </style>
    @endsection
