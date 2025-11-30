@extends('layouts.bootstrap')

@section('content')
    <style>
        body{
            margin-top: -70px;
        }
        .page-header-badge {
            font-size: .75rem;
            border-radius: 999px;
            padding-inline: .75rem;
        }

        .card-header-soft {
            background: #f8f9fa;
            border-bottom: 0;
        }

        .icon-circle {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .table thead th {
            vertical-align: middle;
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
                    <h1 class="h5 mb-0">Pembinaan Calon Penangkar</h1>
                </div>
                <p class="text-muted small mb-0">
                    Kelola pengajuan pembinaan, susun sesi pertemuan, dan pantau status pelaksanaannya.
                </p>
            </div>
            <div class="col-auto d-none d-md-block">
                <span class="badge bg-success-subtle text-success border border-success-subtle page-header-badge">
                    <i class="bi bi-shield-check me-1"></i> Admin Verifikator
                </span>
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

        {{-- FILTER & SEARCH --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <form method="GET"
                      action="{{ route('admin.verifikator.pembinaan.index') }}"
                      class="row g-2 align-items-end">
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label small mb-0 text-muted">
                            <i class="bi bi-search me-1"></i>Cari
                        </label>
                        <input type="text"
                               name="q"
                               class="form-control form-control-sm"
                               placeholder="Nama penangkar / penanggung jawab / email"
                               value="{{ $search }}">
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <label class="form-label small mb-0 text-muted">Status Pengajuan</label>
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
                        @if (request()->hasAny(['q', 'status']) && (request('q') || request('status')))
                            <a href="{{ route('admin.verifikator.pembinaan.index') }}"
                               class="btn btn-sm btn-outline-light border ms-1">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- PENGAJUAN + BUAT SESI --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header card-header-soft d-flex justify-content-between align-items-center">
                <div class="d-flex flex-column">
                    <span class="fw-semibold small">Daftar Pengajuan Pembinaan</span>
                    <span class="text-muted small">
                        Total: {{ $pembinaanList->total() }} pengajuan
                    </span>
                </div>
                <button type="button"
                        class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                        id="btnOpenSesiModal"
                        data-bs-toggle="modal"
                        data-bs-target="#createSesiModal">
                    <i class="bi bi-calendar-plus"></i>
                    <span class="d-none d-sm-inline">Buat Sesi Pembinaan</span>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-sm mb-0 align-middle">
                        <thead class="table-light text-center small">
                            <tr>
                                <th style="width: 35px;">
                                    <input type="checkbox" class="form-check-input" id="checkAllPengajuan">
                                </th>
                                <th style="width: 50px;">No</th>
                                <th>Penangkar & Pemohon</th>
                                <th>Benih & Lahan</th>
                                <th>Jadwal Pembinaan</th>
                                <th>Status Pembinaan</th>
                                <th>Status Perizinan</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse ($pembinaanList as $index => $row)
                                @php
                                    $canSelect = $row->status === 'menunggu_jadwal';
                                @endphp
                                <tr>
                                    {{-- CHECKBOX --}}
                                    <td class="text-center">
                                        <input type="checkbox"
                                               class="form-check-input pembinaan-checkbox"
                                               value="{{ $row->id }}"
                                               @unless ($canSelect) disabled @endunless>
                                    </td>

                                    {{-- NO --}}
                                    <td class="text-center">
                                        {{ $pembinaanList->firstItem() + $index }}
                                    </td>

                                    {{-- PENANGKAR & PEMOHON --}}
                                    <td style="white-space: normal;">
                                        <div class="fw-semibold">
                                            {{ $row->nama_penangkar }}
                                        </div>
                                        <div class="text-muted">
                                            Penanggung jawab: {{ $row->nama_penanggung_jawab ?? '-' }}
                                        </div>
                                        <div class="text-muted">
                                            NIK: {{ $row->nik ?? '-' }}
                                        </div>
                                        <div class="text-muted">
                                            HP/WA: {{ $row->no_hp ?? '-' }}
                                        </div>
                                        <div class="text-muted">
                                            Alamat: {{ $row->alamat_penanggung_jawab ?? '-' }}
                                        </div>
                                        <div class="text-muted">
                                            NPWP: {{ $row->npwp ?? '-' }}
                                        </div>
                                        <div class="text-muted">
                                            User:
                                            <span class="fw-semibold">{{ $row->user->name ?? '-' }}</span>
                                            <span class="text-body-secondary">
                                                ({{ $row->user->email ?? '-' }})
                                            </span>
                                        </div>
                                    </td>

                                    {{-- BENIH & LAHAN --}}
                                    <td style="white-space: normal;">
                                        <div>
                                            <strong>Jenis benih:</strong>
                                            {{ $row->jenis_benih_diusahakan ?? '-' }}
                                        </div>
                                        <div class="text-muted">
                                            Status lahan: {{ $row->status_kepemilikan_lahan ?? '-' }}
                                        </div>
                                        <div class="text-muted">
                                            Lokasi usaha: {{ $row->lokasi_usaha ?? '-' }}
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
                                                    <a href="{{ $row->sesi->meet_link }}"
                                                       target="_blank"
                                                       class="small text-decoration-none">
                                                        <i class="bi bi-camera-video me-1"></i>
                                                        Link Meet
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
                                        @if ($row->alasan_status)
                                            <div class="text-muted small mt-1" style="white-space: normal;">
                                                {{ $row->alasan_status }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- STATUS PERIZINAN --}}
                                    <td class="text-center" style="white-space: normal;">
                                        @if ($row->status === 'batal')
                                            <span class="text-muted">-</span>
                                        @else
                                            @php
                                                $perizinanBadge = 'bg-secondary';
                                                $perizinanLabel = ucfirst($row->status_perizinan ?? 'menunggu');
                                                if ($row->status_perizinan === 'menunggu') {
                                                    $perizinanBadge = 'bg-warning text-dark';
                                                } elseif ($row->status_perizinan === 'berhasil') {
                                                    $perizinanBadge = 'bg-success';
                                                } elseif ($row->status_perizinan === 'dibatalkan') {
                                                    $perizinanBadge = 'bg-danger';
                                                }
                                            @endphp
                                            <span class="badge {{ $perizinanBadge }}">
                                                {{ $perizinanLabel }}
                                            </span>
                                            @if ($row->alasan_perizinan)
                                                <div class="text-muted small mt-1" style="white-space: normal;">
                                                    {{ $row->alasan_perizinan }}
                                                </div>
                                            @endif
                                        @endif
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

                <div class="border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Menampilkan {{ $pembinaanList->firstItem() }}–{{ $pembinaanList->lastItem() }}
                        dari {{ $pembinaanList->total() }} pengajuan
                    </div>
                    <div>
                        {{ $pembinaanList->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- DAFTAR SESI --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header card-header-soft fw-semibold small d-flex justify-content-between align-items-center">
                <span>Sesi Pembinaan</span>
                <span class="text-muted small">
                    Total: {{ $sesiList->total() }} sesi
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-sm mb-0 align-middle">
                        <thead class="table-light text-center small">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Nama Sesi</th>
                                <th>Peserta</th>
                                <th>Status</th>
                                <th style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @php
                                $noSesiAwal = ($sesiList->currentPage() - 1) * $sesiList->perPage() + 1;
                            @endphp
                            @forelse ($sesiList as $i => $sesi)
                                <tr>
                                    <td class="text-center">{{ $noSesiAwal + $i }}</td>
                                    <td class="text-center">
                                        {{ $sesi->tanggal?->format('d M Y') ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $sesi->jam_mulai }} - {{ $sesi->jam_selesai }}
                                    </td>
                                    <td style="white-space: normal;">
                                        {{ $sesi->nama_sesi ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            {{ $sesi->peserta_count }} peserta
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = 'bg-secondary';
                                            $label = ucfirst($sesi->status);
                                            if ($sesi->status === 'dijadwalkan') {
                                                $badgeClass = 'bg-info text-dark';
                                            } elseif ($sesi->status === 'selesai') {
                                                $badgeClass = 'bg-success';
                                            } elseif ($sesi->status === 'batal') {
                                                $badgeClass = 'bg-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.verifikator.pembinaan.sesi.show', $sesi) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                            <span class="d-none d-sm-inline">Detail</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox me-1"></i>
                                        Belum ada sesi pembinaan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Menampilkan {{ $sesiList->firstItem() }}–{{ $sesiList->lastItem() }}
                        dari {{ $sesiList->total() }} sesi
                    </div>
                    <div>
                        {{ $sesiList->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== MODAL BUAT SESI ========== --}}
    <div class="modal fade" id="createSesiModal" tabindex="-1" aria-labelledby="createSesiModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="createSesiForm"
                      action="{{ route('admin.verifikator.pembinaan.sesi.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createSesiModalLabel">Buat Sesi Pembinaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-2">
                            Sesi akan dibuat untuk pengajuan yang dipilih pada tabel.
                            Pastikan minimal satu pengajuan dicentang (status <strong>Menunggu Jadwal</strong>).
                        </p>
                        <div class="alert alert-info py-2 small mb-3 d-none" id="selectedInfoBox">
                            <i class="bi bi-info-circle me-1"></i>
                            <span id="selectedInfoText"></span>
                        </div>

                        {{-- hidden pembinaan_ids[] di-generate via JS --}}
                        <div id="selectedPembinaanContainer"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Nama Sesi (opsional)</label>
                                <input type="text"
                                       name="nama_sesi"
                                       class="form-control form-control-sm"
                                       placeholder="Contoh: Batch 1 - Januari">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Tanggal <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="tanggal"
                                       class="form-control form-control-sm"
                                       required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time"
                                       name="jam_mulai"
                                       class="form-control form-control-sm"
                                       required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time"
                                       name="jam_selesai"
                                       class="form-control form-control-sm"
                                       required>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label small">
                                    Link Google Meet / Platform Lain <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="meet_link"
                                       class="form-control form-control-sm"
                                       placeholder="https://meet.google.com/...."
                                       required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small">Materi Pembinaan (opsional)</label>
                                <input type="file"
                                       name="materi"
                                       class="form-control form-control-sm">
                                <small class="text-muted">
                                    Bisa upload PPT, PDF, atau file lain (maksimal 20MB).
                                </small>
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
                            Simpan Sesi
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
            const checkAll       = document.getElementById('checkAllPengajuan');
            const checkboxes     = document.querySelectorAll('.pembinaan-checkbox');
            const btnOpenModal   = document.getElementById('btnOpenSesiModal');
            const containerIds   = document.getElementById('selectedPembinaanContainer');
            const infoBox        = document.getElementById('selectedInfoBox');
            const infoText       = document.getElementById('selectedInfoText');

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        if (!cb.disabled) {
                            cb.checked = checkAll.checked;
                        }
                    });
                });
            }

            if (btnOpenModal) {
                btnOpenModal.addEventListener('click', function(e) {
                    const selected = Array.from(checkboxes)
                        .filter(cb => cb.checked && !cb.disabled)
                        .map(cb => cb.value);

                    if (selected.length === 0) {
                        e.stopPropagation();
                        e.preventDefault();
                        alert('Pilih minimal satu pengajuan (status Menunggu Jadwal) untuk dibuatkan sesi.');
                        return false;
                    }

                    containerIds.innerHTML = '';
                    selected.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'pembinaan_ids[]';
                        input.value = id;
                        containerIds.appendChild(input);
                    });

                    if (infoBox && infoText) {
                        infoBox.classList.remove('d-none');
                        infoText.textContent = selected.length + ' pengajuan akan dimasukkan ke sesi ini.';
                    }
                });
            }
        });
    </script>
@endpush
