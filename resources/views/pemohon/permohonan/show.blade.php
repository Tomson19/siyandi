@extends('layouts.bootstrap')

@section('title', 'Detail Permohonan')

@section('content')
    
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pemohon/permohonan/show.css') }}">
@endpush
<style>
     body {
            margin-top: -50px;
        }
</style>
    @php
        $status = $permohonan->status_berkas ?? $permohonan->status;

        // Validasi koordinat
        $isValidCoordinate =
            is_numeric($permohonan->latitude ?? null) &&
            is_numeric($permohonan->longitude ?? null) &&
            ($permohonan->latitude >= -90 && $permohonan->latitude <= 90) &&
            ($permohonan->longitude >= -180 && $permohonan->longitude <= 180);

        // Riwayat keterangan admin
        $riwayatKeterangan = $permohonan->keterangan()->orderByDesc('created_at')->get();

        // Step flags
        $step1Done = true;
        $step2Done = in_array($status, ['Disetujui', 'Ditolak']);
        $step2Prog = in_array($status, ['Sedang Diverifikasi', 'Perbaikan']);
        $step3Shown = in_array($status, ['Disetujui', 'Ditolak']);
        $step4Shown = $status === 'Disetujui';

        // Pembayaran & bukti tanam
        $isBerbayar = $permohonan->tipe_pembayaran === 'Berbayar';

        $bolehUploadPembayaran =
            $isBerbayar &&
            $status === 'Disetujui' &&
            $permohonan->status_pembayaran !== 'Berhasil' &&
            !in_array($status, ['Ditolak', 'Dibatalkan']);

        $bolehUploadBuktiTanam =
            $status === 'Disetujui' && $permohonan->status_pengambilan === 'Selesai' && !$permohonan->bukti_tanam;
    @endphp

    <div class="container py-4">

        {{-- ===================== HEADER ===================== --}}
        <div class="text-center mb-4">
            <div class="summary-chip">
                <i class="bi bi-file-earmark-text me-2"></i>
                Detail Permohonan Benih
            </div>
        </div>

        {{-- ===================== ALERT ===================== --}}
        @foreach (['success' => 'success', 'warning' => 'warning', 'error' => 'danger'] as $key => $color)
            @if (session($key))
                <div class="alert alert-{{ $color }} alert-dismissible fade show auto-hide small text-center shadow-sm"
                    role="alert">
                    {{ session($key) }}
                </div>
            @endif
        @endforeach

        {{-- ===================== RINGKASAN + PEMOHON & LOKASI ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">

                <div class="row g-3">
                    {{-- Info Pemohon + Ringkasan --}}
                    <div class="col-md-6">
                        <div class="summary-section h-100">
                            {{-- CHIP TITLE --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="summary-chip">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Informasi Pemohon
                                </div>

                                {{-- Status utama di pojok kanan --}}
                                <div class="d-none d-md-block">
                                    @switch($status)
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

                                        @default
                                            <span class="badge rounded-pill bg-light text-dark">
                                                {{ $status ?? '-' }}
                                            </span>
                                    @endswitch
                                </div>
                            </div>

                            {{-- RINGKASAN PERMOHONAN (YANG KAMU MAU) --}}
                            <div class="mb-2">
                                <div class="summary-row">
                                    <span class="summary-label">Jenis Tanaman</span>
                                    <span class="summary-separator">:</span>
                                    <span class="summary-value text-uppercase fw-semibold text-primary">
                                        {{ $permohonan->jenisTanaman->nama_tanaman ?? '-' }}
                                    </span>
                                </div>

                                <div class="summary-row">
                                    <span class="summary-label">Diajukan pada</span>
                                    <span class="summary-separator">:</span>
                                    <span class="summary-value">
                                        {{ $permohonan->tanggal_diajukan ? \Carbon\Carbon::parse($permohonan->tanggal_diajukan)->format('d M Y') : '-' }}
                                    </span>
                                </div>

                                <div class="summary-row">
                                    <span class="summary-label">Status Utama</span>
                                    <span class="summary-separator">:</span>
                                    <span class="summary-value">
                                        @switch($status)
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

                                            @default
                                                <span class="badge rounded-pill bg-light text-dark">
                                                    {{ $status ?? '-' }}
                                                </span>
                                        @endswitch
                                    </span>
                                </div>

                                <div class="summary-row">
                                    <span class="summary-label">Tipe Permohonan</span>
                                    <span class="summary-separator">:</span>
                                    <span class="summary-value">
                                        @if ($isBerbayar)
                                            <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis">
                                                Berbayar
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-success-subtle text-success-emphasis">
                                                Gratis
                                            </span>
                                        @endif
                                    </span>
                                </div>

                                <div class="summary-row">
                                    <span class="summary-label">Status Pembayaran</span>
                                    <span class="summary-separator">:</span>
                                    <span class="summary-value">
                                        @if ($isBerbayar)
                                            @php $sp = $permohonan->status_pembayaran; @endphp
                                            <span
                                                class="badge rounded-pill
                                        @switch($sp)
                                            @case('Menunggu')             bg-secondary-subtle text-secondary-emphasis @break
                                            @case('Menunggu Verifikasi')  bg-info-subtle text-info-emphasis           @break
                                            @case('Berhasil')             bg-success-subtle text-success-emphasis     @break
                                            @case('Gagal')                bg-danger-subtle text-danger-emphasis       @break
                                            @default                      bg-light text-dark
                                        @endswitch
                                    ">
                                                {{ $sp ?? 'Menunggu' }}
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic">Tidak Berlaku</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <hr class="my-2">

                            {{-- DETAIL PEMOHON (PAKAI TABEL) --}}
                            <table class="table table-sm table-borderless mb-0 align-middle info-table">
                                <tr>
                                    <th class="text-nowrap">Nama Pemohon</th>
                                    <td>:</td>
                                    <td>{{ $permohonan->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">NIK</th>
                                    <td>:</td>
                                    <td>{{ $permohonan->nik ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Alamat</th>
                                    <td>:</td>
                                    <td>{{ $permohonan->alamat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">No. Telepon</th>
                                    <td>:</td>
                                    <td>{{ $permohonan->no_telp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Jenis Benih</th>
                                    <td>:</td>
                                    <td>{{ ucfirst($permohonan->jenis_benih ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Jumlah Diajukan</th>
                                    <td>:</td>
                                    <td>{{ $permohonan->jumlah_tanaman ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Jumlah Disetujui</th>
                                    <td>:</td>
                                    <td>{{ $permohonan->jumlah_disetujui ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">Luas Area (Ha)</th>
                                    <td>:</td>
                                    <td>{{ $permohonan->luas_area ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Lokasi --}}
                    <div class="col-md-6">
                        <div class="summary-chip">
                            <i class="bi bi-geo-alt me-1"></i>
                            Lokasi Lahan
                        </div>


                        <table class="table table-sm table-borderless mb-2 small align-middle info-table">
                            <tr>
                                <th class="text-nowrap">Latitude</th>
                                <td>:</td>
                                <td>{{ $permohonan->latitude ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-nowrap">Longitude</th>
                                <td>:</td>
                                <td>{{ $permohonan->longitude ?? '-' }}</td>
                            </tr>
                        </table>

                        @if ($isValidCoordinate)
                            <div id="map" class="rounded shadow-sm mt-2" style="height: 260px; width: 100%;"></div>
                        @else
                            <div class="text-muted fst-italic py-3 text-center mt-2 border rounded bg-light">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                Koordinat belum diisi atau tidak valid.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>



        {{-- ===================== TIMELINE PROSES ===================== --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="summary-chip">
                    <i class="bi bi-list-check me-1"></i>
                    Alur Proses Permohonan
                </div>
                <div class="timeline small">

                    {{-- STEP 1: PENGAJUAN --}}
                    @php
                        $step1StateClass = $step1Done ? 'timeline-item-complete' : 'timeline-item-active';
                    @endphp
                    <div class="timeline-item {{ $step1StateClass }}">
                        <div class="timeline-icon {{ $step1Done ? 'bg-success' : 'bg-primary' }}">
                            <i class="bi bi-1-circle-fill text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <div class="timeline-step-label">Langkah 1</div>
                                    <div class="timeline-title fw-semibold">
                                        <span>Pengajuan Permohonan</span>
                                    </div>
                                </div>
                                <span
                                    class="badge {{ $step1Done ? 'bg-success-subtle text-success-emphasis' : 'bg-primary-subtle text-primary-emphasis' }}">
                                    {{ $step1Done ? 'Selesai' : 'Sedang Berjalan' }}
                                </span>
                            </div>
                            <p class="mb-0 text-muted small">
                                Permohonan telah dibuat di sistem dengan data pemohon dan rencana penanaman.
                            </p>
                        </div>
                    </div>

                    {{-- STEP 2: DOKUMEN & VERIFIKASI --}}
                    @php
                        $step2Class = $step2Done
                            ? 'timeline-item-complete'
                            : ($step2Prog
                                ? 'timeline-item-active'
                                : '');
                    @endphp
                    <div class="timeline-item {{ $step2Class }}">
                        <div
                            class="timeline-icon
            @if ($step2Done) bg-success
            @elseif($step2Prog) bg-info
            @else bg-secondary @endif">
                            <i class="bi bi-2-circle-fill text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <div class="timeline-step-label">Langkah 2</div>
                                    <div class="timeline-title fw-semibold">
                                        Upload Dokumen & Verifikasi Admin
                                    </div>
                                </div>

                                @if ($step2Done)
                                    <span class="badge bg-success-subtle text-success-emphasis">Selesai</span>
                                @elseif ($step2Prog)
                                    <span class="badge bg-info-subtle text-info-emphasis">Sedang Berjalan</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Menunggu Dokumen</span>
                                @endif
                            </div>

                            @if ($status === 'Menunggu Dokumen')
                                <p class="mb-1 text-muted small">
                                    Silakan upload dokumen pendukung (Surat Permohonan, Surat Pernyataan, KK, KTP, Surat
                                    Tanah).
                                </p>
                            @elseif ($status === 'Perbaikan')
                                <p class="mb-1 text-muted small">
                                    Admin meminta perbaikan dokumen / data. Silakan cek catatan admin di bawah.
                                </p>
                            @elseif ($status === 'Sedang Diverifikasi')
                                <p class="mb-1 text-muted small">
                                    Dokumen Anda sedang diperiksa oleh admin. Mohon menunggu.
                                </p>
                            @elseif (in_array($status, ['Disetujui', 'Ditolak']))
                                <p class="mb-1 text-muted small">
                                    Proses verifikasi telah selesai. Lihat detail keputusan di langkah berikutnya.
                                </p>
                            @endif

                            @if (in_array($status, ['Menunggu Dokumen', 'Perbaikan']))
                                <div class="mt-2 d-flex flex-wrap gap-2 timeline-actions">
                                    <button type="button" class="btn btn-success btn-sm rounded-pill"
                                        data-bs-toggle="modal" data-bs-target="#modalUploadDokumen">
                                        <i class="bi bi-upload me-1"></i> Upload / Perbarui Dokumen
                                    </button>
                                </div>
                            @endif

                            @if ($riwayatKeterangan->count())
                                <hr class="my-2">
                                <div class="small text-muted mb-1">Riwayat Keterangan Admin:</div>
                                <ul class="list-unstyled mb-0 timeline-list">
                                    @foreach ($riwayatKeterangan as $ket)
                                        <li class="mb-1">
                                            <span
                                                class="badge
                                @switch($ket->jenis_keterangan)
                                    @case('Perlu Diperbaiki') bg-secondary @break
                                    @case('Sedang Diverifikasi') bg-info text-dark @break
                                    @case('Disetujui') bg-success @break
                                    @case('Ditolak') bg-danger @break
                                    @case('Dibatalkan') bg-dark @break
                                    @default bg-light text-dark
                                @endswitch
                            ">
                                                {{ $ket->jenis_keterangan }}
                                            </span>
                                            <span class="text-muted small">
                                                -
                                                {{ $ket->tanggal_keterangan
                                                    ? \Carbon\Carbon::parse($ket->tanggal_keterangan)->format('d M Y')
                                                    : $ket->created_at->format('d M Y H:i') }}
                                            </span>
                                            @if ($ket->isi_keterangan)
                                                <div class="ms-1">
                                                    {{ $ket->isi_keterangan }}
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 3: SURAT KEPUTUSAN --}}
                    @php
                        $step3Class = $step3Shown ? 'timeline-item-active' : 'timeline-item-disabled';
                        $sp = $permohonan->status_pembayaran;
                    @endphp

                    <div class="timeline-item {{ $step3Class }}">
                        <div class="timeline-icon {{ $step3Shown ? 'bg-primary' : 'bg-secondary' }}">
                            <i class="bi bi-3-circle-fill text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <div class="timeline-step-label">Langkah 3</div>
                                    <div class="timeline-title fw-semibold">
                                        Surat Keputusan (Persetujuan / Penolakan)
                                    </div>
                                </div>

                                @if ($status === 'Disetujui')
                                    <span class="badge bg-success-subtle text-success-emphasis">Disetujui</span>
                                @elseif ($status === 'Ditolak')
                                    <span class="badge bg-danger-subtle text-danger-emphasis">Ditolak</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Belum Terbit</span>
                                @endif
                            </div>

                            {{-- ===================== STATUS: DISETUJUI ===================== --}}
                            @if ($status === 'Disetujui')

                                {{-- ===================== GRATIS ===================== --}}
                                @if (!$isBerbayar)
                                    <p class="mb-1 text-muted small">
                                        Permohonan Anda telah <strong>DISETUJUI</strong>. Silakan unduh surat persetujuan /
                                        pengambilan yang sudah disiapkan oleh admin.
                                    </p>
                                    <ul class="mb-1 small">
                                        <li>
                                            Tanggal Disetujui:
                                            <strong>
                                                {{ $permohonan->tanggal_disetujui
                                                    ? \Carbon\Carbon::parse($permohonan->tanggal_disetujui)->format('d M Y')
                                                    : '-' }}
                                            </strong>
                                        </li>
                                        <li>
                                            Jumlah Disetujui:
                                            <strong>{{ $permohonan->jumlah_disetujui ?? $permohonan->jumlah_tanaman }}</strong>
                                            tanaman
                                        </li>
                                    </ul>

                                    <div class="mt-2 d-flex flex-wrap gap-2">
                                        @if ($permohonan->scan_surat_pengambilan)
                                            <a href="{{ asset('storage/' . $permohonan->scan_surat_pengambilan) }}"
                                                target="_blank" class="btn btn-outline-primary btn-sm rounded-pill">
                                                <i class="bi bi-file-earmark-arrow-down me-1"></i>
                                                Surat Persetujuan / Pengambilan
                                            </a>
                                        @else
                                            <span class="text-muted small fst-italic">
                                                Surat persetujuan belum diunggah oleh admin.
                                            </span>
                                        @endif
                                    </div>

                                    <div class="alert alert-warning small mt-3 mb-0">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Silakan datang ke Kantor UPT Benih untuk mengambil bibit dengan membawa
                                        <strong>Surat Pengambilan Bibit</strong> yang sudah diunduh / dicetak.
                                    </div>

                                    {{-- ===================== BERBAYAR ===================== --}}
                                @else
                                    {{-- TEKS PEMBUKA BERBEDA JIKA SUDAH BAYAR --}}
                                    @if ($sp === 'Berhasil')
                                        <p class="mb-1 text-muted small">
                                            Permohonan Anda telah <strong>DISETUJUI</strong> dan
                                            <strong>PEMBAYARAN BERHASIL</strong>.
                                            Anda dapat melanjutkan proses ke tahap pengambilan benih sesuai surat
                                            pengambilan.
                                        </p>
                                    @else
                                        <p class="mb-1 text-muted small">
                                            Permohonan Anda telah <strong>DISETUJUI</strong>. Silakan melakukan pembayaran
                                            terlebih dahulu
                                            sesuai instruksi dan barcode di bawah ini.
                                        </p>
                                    @endif

                                    <ul class="mb-1 small">
                                        <li>
                                            Tanggal Disetujui:
                                            <strong>
                                                {{ $permohonan->tanggal_disetujui
                                                    ? \Carbon\Carbon::parse($permohonan->tanggal_disetujui)->format('d M Y')
                                                    : '-' }}
                                            </strong>
                                        </li>
                                        <li>
                                            Jumlah Disetujui:
                                            <strong>{{ $permohonan->jumlah_disetujui ?? $permohonan->jumlah_tanaman }}</strong>
                                            tanaman
                                        </li>
                                        <li>
                                            Harga per Bibit:
                                            <strong>Rp{{ number_format($permohonan->benih->harga ?? 0, 0, ',', '.') }}</strong>
                                        </li>
                                    </ul>

                                    {{-- QRIS HANYA JIKA PEMBAYARAN BELUM BERHASIL --}}
                                    @if ($sp !== 'Berhasil')
                                        <div class="mt-3 text-center">
                                            <p class="small mb-1"><strong>Barcode / QR Pembayaran</strong></p>
                                            @php
                                                $qris = \App\Models\PengaturanQris::where('aktif', true)->first();
                                            @endphp
                                            @if ($qris && $qris->gambar_qris)
                                                <img src="{{ asset('storage/' . $qris->gambar_qris) }}"
                                                    alt="QRIS Pembayaran" class="img-thumbnail shadow-sm"
                                                    style="max-width:220px;height:auto;">
                                                <div class="small text-muted mt-1">
                                                    {{ $qris->nama_qris ?? 'QRIS Pembayaran' }}
                                                </div>
                                            @else
                                                <div class="text-muted small fst-italic">
                                                    QRIS belum diatur oleh admin.
                                                </div>
                                            @endif

                                            <p class="mt-2 mb-0 fw-bold text-primary">
                                                Total Pembayaran:
                                                Rp{{ number_format($permohonan->nominal_pembayaran ?? 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    @endif

                                    {{-- JIKA SUDAH BAYAR & SURAT PENGAMBILAN SUDAH ADA --}}
                                    @if ($sp === 'Berhasil' && $permohonan->scan_surat_pengambilan)
                                        <div
                                            class="mt-3 d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                                            <a href="{{ asset('storage/' . $permohonan->scan_surat_pengambilan) }}"
                                                target="_blank" class="btn btn-outline-primary btn-sm rounded-pill">
                                                <i class="bi bi-file-earmark-arrow-down me-1"></i>
                                                Surat Persetujuan / Pengambilan
                                            </a>
                                        </div>
                                    @endif

                                    <hr class="my-2">

                                    {{-- ALERT DI BAWAHNYA DIBEDAKAN JUGA --}}
                                    @if ($sp === 'Berhasil')
                                        <div class="alert alert-success small mb-0">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Pembayaran Anda telah <strong>BERHASIL</strong>
                                            @if ($permohonan->tanggal_verifikasi_pembayaran)
                                                dan telah diverifikasi pada
                                                <strong>
                                                    {{ \Carbon\Carbon::parse($permohonan->tanggal_verifikasi_pembayaran)->format('d M Y H:i') }}
                                                </strong>.
                                            @endif
                                            Anda dapat menggunakan <strong>Surat Pengambilan Bibit</strong> untuk mengambil
                                            bibit di Kantor UPT Benih.
                                        </div>
                                    @else
                                        <div class="alert alert-warning small mb-0">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            Karena permohonan ini <strong>BERBAYAR</strong>, silakan melakukan pembayaran
                                            sesuai
                                            barcode di atas. Jika dalam <strong>7 hari</strong> belum ada pembayaran,
                                            permohonan dapat
                                            dibatalkan. Setelah pembayaran berstatus <strong>BERHASIL</strong>, Anda dapat
                                            mengunduh
                                            <strong>Surat Pengambilan Bibit</strong> dan mengambil bibit di Kantor UPT.
                                        </div>
                                    @endif
                                @endif {{-- end if !$isBerbayar --}}

                                {{-- ===================== STATUS: DITOLAK ===================== --}}
                            @elseif ($status === 'Ditolak')
                                @php
                                    // Surat penolakan saat ini disimpan di kolom scan_surat_pengambilan
                                    $suratPenolakanPath = $permohonan->scan_surat_pengambilan ?? null;
                                @endphp

                                <div class="mb-2">
                                    <p class="mb-1 text-muted small">
                                        Permohonan Anda <strong>DITOLAK</strong>.
                                    </p>

                                    <ul class="mb-2 small">
                                        <li>
                                            Tanggal Ditolak:
                                            <strong>
                                                {{ $permohonan->tanggal_ditolak ? \Carbon\Carbon::parse($permohonan->tanggal_ditolak)->format('d M Y') : '-' }}
                                            </strong>
                                        </li>
                                        <li>
                                            Jenis Tanaman:
                                            <strong>{{ $permohonan->jenisTanaman->nama_tanaman ?? '-' }}</strong>
                                        </li>
                                        <li>
                                            Jumlah yang Diajukan:
                                            <strong>{{ $permohonan->jumlah_tanaman ?? '-' }}</strong> tanaman
                                        </li>
                                    </ul>

                                    <div class="alert alert-info small mb-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Detail <strong>alasan penolakan</strong> dapat Anda lihat pada
                                        bagian <strong>"Riwayat Keterangan Admin"</strong> di atas.
                                    </div>

                                    @if ($suratPenolakanPath)
                                        <div class="mt-1 d-flex flex-wrap gap-2">
                                            <a href="{{ asset('storage/' . $suratPenolakanPath) }}" target="_blank"
                                                class="btn btn-outline-danger btn-sm rounded-pill">
                                                <i class="bi bi-file-earmark-arrow-down me-1"></i>
                                                Lihat / Unduh Surat Penolakan
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                {{-- ===================== STATUS LAIN (BELUM TERBIT) ===================== --}}
                            @else
                                <p class="mb-0 text-muted small">
                                    Surat persetujuan / penolakan akan tersedia setelah proses verifikasi selesai.
                                </p>
                            @endif


                        </div>
                    </div>


                    {{-- STEP 4: PENGAMBILAN BENIH --}}
                    @php
                        $step4Class = $step4Shown ? 'timeline-item-active' : 'timeline-item-disabled';
                    @endphp
                    <div class="timeline-item {{ $step4Class }}">
                        <div class="timeline-icon {{ $step4Shown ? 'bg-primary' : 'bg-secondary' }}">
                            <i class="bi bi-4-circle-fill text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <div class="timeline-step-label">Langkah 4</div>
                                    <div class="timeline-title fw-semibold">Pengambilan Benih</div>
                                </div>

                                @if ($step4Shown)
                                    @switch($permohonan->status_pengambilan)
                                        @case('Belum Diambil')
                                            <span class="badge bg-warning-subtle text-warning-emphasis">Belum Diambil</span>
                                        @break

                                        @case('Selesai')
                                            <span class="badge bg-success-subtle text-success-emphasis">Selesai Diambil</span>
                                        @break

                                        @case('Dibatalkan')
                                            <span class="badge bg-danger-subtle text-danger-emphasis">Dibatalkan</span>
                                        @break

                                        @default
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                                {{ $permohonan->status_pengambilan ?? '-' }}
                                            </span>
                                    @endswitch
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Belum Terbuka</span>
                                @endif
                            </div>

                            @if ($step4Shown)
                                <p class="mb-1 text-muted small">
                                    Pengambilan benih dilakukan di Kantor UPT Benih sesuai jadwal dan ketentuan pada surat
                                    persetujuan /
                                    pengambilan.
                                </p>

                                <ul class="mb-2 small">
                                    <li>Status Pengambilan
                                        <strong>{{ $permohonan->status_pengambilan ?? '-' }}</strong>
                                    </li>
                                    <li>Tanggal Pengambilan
                                        <strong>
                                            {{ $permohonan->tanggal_pengambilan
                                                ? \Carbon\Carbon::parse($permohonan->tanggal_pengambilan)->format('d M Y')
                                                : '-' }}
                                        </strong>
                                    </li>
                                    <li>Tanggal Selesai
                                        <strong>
                                            {{ $permohonan->tanggal_selesai ? \Carbon\Carbon::parse($permohonan->tanggal_selesai)->format('d M Y') : '-' }}
                                        </strong>
                                    </li>
                                </ul>

                                @if (!$isBerbayar && $permohonan->status_pengambilan === 'Belum Diambil')
                                    <div class="alert alert-warning small mb-0">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Silakan datang ke Kantor UPT Benih untuk mengambil bibit dengan membawa
                                        <strong>Surat Pengambilan Bibit</strong>.
                                    </div>
                                @elseif ($isBerbayar)
                                    @if ($permohonan->status_pembayaran !== 'Berhasil')
                                        <div class="alert alert-warning small mb-0">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Setelah Anda melakukan pembayaran sesuai instruksi pada surat,
                                            Anda dapat mengambil bibit ke Kantor UPT Benih dengan membawa
                                            <strong>Surat Pengambilan Bibit</strong>.
                                        </div>
                                    @elseif ($permohonan->status_pengambilan === 'Belum Diambil')
                                        <div class="alert alert-warning small mb-0">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Pembayaran Anda telah tercatat. Silakan datang ke Kantor UPT Benih
                                            untuk mengambil bibit dengan membawa
                                            <strong>Surat Pengambilan Bibit</strong>.
                                        </div>
                                    @endif
                                @endif
                            @else
                                <p class="mb-0 text-muted small">
                                    Tahap pengambilan benih akan aktif setelah permohonan disetujui.
                                </p>
                            @endif
                        </div>
                    </div>

                </div> {{-- end .timeline --}}

            </div>
        </div>
        {{-- ===================== DOKUMEN TERUNGGAH ===================== --}}
        @if (
            $permohonan->scan_surat_permohonan ||
                $permohonan->scan_surat_pernyataan ||
                $permohonan->scan_kk ||
                $permohonan->scan_ktp ||
                $permohonan->scan_surat_tanah)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 p-md-4 small">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="summary-chip">
                            <i class="bi bi-folder2-open me-1"></i>
                            Dokumen Terunggah
                        </div>
                        <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">
                            PDF Dokumen Permohonan
                        </span>
                    </div>

                    <div class="row g-3">
                        @foreach ([
            'scan_surat_permohonan' => 'Surat Permohonan',
            'scan_surat_pernyataan' => 'Surat Pernyataan',
            'scan_kk' => 'Kartu Keluarga (KK)',
            'scan_ktp' => 'Kartu Tanda Penduduk (KTP)',
            'scan_surat_tanah' => 'Surat Kepemilikan Tanah',
        ] as $field => $label)
                            @if ($permohonan->$field)
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="doc-card h-100 d-flex flex-column justify-content-between text-center p-3">
                                        <div>
                                            <div class="doc-icon-circle mb-2">
                                                <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                            </div>
                                            <p class="doc-label mb-1">{{ $label }}</p>
                                            <div class="doc-meta mb-1">
                                                <span class="badge bg-danger-subtle text-danger-emphasis doc-badge-type">
                                                    PDF
                                                </span>
                                                {{-- kalau mau tambahin info lain, bisa di sini --}}
                                            </div>
                                        </div>
                                        <div class="doc-actions mt-2 d-flex justify-content-center gap-2 flex-wrap">
                                            <a href="{{ asset('storage/' . $permohonan->$field) }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm rounded-pill">
                                                <i class="bi bi-eye me-1"></i> Lihat
                                            </a>
                                            <a href="{{ asset('storage/' . $permohonan->$field) }}" download
                                                class="btn btn-light btn-sm rounded-pill border">
                                                <i class="bi bi-download me-1"></i> Unduh
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="mt-3 text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Jika dokumen yang diunggah perlu diperbarui, silakan gunakan tombol
                        <strong>"Upload / Perbarui Dokumen"</strong> pada langkah verifikasi di atas.
                    </div>
                </div>
            </div>
        @endif

        {{-- ===================== PEMBAYARAN (UNTUK BERBAYAR) ===================== --}}
        @if ($isBerbayar && $status === 'Disetujui')
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 p-md-4 small">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="payment-chip">
                            <i class="bi bi-credit-card me-1"></i>
                            Pembayaran Permohonan
                        </div>
                        <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis">
                            ID Permohonan: #{{ $permohonan->id }}
                        </span>
                    </div>

                    <div class="row g-3">
                        {{-- Ringkasan Pembayaran --}}
                        <div class="col-md-6">
                            <div class="payment-section h-100">
                                @php
                                    $sp = $permohonan->status_pembayaran;
                                @endphp

                                {{-- BARIS STATUS & TOTAL --}}
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="payment-row mb-0">
                                        <span class="payment-label">Status Pembayaran</span>
                                        <span class="payment-separator">:</span>
                                        <span class="payment-value">
                                            <span
                                                class="badge
                                        @switch($sp)
                                            @case('Menunggu')             bg-secondary-subtle text-secondary-emphasis @break
                                            @case('Menunggu Verifikasi')  bg-info-subtle text-info-emphasis           @break
                                            @case('Berhasil')             bg-success-subtle text-success-emphasis     @break
                                            @case('Gagal')                bg-danger-subtle text-danger-emphasis       @break
                                            @default                      bg-light text-dark
                                        @endswitch
                                    ">
                                                {{ $sp ?? 'Menunggu' }}
                                            </span>
                                        </span>
                                    </div>

                                    @if ($permohonan->nominal_pembayaran)
                                        <div class="text-end">
                                            <div class="payment-note mb-0">Total Pembayaran</div>
                                            <div class="fw-bold text-primary" style="font-size: .95rem;">
                                                Rp{{ number_format($permohonan->nominal_pembayaran ?? 0, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <hr class="my-2">

                                {{-- DETAIL LAINNYA --}}
                                <div class="mb-1">
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
                                                <span class="text-muted">Belum ada bukti yang diupload.</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                {{-- Pesan & Catatan --}}
                                <hr class="my-2">
                                <div class="mb-1">
                                    <div class="payment-note mb-1 fw-semibold">Pesan Anda:</div>
                                    @if ($permohonan->pesan_pemohon_pembayaran)
                                        <div class="payment-note px-2 py-1 bg-white rounded border">
                                            "{{ $permohonan->pesan_pemohon_pembayaran }}"
                                        </div>
                                    @else
                                        <div class="payment-note text-muted">Belum ada pesan.</div>
                                    @endif
                                </div>

                                <div>
                                    <div class="payment-note mb-1 fw-semibold">Catatan Admin:</div>
                                    @if ($permohonan->catatan_pembayaran_admin)
                                        <div class="payment-note px-2 py-1 bg-white rounded border border-dashed">
                                            "{{ $permohonan->catatan_pembayaran_admin }}"
                                        </div>
                                    @else
                                        <div class="payment-note text-muted">Belum ada catatan.</div>
                                    @endif
                                </div>

                                <div class="mt-3 payment-note">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Pastikan nominal dan tujuan pembayaran sudah sesuai sebelum mengunggah bukti.
                                </div>
                            </div>
                        </div>

                        {{-- Form upload bukti pembayaran --}}
                        @if ($bolehUploadPembayaran)
                            <div class="col-md-6">
                                <div class="payment-upload-card p-3 h-100">
                                    <p class="fw-semibold mb-2">
                                        <i class="bi bi-cloud-upload me-1"></i>
                                        Upload / Ubah Bukti Pembayaran
                                    </p>
                                    <p class="payment-note mb-2">
                                        Unggah bukti pembayaran Anda (screenshot / foto struk / PDF). Admin akan
                                        memeriksa dan mengubah status pembayaran setelah bukti diterima.
                                    </p>

                                    <form action="{{ route('pemohon.permohonan.pembayaran.store', $permohonan->id) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">
                                                File Bukti Pembayaran
                                            </label>
                                            <input type="file" name="bukti_pembayaran"
                                                class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf"
                                                required>
                                            <small class="text-muted d-block">
                                                Format: JPG, PNG, atau PDF &middot; Maksimal 2 MB.
                                            </small>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">
                                                Pesan untuk Admin <span class="text-muted">(opsional)</span>
                                            </label>
                                            <textarea name="pesan_pemohon_pembayaran" rows="3" class="form-control form-control-sm"
                                                placeholder="Contoh: Sudah transfer via mobile banking, mohon dicek.">{{ old('pesan_pemohon_pembayaran', $permohonan->pesan_pemohon_pembayaran) }}</textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill mt-1">
                                            <i class="bi bi-save me-1"></i> Kirim Bukti Pembayaran
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6">
                                <div class="payment-upload-card p-3 h-100 bg-light-subtle">
                                    <p class="mb-1 fw-semibold">
                                        <i class="bi bi-lock me-1"></i> Upload Ditutup
                                    </p>
                                    <p class="mb-0 payment-note">
                                        Upload bukti pembayaran tidak tersedia (status pembayaran:
                                        <strong>{{ $permohonan->status_pembayaran ?? '-' }}</strong>).
                                        Jika Anda merasa ada kesalahan, silakan hubungi admin.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif


        {{-- ===================== BUKTI TANAM ===================== --}}
        @if ($status === 'Disetujui' && $permohonan->status_pengambilan === 'Selesai')
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3 p-md-4 small">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="plant-proof-chip">
                            <i class="bi bi-tree me-1"></i>
                            Bukti Tanam di Lahan
                        </div>

                        @if ($permohonan->bukti_tanam)
                            <span class="badge rounded-pill bg-success-subtle text-success-emphasis">
                                Bukti Tanam Terunggah
                            </span>
                        @elseif ($bolehUploadBuktiTanam)
                            <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
                                Menunggu Bukti Tanam
                            </span>
                        @else
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">
                                Tidak Dapat Mengunggah
                            </span>
                        @endif
                    </div>

                    <div class="row g-3">
                        {{-- Ringkasan bukti tanam --}}
                        <div class="col-md-6">
                            <div class="plant-proof-section h-100">
                                <div class="plant-proof-row">
                                    <span class="plant-proof-label">Deadline Upload</span>
                                    <span class="plant-proof-separator">:</span>
                                    <span class="plant-proof-value">
                                        {{ $permohonan->tanggal_tanam_deadline
                                            ? \Carbon\Carbon::parse($permohonan->tanggal_tanam_deadline)->format('d M Y')
                                            : '-' }}
                                    </span>
                                </div>

                                <div class="plant-proof-row">
                                    <span class="plant-proof-label">Tanggal Tanam</span>
                                    <span class="plant-proof-separator">:</span>
                                    <span class="plant-proof-value">
                                        {{ $permohonan->tanggal_tanam ? \Carbon\Carbon::parse($permohonan->tanggal_tanam)->format('d M Y') : '-' }}
                                    </span>
                                </div>

                                <div class="plant-proof-row">
                                    <span class="plant-proof-label">Status Bukti Tanam</span>
                                    <span class="plant-proof-separator">:</span>
                                    <span class="plant-proof-value">
                                        @if ($permohonan->bukti_tanam)
                                            <span class="badge rounded-pill bg-success-subtle text-success-emphasis">
                                                Sudah Diupload
                                            </span>
                                        @elseif ($bolehUploadBuktiTanam)
                                            <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
                                                Menunggu Upload
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </span>
                                </div>

                                <hr class="my-2">

                                <div class="plant-proof-note">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Unggah foto kondisi tanaman di lahan sebagai bukti bahwa bibit benar-benar sudah
                                    ditanam.
                                    Usahakan foto cukup jelas dan diambil di lokasi yang sesuai.
                                </div>
                            </div>
                        </div>

                        {{-- Preview / Form Upload --}}
                        <div class="col-md-6">
                            @if ($permohonan->bukti_tanam)
                                {{-- SUDAH ADA BUKTI TANAM --}}
                                @php
                                    $path = $permohonan->bukti_tanam;
                                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp

                                <div class="plant-proof-upload-card p-3 h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <p class="fw-semibold mb-2">
                                            <i class="bi bi-check-circle text-success me-1"></i>
                                            Bukti Tanam Telah Diterima
                                        </p>

                                        @if ($isImage)
                                            <div class="text-center mb-2">
                                                <img src="{{ asset('storage/' . $path) }}" alt="Bukti Tanam"
                                                    class="plant-proof-thumb">
                                            </div>
                                        @else
                                            <div class="text-center mb-2">
                                                <div class="doc-icon-circle mb-2">
                                                    <i class="bi bi-file-earmark-pdf fs-5"></i>
                                                </div>
                                                <div class="plant-proof-note">
                                                    Dokumen bukti tanam dalam bentuk file
                                                    <strong>{{ strtoupper($ext) }}</strong>.
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div
                                        class="d-flex gap-2 flex-wrap justify-content-center justify-content-md-start mt-2">
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                            class="btn btn-outline-success btn-sm rounded-pill">
                                            <i class="bi bi-eye me-1"></i> Lihat Bukti Tanam
                                        </a>
                                    </div>

                                    <div class="alert alert-success small mb-0 mt-3">
                                        Terima kasih, bukti tanam Anda sudah diterima.
                                    </div>
                                </div>
                            @elseif ($bolehUploadBuktiTanam)
                                {{-- FORM UPLOAD BUKTI TANAM --}}
                                <div class="plant-proof-upload-card p-3 h-100">
                                    <p class="fw-semibold mb-2">
                                        <i class="bi bi-cloud-upload me-1"></i>
                                        Upload Bukti Tanam
                                    </p>
                                    <p class="plant-proof-note mb-2">
                                        Unggah minimal satu foto (atau dokumen) yang menunjukkan bibit sudah ditanam di
                                        lahan.
                                    </p>

                                    <form action="{{ route('pemohon.permohonan.bukti_tanam.store', $permohonan->id) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Tanggal Tanam</label>
                                            <input type="date" name="tanggal_tanam"
                                                class="form-control form-control-sm" required>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">
                                                File Bukti Tanam <span class="text-muted">(foto / PDF)</span>
                                            </label>
                                            <input type="file" name="bukti_tanam" class="form-control form-control-sm"
                                                accept=".jpg,.jpeg,.png,.pdf" required>
                                            <small class="text-muted d-block">
                                                Format: JPG, PNG, atau PDF &middot; Maksimal 2 MB.
                                            </small>
                                        </div>

                                        <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill mt-1">
                                            <i class="bi bi-send-check me-1"></i> Kirim Bukti Tanam
                                        </button>
                                    </form>
                                </div>
                            @else
                                {{-- TIDAK BISA UPLOAD --}}
                                <div class="plant-proof-upload-card p-3 h-100 bg-light-subtle">
                                    <p class="fw-semibold mb-1">
                                        <i class="bi bi-lock me-1"></i>
                                        Upload Bukti Tanam Tidak Tersedia
                                    </p>
                                    <p class="plant-proof-note mb-0">
                                        Upload bukti tanam belum tersedia atau sudah tidak diperlukan untuk permohonan ini.
                                        Jika Anda merasa masih perlu mengunggah bukti, silakan hubungi admin.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif


        {{-- ===================== AKSI BAWAH ===================== --}}
        <div class="border-top pt-3 mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2" >
            <a href="{{ route('pemohon.permohonan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>

            {{-- kalau nanti mau tambah aksi lain di kanan, tinggal taruh di sini --}}
        </div>

    </div>

    {{-- ===================== MODAL UPLOAD DOKUMEN ===================== --}}
    <div class="modal fade" id="modalUploadDokumen" tabindex="-1" aria-labelledby="modalUploadDokumenLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title small fw-semibold" id="modalUploadDokumenLabel">
                        <i class="bi bi-cloud-upload text-primary me-1"></i> Upload Dokumen Permohonan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    {{-- Petunjuk --}}
                    <div class="alert alert-secondary bg-light small py-2 mb-3 border-0">
                        <div class="fw-semibold mb-1">
                            <i class="bi bi-info-circle me-1 text-primary"></i> Petunjuk:
                        </div>
                        <p class="mb-1">
                            Unggah <b>Surat Permohonan</b> dan <b>Surat Pernyataan</b> yang sudah ditandatangani,
                            beserta dokumen pendukung berikut:
                        </p>
                        <ul class="mb-2 small text-muted ps-3">
                            <li>Surat Permohonan (bertanda tangan)</li>
                            <li>Surat Pernyataan</li>
                            <li>Kartu Keluarga (KK)</li>
                            <li>Kartu Tanda Penduduk (KTP)</li>
                            <li>Surat Kepemilikan Tanah</li>
                        </ul>
                        <div class="text-muted small">
                            Anda dapat mengganti file kapan saja sebelum diverifikasi admin.
                        </div>
                    </div>

                    {{-- Error validasi (kalau ada) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger small py-2">
                            <strong><i class="bi bi-x-circle me-1"></i>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORM UPLOAD --}}
                    <form id="formUploadDokumen" action="{{ route('pemohon.permohonan.uploadStore', $permohonan->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf

                        @php
                            $files = [
                                [
                                    'label' => 'Surat Permohonan (bertanda tangan)',
                                    'name' => 'scan_surat_permohonan',
                                    'file' => $permohonan->scan_surat_permohonan,
                                ],
                                [
                                    'label' => 'Surat Pernyataan',
                                    'name' => 'scan_surat_pernyataan',
                                    'file' => $permohonan->scan_surat_pernyataan,
                                ],
                                ['label' => 'Kartu Keluarga (KK)', 'name' => 'scan_kk', 'file' => $permohonan->scan_kk],
                                [
                                    'label' => 'Kartu Tanda Penduduk (KTP)',
                                    'name' => 'scan_ktp',
                                    'file' => $permohonan->scan_ktp,
                                ],
                                [
                                    'label' => 'Surat Kepemilikan Tanah',
                                    'name' => 'scan_surat_tanah',
                                    'file' => $permohonan->scan_surat_tanah,
                                ],
                            ];
                        @endphp

                        @foreach ($files as $item)
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-dark">{{ $item['label'] }}</label>

                                @if ($item['file'])
                                    <div class="mb-1">
                                        <a href="{{ asset('storage/' . $item['file']) }}" target="_blank"
                                            class="small text-decoration-none text-primary">
                                            <i class="bi bi-file-earmark-text me-1"></i> Lihat file sebelumnya
                                        </a>
                                    </div>
                                @endif

                                <div class="input-group input-group-sm mb-1">
                                    <label class="input-group-text bg-light">
                                        <i class="bi bi-paperclip"></i>
                                    </label>
                                    <input type="file" name="{{ $item['name'] }}" class="form-control file-input"
                                        accept=".pdf,.jpg,.jpeg,.png" data-preview-target="preview_{{ $item['name'] }}">
                                </div>

                                {{-- Preview file --}}
                                <div id="preview_{{ $item['name'] }}" class="mt-1"></div>

                                <small class="text-muted">Maksimal 2 MB • Kosongkan jika tidak ingin mengganti</small>
                            </div>
                        @endforeach
                    </form>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="submit" form="formUploadDokumen" class="btn btn-primary btn-sm">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Simpan & Upload
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MAP & ALERT SCRIPT ===================== --}}
    @if ($isValidCoordinate)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var map = L.map('map').setView([{{ $permohonan->latitude }}, {{ $permohonan->longitude }}], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                L.marker([{{ $permohonan->latitude }}, {{ $permohonan->longitude }}])
                    .addTo(map)
                    .bindPopup(
                        "<strong>Lokasi Lahan:</strong><br>Lat: {{ $permohonan->latitude }}<br>Lng: {{ $permohonan->longitude }}"
                    )
                    .openPopup();
            });
        </script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const alerts = document.querySelectorAll('.auto-hide');
            alerts.forEach(alert => {
                setTimeout(() => alert.classList.remove('show'), 3000);
            });

            // Preview file + validasi 2MB dalam modal
            document.querySelectorAll('.file-input').forEach(input => {
                input.addEventListener('change', function() {
                    const preview = document.getElementById(this.dataset.previewTarget);
                    const file = this.files[0];
                    preview.innerHTML = '';

                    if (file) {
                        const maxSize = 2 * 1024 * 1024; // 2MB
                        if (file.size > maxSize) {
                            alert("⚠️ Ukuran file terlalu besar! Maksimal 2 MB per dokumen.");
                            this.value = "";
                            return;
                        }

                        const ext = file.name.split('.').pop().toLowerCase();
                        if (['jpg', 'jpeg', 'png'].includes(ext)) {
                            const img = document.createElement('img');
                            img.src = URL.createObjectURL(file);
                            img.classList.add('img-thumbnail', 'mt-1');
                            img.style.maxWidth = '150px';
                            img.style.height = 'auto';
                            preview.appendChild(img);
                        } else if (ext === 'pdf') {
                            preview.innerHTML = `<div class="text-muted small mt-1">
                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i> ${file.name}
                        </div>`;
                        } else {
                            preview.innerHTML =
                                '<span class="text-danger small">Format file tidak didukung.</span>';
                        }
                    }
                });
            });
        });
    </script>

    <style>
        .timeline {
            border-left: 2px solid #e5e7eb;
            padding-left: 0.75rem;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.3rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-icon {
            position: absolute;
            left: -18px;
            top: 2px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .timeline-content {
            margin-left: 0.7rem;
            padding-left: 0.3rem;
        }

        .timeline-item-disabled {
            opacity: 0.55;
        }
    </style>
@endsection
