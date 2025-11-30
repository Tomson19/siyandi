@extends('layouts.bootstrap')

@section('content')
    <style>
        /* Kalau memang butuh offset navbar, sebaiknya pakai wrapper, bukan body */
        .page-wrapper {
            margin-top: -70px;
        }

        @media (max-width: 768px) {
            .page-wrapper {
                margin-top: -40px;
            }
        }
    </style>

    <div class="container-fluid page-wrapper py-4">
        {{-- HEADER --}}
        <div class="row align-items-center mb-4">
            <div class="col">
                <h1 class="h4 mb-1">Pembinaan Calon Penangkar</h1>
                <p class="text-muted mb-0 small">
                    Ajukan pembinaan dan pantau jadwal, hasil pembinaan, serta status perizinan calon penangkar.
                </p>
            </div>
            <div class="col-auto">
                <button type="button"
                        class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                        data-bs-toggle="modal"
                        data-bs-target="#createPembinaanModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>Ajukan Pembinaan</span>
                </button>
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

        {{-- FILTER STATUS --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <form method="GET"
                      action="{{ route('pemohon.pembinaan.index') }}"
                      class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="form-label small mb-0 text-muted">
                            <i class="bi bi-funnel me-1"></i>Status Pembinaan
                        </label>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua Status</option>
                            <option value="menunggu_jadwal" {{ $status === 'menunggu_jadwal' ? 'selected' : '' }}>
                                Menunggu Jadwal
                            </option>
                            <option value="dijadwalkan" {{ $status === 'dijadwalkan' ? 'selected' : '' }}>
                                Sudah Dijadwalkan
                            </option>
                            <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>
                            <option value="batal" {{ $status === 'batal' ? 'selected' : '' }}>
                                Dibatalkan
                            </option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-secondary" type="submit">
                            Terapkan
                        </button>
                        @if (request()->has('status') && request('status') !== null && request('status') !== '')
                            <a href="{{ route('pemohon.pembinaan.index') }}"
                               class="btn btn-sm btn-outline-light border ms-1">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL PENGAJUAN --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">Daftar Pengajuan Pembinaan</h2>
                    <span class="small text-muted">
                        Total: {{ $pembinaanList->total() }} pengajuan
                    </span>
                </div>
            </div>

            <div class="card-body p-0 mt-2">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-sm mb-0 align-middle">
                        <thead class="table-light text-center small">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Penangkar</th>
                                <th>Jenis Benih</th>
                                <th>Jadwal Pembinaan</th>
                                <th>Status Pembinaan</th>
                                <th>Status Perizinan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse ($pembinaanList as $index => $row)
                                <tr>
                                    <td class="text-center">
                                        {{ $pembinaanList->firstItem() + $index }}
                                    </td>

                                    {{-- PENANGKAR --}}
                                    <td  style="white-space: normal;">
                                        <div class="fw-semibold">
                                            {{ $row->nama_penangkar }}
                                        </div>
                                        <div class="text-muted">
                                            <span class="me-2">
                                                <i class="bi bi-person-badge me-1"></i>
                                                NIK: {{ $row->nik ?? '-' }}
                                            </span>
                                        </div>
                                        <div class="text-muted">
                                            <i class="bi bi-telephone me-1"></i>
                                            HP/WA: {{ $row->no_hp ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- JENIS BENIH --}}
                                    <td class="text-center" style="white-space: normal;">
                                        {{ $row->jenis_benih_diusahakan ?? '-' }}
                                    </td>

                                    {{-- JADWAL --}}
                                    <td style="white-space: normal;">
                                        @if ($row->sesi)
                                            <div class="fw-semibold">
                                                {{ $row->sesi->tanggal?->format('d M Y') }}
                                            </div>
                                            <div class="text-muted">
                                                {{ $row->sesi->jam_mulai }} - {{ $row->sesi->jam_selesai }}
                                            </div>

                                            @if ($row->sesi->meet_link)
                                                <div class="mt-1">
                                                    <a href="{{ $row->sesi->meet_link }}"
                                                       target="_blank"
                                                       class="small text-decoration-none">
                                                        <i class="bi bi-camera-video me-1"></i>
                                                        Link Meet
                                                    </a>
                                                </div>
                                            @endif

                                            @if (!empty($row->sesi->materi_path ?? null))
                                                <div class="mt-1">
                                                    <a href="{{ Storage::url($row->sesi->materi_path) }}"
                                                       target="_blank"
                                                       class="small text-decoration-none">
                                                        <i class="bi bi-file-earmark-text me-1"></i>
                                                        Materi
                                                    </a>
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-muted border">
                                                Belum dijadwalkan
                                            </span>
                                        @endif
                                    </td>

                                    {{-- STATUS PEMBINAAN --}}
                                    <td class="text-center" style="white-space: normal;">
                                        @php
                                            $badgeClass = 'bg-secondary';
                                            $label = ucfirst(str_replace('_', ' ', $row->status));

                                            if ($row->status === 'menunggu_jadwal') {
                                                $badgeClass = 'bg-warning text-dark';
                                            } elseif ($row->status === 'dijadwalkan') {
                                                $badgeClass = 'bg-info text-dark';
                                            } elseif ($row->status === 'selesai') {
                                                $badgeClass = 'bg-success';
                                            } elseif ($row->status === 'batal') {
                                                $badgeClass = 'bg-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $label }}
                                        </span>
                                    </td>

                                    {{-- STATUS PERIZINAN --}}
                                    <td class="text-center" style="white-space: normal;">
                                        @php
                                            $perizinanStatus = $row->status_perizinan ?? 'menunggu';
                                            $perizinanBadge = 'bg-secondary';
                                            $perizinanLabel = ucfirst($perizinanStatus);

                                            if ($perizinanStatus === 'menunggu') {
                                                $perizinanBadge = 'bg-warning text-dark';
                                            } elseif ($perizinanStatus === 'berhasil') {
                                                $perizinanBadge = 'bg-success';
                                            } elseif ($perizinanStatus === 'dibatalkan') {
                                                $perizinanBadge = 'bg-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $perizinanBadge }}">
                                            {{ $perizinanLabel }}
                                        </span>
                                    </td>

                                    {{-- AKSI --}}
                                    <td  style="white-space: normal;">
                                        <div class="d-flex flex-wrap gap-1">
                                            {{-- Detail --}}
                                            <a href="{{ route('pemohon.pembinaan.show', $row) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                Detail
                                            </a>

                                            {{-- Edit pembinaan (hanya menunggu jadwal) --}}
                                            @if ($row->is_menunggu_jadwal)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary btn-edit-pembinaan"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editPembinaanModal"
                                                        data-id="{{ $row->id }}"
                                                        data-nama_penangkar="{{ $row->nama_penangkar }}"
                                                        data-nama_penanggung_jawab="{{ $row->nama_penanggung_jawab }}"
                                                        data-nik="{{ $row->nik }}"
                                                        data-npwp="{{ $row->npwp }}"
                                                        data-alamat_penanggung_jawab="{{ $row->alamat_penanggung_jawab }}"
                                                        data-lokasi_usaha="{{ $row->lokasi_usaha }}"
                                                        data-status_kepemilikan_lahan="{{ $row->status_kepemilikan_lahan }}"
                                                        data-jenis_benih_diusahakan="{{ $row->jenis_benih_diusahakan }}"
                                                        data-no_hp="{{ $row->no_hp }}">
                                                    Edit
                                                </button>
                                            @endif

                                            {{-- Isi / Edit OSS --}}
                                            @if ($row->status === 'selesai' && $perizinanStatus !== 'dibatalkan')
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-success btn-isi-oss"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#ossModal"
                                                        data-id="{{ $row->id }}"
                                                        data-nib="{{ $row->nib }}"
                                                        data-sertifikat="{{ $row->no_sertifikat_standar }}">
                                                    {{ $perizinanStatus === 'berhasil' ? 'Edit OSS' : 'Isi OSS' }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox me-1"></i>
                                        Belum ada pengajuan pembinaan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== MODAL AJUKAN PEMBINAAN (CREATE) ========== --}}
    <div class="modal fade" id="createPembinaanModal" tabindex="-1" aria-labelledby="createPembinaanModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('pemohon.pembinaan.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPembinaanModalLabel">
                            Ajukan Pembinaan Calon Penangkar
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Isi data calon penangkar dengan lengkap agar proses pembinaan dapat dijadwalkan dengan tepat.
                        </p>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small">
                                    Nama Penangkar <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="nama_penangkar"
                                       class="form-control form-control-sm"
                                       value="{{ old('nama_penangkar') }}"
                                       required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">
                                    Nama Penanggung Jawab <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="nama_penanggung_jawab"
                                       class="form-control form-control-sm"
                                       value="{{ old('nama_penanggung_jawab') }}"
                                       required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">NIK</label>
                                <input type="text"
                                       name="nik"
                                       class="form-control form-control-sm"
                                       value="{{ old('nik') }}"
                                       maxlength="16"
                                       inputmode="numeric"
                                       pattern="\d*"
                                       placeholder="16 digit (opsional)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">No NPWP</label>
                                <input type="text"
                                       name="npwp"
                                       class="form-control form-control-sm"
                                       value="{{ old('npwp') }}"
                                       placeholder="Opsional">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Alamat Penanggung Jawab</label>
                                <input type="text"
                                       name="alamat_penanggung_jawab"
                                       class="form-control form-control-sm"
                                       value="{{ old('alamat_penanggung_jawab') }}"
                                       placeholder="Alamat lengkap">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Lokasi Usaha / Kebun</label>
                                <input type="text"
                                       name="lokasi_usaha"
                                       class="form-control form-control-sm"
                                       value="{{ old('lokasi_usaha') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Status Kepemilikan Lahan</label>
                                <input type="text"
                                       name="status_kepemilikan_lahan"
                                       class="form-control form-control-sm"
                                       value="{{ old('status_kepemilikan_lahan') }}"
                                       placeholder="Milik sendiri / sewa / dll">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">
                                    Jenis Benih yang Diusahakan <span class="text-danger">*</span>
                                </label>
                                <select name="jenis_benih_diusahakan"
                                        class="form-select form-select-sm"
                                        required>
                                    <option value="">-- Pilih Jenis Benih --</option>
                                    <option value="Biji" {{ old('jenis_benih_diusahakan') == 'Biji' ? 'selected' : '' }}>
                                        Biji
                                    </option>
                                    <option value="Siap Tanam" {{ old('jenis_benih_diusahakan') == 'Siap Tanam' ? 'selected' : '' }}>
                                        Siap Tanam
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">No HP / WA</label>
                                <input type="text"
                                       name="no_hp"
                                       class="form-control form-control-sm"
                                       value="{{ old('no_hp') }}"
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
                        <button type="submit" class="btn btn-sm btn-primary">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========== MODAL EDIT PEMBINAAN ========== --}}
    <div class="modal fade" id="editPembinaanModal" tabindex="-1" aria-labelledby="editPembinaanModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <form id="editPembinaanForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPembinaanModalLabel">
                            Edit Data Pembinaan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            Data hanya dapat diubah selama status pembinaan masih
                            <strong>Menunggu Jadwal</strong>.
                        </p>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small">
                                    Nama Penangkar <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="nama_penangkar"
                                       class="form-control form-control-sm"
                                       required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">
                                    Nama Penanggung Jawab <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="nama_penanggung_jawab"
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
                                <label class="form-label small">No NPWP</label>
                                <input type="text"
                                       name="npwp"
                                       class="form-control form-control-sm"
                                       placeholder="Opsional">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Alamat Penanggung Jawab</label>
                                <input type="text"
                                       name="alamat_penanggung_jawab"
                                       class="form-control form-control-sm"
                                       placeholder="Alamat lengkap">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Lokasi Usaha / Kebun</label>
                                <input type="text"
                                       name="lokasi_usaha"
                                       class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Status Kepemilikan Lahan</label>
                                <input type="text"
                                       name="status_kepemilikan_lahan"
                                       class="form-control form-control-sm"
                                       placeholder="Milik sendiri / sewa / dll">
                            </div>

                            <div class="col-md-4">
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

                            <div class="col-md-4">
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
                        <button type="submit" class="btn btn-sm btn-primary">
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
                        <button type="submit" class="btn btn-sm btn-primary">
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
        document.addEventListener('DOMContentLoaded', function() {
            // ========= OSS MODAL =========
            const ossModal = document.getElementById('ossModal');
            const ossForm = document.getElementById('ossForm');
            const ossUrlTpl = "{{ route('pemohon.pembinaan.oss.store', ['pembinaan' => '__ID__']) }}";

            if (ossModal) {
                ossModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const id = button.getAttribute('data-id');
                    const nib = button.getAttribute('data-nib') || '';
                    const sertifikat = button.getAttribute('data-sertifikat') || '';

                    const actionUrl = ossUrlTpl.replace('__ID__', id);
                    ossForm.setAttribute('action', actionUrl);

                    ossForm.querySelector('[name="nib"]').value = nib;
                    ossForm.querySelector('[name="no_sertifikat_standar"]').value = sertifikat;
                });
            }

            // ========= EDIT PEMBINAAN MODAL =========
            const editModal = document.getElementById('editPembinaanModal');
            const editForm = document.getElementById('editPembinaanForm');
            const editUrlTpl = "{{ route('pemohon.pembinaan.update', ['pembinaan' => '__ID__']) }}";

            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const id = button.getAttribute('data-id');
                    const actionUrl = editUrlTpl.replace('__ID__', id);
                    editForm.setAttribute('action', actionUrl);

                    editForm.querySelector('[name="nama_penangkar"]').value =
                        button.getAttribute('data-nama_penangkar') || '';
                    editForm.querySelector('[name="nama_penanggung_jawab"]').value =
                        button.getAttribute('data-nama_penanggung_jawab') || '';
                    editForm.querySelector('[name="nik"]').value =
                        button.getAttribute('data-nik') || '';
                    editForm.querySelector('[name="npwp"]').value =
                        button.getAttribute('data-npwp') || '';
                    editForm.querySelector('[name="alamat_penanggung_jawab"]').value =
                        button.getAttribute('data-alamat_penanggung_jawab') || '';
                    editForm.querySelector('[name="lokasi_usaha"]').value =
                        button.getAttribute('data-lokasi_usaha') || '';
                    editForm.querySelector('[name="status_kepemilikan_lahan"]').value =
                        button.getAttribute('data-status_kepemilikan_lahan') || '';
                    editForm.querySelector('[name="no_hp"]').value =
                        button.getAttribute('data-no_hp') || '';

                    const jenisSelect = editForm.querySelector('[name="jenis_benih_diusahakan"]');
                    const jenisVal = button.getAttribute('data-jenis_benih_diusahakan') || '';
                    if (jenisSelect) {
                        jenisSelect.value = jenisVal;
                    }
                });
            }
        });
    </script>
@endpush
