{{-- resources/views/pemohon/pembinaan/show.blade.php --}}
@extends('layouts.bootstrap')

@section('content')
<style>
    body{
        margin-top: -70px;
    }
    .page-header-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
    }

    .card-header-soft {
        background: #f8f9fa;
        border-bottom: 0;
    }

    .card-header-soft .icon-circle {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
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
</style>

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="row mb-3 align-items-center">
        <div class="col">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="icon-circle bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </span>
                <h1 class="h5 mb-0">Detail Pembinaan Calon Penangkar</h1>
            </div>
            <p class="text-muted small mb-0">
                Lihat detail pengajuan, jadwal pembinaan, dan status perizinan calon penangkar.
            </p>
        </div>
        <div class="col-auto d-flex gap-2 page-header-actions">
            <a href="{{ route('pemohon.pembinaan.index') }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                <span class="d-none d-sm-inline">Kembali</span>
            </a>

            {{-- Edit hanya bila masih menunggu jadwal --}}
            @if($pembinaan->is_menunggu_jadwal)
                <button type="button"
                        class="btn btn-sm btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#editPembinaanModal"
                        id="btnOpenEditModal"
                        data-id="{{ $pembinaan->id }}"
                        data-nama_penangkar="{{ $pembinaan->nama_penangkar }}"
                        data-nama_penanggung_jawab="{{ $pembinaan->nama_penanggung_jawab }}"
                        data-nik="{{ $pembinaan->nik }}"
                        data-npwp="{{ $pembinaan->npwp }}"
                        data-alamat_penanggung_jawab="{{ $pembinaan->alamat_penanggung_jawab }}"
                        data-lokasi_usaha="{{ $pembinaan->lokasi_usaha }}"
                        data-status_kepemilikan_lahan="{{ $pembinaan->status_kepemilikan_lahan }}"
                        data-jenis_benih_diusahakan="{{ $pembinaan->jenis_benih_diusahakan }}"
                        data-no_hp="{{ $pembinaan->no_hp }}">
                    <i class="bi bi-pencil-square"></i>
                    <span class="d-none d-sm-inline">Edit Data</span>
                </button>
            @endif

            {{-- Tombol isi OSS jika pembinaan selesai dan perizinan masih menunggu --}}
            @if($pembinaan->is_selesai && $pembinaan->is_perizinan_menunggu)
                <button type="button"
                        class="btn btn-sm btn-primary btn-isi-oss"
                        data-bs-toggle="modal"
                        data-bs-target="#ossModal"
                        data-id="{{ $pembinaan->id }}"
                        data-nib="{{ $pembinaan->nib }}"
                        data-sertifikat="{{ $pembinaan->no_sertifikat_standar }}">
                    <i class="bi bi-file-earmark-plus"></i>
                    <span class="d-none d-sm-inline">Isi Data OSS</span>
                </button>
            @endif
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

    {{-- RINGKASAN STATUS --}}
    <div class="row g-3 mb-3">
        {{-- Status Pembinaan --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="small text-muted">Status Pembinaan</div>
                        <span class="icon-circle bg-light text-muted">
                            <i class="bi bi-clipboard-check"></i>
                        </span>
                    </div>

                    @php
                        $badgeClass = 'bg-secondary';
                        $label = ucfirst(str_replace('_', ' ', $pembinaan->status));
                        if ($pembinaan->status === 'menunggu_jadwal') $badgeClass = 'bg-warning text-dark';
                        elseif ($pembinaan->status === 'dijadwalkan') $badgeClass = 'bg-info text-dark';
                        elseif ($pembinaan->status === 'selesai')     $badgeClass = 'bg-success';
                        elseif ($pembinaan->status === 'batal')       $badgeClass = 'bg-danger';
                    @endphp

                    <span class="badge {{ $badgeClass }} mb-2 px-3 py-1">
                        {{ $label }}
                    </span>

                    @if ($pembinaan->alasan_status)
                        <div class="text-muted small" style="white-space: normal;">
                            {{ $pembinaan->alasan_status }}
                        </div>
                    @else
                        <div class="text-muted small">
                            Tidak ada catatan tambahan.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Status Perizinan --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="small text-muted">Status Perizinan</div>
                        <span class="icon-circle bg-light text-muted">
                            <i class="bi bi-shield-check"></i>
                        </span>
                    </div>

                    @php
                        $perizinanBadge = 'bg-secondary';
                        $perizinanLabel = ucfirst($pembinaan->status_perizinan ?? 'menunggu');
                        if ($pembinaan->status_perizinan === 'menunggu')   $perizinanBadge = 'bg-warning text-dark';
                        elseif ($pembinaan->status_perizinan === 'berhasil')   $perizinanBadge = 'bg-success';
                        elseif ($pembinaan->status_perizinan === 'dibatalkan') $perizinanBadge = 'bg-danger';
                    @endphp

                    <span class="badge {{ $perizinanBadge }} mb-2 px-3 py-1">
                        Perizinan {{ $perizinanLabel }}
                    </span>

                    @if ($pembinaan->alasan_perizinan)
                        <div class="text-muted small" style="white-space: normal;">
                            {{ $pembinaan->alasan_perizinan }}
                        </div>
                    @else
                        <div class="text-muted small">
                            @if($pembinaan->is_perizinan_menunggu)
                                Menunggu pemohon mengisi data OSS atau keputusan admin.
                            @elseif($pembinaan->is_perizinan_berhasil)
                                Perizinan dinyatakan berhasil setelah data OSS lengkap.
                            @else
                                Tidak ada catatan tambahan.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Data OSS --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="small text-muted">Data OSS</div>
                        <span class="icon-circle bg-light text-muted">
                            <i class="bi bi-file-earmark-text"></i>
                        </span>
                    </div>

                    @if ($pembinaan->nib && $pembinaan->no_sertifikat_standar)
                        <div class="mb-1">
                            <strong class="small d-block text-muted">NIB</strong>
                            <span class="fw-semibold">{{ $pembinaan->nib }}</span>
                        </div>
                        <div class="mb-1">
                            <strong class="small d-block text-muted">Sertifikat Standar</strong>
                            <span class="fw-semibold">{{ $pembinaan->no_sertifikat_standar }}</span>
                        </div>
                        <span class="badge bg-success-subtle text-success border mt-2">
                            <i class="bi bi-check-circle me-1"></i> Data OSS lengkap
                        </span>
                    @else
                        <div class="text-muted small">
                            Data OSS belum lengkap.
                            @if($pembinaan->is_selesai && $pembinaan->is_perizinan_menunggu)
                                <br>Silakan isi melalui tombol <strong>Isi Data OSS</strong>.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL DATA --}}
    <div class="row g-3">
        {{-- DATA PENANGKAR --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header card-header-soft small fw-semibold d-flex align-items-center justify-content-between">
                    <span>Data Penangkar & Penanggung Jawab</span>
                    <i class="bi bi-person-badge text-muted"></i>
                </div>
                <div class="card-body small">
                    <dl class="row mb-0 dl-clean">
                        <dt class="col-sm-4">Nama Penangkar</dt>
                        <dd class="col-sm-8">{{ $pembinaan->nama_penangkar }}</dd>

                        <dt class="col-sm-4">Nama Penanggung Jawab</dt>
                        <dd class="col-sm-8">{{ $pembinaan->nama_penanggung_jawab }}</dd>

                        <dt class="col-sm-4">NIK</dt>
                        <dd class="col-sm-8">{{ $pembinaan->nik ?? '-' }}</dd>

                        <dt class="col-sm-4">NPWP</dt>
                        <dd class="col-sm-8">{{ $pembinaan->npwp ?? '-' }}</dd>

                        <dt class="col-sm-4">Alamat</dt>
                        <dd class="col-sm-8">{{ $pembinaan->alamat_penanggung_jawab ?? '-' }}</dd>

                        <dt class="col-sm-4">No HP / WA</dt>
                        <dd class="col-sm-8">{{ $pembinaan->no_hp ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- DATA USAHA & JENIS BENIH --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header card-header-soft small fw-semibold d-flex align-items-center justify-content-between">
                    <span>Usaha & Jenis Benih</span>
                    <i class="bi bi-tree text-muted"></i>
                </div>
                <div class="card-body small">
                    <dl class="row mb-0 dl-clean">
                        <dt class="col-sm-4">Lokasi Usaha / Kebun</dt>
                        <dd class="col-sm-8">{{ $pembinaan->lokasi_usaha ?? '-' }}</dd>

                        <dt class="col-sm-4">Status Kepemilikan</dt>
                        <dd class="col-sm-8">{{ $pembinaan->status_kepemilikan_lahan ?? '-' }}</dd>

                        <dt class="col-sm-4">Jenis Benih</dt>
                        <dd class="col-sm-8">{{ $pembinaan->jenis_benih_diusahakan ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- JADWAL PEMBINAAN & INFO SISTEM --}}
    <div class="row g-3 mt-3">
        {{-- JADWAL PEMBINAAN --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header card-header-soft small fw-semibold d-flex align-items-center justify-content-between">
                    <span>Jadwal Pembinaan</span>
                    <i class="bi bi-calendar-event text-muted"></i>
                </div>
                <div class="card-body small">
                    @if($pembinaan->sesi)
                        <dl class="row mb-0 dl-clean">
                            <dt class="col-sm-4">Nama Sesi</dt>
                            <dd class="col-sm-8">{{ $pembinaan->sesi->nama_sesi ?? '-' }}</dd>

                            <dt class="col-sm-4">Tanggal</dt>
                            <dd class="col-sm-8">
                                {{ $pembinaan->sesi->tanggal?->format('d M Y') ?? '-' }}
                            </dd>

                            <dt class="col-sm-4">Jam</dt>
                            <dd class="col-sm-8">
                                {{ $pembinaan->sesi->jam_mulai }} - {{ $pembinaan->sesi->jam_selesai }}
                            </dd>

                            <dt class="col-sm-4">Link Pertemuan</dt>
                            <dd class="col-sm-8">
                                @if($pembinaan->sesi->meet_link)
                                    <a href="{{ $pembinaan->sesi->meet_link }}" target="_blank">
                                        {{ $pembinaan->sesi->meet_link }}
                                    </a>
                                @else
                                    <span class="text-muted">Belum tersedia.</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4">Materi Pembinaan</dt>
                            <dd class="col-sm-8">
                                @if($pembinaan->sesi->materi_path)
                                    <a href="{{ Storage::disk('public')->url($pembinaan->sesi->materi_path) }}" target="_blank">
                                        Download Materi
                                    </a>
                                @else
                                    <span class="text-muted">Belum diunggah.</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4">Status Sesi</dt>
                            <dd class="col-sm-8">
                                @php
                                    $sesiBadge = 'bg-secondary';
                                    $sesiLabel = ucfirst($pembinaan->sesi->status);
                                    if ($pembinaan->sesi->status === 'dijadwalkan') $sesiBadge = 'bg-info text-dark';
                                    elseif ($pembinaan->sesi->status === 'selesai') $sesiBadge = 'bg-success';
                                    elseif ($pembinaan->sesi->status === 'batal')   $sesiBadge = 'bg-danger';
                                @endphp
                                <span class="badge {{ $sesiBadge }}">{{ $sesiLabel }}</span>
                            </dd>
                        </dl>
                    @else
                        <div class="text-muted">
                            Pembinaan Anda belum dijadwalkan. Admin akan menghubungi dan mengatur jadwal sesi pembinaan.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- INFO SISTEM --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header card-header-soft small fw-semibold d-flex align-items-center justify-content-between">
                    <span>Informasi Sistem</span>
                    <i class="bi bi-info-circle text-muted"></i>
                </div>
                <div class="card-body small">
                    <dl class="row mb-0 dl-clean">
                        <dt class="col-sm-4">Dibuat</dt>
                        <dd class="col-sm-8">
                            <i class="bi bi-clock-history me-1 text-muted"></i>
                            {{ $pembinaan->created_at?->format('d M Y H:i') ?? '-' }}
                        </dd>

                        <dt class="col-sm-4">Diperbarui</dt>
                        <dd class="col-sm-8">
                            <i class="bi bi-arrow-repeat me-1 text-muted"></i>
                            {{ $pembinaan->updated_at?->format('d M Y H:i') ?? '-' }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========== MODAL EDIT PEMBINAAN ========== --}}
<div class="modal fade" id="editPembinaanModal" tabindex="-1" aria-labelledby="editPembinaanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form id="editPembinaanForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editPembinaanModalLabel">Edit Data Pembinaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Data hanya dapat diubah selama status pembinaan masih <strong>Menunggu Jadwal</strong>.
                    </p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Nama Penangkar <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="nama_penangkar"
                                   class="form-control form-control-sm"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">Nama Penanggung Jawab <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="nama_penanggung_jawab"
                                   class="form-control form-control-sm"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">NIK</label>
                            <input type="text"
                                   name="nik"
                                   class="form-control form-control-sm"
                                   maxlength="16"
                                   inputmode="numeric"
                                   pattern="\d*"
                                   placeholder="16 digit (opsional)">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">No NPWP</label>
                            <input type="text"
                                   name="npwp"
                                   class="form-control form-control-sm"
                                   placeholder="Opsional">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small">Alamat Penanggung Jawab</label>
                            <input type="text"
                                   name="alamat_penanggung_jawab"
                                   class="form-control form-control-sm"
                                   placeholder="Alamat lengkap">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">Lokasi Usaha / Kebun</label>
                            <input type="text"
                                   name="lokasi_usaha"
                                   class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">Status Kepemilikan Lahan</label>
                            <input type="text"
                                   name="status_kepemilikan_lahan"
                                   class="form-control form-control-sm"
                                   placeholder="Milik sendiri / sewa / dll">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">
                                Jenis Benih yang Diusahakan <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_benih_diusahakan"
                                    class="form-select form-select-sm"
                                    required>
                                <option value="">-- Pilih Jenis Benih --</option>
                                <option value="Biji">Biji</option>
                                <option value="Siap Tanam">Siap Tanam</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">No HP / WA</label>
                            <input type="text"
                                   name="no_hp"
                                   class="form-control form-control-sm"
                                   placeholder="08xxxxxxxxxx">
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
                            class="btn btn-sm btn-primary">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========== MODAL ISI DATA OSS ========== --}}
<div class="modal fade" id="ossModal" tabindex="-1" aria-labelledby="ossModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="ossForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ossModalLabel">Isi Data OSS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        Silakan isi NIB dan Nomor Sertifikat Standar setelah pembinaan dinyatakan
                        <strong>selesai</strong>.
                    </p>

                    <div class="mb-3">
                        <label class="form-label small">Nomor Induk Berusaha (NIB)</label>
                        <input type="text"
                               name="nib"
                               class="form-control form-control-sm"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Nomor Sertifikat Standar</label>
                        <input type="text"
                               name="no_sertifikat_standar"
                               class="form-control form-control-sm"
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit"
                            class="btn btn-sm btn-primary">
                        Simpan Data OSS
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
    // ========= EDIT PEMBINAAN MODAL =========
    const editModal   = document.getElementById('editPembinaanModal');
    const editForm    = document.getElementById('editPembinaanForm');
    const editUrlTpl  = "{{ route('pemohon.pembinaan.update', ['pembinaan' => '__ID__']) }}";

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget || document.getElementById('btnOpenEditModal');
            if (!button) return;

            const id   = button.getAttribute('data-id');

            const actionUrl = editUrlTpl.replace('__ID__', id);
            editForm.setAttribute('action', actionUrl);

            editForm.querySelector('[name="nama_penangkar"]').value           = button.getAttribute('data-nama_penangkar') || '';
            editForm.querySelector('[name="nama_penanggung_jawab"]').value    = button.getAttribute('data-nama_penanggung_jawab') || '';
            editForm.querySelector('[name="nik"]').value                      = button.getAttribute('data-nik') || '';
            editForm.querySelector('[name="npwp"]').value                     = button.getAttribute('data-npwp') || '';
            editForm.querySelector('[name="alamat_penanggung_jawab"]').value  = button.getAttribute('data-alamat_penanggung_jawab') || '';
            editForm.querySelector('[name="lokasi_usaha"]').value             = button.getAttribute('data-lokasi_usaha') || '';
            editForm.querySelector('[name="status_kepemilikan_lahan"]').value = button.getAttribute('data-status_kepemilikan_lahan') || '';
            editForm.querySelector('[name="no_hp"]').value                    = button.getAttribute('data-no_hp') || '';

            const jenisSelect = editForm.querySelector('[name="jenis_benih_diusahakan"]');
            const jenisVal    = button.getAttribute('data-jenis_benih_diusahakan') || '';
            if (jenisSelect) {
                jenisSelect.value = jenisVal;
            }
        });
    }

    // ========= OSS MODAL =========
    const ossModal   = document.getElementById('ossModal');
    const ossForm    = document.getElementById('ossForm');
    const ossUrlTpl  = "{{ route('pemohon.pembinaan.oss.store', ['pembinaan' => '__ID__']) }}";

    if (ossModal) {
        ossModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id         = button.getAttribute('data-id');
            const nib        = button.getAttribute('data-nib') || '';
            const sertifikat = button.getAttribute('data-sertifikat') || '';

            const actionUrl = ossUrlTpl.replace('__ID__', id);
            ossForm.setAttribute('action', actionUrl);

            ossForm.querySelector('[name="nib"]').value                   = nib;
            ossForm.querySelector('[name="no_sertifikat_standar"]').value = sertifikat;
        });
    }
});
</script>
@endpush
