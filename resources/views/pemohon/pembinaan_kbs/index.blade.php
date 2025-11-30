@extends('layouts.bootstrap')

@section('content')
    <style>
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
                <h1 class="h4 mb-1">Pembinaan Kebun Benih Sumber</h1>
                <p class="text-muted mb-0 small">
                    Ajukan pembinaan dan pantau jadwal serta status pelaksanaan pembinaan kebun benih sumber.
                </p>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal"
                    data-bs-target="#createPembinaanKbsModal">
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
                <form method="GET" action="{{ route('pemohon.pembinaan-kbs.index') }}" class="row g-2 align-items-center">
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
                            <a href="{{ route('pemohon.pembinaan-kbs.index') }}"
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
                                <th>Nama & Kontak</th>
                                <th>Komoditas</th>
                                <th>Lokasi Kebun</th>
                                <th>Jadwal Pembinaan</th>
                                <th>Status Pembinaan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse ($pembinaanList as $index => $row)
                                <tr>
                                    <td class="text-center">
                                        {{ $pembinaanList->firstItem() + $index }}
                                    </td>

                                    {{-- NAMA & KONTAK --}}
                                    <td style="white-space: normal;">
                                        <div class="fw-semibold">
                                            {{ $row->nama }}
                                        </div>
                                        <div class="text-muted">
                                            <i class="bi bi-person-badge me-1"></i>
                                            NIK: {{ $row->nik ?? '-' }}
                                        </div>
                                        <div class="text-muted">
                                            <i class="bi bi-telephone me-1"></i>
                                            HP/WA: {{ $row->no_hp ?? '-' }}
                                        </div>
                                        <div class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            {{ $row->alamat ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- KOMODITAS --}}
                                    <td class="text-center" style="white-space: normal;">
                                        {{ $row->jenisTanaman->nama_tanaman ?? '-' }}
                                    </td>

                                    {{-- LOKASI KEBUN --}}
                                    <td style="white-space: normal;">
                                        <div class="fw-semibold">
                                            {{ $row->lokasi_kebun ?? '-' }}
                                        </div>
                                        <div class="text-muted">
                                            Koordinat:
                                            @if (!is_null($row->latitude_kebun) && !is_null($row->longitude_kebun))
                                                {{ $row->latitude_kebun }}, {{ $row->longitude_kebun }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                        <div class="text-muted">
                                            Pohon induk:
                                            {{ $row->jumlah_pohon_induk ?? '-' }}
                                        </div>
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
                                                    <a href="{{ $row->sesi->meet_link }}" target="_blank"
                                                        class="small text-decoration-none">
                                                        <i class="bi bi-camera-video me-1"></i>
                                                        Link Meet
                                                    </a>
                                                </div>
                                            @endif

                                            @if (!empty($row->sesi->materi_path ?? null))
                                                <div class="mt-1">
                                                    <a href="{{ Storage::url($row->sesi->materi_path) }}" target="_blank"
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

                                    {{-- AKSI --}}
                                    <td style="white-space: normal;">
                                        <div class="d-flex flex-wrap gap-1">
                                            {{-- Detail --}}
                                            <a href="{{ route('pemohon.pembinaan-kbs.show', $row) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Detail
                                            </a>

                                            {{-- Edit pembinaan (hanya menunggu jadwal) --}}
                                            @if ($row->status === 'menunggu_jadwal')
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary btn-edit-pembinaan-kbs"
                                                    data-bs-toggle="modal" data-bs-target="#editPembinaanKbsModal"
                                                    data-id="{{ $row->id }}" data-nama="{{ $row->nama }}"
                                                    data-nik="{{ $row->nik }}" data-alamat="{{ $row->alamat }}"
                                                    data-no_hp="{{ $row->no_hp }}"
                                                    data-jenis_tanaman_id="{{ $row->jenis_tanaman_id }}"
                                                    data-lokasi_kebun="{{ $row->lokasi_kebun }}"
                                                    data-latitude_kebun="{{ $row->latitude_kebun }}"
                                                    data-longitude_kebun="{{ $row->longitude_kebun }}"
                                                    data-jumlah_pohon_induk="{{ $row->jumlah_pohon_induk }}">
                                                    Edit
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox me-1"></i>
                                        Belum ada pengajuan pembinaan kebun benih sumber.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($pembinaanList->hasPages())
                <div class="card-footer bg-white border-0 py-2">
                    <div class="d-flex justify-content-end">
                        {{ $pembinaanList->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ========== MODAL AJUKAN PEMBINAAN KBS (CREATE) ========== --}}
<div class="modal fade" id="createPembinaanKbsModal" tabindex="-1" aria-labelledby="createPembinaanKbsModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form id="createPembinaanKbsForm" action="{{ route('pemohon.pembinaan-kbs.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPembinaanKbsModalLabel">
                        Ajukan Pembinaan Kebun Benih Sumber
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Isi data kebun benih sumber dengan lengkap agar proses pembinaan dapat dijadwalkan dengan tepat.
                    </p>

                    <div class="row g-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label small">
                                    Nama <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="nama"
                                       class="form-control form-control-sm js-validate"
                                       data-required="true"
                                       value="{{ old('nama') }}">
                                <small class="invalid-feedback"></small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">NIK</label>
                                <input type="text"
                                       name="nik"
                                       class="form-control form-control-sm js-validate"
                                       data-nik="true"
                                       value="{{ old('nik') }}"
                                       maxlength="16"
                                       inputmode="numeric"
                                       pattern="\d*"
                                       placeholder="16 digit (opsional)">
                                <small class="invalid-feedback"></small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">No HP / WA</label>
                                <input type="text"
                                       name="no_hp"
                                       class="form-control form-control-sm js-validate"
                                       data-phone="true"
                                       value="{{ old('no_hp') }}"
                                       placeholder="08xxxxxxxxxx">
                                <small class="invalid-feedback"></small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label small">Alamat</label>
                                <input type="text"
                                       name="alamat"
                                       class="form-control form-control-sm"
                                       value="{{ old('alamat') }}"
                                       placeholder="Alamat lengkap">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">
                                    Komoditas <span class="text-danger">*</span>
                                </label>
                                <select name="jenis_tanaman_id"
                                        class="form-select form-select-sm js-validate"
                                        data-required="true">
                                    <option value="">-- Pilih Komoditas --</option>
                                    @foreach($jenisTanaman as $tanaman)
                                        <option value="{{ $tanaman->id }}"
                                            {{ old('jenis_tanaman_id') == $tanaman->id ? 'selected' : '' }}>
                                            {{ $tanaman->nama_tanaman }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="invalid-feedback"></small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Lokasi Kebun</label>
                                <input type="text"
                                       name="lokasi_kebun"
                                       class="form-control form-control-sm"
                                       value="{{ old('lokasi_kebun') }}">
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-4">
                            <label class="form-label small">Latitude Kebun</label>
                            <input type="number"
                                   step="0.0000001"
                                   name="latitude_kebun"
                                   class="form-control form-control-sm js-validate"
                                   data-latitude="true"
                                   value="{{ old('latitude_kebun') }}"
                                   placeholder="-6.xxxxxx">
                            <small class="invalid-feedback"></small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">Longitude Kebun</label>
                            <input type="number"
                                   step="0.0000001"
                                   name="longitude_kebun"
                                   class="form-control form-control-sm js-validate"
                                   data-longitude="true"
                                   value="{{ old('longitude_kebun') }}"
                                   placeholder="106.xxxxxx">
                            <small class="invalid-feedback"></small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">Jumlah Pohon Induk</label>
                            <input type="number"
                                   name="jumlah_pohon_induk"
                                   class="form-control form-control-sm js-validate"
                                   data-min="0"
                                   value="{{ old('jumlah_pohon_induk') }}"
                                   min="0">
                            <small class="invalid-feedback"></small>
                        </div>
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


    {{-- ========== MODAL EDIT PEMBINAAN KBS ========== --}}
<div class="modal fade" id="editPembinaanKbsModal" tabindex="-1" aria-labelledby="editPembinaanKbsModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form id="editPembinaanKbsForm" method="POST" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editPembinaanKbsModalLabel">
                        Edit Data Pembinaan Kebun Benih Sumber
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Data hanya dapat diubah selama status pembinaan masih
                        <strong>Menunggu Jadwal</strong>.
                    </p>

                    <div class="row g-3">
                        <div class="row">
                        <div class="col-md-4">
                            <label class="form-label small">
                                Nama <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="nama"
                                   class="form-control form-control-sm js-validate"
                                   data-required="true">
                            <small class="invalid-feedback"></small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">NIK</label>
                            <input type="text"
                                   name="nik"
                                   class="form-control form-control-sm js-validate"
                                   data-nik="true"
                                   maxlength="16"
                                   inputmode="numeric"
                                   pattern="\d*"
                                   placeholder="16 digit (opsional)">
                            <small class="invalid-feedback"></small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">No HP / WA</label>
                            <input type="text"
                                   name="no_hp"
                                   class="form-control form-control-sm js-validate"
                                   data-phone="true"
                                   placeholder="08xxxxxxxxxx">
                            <small class="invalid-feedback"></small>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-4">
                            <label class="form-label small">Alamat</label>
                            <input type="text"
                                   name="alamat"
                                   class="form-control form-control-sm"
                                   placeholder="Alamat lengkap">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">
                                Komoditas <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_tanaman_id"
                                    class="form-select form-select-sm js-validate"
                                    data-required="true">
                                <option value="">-- Pilih Komoditas --</option>
                                @foreach($jenisTanaman as $tanaman)
                                    <option value="{{ $tanaman->id }}">
                                        {{ $tanaman->nama_tanaman }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="invalid-feedback"></small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">Lokasi Kebun</label>
                            <input type="text"
                                   name="lokasi_kebun"
                                   class="form-control form-control-sm">
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-4">
                            <label class="form-label small">Latitude Kebun</label>
                            <input type="number"
                                   step="0.0000001"
                                   name="latitude_kebun"
                                   class="form-control form-control-sm js-validate"
                                   data-latitude="true"
                                   placeholder="-6.xxxxxx">
                            <small class="invalid-feedback"></small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">Longitude Kebun</label>
                            <input type="number"
                                   step="0.0000001"
                                   name="longitude_kebun"
                                   class="form-control form-control-sm js-validate"
                                   data-longitude="true"
                                   placeholder="106.xxxxxx">
                            <small class="invalid-feedback"></small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">Jumlah Pohon Induk</label>
                            <input type="number"
                                   name="jumlah_pohon_induk"
                                   class="form-control form-control-sm js-validate"
                                   data-min="0"
                                   min="0">
                            <small class="invalid-feedback"></small>
                        </div>
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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ================== EDIT MODAL (prefill data) ==================
    const editModal = document.getElementById('editPembinaanKbsModal');
    const editForm  = document.getElementById('editPembinaanKbsForm');
    const editUrlTpl = "{{ route('pemohon.pembinaan-kbs.update', ['pembinaanKbs' => '__ID__']) }}";

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const id = button.getAttribute('data-id');
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
            const jenisVal = button.getAttribute('data-jenis_tanaman_id') || '';
            if (jenisSelect) {
                jenisSelect.value = jenisVal;
            }
        });
    }

    // ================== VALIDASI CLIENT-SIDE GENERAL ==================
    function getFeedbackEl(input) {
        let fb = input.parentElement.querySelector('.invalid-feedback');
        if (!fb && input.closest('.form-group')) {
            fb = input.closest('.form-group').querySelector('.invalid-feedback');
        }
        return fb;
    }

    function setError(input, message) {
        const fb = getFeedbackEl(input);
        input.classList.add('is-invalid');
        if (fb) fb.textContent = message || 'Field ini tidak valid.';
    }

    function clearError(input) {
        const fb = getFeedbackEl(input);
        input.classList.remove('is-invalid');
        if (fb) fb.textContent = '';
    }

    function validateField(input) {
        const val = (input.value || '').trim();
        const name = input.name;
        let message = '';

        // required
        if (input.dataset.required && !val) {
            message = 'Field ini wajib diisi.';
        }

        // NIK 16 digit
        if (!message && input.dataset.nik && val) {
            if (!/^\d{16}$/.test(val)) {
                message = 'NIK harus terdiri dari 16 digit angka.';
            }
        }

        // No HP sederhana
        if (!message && input.dataset.phone && val) {
            if (!/^0[0-9]{9,14}$/.test(val)) {
                message = 'No HP tidak valid.';
            }
        }

        // latitude -90..90
        if (!message && input.dataset.latitude && val) {
            const num = parseFloat(val);
            if (isNaN(num) || num < -90 || num > 90) {
                message = 'Latitude harus antara -90 s.d 90.';
            }
        }

        // longitude -180..180
        if (!message && input.dataset.longitude && val) {
            const num = parseFloat(val);
            if (isNaN(num) || num < -180 || num > 180) {
                message = 'Longitude harus antara -180 s.d 180.';
            }
        }

        // min value
        if (!message && input.dataset.min !== undefined && val !== '') {
            const minVal = parseFloat(input.dataset.min);
            const num = parseFloat(val);
            if (!isNaN(minVal) && (isNaN(num) || num < minVal)) {
                message = 'Nilai minimal adalah ' + minVal + '.';
            }
        }

        if (message) {
            setError(input, message);
            return false;
        } else {
            clearError(input);
            return true;
        }
    }

    function attachLiveValidation(form) {
        if (!form) return;
        const inputs = form.querySelectorAll('.js-validate');

        inputs.forEach((input) => {
            // cek saat blur
            input.addEventListener('blur', function () {
                validateField(input);
            });

            // cek saat user ngetik
            input.addEventListener('input', function () {
                // kalau sebelumnya error, cek ulang
                if (input.classList.contains('is-invalid')) {
                    validateField(input);
                }
            });
        });

        form.addEventListener('submit', function (e) {
            let valid = true;
            inputs.forEach((input) => {
                const ok = validateField(input);
                if (!ok) valid = false;
            });

            if (!valid) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }

    // aktifkan untuk kedua form
    attachLiveValidation(document.getElementById('createPembinaanKbsForm'));
    attachLiveValidation(document.getElementById('editPembinaanKbsForm'));
});
</script>
@endpush

