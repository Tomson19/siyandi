@extends('layouts.bootstrap')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<style>
    body{
        margin-top: -70px;
    }
    .icon-circle {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    .card-header-soft {
        background: #f8f9fa;
        border-bottom: 0;
    }

    .dl-clean dt {
        font-weight: 500;
        color: #6c757d;
    }

    .dl-clean dd {
        margin-bottom: .4rem;
        font-weight: 500;
        color: #212529;
    }

    .btn-icon {
        width: 30px;
        height: 30px;
        padding: 0;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-icon i {
        font-size: 0.9rem;
    }
</style>

<div class="container-fluid py-4">
    {{-- HEADER --}}
    <div class="row mb-3 align-items-center">
        <div class="col">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="icon-circle bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-calendar-event"></i>
                </span>
                <h1 class="h5 mb-0">Detail Sesi Pembinaan</h1>
            </div>
            <p class="text-muted small mb-0">
                Kelola jadwal, status sesi, materi, serta pantau progres pembinaan setiap peserta.
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.verifikator.pembinaan.index') }}"
               class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span class="d-none d-sm-inline">Kembali</span>
            </a>
        </div>
    </div>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-info-circle me-1"></i>
            Terjadi kesalahan input, silakan periksa kembali form Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @php
        // waktu selesai sesi
        $hasEnded = false;
        if ($sesi->tanggal && $sesi->jam_selesai) {
            $endDateTime = \Carbon\Carbon::parse($sesi->tanggal->format('Y-m-d') . ' ' . $sesi->jam_selesai);
            $hasEnded = now()->greaterThan($endDateTime);
        }

        // boleh edit jadwal SELAMA sesi belum lewat dan status masih dijadwalkan
        $canEditSchedule = !$hasEnded && $sesi->status === 'dijadwalkan';

        // form ubah status sesi umum muncul setelah sesi lewat
        $canUpdateStatus = $hasEnded;
    @endphp

    {{-- RINGKASAN SESI + FORM --}}
    <div class="row g-3 mb-4">
        {{-- RINGKASAN SESI --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header card-header-soft d-flex justify-content-between align-items-center">
                    <span class="small fw-semibold">Informasi Sesi</span>
                    <span class="icon-circle bg-light text-muted">
                        <i class="bi bi-info-circle"></i>
                    </span>
                </div>
                <div class="card-body small">
                    <dl class="row dl-clean mb-0">
                        <dt class="col-5">Nama Sesi</dt>
                        <dd class="col-7">
                            {{ $sesi->nama_sesi ?? '-' }}
                        </dd>

                        <dt class="col-5">Tanggal</dt>
                        <dd class="col-7">
                            {{ $sesi->tanggal?->format('d M Y') ?? '-' }}
                        </dd>

                        <dt class="col-5">Jam</dt>
                        <dd class="col-7">
                            {{ $sesi->jam_mulai }} - {{ $sesi->jam_selesai }}
                        </dd>

                        <dt class="col-5">Link Pertemuan</dt>
                        <dd class="col-7">
                            @if($sesi->meet_link)
                                <a href="{{ $sesi->meet_link }}" target="_blank">
                                    {{ $sesi->meet_link }}
                                </a>
                            @else
                                <span class="text-muted">Belum diisi.</span>
                            @endif
                        </dd>

                        <dt class="col-5">Materi</dt>
                        <dd class="col-7">
                            @if($sesi->materi_path)
                                <a href="{{ Storage::url($sesi->materi_path) }}" target="_blank">
                                    <i class="bi bi-file-earmark-text me-1"></i> Lihat Materi
                                </a>
                            @else
                                <span class="text-muted">Belum diunggah.</span>
                            @endif
                        </dd>

                        <dt class="col-5">Bukti Pembinaan</dt>
                        <dd class="col-7">
                            @if($sesi->bukti_pembinaan_path)
                                <a href="{{ Storage::url($sesi->bukti_pembinaan_path) }}" target="_blank">
                                    <i class="bi bi-file-earmark-check me-1"></i> Lihat Bukti
                                </a>
                            @else
                                <span class="text-muted">Belum diunggah.</span>
                            @endif
                        </dd>

                        <dt class="col-5">Status Sesi</dt>
                        <dd class="col-7">
                            @php
                                $badgeClass = 'bg-secondary';
                                $label = ucfirst($sesi->status);
                                if ($sesi->status === 'dijadwalkan') $badgeClass = 'bg-info text-dark';
                                if ($sesi->status === 'selesai')     $badgeClass = 'bg-success';
                                if ($sesi->status === 'batal')       $badgeClass = 'bg-danger';
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ $label }}
                            </span>
                            @if($hasEnded)
                                <span class="badge bg-light text-muted border ms-1">
                                    <i class="bi bi-clock-history me-1"></i> Waktu sesi terlewati
                                </span>
                            @endif
                        </dd>

                        @if($sesi->alasan)
                            <dt class="col-5">Alasan / Catatan</dt>
                            <dd class="col-7" style="white-space: normal;">
                                {{ $sesi->alasan }}
                            </dd>
                        @endif

                        <dt class="col-5">Dibuat oleh</dt>
                        <dd class="col-7">
                            {{ $sesi->creator->name ?? '-' }}
                        </dd>
                    </dl>

                    @if(!$hasEnded)
                        <div class="alert alert-info mt-3 py-2 small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Sesi belum selesai. Anda masih dapat mengubah
                            <strong>jadwal</strong> dan <strong>link pertemuan</strong>.
                            Form <strong>Ubah Status Sesi</strong> akan muncul setelah waktu selesai terlewati.
                        </div>
                    @else
                        <div class="alert alert-warning mt-3 py-2 small mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Waktu sesi telah berakhir. Anda dapat mengubah status sesi, serta mengatur status
                            pembinaan dan perizinan tiap peserta di tabel di bawah.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- FORM EDIT JADWAL / STATUS SESI (UMUM) --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header card-header-soft d-flex justify-content-between align-items-center">
                    <span class="small fw-semibold">
                        @if($canEditSchedule)
                            Edit Jadwal & Data Sesi
                        @elseif($canUpdateStatus)
                            Ubah Status Sesi (Umum)
                        @else
                            Status Sesi
                        @endif
                    </span>
                    <span class="icon-circle bg-light text-muted">
                        <i class="bi bi-pencil-square"></i>
                    </span>
                </div>
                <div class="card-body small">
                    @if($canEditSchedule)
                        <form action="{{ route('admin.verifikator.pembinaan.sesi.update', $sesi) }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- status tetap dijadwalkan, hanya edit jadwal --}}
                            <input type="hidden" name="status" value="{{ $sesi->status }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small">Nama Sesi (opsional)</label>
                                    <input type="text"
                                           name="nama_sesi"
                                           class="form-control form-control-sm"
                                           value="{{ old('nama_sesi', $sesi->nama_sesi) }}"
                                           placeholder="Contoh: Batch 1 - Januari">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Tanggal</label>
                                    <input type="date"
                                           name="tanggal"
                                           class="form-control form-control-sm"
                                           value="{{ old('tanggal', $sesi->tanggal?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Jam Mulai</label>
                                    <input type="time"
                                           name="jam_mulai"
                                           class="form-control form-control-sm"
                                           value="{{ old('jam_mulai', $sesi->jam_mulai) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Jam Selesai</label>
                                    <input type="time"
                                           name="jam_selesai"
                                           class="form-control form-control-sm"
                                           value="{{ old('jam_selesai', $sesi->jam_selesai) }}">
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label small">
                                        Link Google Meet / Platform Lain
                                    </label>
                                    <input type="text"
                                           name="meet_link"
                                           class="form-control form-control-sm"
                                           value="{{ old('meet_link', $sesi->meet_link) }}"
                                           placeholder="https://meet.google.com/....">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small">Materi Pembinaan (opsional)</label>
                                    <input type="file"
                                           name="materi"
                                           class="form-control form-control-sm">
                                    <small class="text-muted">
                                        Dapat mengunggah PPT, PDF, atau file lain (maksimal 20MB).
                                        Mengganti materi lama jika diunggah.
                                    </small>
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                                    <i class="bi bi-save"></i>
                                    <span>Simpan Perubahan Jadwal</span>
                                </button>
                            </div>
                        </form>
                    @elseif($canUpdateStatus)
                        <form action="{{ route('admin.verifikator.pembinaan.sesi.update', $sesi) }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small">Status Sesi</label>
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="dijadwalkan" {{ $sesi->status === 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                                        <option value="selesai"     {{ $sesi->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="batal"       {{ $sesi->status === 'batal' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small">Alasan / Catatan</label>
                                    <textarea name="alasan"
                                              class="form-control form-control-sm"
                                              rows="3"
                                              placeholder="Alasan pembatalan atau catatan pelaksanaan (opsional)">{{ old('alasan', $sesi->alasan) }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small">Upload Bukti Pembinaan (opsional)</label>
                                    <input type="file"
                                           name="bukti_pembinaan"
                                           class="form-control form-control-sm">
                                    <small class="text-muted">
                                        File foto / PDF / dokumen lain, maksimal 20MB.
                                        Mengganti file lama jika sudah ada.
                                    </small>
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                                    <i class="bi bi-save"></i>
                                    <span>Simpan Perubahan Status</span>
                                </button>
                            </div>
                        </form>
                    @else
                        <p class="text-muted mb-0">
                            Sesi telah diset ke status
                            <strong>{{ ucfirst($sesi->status) }}</strong>.
                            Perubahan jadwal sudah tidak diperbolehkan. Anda tetap dapat mengelola status pembinaan dan
                            perizinan per peserta di tabel peserta.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- PESERTA --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header card-header-soft fw-semibold small d-flex justify-content-between align-items-center">
            <span>Peserta Sesi</span>
            <span class="text-muted">
                {{ $sesi->peserta->count() }} peserta
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm mb-0 align-middle">
                    <thead class="table-light text-center small">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Penangkar</th>
                            <th>Penanggung Jawab</th>
                            <th>Jenis Benih</th>
                            <th>Status Pembinaan</th>
                            <th>Status Perizinan</th>
                            <th>Data OSS</th>
                            <th style="width: 220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($sesi->peserta as $i => $row)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>

                                {{-- PENANGKAR --}}
                                <td style="white-space: normal;">
                                    <div class="fw-semibold">
                                        {{ $row->nama_penangkar }}
                                    </div>
                                    <div class="text-muted">
                                        NIK: {{ $row->nik ?? '-' }}
                                    </div>
                                    <div class="text-muted">
                                        User: {{ $row->user->name ?? '-' }}
                                    </div>
                                </td>

                                {{-- PENANGGUNG JAWAB --}}
                                <td style="white-space: normal;">
                                    <div>{{ $row->nama_penanggung_jawab }}</div>
                                    <div class="text-muted">
                                        {{ $row->no_hp ?? '-' }}
                                    </div>
                                </td>

                                {{-- JENIS BENIH --}}
                                <td style="white-space: normal;">
                                    {{ $row->jenis_benih_diusahakan ?? '-' }}
                                </td>

                                {{-- STATUS PEMBINAAN --}}
                                <td class="text-center" style="white-space: normal;">
                                    @php
                                        $badgeClass = 'bg-secondary';
                                        $label = ucfirst(str_replace('_', ' ', $row->status));
                                        if ($row->status === 'menunggu_jadwal') $badgeClass = 'bg-warning text-dark';
                                        if ($row->status === 'dijadwalkan')    $badgeClass = 'bg-info text-dark';
                                        if ($row->status === 'selesai')        $badgeClass = 'bg-success';
                                        if ($row->status === 'batal')          $badgeClass = 'bg-danger';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $label }}
                                    </span>
                                    @if($row->alasan_status)
                                        <div class="text-muted small mt-1" style="white-space: normal;">
                                            {{ $row->alasan_status }}
                                        </div>
                                    @endif
                                </td>

                                {{-- STATUS PERIZINAN --}}
                                <td class="text-center" style="white-space: normal;">
                                    @if ($row->status === 'batal')
                                        <span class="badge bg-secondary">-</span>
                                    @else
                                        @php
                                            $perBadge = 'bg-secondary';
                                            $perLabel = ucfirst($row->status_perizinan ?? 'menunggu');
                                            if ($row->status_perizinan === 'menunggu')   $perBadge = 'bg-warning text-dark';
                                            if ($row->status_perizinan === 'berhasil')   $perBadge = 'bg-success';
                                            if ($row->status_perizinan === 'dibatalkan') $perBadge = 'bg-danger';
                                        @endphp
                                        <span class="badge {{ $perBadge }}">
                                            {{ $perLabel }}
                                        </span>
                                    @endif

                                    @if($row->alasan_perizinan && $row->status !== 'batal')
                                        <div class="text-muted small mt-1" style="white-space: normal;">
                                            {{ $row->alasan_perizinan }}
                                        </div>
                                    @endif
                                </td>

                                {{-- DATA OSS --}}
                                <td style="white-space: normal;">
                                    @if ($row->status === 'batal')
                                        <span class="text-muted small">-</span>
                                    @elseif ($row->nib || $row->no_sertifikat_standar)
                                        <div><strong>NIB:</strong> {{ $row->nib ?? '-' }}</div>
                                        <div><strong>Sertifikat:</strong> {{ $row->no_sertifikat_standar ?? '-' }}</div>
                                    @else
                                        <span class="text-muted small">Belum diisi pemohon.</span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="text-center" style="white-space: normal;">
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        {{-- Ubah Status Pembinaan --}}
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary btn-icon btn-ubah-status"
                                                data-bs-toggle="modal"
                                                data-bs-target="#statusPembinaanModal"
                                                data-id="{{ $row->id }}"
                                                data-nama="{{ $row->nama_penangkar }}"
                                                data-status="{{ $row->status }}"
                                                data-alasan_status="{{ $row->alasan_status }}"
                                                data-bs-toggle-tooltip="tooltip"
                                                title="Ubah Status Pembinaan">
                                            <i class="bi bi-sliders"></i>
                                        </button>

                                        {{-- Kelola Perizinan --}}
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-icon btn-kelola-perizinan"
                                                data-bs-toggle="modal"
                                                data-bs-target="#perizinanModal"
                                                data-id="{{ $row->id }}"
                                                data-nama="{{ $row->nama_penangkar }}"
                                                data-status-pembinaan="{{ $row->status }}"
                                                data-status-perizinan="{{ $row->status_perizinan ?? 'menunggu' }}"
                                                data-alasan-perizinan="{{ $row->alasan_perizinan }}"
                                                data-nib="{{ $row->nib }}"
                                                data-sertifikat="{{ $row->no_sertifikat_standar }}"
                                                @if($row->status === 'batal') disabled @endif
                                                data-bs-toggle-tooltip="tooltip"
                                                title="Kelola Perizinan">
                                            <i class="bi bi-shield-check"></i>
                                        </button>
                                    </div>

                                    @if($row->status !== 'selesai' && $row->status !== 'batal')
                                        <div class="small text-muted mt-1">
                                            Perizinan dapat diubah setelah pembinaan <strong>selesai</strong>.
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox me-1"></i>
                                    Belum ada peserta terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ========== MODAL 1: UBAH STATUS PEMBINAAN (PER PESERTA) ========== --}}
<div class="modal fade" id="statusPembinaanModal" tabindex="-1" aria-labelledby="statusPembinaanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="statusPembinaanForm" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title" id="statusPembinaanModalLabel">Ubah Status Pembinaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body small">
                    <p class="text-muted mb-2">
                        Ubah status pembinaan untuk peserta:
                        <strong id="statusNamaPenangkar"></strong>
                    </p>

                    <div class="mb-3">
                        <label class="form-label small">Status Pembinaan</label>
                        <select name="status" class="form-select form-select-sm" required>
                            <option value="menunggu_jadwal">Menunggu Jadwal</option>
                            <option value="dijadwalkan">Dijadwalkan</option>
                            <option value="selesai">Selesai</option>
                            <option value="batal">Dibatalkan</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small">Alasan / Catatan (opsional)</label>
                        <textarea name="alasan_status"
                                  class="form-control form-control-sm"
                                  rows="3"
                                  placeholder="Alasan batal atau catatan pelaksanaan (opsional)"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========== MODAL 2: KELOLA PERIZINAN (PER PESERTA) ========== --}}
<div class="modal fade" id="perizinanModal" tabindex="-1" aria-labelledby="perizinanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="perizinanForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="perizinanModalLabel">Kelola Perizinan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body small">
                    <p class="text-muted mb-2">
                        Atur status perizinan untuk peserta:
                        <strong id="perizinanNamaPenangkar"></strong>
                    </p>

                    <div class="mb-2" id="perizinanInfoPembinaan"></div>

                    <div class="mb-3">
                        <label class="form-label small mb-0">Status Perizinan</label>
                        <select name="status_perizinan"
                                class="form-select form-select-sm"
                                id="perizinanStatusSelect">
                            <option value="menunggu">Menunggu</option>
                            <option value="berhasil">Berhasil</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small mb-0">Alasan (opsional)</label>
                        <input type="text"
                               name="alasan_perizinan"
                               id="perizinanAlasanInput"
                               class="form-control form-control-sm"
                               placeholder="Isi saat dibatalkan / catatan lain">
                    </div>

                    <div class="border rounded-3 p-2 bg-light">
                        <div class="fw-semibold mb-1">Data OSS (hanya baca)</div>
                        <div id="perizinanOssInfo" style="white-space: normal;">
                            {{-- diisi via JS --}}
                        </div>
                        <div class="text-muted mt-1">
                            Data OSS diinput oleh pemohon. Anda hanya mengatur <strong>status perizinan</strong> di sini.
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-sm btn-primary"
                            id="perizinanSubmitBtn">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tooltip untuk icon-only button
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle-tooltip="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // ========= URL TEMPLATE UNTUK ACTION FORM =========
    const statusUrlTpl    = "{{ route('admin.verifikator.pembinaan.peserta.status', ['pembinaan' => '__ID__']) }}";
    const perizinanUrlTpl = "{{ route('admin.verifikator.pembinaan.perizinan.update', ['pembinaan' => '__ID__']) }}";

    // ========= MODAL 1: UBAH STATUS PEMBINAAN =========
    const statusModal = document.getElementById('statusPembinaanModal');
    const statusForm  = document.getElementById('statusPembinaanForm');
    const statusNama  = document.getElementById('statusNamaPenangkar');

    if (statusModal) {
        statusModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id     = button.getAttribute('data-id');
            const nama   = button.getAttribute('data-nama') || '-';
            const status = button.getAttribute('data-status') || 'menunggu_jadwal';
            const alasan = button.getAttribute('data-alasan_status') || '';

            const actionUrl = statusUrlTpl.replace('__ID__', id);
            statusForm.setAttribute('action', actionUrl);

            statusNama.textContent = nama;
            const selectStatus   = statusForm.querySelector('select[name="status"]');
            const textareaAlasan = statusForm.querySelector('textarea[name="alasan_status"]');

            if (selectStatus)   selectStatus.value = status;
            if (textareaAlasan) textareaAlasan.value = alasan;
        });
    }

    // ========= MODAL 2: KELOLA PERIZINAN =========
    const perModal         = document.getElementById('perizinanModal');
    const perForm          = document.getElementById('perizinanForm');
    const perNama          = document.getElementById('perizinanNamaPenangkar');
    const perInfoPembinaan = document.getElementById('perizinanInfoPembinaan');
    const perStatusSelect  = document.getElementById('perizinanStatusSelect');
    const perAlasanInput   = document.getElementById('perizinanAlasanInput');
    const perOssInfo       = document.getElementById('perizinanOssInfo');
    const perSubmitBtn     = document.getElementById('perizinanSubmitBtn');

    if (perModal) {
        perModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id              = button.getAttribute('data-id');
            const nama            = button.getAttribute('data-nama') || '-';
            const statusPembinaan = button.getAttribute('data-status-pembinaan') || '-';
            const statusPerizinan = button.getAttribute('data-status-perizinan') || 'menunggu';
            const alasanPerizinan = button.getAttribute('data-alasan-perizinan') || '';
            const nib             = button.getAttribute('data-nib') || '';
            const sertifikat      = button.getAttribute('data-sertifikat') || '';

            const actionUrl = perizinanUrlTpl.replace('__ID__', id);
            perForm.setAttribute('action', actionUrl);

            perNama.textContent = nama;

            if (perInfoPembinaan) {
                perInfoPembinaan.innerHTML =
                    'Status pembinaan saat ini: <strong>' +
                    (statusPembinaan.replace('_', ' ')) +
                    '</strong>';
            }

            if (perStatusSelect) perStatusSelect.value = statusPerizinan;
            if (perAlasanInput)  perAlasanInput.value  = alasanPerizinan;

            if (perOssInfo) {
                if (!nib && !sertifikat) {
                    perOssInfo.innerHTML = '<span class="text-muted">Belum ada data OSS (NIB / Sertifikat).</span>';
                } else {
                    let html = '';
                    html += '<div><strong>NIB:</strong> ' + (nib || '-') + '</div>';
                    html += '<div><strong>No Sertifikat Standar:</strong> ' + (sertifikat || '-') + '</div>';
                    perOssInfo.innerHTML = html;
                }
            }

            // logika enable/disable sesuai status pembinaan
            if (statusPembinaan !== 'selesai') {
                if (perStatusSelect) perStatusSelect.setAttribute('disabled', 'disabled');
                if (perAlasanInput)  perAlasanInput.setAttribute('disabled', 'disabled');
                if (perSubmitBtn)    perSubmitBtn.setAttribute('disabled', 'disabled');

                if (perInfoPembinaan) {
                    perInfoPembinaan.innerHTML +=
                        '<br><span class="text-danger">Perizinan hanya dapat diubah jika pembinaan <strong>selesai</strong>.</span>';
                }
            } else {
                if (perStatusSelect) perStatusSelect.removeAttribute('disabled');
                if (perAlasanInput)  perAlasanInput.removeAttribute('disabled');
                if (perSubmitBtn)    perSubmitBtn.removeAttribute('disabled');
            }
        });

        // reset saat modal ditutup
        perModal.addEventListener('hidden.bs.modal', function () {
            if (perStatusSelect) perStatusSelect.removeAttribute('disabled');
            if (perAlasanInput)  perAlasanInput.removeAttribute('disabled');
            if (perSubmitBtn)    perSubmitBtn.removeAttribute('disabled');
        });
    }
});
</script>
@endpush
