@extends('layouts.bootstrap')

@section('title', 'Detail Permohonan (Verifikator)')

@section('content')
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/verifikator/permohonan-show.css') }}">
@endpush
<style>
    body{
        margin-top: -50px;
    }
</style>
    <div class="container py-4">

        @php
            $status = $permohonan->status ?? '-';
            $tipePembayaran = $permohonan->tipe_pembayaran ?? 'Gratis';
            $isBerbayar = $tipePembayaran === 'Berbayar';
            $statusPembayaran = $permohonan->status_pembayaran ?? ($isBerbayar ? 'Menunggu' : 'Tidak Berlaku');

            $statusClassMap = [
                'Sedang Diverifikasi' => 'bg-info-subtle text-info-emphasis',
                'Disetujui' => 'bg-success-subtle text-success-emphasis',
                'Ditolak' => 'bg-danger-subtle text-danger-emphasis',
                'Perbaikan' => 'bg-secondary-subtle text-secondary-emphasis',
                'Menunggu Dokumen' => 'bg-warning-subtle text-warning-emphasis',
                'Dibatalkan' => 'bg-dark text-light',
            ];
            $statusClass = $statusClassMap[$status] ?? 'bg-light text-dark';

            $statusPembayaranClassMap = [
                'Menunggu' => 'bg-secondary-subtle text-secondary-emphasis',
                'Menunggu Verifikasi' => 'bg-info-subtle text-info-emphasis',
                'Berhasil' => 'bg-success-subtle text-success-emphasis',
                'Gagal' => 'bg-danger-subtle text-danger-emphasis',
                'Tidak Berlaku' => 'bg-light text-muted',
            ];
            $statusPembayaranClass = $statusPembayaranClassMap[$statusPembayaran] ?? 'bg-light text-dark';
            // validasi koordinat (opsional)
            $isValidCoordinate =
                is_numeric($permohonan->latitude ?? null) &&
                is_numeric($permohonan->longitude ?? null) &&
                $permohonan->latitude >= -90 &&
                $permohonan->latitude <= 90 &&
                $permohonan->longitude >= -180 &&
                $permohonan->longitude <= 180;
        @endphp

        {{-- HEADER UTAMA BARU --}}
        <div class="permohonan-header-card mb-3">
            {{-- BARIS CHIP + ID + TANGGAL --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="permohonan-chip">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        Detail Permohonan Benih
                    </div>
                    <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis small">
                        ID: #{{ $permohonan->id ?? '-' }}
                    </span>
                </div>

                <div class="text-end small text-muted">
                    <div>
                        <i class="bi bi-calendar-event me-1"></i>
                        Diajukan:
                        <strong>
                            {{ $permohonan->tanggal_diajukan ? \Carbon\Carbon::parse($permohonan->tanggal_diajukan)->format('d M Y') : '-' }}
                        </strong>
                    </div>
                </div>
            </div>

            {{-- BARIS BAWAH: 2 kolom --}}
            <div class="row g-3">
                {{-- KIRI: info singkat permohonan + pemohon --}}
                <div class="col-md-6">
                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">Nama</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value text-uppercase fw-semibold text-primary">
                            {{ $permohonan->nama ?? '-' }}
                        </span>
                    </div>

                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">NIK</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            {{ $permohonan->nik ?? '-' }}
                        </span>
                    </div>

                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">Jenis Tanaman</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            {{ $permohonan->jenisTanaman->nama_tanaman ?? '-' }}
                        </span>
                    </div>

                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">Jenis Benih</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            {{ ucfirst($permohonan->jenis_benih ?? '-') }}
                        </span>
                    </div>

                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">Jumlah Diajukan</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            {{ $permohonan->jumlah_tanaman ?? '-' }} tanaman
                        </span>
                    </div>

                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">Jumlah Disetujui</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            @if ($permohonan->jumlah_disetujui)
                                <span class="text-success fw-semibold">
                                    {{ $permohonan->jumlah_disetujui }}
                                </span>
                            @else
                                <span class="text-muted fst-italic">Belum diisi</span>
                            @endif
                        </span>
                    </div>

                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">Luas Area</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            {{ $permohonan->luas_area ?? '-' }} Ha
                        </span>
                    </div>
                </div>

                {{-- KANAN: status-status + kontak & alamat --}}
                <div class="col-md-6">
                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">Status Permohonan</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            <span class="badge {{ $statusClass }}">
                                {{ $status }}
                            </span>
                        </span>
                    </div>

                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">Tipe Permohonan</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            @if ($isBerbayar)
                                <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">
                                    Berbayar
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill">
                                    Gratis
                                </span>
                            @endif
                        </span>
                    </div>

                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">Status Pembayaran</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            @if ($isBerbayar)
                                <span class="badge {{ $statusPembayaranClass }} rounded-pill">
                                    {{ $statusPembayaran }}
                                </span>
                            @else
                                <span class="text-muted fst-italic small">Tidak Berlaku</span>
                            @endif
                        </span>
                    </div>

                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">Alamat</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            {{ $permohonan->alamat ?? '-' }}
                        </span>
                    </div>

                    <div class="permohonan-meta-row">
                        <span class="permohonan-meta-label">No. Telepon</span>
                        <span class="permohonan-meta-separator">:</span>
                        <span class="permohonan-meta-value">
                            {{ $permohonan->no_telp ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FLASH MESSAGES --}}
        @foreach (['success', 'error', 'info'] as $t)
            @if (session($t))
                <div class="alert alert-{{ $t == 'error' ? 'danger' : $t }} alert-dismissible fade show small mb-3"
                    role="alert">
                    <i
                        class="bi {{ $t == 'success' ? 'bi-check-circle' : ($t == 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle') }} me-1"></i>
                    {{ session($t) }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endforeach
        {{-- LOKASI LAHAN --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body small">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="permohonan-chip">
                            <i class="bi bi-geo-alt me-1"></i>
                            Lokasi Lahan
                        </div>
                    </div>

                    <div class="text-end text-muted">
                        @if ($isValidCoordinate)
                            <div class="small">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                Koordinat valid
                            </div>
                        @else
                            <div class="small">
                                <i class="bi bi-exclamation-circle text-warning me-1"></i>
                                Koordinat belum diisi / tidak valid
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info koordinat singkat --}}
                <div class="row g-3">
                    <div class="col-md-4">
                        <table class="table table-sm table-borderless mb-0 align-middle">
                            <tr>
                                <th class="text-muted" style="width: 40%;">Latitude</th>
                                <td style="width: 5%;">:</td>
                                <td>{{ $permohonan->latitude ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Longitude</th>
                                <td>:</td>
                                <td>{{ $permohonan->longitude ?? '-' }}</td>
                            </tr>
                        </table>

                        <div class="mt-2 text-muted fst-italic small">
                            @if ($isValidCoordinate)
                                Titik di peta menggunakan koordinat di atas.
                            @else
                                Peta akan tampil bila pemohon mengisi latitude & longitude dengan benar.
                            @endif
                        </div>
                    </div>

                    {{-- Peta --}}
                    <div class="col-md-8">
                        @if ($isValidCoordinate)
                            <div id="map-lokasi" class="rounded shadow-sm" style="height: 260px; width: 100%;"></div>
                        @else
                            <div class="border rounded bg-light d-flex align-items-center justify-content-center"
                                style="height: 260px;">
                                <div class="text-center text-muted">
                                    <i class="bi bi-map text-secondary mb-2" style="font-size: 1.6rem;"></i>
                                    <div class="small">
                                        Koordinat belum diisi atau berada di luar jangkauan.<br>
                                        Peta tidak dapat ditampilkan.
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>



        {{-- ===================== DOKUMEN PEMOHON ===================== --}}
        @php
            $dokumen = [
                ['label' => 'Surat Permohonan (Sistem / Ditandatangani)', 'field' => 'scan_surat_permohonan'],
                ['label' => 'Surat Pernyataan', 'field' => 'scan_surat_pernyataan'],
                ['label' => 'Kartu Keluarga (KK)', 'field' => 'scan_kk'],
                ['label' => 'Kartu Tanda Penduduk (KTP)', 'field' => 'scan_ktp'],
                ['label' => 'Surat Kepemilikan Tanah', 'field' => 'scan_surat_tanah'],
            ];

            $totalDokumen = count($dokumen);
            $jumlahLengkap = collect($dokumen)->filter(fn($d) => !empty($permohonan->{$d['field']}))->count();
        @endphp

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body small">

                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="permohonan-chip">
                            <i class="bi bi-folder2-open me-1"></i>
                            Dokumen Pemohon
                        </div>
                    </div>

                    <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">
                        Lengkap {{ $jumlahLengkap }}/{{ $totalDokumen }} dokumen
                    </span>
                </div>

                @if ($jumlahLengkap === 0)
                    <div class="border rounded bg-light-subtle text-center py-3 text-muted">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Belum ada dokumen yang diunggah untuk permohonan ini.
                    </div>
                @else
                    <div class="row g-3">
                        @foreach ($dokumen as $doc)
                            @php $path = $permohonan->{$doc['field']}; @endphp
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                @if ($path)
                                    {{-- KARTU DOKUMEN ADA --}}
                                    <div class="doc-card d-flex flex-column justify-content-between p-3">
                                        <div>
                                            <div class="doc-icon-circle">
                                                <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                            </div>
                                            <p class="doc-label mb-1 text-center">
                                                {{ $doc['label'] }}
                                            </p>
                                            <div class="doc-meta text-center mb-1">
                                                <span class="badge bg-danger-subtle text-danger-emphasis doc-badge-type">
                                                    PDF
                                                </span>
                                            </div>
                                        </div>

                                        <div class="doc-actions mt-2 d-flex justify-content-center gap-2 flex-wrap">
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm rounded-pill">
                                                <i class="bi bi-eye me-1"></i> Lihat
                                            </a>
                                            <a href="{{ asset('storage/' . $path) }}" download
                                                class="btn btn-light btn-sm rounded-pill border">
                                                <i class="bi bi-download me-1"></i> Unduh
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    {{-- KARTU BELUM DIUNGGAH --}}
                                    <div class="doc-card doc-card-empty d-flex flex-column justify-content-between p-3">
                                        <div class="text-center">
                                            <div class="doc-icon-circle mb-2">
                                                <i class="bi bi-file-earmark-x fs-5"></i>
                                            </div>
                                            <p class="doc-label mb-1">{{ $doc['label'] }}</p>
                                            <div class="doc-meta">
                                                <span class="badge bg-light text-muted border doc-badge-type">
                                                    Belum diunggah
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-3 text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Semua dokumen menggunakan format <strong>PDF</strong>.
                    Untuk memperbarui dokumen, admin dapat mengunggah ulang melalui form pengelolaan permohonan.
                </div>

            </div>
        </div>


        {{-- ===================== TINDAKAN VERIFIKATOR (SETUJUI / TOLAK / PERBAIKAN) ===================== --}}
        @php
            $bisaVerifikasi = $permohonan->status === 'Sedang Diverifikasi';
        @endphp

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body small">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="permohonan-chip">
                            <i class="bi bi-check2-square me-1"></i>
                            Tindakan Verifikator
                        </div>
                    </div>

                    <div class="text-end small">
                        <span class="text-muted d-block">Status Saat Ini:</span>
                        <span class="badge bg-light text-dark rounded-pill">
                            {{ $permohonan->status ?? '-' }}
                        </span>
                    </div>
                </div>

                @if ($bisaVerifikasi)
                    <div class="alert alert-info small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Permohonan ini <strong>siap diverifikasi</strong>. Pilih salah satu tindakan di bawah ini.
                    </div>

                    <div class="row g-3">
                        {{-- SETUJUI --}}
                        <div class="col-md-4">
                            <div class="verif-action-card p-3 h-100 border-success-subtle">
                                <h6 class="fw-semibold text-success mb-2 verif-title">
                                    <i class="bi bi-check-circle me-1"></i> Setujui Permohonan
                                </h6>
                                <form action="{{ route('admin.verifikator.permohonan.approve', $permohonan->id) }}"
                                    method="POST" onsubmit="return confirm('Setujui permohonan ini?')">
                                    @csrf

                                    <label class="form-label small mb-1">Jumlah yang Disetujui</label>
                                    <input type="number" name="jumlah_disetujui" id="jumlah_disetujui"
                                        class="form-control form-control-sm mb-2" min="1"
                                        max="{{ $permohonan->jumlah_tanaman }}"
                                        value="{{ old('jumlah_disetujui', $permohonan->jumlah_disetujui ?? $permohonan->jumlah_tanaman) }}"
                                        required>

                                    {{-- Info pembayaran bila BERBAYAR --}}
                                    @if ($permohonan->tipe_pembayaran === 'Berbayar' && $permohonan->benih)
                                        <div class="alert alert-light border small mb-2">
                                            <div><strong>Harga Satuan:</strong> Rp
                                                {{ number_format($permohonan->benih->harga, 0, ',', '.') }}
                                            </div>
                                            <div>
                                                <strong>Total Pembayaran:</strong>
                                                <span id="totalBayar">Rp 0</span>
                                            </div>
                                            <small class="text-muted">
                                                Nominal dihitung otomatis = Harga × Jumlah Disetujui
                                            </small>
                                        </div>
                                    @endif

                                    <label class="form-label small mb-1">Catatan / Alasan (wajib)</label>
                                    <textarea name="alasan" class="form-control form-control-sm mb-2" rows="3"
                                        placeholder="Contoh: Data lengkap dan dokumen sudah sesuai." required>{{ old('alasan') }}</textarea>

                                    <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill">
                                        <i class="bi bi-check-circle me-1"></i> Setujui &amp; Buat Surat
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- TOLAK --}}
                        <div class="col-md-4">
                            <div class="verif-action-card p-3 h-100 border-danger-subtle">
                                <h6 class="fw-semibold text-danger mb-2 verif-title">
                                    <i class="bi bi-x-circle me-1"></i> Tolak Permohonan
                                </h6>
                                <form action="{{ route('admin.verifikator.permohonan.reject', $permohonan->id) }}"
                                    method="POST" onsubmit="return confirm('Tolak permohonan ini?')">
                                    @csrf

                                    <label class="form-label small mb-1">Alasan Penolakan (wajib)</label>
                                    <textarea name="alasan" class="form-control form-control-sm mb-2" rows="4"
                                        placeholder="Contoh: Dokumen tidak sesuai ketentuan, data tidak valid, atau alasan lainnya." required>{{ old('alasan') }}</textarea>

                                    <button type="submit" class="btn btn-danger btn-sm w-100 rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i> Tolak &amp; Buat Surat
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- PERBAIKAN --}}
                        <div class="col-md-4">
                            <div class="verif-action-card p-3 h-100 border-warning-subtle">
                                <h6 class="fw-semibold text-warning mb-2 verif-title">
                                    <i class="bi bi-pencil-square me-1"></i> Minta Perbaikan
                                </h6>
                                <form action="{{ route('admin.verifikator.permohonan.perbaiki', $permohonan->id) }}"
                                    method="POST" onsubmit="return confirm('Kirim permintaan perbaikan ke pemohon?')">
                                    @csrf

                                    <label class="form-label small mb-1">Catatan Perbaikan (wajib)</label>
                                    <textarea name="alasan" class="form-control form-control-sm mb-2" rows="4"
                                        placeholder="Contoh: Mohon upload ulang KTP, lengkapi surat tanah, atau perbaiki data lainnya." required>{{ old('alasan') }}</textarea>

                                    <button type="submit" class="btn btn-warning btn-sm w-100 rounded-pill text-dark">
                                        <i class="bi bi-tools me-1"></i> Kirim Permintaan Perbaikan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif ($permohonan->status === 'Menunggu Dokumen')
                    <div class="alert alert-warning text-center mb-0">
                        <i class="bi bi-hourglass-split me-1"></i>
                        Pemohon belum melengkapi dokumen. <strong>Tindakan verifikasi belum dapat dilakukan.</strong>
                    </div>
                @elseif ($permohonan->status === 'Perbaikan')
                    <div class="alert alert-info text-center mb-0">
                        <i class="bi bi-tools me-1"></i>
                        Permohonan sedang diperbaiki oleh pemohon. Tunggu hingga pemohon mengirim perbaikan.
                    </div>
                @else
                    <div class="alert alert-secondary text-center mb-0">
                        <i class="bi bi-lock me-1"></i>
                        Semua tindakan dikunci (status: <b>{{ $permohonan->status }}</b>).
                    </div>
                @endif
            </div>
        </div>

        {{-- JS: hitung total pembayaran otomatis (hanya jika berbayar & punya harga) --}}
        @if ($bisaVerifikasi && $permohonan->tipe_pembayaran === 'Berbayar' && $permohonan->benih)
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const input = document.getElementById('jumlah_disetujui');
                    const totalLabel = document.getElementById('totalBayar');
                    if (!input || !totalLabel) return;

                    const harga = {{ $permohonan->benih->harga ?? 0 }};

                    const updateTotal = () => {
                        const jumlah = parseInt(input.value || 0);
                        const total = harga * jumlah;
                        totalLabel.textContent = 'Rp ' + total.toLocaleString('id-ID');
                    };

                    updateTotal();
                    input.addEventListener('input', updateTotal);
                });
            </script>
        @endif


        {{-- ===================== PANEL PEMBAYARAN (HANYA BERBAYAR & SUDAH DISETUJUI) ===================== --}}
        @php
            $isBerbayarDisetujui = $permohonan->tipe_pembayaran === 'Berbayar' && $permohonan->status === 'Disetujui';

            $sp = $permohonan->status_pembayaran ?? 'Menunggu';
            $statusPembayaranClassMap = [
                'Menunggu' => 'bg-secondary-subtle text-secondary-emphasis',
                'Menunggu Verifikasi' => 'bg-info-subtle text-info-emphasis',
                'Berhasil' => 'bg-success-subtle text-success-emphasis',
                'Gagal' => 'bg-danger-subtle text-danger-emphasis',
            ];
            $spClass = $statusPembayaranClassMap[$sp] ?? 'bg-light text-dark';

            // total pembayaran (kalau ada nominal, pakai itu; kalau tidak, hitung dari harga x jumlah)
            $hargaSatuan = $permohonan->benih->harga ?? 0;
            $jumlahDisetujui = $permohonan->jumlah_disetujui ?? ($permohonan->jumlah_tanaman ?? 0);
            $nominalDariHarga = $hargaSatuan * $jumlahDisetujui;
            $totalPembayaran = $permohonan->nominal_pembayaran ?? $nominalDariHarga;
        @endphp

        @if ($isBerbayarDisetujui)
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body small">

                    {{-- HEADER --}}
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="permohonan-chip">
                                <i class="bi bi-credit-card me-1"></i>
                                Pembayaran Permohonan
                            </div>
                            <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">
                                ID: #{{ $permohonan->id }}
                            </span>
                        </div>

                        <div class="text-end small">
                            <span class="text-muted d-block">Status Pembayaran</span>
                            <span class="badge rounded-pill px-3 py-1 {{ $spClass }}">
                                {{ $sp }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- INFO PEMBAYARAN --}}
                        <div class="col-md-6">
                            <div class="payment-section h-100">
                                <div class="payment-row mb-1">
                                    <span class="payment-label">Total Pembayaran</span>
                                    <span class="payment-separator">:</span>
                                    <span class="payment-value fw-semibold text-primary">
                                        Rp{{ number_format($totalPembayaran, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="payment-row">
                                    <span class="payment-label">Harga Satuan</span>
                                    <span class="payment-separator">:</span>
                                    <span class="payment-value">
                                        Rp{{ number_format($hargaSatuan, 0, ',', '.') }}
                                        <span class="text-muted">/ bibit</span>
                                    </span>
                                </div>

                                <div class="payment-row">
                                    <span class="payment-label">Jumlah Disetujui</span>
                                    <span class="payment-separator">:</span>
                                    <span class="payment-value">
                                        {{ $jumlahDisetujui }} tanaman
                                    </span>
                                </div>

                                <hr class="my-2">

                                <div class="payment-row">
                                    <span class="payment-label">Batas Pembayaran</span>
                                    <span class="payment-separator">:</span>
                                    <span class="payment-value">
                                        {{ $permohonan->batas_pembayaran ? \Carbon\Carbon::parse($permohonan->batas_pembayaran)->format('d M Y') : '-' }}
                                    </span>
                                </div>

                                <div class="payment-row">
                                    <span class="payment-label">Tgl Verifikasi</span>
                                    <span class="payment-separator">:</span>
                                    <span class="payment-value">
                                        {{ $permohonan->tanggal_verifikasi_pembayaran
                                            ? \Carbon\Carbon::parse($permohonan->tanggal_verifikasi_pembayaran)->format('d M Y H:i')
                                            : '-' }}
                                    </span>
                                </div>

                                <div class="payment-row">
                                    <span class="payment-label">Bukti Pembayaran</span>
                                    <span class="payment-separator">:</span>
                                    <span class="payment-value">
                                        @if ($permohonan->bukti_pembayaran)
                                            <a href="{{ asset('storage/' . $permohonan->bukti_pembayaran) }}"
                                                target="_blank" class="btn btn-outline-primary btn-sm rounded-pill">
                                                <i class="bi bi-eye me-1"></i> Lihat Bukti
                                            </a>
                                        @else
                                            <span class="text-muted">Belum ada bukti yang diupload pemohon.</span>
                                        @endif
                                    </span>
                                </div>

                                <hr class="my-2">

                                <div class="payment-row mb-1">
                                    <span class="payment-label">Pesan Pemohon</span>
                                    <span class="payment-separator">:</span>
                                    <span class="payment-value">
                                        @if ($permohonan->pesan_pemohon_pembayaran)
                                            <span class="payment-note">
                                                "{{ $permohonan->pesan_pemohon_pembayaran }}"
                                            </span>
                                        @else
                                            <span class="payment-note text-muted">Belum ada pesan dari pemohon.</span>
                                        @endif
                                    </span>
                                </div>

                                @if ($permohonan->catatan_pembayaran_admin)
                                    <div class="payment-row">
                                        <span class="payment-label">Catatan Admin</span>
                                        <span class="payment-separator">:</span>
                                        <span class="payment-value">
                                            <span class="payment-note">
                                                "{{ $permohonan->catatan_pembayaran_admin }}"
                                            </span>
                                        </span>
                                    </div>
                                @endif

                                <div class="mt-2 payment-note">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Pastikan nominal dan bukti pembayaran sudah sesuai sebelum mengubah status.
                                </div>
                            </div>
                        </div>

                        {{-- FORM VERIFIKASI PEMBAYARAN --}}
                        <div class="col-md-6">
                            <div class="payment-verify-card p-3 h-100">
                                <h6 class="fw-semibold mb-2" style="font-size: .85rem;">
                                    <i class="bi bi-shield-check me-1"></i> Verifikasi Pembayaran
                                </h6>
                                <p class="payment-note mb-2">
                                    Pilih hasil verifikasi pembayaran dan tuliskan catatan singkat
                                    sebagai dokumentasi internal.
                                </p>

                                <form
                                    action="{{ route('admin.verifikator.permohonan.verifikasi_pembayaran', $permohonan->id) }}"
                                    method="POST" onsubmit="return confirm('Simpan hasil verifikasi pembayaran?')">
                                    @csrf

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Status Pembayaran</label>
                                        <select name="status_pembayaran" class="form-select form-select-sm" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="Berhasil"
                                                {{ $permohonan->status_pembayaran == 'Berhasil' ? 'selected' : '' }}>
                                                Berhasil (Pembayaran diterima)
                                            </option>
                                            <option value="Gagal"
                                                {{ $permohonan->status_pembayaran == 'Gagal' ? 'selected' : '' }}>
                                                Gagal (Pembayaran tidak valid / belum masuk)
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Catatan Admin</label>
                                        <textarea name="catatan_pembayaran_admin" class="form-control form-control-sm" rows="4"
                                            placeholder="Contoh: Bukti transfer tidak sesuai, mohon upload ulang / Pembayaran sudah masuk sesuai nominal.">{{ old('catatan_pembayaran_admin', $permohonan->catatan_pembayaran_admin) }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">
                                        <i class="bi bi-save me-1"></i> Simpan Verifikasi Pembayaran
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif


        {{-- ===================== SURAT KEPUTUSAN ===================== --}}
        @php
            $statusKeputusan = $permohonan->status; // Disetujui / Ditolak
            $pathKeputusan = $permohonan->scan_surat_pengambilan;
            $extKeputusan = $pathKeputusan ? strtolower(pathinfo($pathKeputusan, PATHINFO_EXTENSION)) : null;

            $statusKeputusanClassMap = [
                'Disetujui' => 'bg-success-subtle text-success-emphasis',
                'Ditolak' => 'bg-danger-subtle text-danger-emphasis',
            ];
            $statusKeputusanClass = $statusKeputusanClassMap[$statusKeputusan] ?? 'bg-light text-dark';

            // tanggal keputusan
            $tanggalKeputusan =
                $statusKeputusan === 'Disetujui' ? $permohonan->tanggal_disetujui : $permohonan->tanggal_ditolak;
        @endphp

        @if (in_array($statusKeputusan, ['Disetujui', 'Ditolak']))
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body small">

                    {{-- HEADER --}}
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="permohonan-chip">
                                <i class="bi bi-file-earmark-check me-1"></i>
                                Surat Keputusan
                            </div>
                            <span class="badge rounded-pill {{ $statusKeputusanClass }}">
                                {{ $statusKeputusan }}
                            </span>
                        </div>

                        <div class="text-end small text-muted">
                            <div>
                                <i class="bi bi-calendar-check me-1"></i>
                                Tanggal Keputusan:
                                <strong>
                                    {{ $tanggalKeputusan ? \Carbon\Carbon::parse($tanggalKeputusan)->format('d M Y') : '-' }}
                                </strong>
                            </div>
                            <div>
                                ID Permohonan: <strong>#{{ $permohonan->id }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- RINGKASAN KEPUTUSAN --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0 align-middle">
                                <tr>
                                    <th class="text-muted" style="width: 38%;">Jenis Tanaman</th>
                                    <td style="width: 5%;">:</td>
                                    <td class="text-uppercase fw-semibold text-primary">
                                        {{ $permohonan->jenisTanaman->nama_tanaman ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Jenis Benih</th>
                                    <td>:</td>
                                    <td>{{ ucfirst($permohonan->jenis_benih ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Jumlah Diajukan</th>
                                    <td>:</td>
                                    <td>{{ $permohonan->jumlah_tanaman ?? '-' }} tanaman</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Jumlah Disetujui</th>
                                    <td>:</td>
                                    <td>
                                        @if ($statusKeputusan === 'Disetujui')
                                            <span class="fw-semibold text-success">
                                                {{ $permohonan->jumlah_disetujui ?? $permohonan->jumlah_tanaman }}
                                            </span> tanaman
                                        @else
                                            <span class="text-muted fst-italic">Tidak berlaku (permohonan ditolak)</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- Keterangan singkat --}}
                        <div class="col-md-6">
                            <div class="alert alert-light border payment-note mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Surat keputusan ini menjadi dasar untuk proses
                                <strong>
                                    @if ($statusKeputusan === 'Disetujui')
                                        pengambilan benih dan pembayaran (jika berbayar).
                                    @else
                                        penolakan permohonan kepada pemohon.
                                    @endif
                                </strong>
                                Pastikan file yang diupload sudah <strong>ditandatangani pejabat berwenang</strong>.
                            </div>
                        </div>
                    </div>

                    {{-- DOKUMEN SK + FORM UPLOAD --}}
                    <div class="row g-3">
                        {{-- KARTU SURAT KEPUTUSAN --}}
                        <div class="col-md-6">
                            @if ($pathKeputusan)
                                <div class="doc-card d-flex flex-column justify-content-between p-3 h-100">
                                    <div class="text-center">
                                        <div class="doc-icon-circle mb-2">
                                            @if ($extKeputusan === 'pdf')
                                                <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                            @else
                                                <i class="bi bi-file-earmark-text fs-5"></i>
                                            @endif
                                        </div>
                                        <p class="doc-label mb-1">
                                            Surat Keputusan
                                            @if ($statusKeputusan === 'Disetujui')
                                                (Persetujuan)
                                            @else
                                                (Penolakan)
                                            @endif
                                        </p>
                                        <div class="doc-meta mb-1">
                                            <span class="badge bg-danger-subtle text-danger-emphasis doc-badge-type">
                                                {{ strtoupper($extKeputusan ?? 'PDF') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="doc-actions mt-2 d-flex justify-content-center gap-2 flex-wrap">
                                        <a href="{{ asset('storage/' . $pathKeputusan) }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm rounded-pill">
                                            <i class="bi bi-eye me-1"></i>
                                            Lihat Surat
                                        </a>
                                        <a href="{{ asset('storage/' . $pathKeputusan) }}" download
                                            class="btn btn-light btn-sm rounded-pill border">
                                            <i class="bi bi-download me-1"></i>
                                            Unduh Surat
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="doc-card doc-card-empty d-flex flex-column justify-content-center p-3 h-100">
                                    <div class="text-center">
                                        <div class="doc-icon-circle mb-2">
                                            <i class="bi bi-file-earmark-x fs-5"></i>
                                        </div>
                                        <p class="doc-label mb-1">
                                            Surat Keputusan Belum Diunggah
                                        </p>
                                        <div class="doc-meta">
                                            Gunakan tombol <strong>Setujui</strong> / <strong>Tolak</strong> untuk membuat
                                            draft,
                                            lalu upload surat yang sudah ditandatangani.
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- FORM UPLOAD SURAT TTD --}}
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                <p class="fw-semibold mb-2" style="font-size: .85rem;">
                                    <i class="bi bi-cloud-upload me-1"></i>
                                    Upload Surat Keputusan yang Sudah Ditandatangani
                                </p>
                                <p class="payment-note mb-2">
                                    Unggah file <strong>PDF</strong> surat keputusan yang sudah ditandatangani
                                    pejabat berwenang. File ini yang akan digunakan sebagai rujukan resmi dan
                                    bisa diunduh oleh pemohon.
                                </p>

                                <form
                                    action="{{ route('admin.verifikator.permohonan.uploadKeputusan', $permohonan->id) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold mb-1">
                                            File Surat Keputusan (PDF)
                                        </label>
                                        <input type="file" name="surat_pdf" class="form-control form-control-sm"
                                            accept=".pdf" required>
                                        <small class="text-muted d-block mt-1">
                                            Format: PDF &middot; Maksimal 2 MB.
                                        </small>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill mt-1">
                                        <i class="bi bi-cloud-arrow-up me-1"></i>
                                        Simpan / Upload Surat PDF
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif


        {{-- ===================== STATUS PENGAMBILAN + BUKTI PENGAMBILAN ===================== --}}
        @php
            $statusPengambilan = $permohonan->status_pengambilan ?? 'Belum Diambil';
            $statusPickupClassMap = [
                'Belum Diambil' => 'bg-warning-subtle text-warning-emphasis',
                'Selesai' => 'bg-success-subtle text-success-emphasis',
                'Dibatalkan' => 'bg-danger-subtle text-danger-emphasis',
            ];
            $statusPickupClass =
                $statusPickupClassMap[$statusPengambilan] ?? 'bg-secondary-subtle text-secondary-emphasis';
        @endphp

        @if ($permohonan->status === 'Disetujui')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body small">

                    {{-- HEADER --}}
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="permohonan-chip">
                                <i class="bi bi-box-seam me-1"></i>
                                Status Pengambilan Bibit
                            </div>
                        </div>

                        <div class="text-end small">
                            <span class="text-muted d-block">Status Tersimpan</span>
                            <span class="badge rounded-pill px-3 py-1 {{ $statusPickupClass }}">
                                {{ $statusPengambilan }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- FORM UPDATE --}}
                        <div class="col-md-6">
                            <div class="pickup-section h-100">
                                <p class="pickup-note mb-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Gunakan form ini untuk mengubah status pengambilan bibit dan menyimpan bukti serah
                                    terima.
                                </p>

                                <form
                                    action="{{ route('admin.verifikator.permohonan.updatePengambilan', $permohonan->id) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Status Pengambilan</label>
                                        <select name="status_pengambilan" class="form-select form-select-sm">
                                            <option value="Belum Diambil"
                                                {{ $statusPengambilan == 'Belum Diambil' ? 'selected' : '' }}>
                                                Belum Diambil
                                            </option>
                                            <option value="Selesai"
                                                {{ $statusPengambilan == 'Selesai' ? 'selected' : '' }}>
                                                Selesai
                                            </option>
                                            <option value="Dibatalkan"
                                                {{ $statusPengambilan == 'Dibatalkan' ? 'selected' : '' }}>
                                                Dibatalkan
                                            </option>
                                        </select>
                                        <small class="pickup-note">
                                            Pilih <strong>Selesai</strong> bila bibit sudah diserahkan ke pemohon.
                                        </small>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Tanggal Pengambilan</label>
                                        <input type="date" name="tanggal_pengambilan"
                                            value="{{ $permohonan->tanggal_pengambilan }}"
                                            class="form-control form-control-sm">
                                        <small class="pickup-note">
                                            Disarankan diisi saat status <strong>Selesai</strong>.
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Bukti Pengambilan (opsional)</label>

                                        @if ($permohonan->bukti_pengambilan)
                                            <div class="mb-1">
                                                <a href="{{ asset('storage/' . $permohonan->bukti_pengambilan) }}"
                                                    target="_blank" class="small text-decoration-none">
                                                    <i class="bi bi-image me-1"></i> Lihat Bukti Sebelumnya
                                                </a>
                                            </div>
                                        @endif

                                        <input type="file" name="bukti_pengambilan"
                                            class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf">
                                        <small class="pickup-note">
                                            Format: JPG, PNG, atau PDF &middot; Maksimal 2 MB.
                                        </small>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">
                                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- RINGKASAN TERSIMPAN --}}
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                <div class="fw-semibold mb-2" style="font-size: .85rem;">
                                    Ringkasan Pengambilan (Tersimpan)
                                </div>

                                <div class="pickup-row">
                                    <span class="pickup-label">Status Pengambilan</span>
                                    <span class="pickup-separator">:</span>
                                    <span class="pickup-value">
                                        <span class="badge rounded-pill {{ $statusPickupClass }}">
                                            {{ $statusPengambilan }}
                                        </span>
                                    </span>
                                </div>

                                <div class="pickup-row">
                                    <span class="pickup-label">Tanggal Pengambilan</span>
                                    <span class="pickup-separator">:</span>
                                    <span class="pickup-value">
                                        @if ($permohonan->tanggal_pengambilan)
                                            {{ \Carbon\Carbon::parse($permohonan->tanggal_pengambilan)->format('d M Y') }}
                                        @else
                                            <span class="fst-italic text-muted">Belum diisi</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="pickup-row align-items-start mt-2">
                                    <span class="pickup-label">Bukti Pengambilan</span>
                                    <span class="pickup-separator">:</span>
                                    <span class="pickup-value">
                                        @if ($permohonan->bukti_pengambilan)
                                            @php
                                                $ext = strtolower(
                                                    pathinfo($permohonan->bukti_pengambilan, PATHINFO_EXTENSION),
                                                );
                                                $urlBukti = asset('storage/' . $permohonan->bukti_pengambilan);
                                            @endphp

                                            @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                                <a href="{{ $urlBukti }}" target="_blank" class="d-inline-block">
                                                    <img src="{{ $urlBukti }}" alt="bukti pengambilan"
                                                        class="img-thumbnail" style="max-height: 120px;">
                                                </a>
                                                <div class="pickup-note mt-1">
                                                    Klik gambar untuk melihat lebih besar.
                                                </div>
                                            @elseif ($ext === 'pdf')
                                                <a href="{{ $urlBukti }}" target="_blank"
                                                    class="btn btn-sm btn-outline-secondary rounded-pill">
                                                    <i class="bi bi-file-earmark-pdf me-1"></i> Lihat PDF Bukti Pengambilan
                                                </a>
                                            @else
                                                <a href="{{ $urlBukti }}" target="_blank" class="small">
                                                    <i class="bi bi-file-earmark-text me-1"></i> Lihat File Bukti
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-muted fst-italic">
                                                Belum ada bukti pengambilan tersimpan.
                                            </span>
                                        @endif
                                    </span>
                                </div>

                                <hr class="my-2">

                                <div class="pickup-note mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Data ini akan menjadi acuan jika suatu saat perlu menelusuri riwayat
                                    penyerahan bibit kepada pemohon.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif



        {{-- ===================== BUKTI TANAM (UNTUK ADMIN LIHAT) ===================== --}}
        @php
            $hasBuktiTanam = !empty($permohonan->bukti_tanam);
            $deadlineTanam = $permohonan->tanggal_tanam_deadline
                ? \Carbon\Carbon::parse($permohonan->tanggal_tanam_deadline)
                : null;
            $tanggalTanam = $permohonan->tanggal_tanam ? \Carbon\Carbon::parse($permohonan->tanggal_tanam) : null;

            $today = now();

            if ($hasBuktiTanam) {
                $statusBukti = 'Sudah Diupload';
            } elseif ($deadlineTanam && $today->gt($deadlineTanam)) {
                $statusBukti = 'Melewati Deadline';
            } else {
                $statusBukti = 'Menunggu Bukti';
            }

            $statusBuktiClassMap = [
                'Sudah Diupload' => 'bg-success-subtle text-success-emphasis',
                'Menunggu Bukti' => 'bg-warning-subtle text-warning-emphasis',
                'Melewati Deadline' => 'bg-danger-subtle text-danger-emphasis',
            ];
            $statusBuktiClass = $statusBuktiClassMap[$statusBukti] ?? 'bg-secondary-subtle text-secondary-emphasis';
        @endphp

        @if ($hasBuktiTanam || $permohonan->tanggal_tanam_deadline)
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body small">

                    {{-- HEADER --}}
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="permohonan-chip">
                                <i class="bi bi-tree me-1"></i>
                                Bukti Tanam di Lahan
                            </div>
                        </div>

                        <div class="text-end small">
                            <span class="text-muted d-block">Status Bukti Tanam</span>
                            <span class="badge rounded-pill px-3 py-1 {{ $statusBuktiClass }}">
                                {{ $statusBukti }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- RINGKASAN --}}
                        <div class="col-md-6">
                            <div class="plant-proof-section h-100">
                                <div class="plant-proof-row">
                                    <span class="plant-proof-label">Deadline Bukti Tanam</span>
                                    <span class="plant-proof-separator">:</span>
                                    <span class="plant-proof-value">
                                        @if ($deadlineTanam)
                                            {{ $deadlineTanam->format('d M Y') }}
                                            @if (!$hasBuktiTanam && $today->gt($deadlineTanam))
                                                <span class="badge bg-danger-subtle text-danger-emphasis ms-1">
                                                    Terlewat
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted fst-italic">Belum ditentukan</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="plant-proof-row">
                                    <span class="plant-proof-label">Tanggal Tanam Dikirim</span>
                                    <span class="plant-proof-separator">:</span>
                                    <span class="plant-proof-value">
                                        @if ($tanggalTanam)
                                            {{ $tanggalTanam->format('d M Y') }}
                                        @else
                                            <span class="text-muted fst-italic">Belum diisi pemohon</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="plant-proof-row">
                                    <span class="plant-proof-label">Status Permohonan</span>
                                    <span class="plant-proof-separator">:</span>
                                    <span class="plant-proof-value">
                                        <span class="badge bg-light text-dark rounded-pill">
                                            {{ $permohonan->status ?? '-' }}
                                        </span>
                                    </span>
                                </div>

                                <hr class="my-2">

                                <div class="plant-proof-note">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Bukti tanam digunakan untuk memastikan bahwa bibit yang diambil benar-benar sudah
                                    ditanam di lahan yang sesuai. Simpan baik-baik jika sewaktu-waktu dibutuhkan
                                    untuk audit / pelaporan.
                                </div>
                            </div>
                        </div>

                        {{-- PREVIEW BUKTI TANAM --}}
                        <div class="col-md-6">
                            <div
                                class="border rounded-3 p-3 h-100 bg-light-subtle d-flex flex-column justify-content-center">
                                <div class="fw-semibold mb-2" style="font-size: .85rem;">
                                    Preview Bukti Tanam
                                </div>

                                @if ($hasBuktiTanam)
                                    @php
                                        $path = $permohonan->bukti_tanam;
                                        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                        $url = asset('storage/' . $path);
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                                    @endphp

                                    @if ($isImage)
                                        <div class="text-center mb-2">
                                            <a href="{{ $url }}" target="_blank">
                                                <img src="{{ $url }}" alt="Bukti Tanam"
                                                    class="plant-proof-thumb img-thumbnail">
                                            </a>
                                        </div>
                                        <div class="plant-proof-note text-center mb-0">
                                            Klik gambar untuk melihat ukuran penuh.
                                        </div>
                                    @elseif ($ext === 'pdf')
                                        <div class="text-center mb-2">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger"
                                                style="font-size: 2.2rem;"></i>
                                            <div class="plant-proof-note mb-2">
                                                Bukti tanam dalam bentuk dokumen PDF.
                                            </div>
                                            <a href="{{ $url }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="bi bi-eye me-1"></i> Lihat PDF Bukti Tanam
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-center mb-2">
                                            <i class="bi bi-file-earmark-text" style="font-size: 2.2rem;"></i>
                                            <div class="plant-proof-note mb-2">
                                                Format file: <strong>{{ strtoupper($ext) }}</strong>
                                            </div>
                                            <a href="{{ $url }}" target="_blank"
                                                class="btn btn-sm btn-outline-secondary rounded-pill">
                                                <i class="bi bi-eye me-1"></i> Lihat File Bukti Tanam
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center">
                                        <i class="bi bi-image-alt text-muted" style="font-size: 2.2rem;"></i>
                                        <div class="plant-proof-note mt-2 mb-0">
                                            Pemohon <strong>belum mengunggah</strong> bukti tanam.
                                            @if ($deadlineTanam && $today->gt($deadlineTanam))
                                                <br>Deadline sudah terlewat. Pertimbangkan untuk menghubungi pemohon.
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif


        {{-- ===================== RIWAYAT KETERANGAN ===================== --}}
        @php
            $hasKeterangan = isset($keterangan) && $keterangan->isNotEmpty();

            $dotClassMap = [
                'Perlu Diperbaiki' => 'history-dot-warning',
                'Sedang Diverifikasi' => 'history-dot-info',
                'Disetujui' => 'history-dot-success',
                'Ditolak' => 'history-dot-danger',
                'Dibatalkan' => 'history-dot-dark',
            ];

            $badgeClassMap = [
                'Perlu Diperbaiki' => 'bg-secondary-subtle text-secondary-emphasis',
                'Sedang Diverifikasi' => 'bg-info-subtle text-info-emphasis',
                'Disetujui' => 'bg-success-subtle text-success-emphasis',
                'Ditolak' => 'bg-danger-subtle text-danger-emphasis',
                'Dibatalkan' => 'bg-dark text-light',
            ];
        @endphp

        @if ($hasKeterangan)
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body small">

                    {{-- HEADER --}}
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="permohonan-chip">
                                <i class="bi bi-clock-history me-1"></i>
                                Riwayat Keterangan
                            </div>
                        </div>
                        <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">
                            {{ $keterangan->count() }} catatan
                        </span>
                    </div>

                    {{-- TIMELINE --}}
                    <div class="history-wrapper">
                        <ul class="history-timeline mb-0">
                            @foreach ($keterangan as $ket)
                                @php
                                    $jenis = $ket->jenis_keterangan ?? '-';
                                    $dotClass = $dotClassMap[$jenis] ?? 'history-dot-secondary';
                                    $badgeClass = $badgeClassMap[$jenis] ?? 'bg-light text-dark';

                                    $tanggal = $ket->tanggal_keterangan
                                        ? \Carbon\Carbon::parse($ket->tanggal_keterangan)
                                        : $ket->created_at;
                                @endphp

                                <li class="history-item">
                                    <div class="history-dot {{ $dotClass }}"></div>
                                    <div class="history-header">
                                        <span class="badge {{ $badgeClass }} rounded-pill px-2 py-1"
                                            style="font-size: 0.7rem;">
                                            {{ $jenis }}
                                        </span>
                                        <span class="history-meta">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            {{ $tanggal->format('d M Y H:i') }}
                                        </span>
                                        @if ($ket->admin?->name)
                                            <span class="history-meta">
                                                &middot; oleh <strong>{{ $ket->admin->name }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    @if ($ket->isi_keterangan)
                                        <div class="history-body">
                                            {{ $ket->isi_keterangan }}
                                        </div>
                                    @else
                                        <div class="history-body text-muted fst-italic">
                                            (Tidak ada detail keterangan)
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        @endif


        {{-- Tombol Kembali --}}
        <div class="text-end mt-2">
            <a href="{{ route('admin.verifikator.permohonan.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
    @if ($isValidCoordinate)
        {{-- Leaflet CSS & JS --}}
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var map = L.map('map-lokasi').setView(
                    [{{ $permohonan->latitude }}, {{ $permohonan->longitude }}],
                    15
                );

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([{{ $permohonan->latitude }}, {{ $permohonan->longitude }}])
                    .addTo(map)
                    .bindPopup(
                        `<strong>Lokasi Lahan</strong><br>
                    Lat: {{ $permohonan->latitude }}<br>
                    Lng: {{ $permohonan->longitude }}`
                    );
            });
        </script>
    @endif

@endsection
