{{-- resources/views/pemohon/pembinaan_kbs/show.blade.php --}}
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
                    <i class="bi bi-tree"></i>
                </span>
                <h1 class="h5 mb-0">Detail Pembinaan Kebun Benih Sumber</h1>
            </div>
            <p class="text-muted small mb-0">
                Lihat detail pengajuan, jadwal pembinaan, dan status pelaksanaan pembinaan kebun benih sumber.
            </p>
        </div>
        <div class="col-auto d-flex gap-2 page-header-actions">
            <a href="{{ route('pemohon.pembinaan-kbs.index') }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                <span class="d-none d-sm-inline">Kembali</span>
            </a>

            {{-- Edit hanya bila masih menunggu jadwal --}}
            @if($pembinaanKbs->status === 'menunggu_jadwal')
                <button type="button"
                        class="btn btn-sm btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#editPembinaanKbsModal"
                        id="btnOpenEditModalKbs"
                        data-id="{{ $pembinaanKbs->id }}"
                        data-nama="{{ $pembinaanKbs->nama }}"
                        data-nik="{{ $pembinaanKbs->nik }}"
                        data-alamat="{{ $pembinaanKbs->alamat }}"
                        data-no_hp="{{ $pembinaanKbs->no_hp }}"
                        data-jenis_tanaman_id="{{ $pembinaanKbs->jenis_tanaman_id }}"
                        data-lokasi_kebun="{{ $pembinaanKbs->lokasi_kebun }}"
                        data-latitude_kebun="{{ $pembinaanKbs->latitude_kebun }}"
                        data-longitude_kebun="{{ $pembinaanKbs->longitude_kebun }}"
                        data-jumlah_pohon_induk="{{ $pembinaanKbs->jumlah_pohon_induk }}">
                    <i class="bi bi-pencil-square"></i>
                    <span class="d-none d-sm-inline">Edit Data</span>
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
                        $label = ucfirst(str_replace('_', ' ', $pembinaanKbs->status));
                        if ($pembinaanKbs->status === 'menunggu_jadwal') $badgeClass = 'bg-warning text-dark';
                        elseif ($pembinaanKbs->status === 'dijadwalkan') $badgeClass = 'bg-info text-dark';
                        elseif ($pembinaanKbs->status === 'selesai')     $badgeClass = 'bg-success';
                        elseif ($pembinaanKbs->status === 'batal')       $badgeClass = 'bg-danger';
                    @endphp

                    <span class="badge {{ $badgeClass }} mb-2 px-3 py-1">
                        {{ $label }}
                    </span>

                    @if ($pembinaanKbs->alasan_status)
                        <div class="text-muted small" style="white-space: normal;">
                            {{ $pembinaanKbs->alasan_status }}
                        </div>
                    @else
                        <div class="text-muted small">
                            Tidak ada catatan tambahan.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Komoditas --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="small text-muted">Komoditas</div>
                        <span class="icon-circle bg-light text-muted">
                            <i class="bi bi-flower3"></i>
                        </span>
                    </div>

                    <div class="fw-semibold mb-1">
                        {{ $pembinaanKbs->jenisTanaman->nama_tanaman ?? '-' }}
                    </div>
                    <div class="text-muted small">
                        Jenis tanaman yang dibina pada kebun benih sumber ini.
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Kebun --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="small text-muted">Ringkasan Kebun</div>
                        <span class="icon-circle bg-light text-muted">
                            <i class="bi bi-geo-alt"></i>
                        </span>
                    </div>

                    <div class="small mb-1">
                        <strong>Lokasi:</strong><br>
                        <span class="text-muted">{{ $pembinaanKbs->lokasi_kebun ?? '-' }}</span>
                    </div>

                    <div class="small mb-1">
                        <strong>Koordinat:</strong><br>
                        <span class="text-muted">
                            @if(!is_null($pembinaanKbs->latitude_kebun) && !is_null($pembinaanKbs->longitude_kebun))
                                {{ $pembinaanKbs->latitude_kebun }}, {{ $pembinaanKbs->longitude_kebun }}
                            @else
                                -
                            @endif
                        </span>
                    </div>

                    <div class="small">
                        <strong>Jumlah Pohon Induk:</strong><br>
                        <span class="text-muted">{{ $pembinaanKbs->jumlah_pohon_induk ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL DATA --}}
    <div class="row g-3">
        {{-- DATA PEMOHON --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header card-header-soft small fw-semibold d-flex align-items-center justify-content-between">
                    <span>Data Pemohon & Kontak</span>
                    <i class="bi bi-person-badge text-muted"></i>
                </div>
                <div class="card-body small">
                    <dl class="row mb-0 dl-clean">
                        <dt class="col-sm-4">Nama</dt>
                        <dd class="col-sm-8">{{ $pembinaanKbs->nama ?? '-' }}</dd>

                        <dt class="col-sm-4">NIK</dt>
                        <dd class="col-sm-8">{{ $pembinaanKbs->nik ?? '-' }}</dd>

                        <dt class="col-sm-4">Alamat</dt>
                        <dd class="col-sm-8">{{ $pembinaanKbs->alamat ?? '-' }}</dd>

                        <dt class="col-sm-4">No HP / WA</dt>
                        <dd class="col-sm-8">{{ $pembinaanKbs->no_hp ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- DATA KEBUN & KOMODITAS --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header card-header-soft small fw-semibold d-flex align-items-center justify-content-between">
                    <span>Data Kebun & Komoditas</span>
                    <i class="bi bi-tree text-muted"></i>
                </div>
                <div class="card-body small">
                    <dl class="row mb-0 dl-clean">
                        <dt class="col-sm-4">Komoditas</dt>
                        <dd class="col-sm-8">{{ $pembinaanKbs->jenisTanaman->nama_tanaman ?? '-' }}</dd>

                        <dt class="col-sm-4">Lokasi Kebun</dt>
                        <dd class="col-sm-8">{{ $pembinaanKbs->lokasi_kebun ?? '-' }}</dd>

                        <dt class="col-sm-4">Koordinat Kebun</dt>
                        <dd class="col-sm-8">
                            @if(!is_null($pembinaanKbs->latitude_kebun) && !is_null($pembinaanKbs->longitude_kebun))
                                {{ $pembinaanKbs->latitude_kebun }}, {{ $pembinaanKbs->longitude_kebun }}
                            @else
                                -
                            @endif
                        </dd>

                        <dt class="col-sm-4">Jumlah Pohon Induk</dt>
                        <dd class="col-sm-8">{{ $pembinaanKbs->jumlah_pohon_induk ?? '-' }}</dd>
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
                    @if($pembinaanKbs->sesi)
                        <dl class="row mb-0 dl-clean">
                            <dt class="col-sm-4">Nama Sesi</dt>
                            <dd class="col-sm-8">{{ $pembinaanKbs->sesi->nama_sesi ?? '-' }}</dd>

                            <dt class="col-sm-4">Tanggal</dt>
                            <dd class="col-sm-8">
                                {{ $pembinaanKbs->sesi->tanggal?->format('d M Y') ?? '-' }}
                            </dd>

                            <dt class="col-sm-4">Jam</dt>
                            <dd class="col-sm-8">
                                {{ $pembinaanKbs->sesi->jam_mulai }} - {{ $pembinaanKbs->sesi->jam_selesai }}
                            </dd>

                            <dt class="col-sm-4">Link Pertemuan</dt>
                            <dd class="col-sm-8">
                                @if($pembinaanKbs->sesi->meet_link)
                                    <a href="{{ $pembinaanKbs->sesi->meet_link }}" target="_blank">
                                        {{ $pembinaanKbs->sesi->meet_link }}
                                    </a>
                                @else
                                    <span class="text-muted">Belum tersedia.</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4">Materi Pembinaan</dt>
                            <dd class="col-sm-8">
                                @if($pembinaanKbs->sesi->materi_path)
                                    <a href="{{ Storage::url($pembinaanKbs->sesi->materi_path) }}" target="_blank">
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
                                    $sesiLabel = ucfirst($pembinaanKbs->sesi->status);
                                    if ($pembinaanKbs->sesi->status === 'dijadwalkan') $sesiBadge = 'bg-info text-dark';
                                    elseif ($pembinaanKbs->sesi->status === 'selesai') $sesiBadge = 'bg-success';
                                    elseif ($pembinaanKbs->sesi->status === 'batal')   $sesiBadge = 'bg-danger';
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
                            {{ $pembinaanKbs->created_at?->format('d M Y H:i') ?? '-' }}
                        </dd>

                        <dt class="col-sm-4">Diperbarui</dt>
                        <dd class="col-sm-8">
                            <i class="bi bi-arrow-repeat me-1 text-muted"></i>
                            {{ $pembinaanKbs->updated_at?->format('d M Y H:i') ?? '-' }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========== MODAL EDIT PEMBINAAN KBS ========== --}}
<div class="modal fade" id="editPembinaanKbsModal" tabindex="-1" aria-labelledby="editPembinaanKbsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form id="editPembinaanKbsForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editPembinaanKbsModalLabel">Edit Data Pembinaan Kebun Benih Sumber</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Data hanya dapat diubah selama status pembinaan masih <strong>Menunggu Jadwal</strong>.
                    </p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Nama <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="nama"
                                   class="form-control form-control-sm"
                                   required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">NIK</label>
                            <input type="text"
                                   name="nik"
                                   class="form-control form-control-sm"
                                   maxlength="16"
                                   inputmode="numeric"
                                   pattern="\d*"
                                   placeholder="16 digit (opsional)">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">No HP / WA</label>
                            <input type="text"
                                   name="no_hp"
                                   class="form-control form-control-sm"
                                   placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small">Alamat</label>
                            <input type="text"
                                   name="alamat"
                                   class="form-control form-control-sm"
                                   placeholder="Alamat lengkap">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">
                                Komoditas <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_tanaman_id"
                                    class="form-select form-select-sm"
                                    required>
                                <option value="">-- Pilih Komoditas --</option>
                                @foreach($jenisTanaman as $tanaman)
                                    <option value="{{ $tanaman->id }}">
                                        {{ $tanaman->nama_tanaman }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small">Lokasi Kebun</label>
                            <input type="text"
                                   name="lokasi_kebun"
                                   class="form-control form-control-sm">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small">Latitude Kebun</label>
                            <input type="number"
                                   step="0.0000001"
                                   name="latitude_kebun"
                                   class="form-control form-control-sm"
                                   placeholder="-6.xxxxxx">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small">Longitude Kebun</label>
                            <input type="number"
                                   step="0.0000001"
                                   name="longitude_kebun"
                                   class="form-control form-control-sm"
                                   placeholder="106.xxxxxx">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">Jumlah Pohon Induk</label>
                            <input type="number"
                                   name="jumlah_pohon_induk"
                                   class="form-control form-control-sm"
                                   min="0">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal   = document.getElementById('editPembinaanKbsModal');
    const editForm    = document.getElementById('editPembinaanKbsForm');
    const editUrlTpl  = "{{ route('pemohon.pembinaan-kbs.update', ['pembinaanKbs' => '__ID__']) }}";

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget || document.getElementById('btnOpenEditModalKbs');
            if (!button) return;

            const id   = button.getAttribute('data-id');
            const actionUrl = editUrlTpl.replace('__ID__', id);
            editForm.setAttribute('action', actionUrl);

            editForm.querySelector('[name="nama"]').value =
                button.getAttribute('data-nama') || '';
            editForm.querySelector('[name="nik"]').value =
                button.getAttribute('data-nik') || '';
            editForm.querySelector('[name="alamat"]').value =
                button.getAttribute('data-alamat') || '';
            editForm.querySelector('[name="no_hp"]').value =
                button.getAttribute('data-no_hp') || '';
            editForm.querySelector('[name="lokasi_kebun"]').value =
                button.getAttribute('data-lokasi_kebun') || '';
            editForm.querySelector('[name="latitude_kebun"]').value =
                button.getAttribute('data-latitude_kebun') || '';
            editForm.querySelector('[name="longitude_kebun"]').value =
                button.getAttribute('data-longitude_kebun') || '';
            editForm.querySelector('[name="jumlah_pohon_induk"]').value =
                button.getAttribute('data-jumlah_pohon_induk') || '';

            const jenisSelect = editForm.querySelector('[name="jenis_tanaman_id"]');
            const jenisVal    = button.getAttribute('data-jenis_tanaman_id') || '';
            if (jenisSelect) {
                jenisSelect.value = jenisVal;
            }
        });
    }
});
</script>
@endpush
