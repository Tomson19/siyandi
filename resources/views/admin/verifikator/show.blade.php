@extends('layouts.bootstrap')

@section('title', 'Detail Permohonan (Verifikator)')

@section('content')
    <div class="container py-4">

        {{-- HEADER UTAMA --}}
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <h4 class="fw-bold mb-1" style="font-size: 1rem;">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>
                    Detail Permohonan Benih
                </h4>
                <div class="small text-muted">
                    Diajukan pada:
                    <strong>
                        {{ $permohonan->tanggal_diajukan ? \Carbon\Carbon::parse($permohonan->tanggal_diajukan)->format('d M Y') : '-' }}
                    </strong>
                </div>
                <div class="small text-muted">
                    Pemohon:
                    <strong>{{ $permohonan->nama }}</strong>
                </div>
            </div>

            <div class="text-end">
                {{-- STATUS UTAMA --}}
                <div class="mb-1">
                    <span class="small text-muted d-block">Status Permohonan</span>
                    <span
                        class="badge px-3 py-2 shadow-sm
                    @switch($permohonan->status)
                        @case('Sedang Diverifikasi') bg-info text-dark @break
                        @case('Disetujui')          bg-success        @break
                        @case('Ditolak')           bg-danger         @break
                        @case('Perbaikan')         bg-warning text-dark @break
                        @case('Menunggu Dokumen')  bg-secondary      @break
                        @case('Dibatalkan')        bg-dark           @break
                        @default                   bg-light text-dark
                    @endswitch">
                        {{ strtoupper($permohonan->status ?? '-') }}
                    </span>
                </div>

                {{-- TIPE PERMOHONAN --}}
                <div class="mb-1">
                    <span class="small text-muted d-block">Tipe Permohonan</span>
                    @if ($permohonan->tipe_pembayaran === 'Berbayar')
                        <span class="badge bg-danger px-3 py-1">Berbayar</span>
                    @else
                        <span class="badge bg-success px-3 py-1">
                            {{ $permohonan->tipe_pembayaran ?? 'Gratis' }}
                        </span>
                    @endif
                </div>

                {{-- STATUS PEMBAYARAN (ringkas di header) --}}
                @if ($permohonan->tipe_pembayaran === 'Berbayar')
                    <div>
                        <span class="small text-muted d-block">Status Pembayaran</span>
                        @php $sp = $permohonan->status_pembayaran; @endphp
                        <span
                            class="badge px-3 py-1
                        @switch($sp)
                            @case('Menunggu')             bg-secondary      @break
                            @case('Menunggu Verifikasi')  bg-info text-dark @break
                            @case('Berhasil')             bg-success        @break
                            @case('Gagal')                bg-danger         @break
                            @default                      bg-light text-dark
                        @endswitch
                    ">
                            {{ $sp ?? 'Belum Ada' }}
                        </span>
                    </div>
                @endif
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

        {{-- ===================== BLOK DATA UTAMA ===================== --}}
        <div class="row g-3 mb-4">
            {{-- Data Pemohon --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body small">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            🧍 Data Pemohon
                        </h6>
                        <p class="mb-1"><strong>Nama:</strong> {{ $permohonan->nama }}</p>
                        <p class="mb-1"><strong>NIK:</strong> {{ $permohonan->nik }}</p>
                        <p class="mb-1"><strong>Alamat:</strong> {{ $permohonan->alamat }}</p>
                        <p class="mb-0"><strong>No. Telepon:</strong> {{ $permohonan->no_telp }}</p>
                    </div>
                </div>
            </div>

            {{-- Data Permohonan --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body small">
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            🌾 Data Permohonan
                        </h6>
                        <p class="mb-1">
                            <strong>Jenis Tanaman:</strong>
                            {{ $permohonan->jenisTanaman->nama_tanaman ?? '-' }}
                        </p>
                        <p class="mb-1">
                            <strong>Jenis Benih:</strong> {{ $permohonan->jenis_benih ?? '-' }}
                        </p>
                        <p class="mb-1">
                            <strong>Jumlah Diajukan:</strong> {{ $permohonan->jumlah_tanaman }}
                        </p>
                        <p class="mb-1">
                            <strong>Jumlah Disetujui:</strong>
                            @if ($permohonan->jumlah_disetujui)
                                <span class="text-success fw-semibold">{{ $permohonan->jumlah_disetujui }}</span>
                            @else
                                <span class="text-muted">Belum diisi</span>
                            @endif
                        </p>
                        <p class="mb-1">
                            <strong>Luas Area:</strong> {{ $permohonan->luas_area }} Ha
                        </p>
                        @if ($permohonan->latitude || $permohonan->longitude)
                            <p class="mb-0">
                                <strong>Koordinat:</strong>
                                {{ $permohonan->latitude }}, {{ $permohonan->longitude }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== DOKUMEN PEMOHON ===================== --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body small">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                    <i class="bi bi-folder2-open me-1"></i> Dokumen Pemohon
                </h6>

                @php
                    $dokumen = [
                        ['label' => 'Surat Permohonan (Sistem / Ditandatangani)', 'field' => 'scan_surat_permohonan'],
                        ['label' => 'Surat Pernyataan', 'field' => 'scan_surat_pernyataan'],
                        ['label' => 'Kartu Keluarga (KK)', 'field' => 'scan_kk'],
                        ['label' => 'Kartu Tanda Penduduk (KTP)', 'field' => 'scan_ktp'],
                        ['label' => 'Surat Kepemilikan Tanah', 'field' => 'scan_surat_tanah'],
                    ];
                @endphp

                <div class="row g-3">
                    @foreach ($dokumen as $doc)
                        @php $path = $permohonan->{$doc['field']}; @endphp
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card border-light shadow-sm h-100 rounded-3">
                                <div class="card-body d-flex flex-column justify-content-between p-3">
                                    <div class="mb-2">
                                        <p class="fw-semibold text-dark mb-1" style="font-size: .8rem;">
                                            {{ $doc['label'] }}
                                        </p>
                                    </div>

                                    @if ($path)
                                        @php
                                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                            $isPdf = $ext === 'pdf';
                                        @endphp
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                            class="btn btn-sm {{ $isPdf ? 'btn-outline-success' : 'btn-outline-primary' }} w-100">
                                            <i class="bi bi-eye me-1"></i>
                                            {{ $isPdf ? 'Lihat (PDF)' : 'Lihat / Unduh' }}
                                        </a>
                                    @else
                                        <span class="badge bg-light text-muted w-100 py-2 border">
                                            Belum diunggah
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- ===================== TINDAKAN VERIFIKATOR (SETUJUI / TOLAK / PERBAIKAN) ===================== --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body small">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                    <i class="bi bi-check2-square me-1"></i> Tindakan Verifikator
                </h6>

                @php $bisaVerifikasi = $permohonan->status === 'Sedang Diverifikasi'; @endphp

                @if ($bisaVerifikasi)
                    <div class="row g-3">
                        {{-- SETUJUI --}}
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                <h6 class="fw-semibold text-success mb-2" style="font-size: .85rem;">
                                    <i class="bi bi-check-circle me-1"></i> Setujui Permohonan
                                </h6>
                                <form action="{{ route('admin.verifikator.permohonan.approve', $permohonan->id) }}"
                                    method="POST" onsubmit="return confirm('Setujui permohonan ini?')">
                                    @csrf
                                    <label class="form-label small mb-1">Jumlah yang Disetujui</label>
                                    <input type="number" name="jumlah_disetujui" class="form-control form-control-sm mb-2"
                                        min="1" max="{{ $permohonan->jumlah_tanaman }}"
                                        value="{{ old('jumlah_disetujui', $permohonan->jumlah_disetujui ?? $permohonan->jumlah_tanaman) }}"
                                        required>
                                    {{-- 🔸 Tambahkan ini untuk BERBAYAR --}}
                                    @if ($permohonan->tipe_pembayaran === 'Berbayar' && $permohonan->benih)
                                        <div class="alert alert-light border small mb-2">
                                            <div><strong>Harga Satuan:</strong> Rp
                                                {{ number_format($permohonan->benih->harga, 0, ',', '.') }}</div>
                                            <div><strong>Total Pembayaran:</strong> <span id="totalBayar">Rp 0</span></div>
                                            <small class="text-muted">Nominal dihitung otomatis = Harga × Jumlah
                                                Disetujui</small>
                                        </div>

                                        <script>
                                            document.addEventListener('input', function() {
                                                const harga = {{ $permohonan->benih->harga ?? 0 }};
                                                const jumlah = parseInt(document.querySelector('[name="jumlah_disetujui"]').value || 0);
                                                document.getElementById('totalBayar').textContent =
                                                    'Rp ' + (harga * jumlah).toLocaleString('id-ID');
                                            });
                                        </script>
                                    @endif

                                    <label class="form-label small mb-1">Catatan / Alasan (wajib)</label>
                                    <textarea name="alasan" class="form-control form-control-sm mb-2" rows="3" required></textarea>
                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                        <i class="bi bi-check-circle me-1"></i> Setujui & Buat Surat
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- TOLAK --}}
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                <h6 class="fw-semibold text-danger mb-2" style="font-size: .85rem;">
                                    <i class="bi bi-x-circle me-1"></i> Tolak Permohonan
                                </h6>
                                <form action="{{ route('admin.verifikator.permohonan.reject', $permohonan->id) }}"
                                    method="POST" onsubmit="return confirm('Tolak permohonan ini?')">
                                    @csrf
                                    <label class="form-label small mb-1">Alasan Penolakan (wajib)</label>
                                    <textarea name="alasan" class="form-control form-control-sm mb-2" rows="3" required></textarea>
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="bi bi-x-circle me-1"></i> Tolak & Buat Surat
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- PERBAIKAN --}}
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                <h6 class="fw-semibold text-warning mb-2" style="font-size: .85rem;">
                                    <i class="bi bi-pencil-square me-1"></i> Minta Perbaikan
                                </h6>
                                <form action="{{ route('admin.verifikator.permohonan.perbaiki', $permohonan->id) }}"
                                    method="POST" onsubmit="return confirm('Kirim permintaan perbaikan ke pemohon?')">
                                    @csrf
                                    <label class="form-label small mb-1">Catatan Perbaikan (wajib)</label>
                                    <textarea name="alasan" class="form-control form-control-sm mb-2" rows="3" required></textarea>
                                    <button type="submit" class="btn btn-warning btn-sm w-100 text-dark">
                                        <i class="bi bi-tools me-1"></i> Kirim Permintaan Perbaikan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif ($permohonan->status === 'Menunggu Dokumen')
                    <div class="alert alert-warning text-center mb-0">
                        <i class="bi bi-hourglass-split me-1"></i>
                        Pemohon belum melengkapi dokumen. Tidak dapat diverifikasi.
                    </div>
                @elseif ($permohonan->status === 'Perbaikan')
                    <div class="alert alert-info text-center mb-0">
                        <i class="bi bi-tools me-1"></i>
                        Permohonan sedang diperbaiki oleh pemohon.
                    </div>
                @else
                    <div class="alert alert-secondary text-center mb-0">
                        <i class="bi bi-lock me-1"></i>
                        Semua tindakan dikunci (status: <b>{{ $permohonan->status }}</b>).
                    </div>
                @endif
            </div>
        </div>

        {{-- ===================== PANEL PEMBAYARAN (HANYA BERBAYAR & SUDAH DISETUJUI) ===================== --}}
        @if ($permohonan->tipe_pembayaran === 'Berbayar' && $permohonan->status === 'Disetujui')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body small">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-credit-card me-1"></i> Pembayaran Permohonan
                    </h6>

                    <div class="row g-3">
                        {{-- INFO PEMBAYARAN --}}
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                <p class="mb-1">
                                    <strong>Status Pembayaran:</strong>
                                    @php $sp = $permohonan->status_pembayaran; @endphp
                                    <span
                                        class="badge ms-1
                                    @switch($sp)
                                        @case('Menunggu')             bg-secondary      @break
                                        @case('Menunggu Verifikasi')  bg-info text-dark @break
                                        @case('Berhasil')             bg-success        @break
                                        @case('Gagal')                bg-danger         @break
                                        @default                      bg-light text-dark
                                    @endswitch
                                ">
                                        {{ $sp ?? 'Menunggu' }}
                                    </span>
                                </p>

                                <p class="mb-1">
                                    <strong>Batas Pembayaran:</strong>
                                    {{ $permohonan->batas_pembayaran ? \Carbon\Carbon::parse($permohonan->batas_pembayaran)->format('d M Y') : '-' }}
                                </p>

                                <p class="mb-1">
                                    <strong>Tgl Verifikasi Pembayaran:</strong>
                                    {{ $permohonan->tanggal_verifikasi_pembayaran
                                        ? \Carbon\Carbon::parse($permohonan->tanggal_verifikasi_pembayaran)->format('d M Y H:i')
                                        : '-' }}
                                </p>

                                <p class="mb-2">
                                    <strong>Bukti Pembayaran:</strong><br>
                                    @if ($permohonan->bukti_pembayaran)
                                        <a href="{{ asset('storage/' . $permohonan->bukti_pembayaran) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary mt-1">
                                            <i class="bi bi-eye me-1"></i> Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-muted">Belum ada bukti yang diupload pemohon.</span>
                                    @endif
                                </p>

                                <p class="mb-0">
                                    <strong>Pesan Pemohon:</strong><br>
                                    @if ($permohonan->pesan_pemohon_pembayaran)
                                        <span class="text-muted">
                                            "{{ $permohonan->pesan_pemohon_pembayaran }}"
                                        </span>
                                    @else
                                        <span class="text-muted">Belum ada pesan dari pemohon.</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- FORM VERIFIKASI PEMBAYARAN --}}
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-semibold mb-2" style="font-size: .85rem;">
                                    <i class="bi bi-shield-check me-1"></i> Verifikasi Pembayaran
                                </h6>

                                {{-- 
                                NOTE:
                                Route ini perlu kamu buat:
                                Route::post('/permohonan/{id}/verifikasi-pembayaran', [VerifikatorPermohonanController::class, 'verifikasiPembayaran'])->name('permohonan.verifikasiPembayaran');
                            --}}
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
                                                Berhasil</option>
                                            <option value="Gagal"
                                                {{ $permohonan->status_pembayaran == 'Gagal' ? 'selected' : '' }}>Gagal
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Catatan Admin</label>
                                        <textarea name="catatan_pembayaran_admin" class="form-control form-control-sm" rows="4"
                                            placeholder="Contoh: Bukti transfer tidak sesuai, mohon upload ulang / Pembayaran sudah masuk sesuai nominal.">{{ old('catatan_pembayaran_admin', $permohonan->catatan_pembayaran_admin) }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm w-100">
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
        @if (in_array($permohonan->status, ['Disetujui', 'Ditolak']))
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body small">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-file-earmark-check me-1"></i> Surat Keputusan
                    </h6>

                    @php
                        $pathKeputusan = $permohonan->scan_surat_pengambilan;
                        $extKeputusan = $pathKeputusan
                            ? strtolower(pathinfo($pathKeputusan, PATHINFO_EXTENSION))
                            : null;
                    @endphp

                    @if ($pathKeputusan)
                        <div class="mb-3">
                            @if ($extKeputusan === 'docx')
                                <a href="{{ asset('storage/' . $pathKeputusan) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-file-earmark-word me-1"></i> Download Surat (Word)
                                </a>
                            @elseif ($extKeputusan === 'pdf')
                                <a href="{{ asset('storage/' . $pathKeputusan) }}" target="_blank"
                                    class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Lihat Surat (PDF)
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning small mb-3">
                            ⚠️ Surat belum tersedia. Gunakan tombol <b>Setujui</b> atau <b>Tolak</b> untuk membuat surat
                            keputusan.
                        </div>
                    @endif

                    <form action="{{ route('admin.verifikator.permohonan.uploadKeputusan', $permohonan->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="form-label small fw-semibold mb-1">
                            Upload Surat yang Sudah Ditandatangani (PDF)
                        </label>
                        <input type="file" name="surat_pdf" class="form-control form-control-sm mb-2" accept=".pdf"
                            required>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-cloud-upload me-1"></i> Simpan / Upload Surat PDF
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- ===================== STATUS PENGAMBILAN + BUKTI PENGAMBILAN ===================== --}}
        @if ($permohonan->status === 'Disetujui')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body small">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-box-seam me-1"></i> Status Pengambilan Bibit
                    </h6>

                    <form action="{{ route('admin.verifikator.permohonan.updatePengambilan', $permohonan->id) }}"
                        method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Status Pengambilan</label>
                            <select name="status_pengambilan" class="form-select form-select-sm">
                                <option value="Belum Diambil"
                                    {{ $permohonan->status_pengambilan == 'Belum Diambil' ? 'selected' : '' }}>Belum
                                    Diambil</option>
                                <option value="Selesai"
                                    {{ $permohonan->status_pengambilan == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Dibatalkan"
                                    {{ $permohonan->status_pengambilan == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Tanggal Pengambilan</label>
                            <input type="date" name="tanggal_pengambilan"
                                value="{{ $permohonan->tanggal_pengambilan }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Bukti Pengambilan (opsional)</label>
                            @if ($permohonan->bukti_pengambilan)
                                <div class="mb-1">
                                    <a href="{{ asset('storage/' . $permohonan->bukti_pengambilan) }}" target="_blank"
                                        class="small text-decoration-none">
                                        <i class="bi bi-image me-1"></i> Lihat Bukti Sebelumnya
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="bukti_pengambilan" class="form-control form-control-sm mb-2"
                                accept=".jpg,.jpeg,.png,.pdf">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>

                    {{-- 🔹 Ringkasan data tersimpan --}}
                    <hr class="my-3">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                <div class="fw-semibold mb-1">Status Pengambilan (Tersimpan)</div>
                                @switch($permohonan->status_pengambilan)
                                    @case('Belum Diambil')
                                        <span class="badge bg-warning text-dark">Belum Diambil</span>
                                    @break

                                    @case('Selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @break

                                    @case('Dibatalkan')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @break

                                    @default
                                        <span class="badge bg-secondary">{{ $permohonan->status_pengambilan ?? '-' }}</span>
                                @endswitch
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                <div class="fw-semibold mb-1">Tanggal Pengambilan (Tersimpan)</div>
                                <div class="text-muted">
                                    @if ($permohonan->tanggal_pengambilan)
                                        {{ \Carbon\Carbon::parse($permohonan->tanggal_pengambilan)->format('d M Y') }}
                                    @else
                                        <span class="fst-italic">Belum diisi</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light-subtle text-center">
                                <div class="fw-semibold mb-1">Bukti Pengambilan (Tersimpan)</div>
                                @if ($permohonan->bukti_pengambilan)
                                    @php
                                        $ext = strtolower(pathinfo($permohonan->bukti_pengambilan, PATHINFO_EXTENSION));
                                    @endphp

                                    @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                        <a href="{{ asset('storage/' . $permohonan->bukti_pengambilan) }}"
                                            target="_blank">
                                            <img src="{{ asset('storage/' . $permohonan->bukti_pengambilan) }}"
                                                alt="bukti pengambilan" class="img-thumbnail" style="max-height: 120px;">
                                        </a>
                                    @elseif ($ext === 'pdf')
                                        <a href="{{ asset('storage/' . $permohonan->bukti_pengambilan) }}"
                                            target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> Lihat PDF
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $permohonan->bukti_pengambilan) }}"
                                            target="_blank" class="small">
                                            <i class="bi bi-file-earmark-text me-1"></i> Lihat File
                                        </a>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">Belum ada bukti tersimpan.</span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif


        {{-- ===================== BUKTI TANAM (UNTUK ADMIN LIHAT) ===================== --}}
        @if ($permohonan->bukti_tanam || $permohonan->tanggal_tanam_deadline)
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body small">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-tree me-1"></i> Bukti Tanam di Lahan
                    </h6>

                    <p class="mb-1">
                        <strong>Deadline Bukti Tanam:</strong>
                        {{ $permohonan->tanggal_tanam_deadline
                            ? \Carbon\Carbon::parse($permohonan->tanggal_tanam_deadline)->format('d M Y')
                            : '-' }}
                    </p>
                    <p class="mb-2">
                        <strong>Tanggal Tanam Dikirim:</strong>
                        {{ $permohonan->tanggal_tanam ? \Carbon\Carbon::parse($permohonan->tanggal_tanam)->format('d M Y') : '-' }}
                    </p>

                    @if ($permohonan->bukti_tanam)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $permohonan->bukti_tanam) }}" target="_blank"
                                class="btn btn-sm btn-outline-success">
                                <i class="bi bi-image me-1"></i> Lihat Bukti Tanam
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning small mb-0">
                            Pemohon belum mengunggah bukti tanam.
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- ===================== RIWAYAT KETERANGAN ===================== --}}
        @if (isset($keterangan) && $keterangan->isNotEmpty())
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body small">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                        <i class="bi bi-clock-history me-1"></i> Riwayat Keterangan
                    </h6>
                    <ul class="list-group list-group-flush">
                        @foreach ($keterangan as $ket)
                            <li class="list-group-item small">
                                <span
                                    class="badge
                                @switch($ket->jenis_keterangan)
                                    @case('Perlu Diperbaiki')    bg-secondary @break
                                    @case('Sedang Diverifikasi') bg-info text-dark @break
                                    @case('Disetujui')           bg-success @break
                                    @case('Ditolak')             bg-danger  @break
                                    @case('Dibatalkan')          bg-dark    @break
                                    @default                     bg-light text-dark
                                @endswitch
                            ">
                                    {{ $ket->jenis_keterangan }}
                                </span>
                                <span class="text-muted ms-1">
                                    {{ $ket->tanggal_keterangan
                                        ? \Carbon\Carbon::parse($ket->tanggal_keterangan)->format('d M Y H:i')
                                        : $ket->created_at->format('d M Y H:i') }}
                                </span>
                                @if ($ket->admin?->name)
                                    <span class="text-muted ms-2">
                                        oleh <strong>{{ $ket->admin->name }}</strong>
                                    </span>
                                @endif
                                @if ($ket->isi_keterangan)
                                    <div class="mt-1">
                                        {{ $ket->isi_keterangan }}
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
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
@endsection
