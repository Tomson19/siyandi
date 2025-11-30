@extends('layouts.bootstrap')

@section('content')
<style>
    body{
        margin-top: -70px;
    }
</style>
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h5 mb-1">Program & Kegiatan</h1>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php
            $user = auth()->user();
            $isVerifikator = $user && $user->hasRole('admin_verifikator');
            $isProduksi = $user && $user->hasRole('admin_bidang_produksi');

            // Hitung colspan "Belum ada data" berdasarkan kolom yang tampil
            if ($isVerifikator) {
                $emptyColspan = 9; // No, Program, Kegiatan, Komoditas, Jumlah Produksi, Jenis Benih, Bidang, Tahun, Aksi
            } elseif ($isProduksi) {
                $emptyColspan = 8; // No, Program, Kegiatan, Komoditas, Kebutuhan Benih, Bidang, Tahun, Aksi
            } else {
                $emptyColspan = 7; // fallback
            }
        @endphp

        {{-- FILTER --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm">
                            <option value="">Semua Tahun</option>
                            @foreach ($listTahun as $t)
                                <option value="{{ $t }}" {{ (string) $tahun === (string) $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small mb-1">Komoditas (Jenis Tanaman)</label>
                        <select name="jenis_tanaman_id" class="form-select form-select-sm">
                            <option value="">Semua Komoditas</option>
                            @foreach ($jenisTanaman as $tanaman)
                                <option value="{{ $tanaman->id }}"
                                    {{ (string) $jenisTanamanId === (string) $tanaman->id ? 'selected' : '' }}>
                                    {{ $tanaman->nama_tanaman }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5 d-flex justify-content-end gap-2">
                        <div class="flex-grow-1 text-end">
                            <label class="form-label small mb-1 d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.program_kegiatan.index') }}" class="btn btn-sm btn-outline-secondary">
                                Reset
                            </a>
                        </div>

                        <div>
                            <label class="form-label small mb-1 d-block">&nbsp;</label>
                            <button type="button"
                                class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#createModal">
                                <i class="bi bi-plus-lg me-1"></i> Tambah
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr class="text-center small text-muted">
                                <th style="width: 60px;">No</th>
                                <th>Nama Program</th>
                                <th>Nama Kegiatan</th>
                                <th>Komoditas</th>

                                @if ($isVerifikator)
                                    <th>Jumlah Produksi</th>
                                    <th>Jenis Benih</th>
                                @elseif ($isProduksi)
                                    <th>Kebutuhan Benih (Batang)</th>
                                @endif

                                <th>Bidang</th>
                                <th style="width: 80px;">Tahun</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($programs as $i => $row)
                                <tr>
                                    <td class="text-center small">
                                        {{ $programs->firstItem() + $i }}
                                    </td>
                                    <td class="small text-center">
                                        <strong>{{ $row->nama_program }}</strong>
                                    </td>
                                    <td class="small text-center">
                                        {{ $row->nama_kegiatan }}
                                    </td>
                                    <td class="small text-center">
                                        {{ $row->jenisTanaman->nama_tanaman ?? '-' }}
                                    </td>

                                    @if ($isVerifikator)
                                        <td class="text-center small">
                                            {{ $row->jumlah_produksi !== null ? number_format($row->jumlah_produksi, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="small text-center">
                                            {{ $row->jenis_benih ?? '-' }}
                                        </td>
                                    @elseif ($isProduksi)
                                        <td class="text-center small">
                                            {{ $row->kebutuhan_benih !== null ? number_format($row->kebutuhan_benih, 0, ',', '.') : '-' }}
                                        </td>
                                    @endif
 
                                    <td class="small text-center">
                                        {{ $row->bidang }}
                                    </td>
                                    <td class="text-center small">
                                        {{ $row->tahun }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-warning btn-edit mb-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal"
                                                data-id="{{ $row->id }}"
                                                data-nama-program="{{ $row->nama_program }}"
                                                data-nama-kegiatan="{{ $row->nama_kegiatan }}"
                                                data-jenis-tanaman-id="{{ $row->jenis_tanaman_id }}"
                                                data-jumlah-produksi="{{ $row->jumlah_produksi }}"
                                                data-kebutuhan-benih="{{ $row->kebutuhan_benih }}"
                                                data-jenis-benih="{{ $row->jenis_benih }}"
                                                data-tahun="{{ $row->tahun }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('admin.program_kegiatan.destroy', $row->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin hapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger mb-1">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $emptyColspan }}" class="text-center text-muted small py-3">
                                        Belum ada data program & kegiatan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-top px-3 py-2">
                    {{ $programs->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- ========== MODAL CREATE ========== --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('admin.program_kegiatan.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Program & Kegiatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Nama Program</label>
                                <input type="text"
                                       name="nama_program"
                                       class="form-control"
                                       value="{{ old('nama_program') }}"
                                       required>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Nama Kegiatan</label>
                                <input type="text"
                                       name="nama_kegiatan"
                                       class="form-control"
                                       value="{{ old('nama_kegiatan') }}"
                                       required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Komoditas (Jenis Tanaman)</label>
                                <select name="jenis_tanaman_id" class="form-select" required>
                                    <option value="">-- Pilih Komoditas --</option>
                                    @foreach ($jenisTanaman as $tanaman)
                                        <option value="{{ $tanaman->id }}"
                                            {{ old('jenis_tanaman_id') == $tanaman->id ? 'selected' : '' }}>
                                            {{ $tanaman->nama_tanaman }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Field khusus role --}}
                            @role('admin_verifikator')
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Jumlah Produksi</label>
                                    <input type="number"
                                           name="jumlah_produksi"
                                           class="form-control"
                                           value="{{ old('jumlah_produksi') }}"
                                           step="0.01">
                                </div>

                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Jenis Benih</label>
                                    <input type="text"
                                           name="jenis_benih"
                                           class="form-control"
                                           value="{{ old('jenis_benih') }}">
                                </div>
                            @endrole

                            @role('admin_bidang_produksi')
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Kebutuhan Benih (Batang)</label>
                                    <input type="number"
                                           name="kebutuhan_benih"
                                           class="form-control"
                                           value="{{ old('kebutuhan_benih') }}"
                                           step="0.01">
                                </div>
                            @endrole
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Tahun</label>
                            <input type="number"
                                   name="tahun"
                                   class="form-control"
                                   value="{{ old('tahun', date('Y')) }}"
                                   required>
                        </div>

                        {{-- bidang tetap tidak ditampilkan, di-set otomatis di controller --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========== MODAL EDIT ========== --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Program & Kegiatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Nama Program</label>
                                <input type="text" name="nama_program" class="form-control" required>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Nama Kegiatan</label>
                                <input type="text" name="nama_kegiatan" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Komoditas (Jenis Tanaman)</label>
                                <select name="jenis_tanaman_id" class="form-select" required>
                                    <option value="">-- Pilih Komoditas --</option>
                                    @foreach ($jenisTanaman as $tanaman)
                                        <option value="{{ $tanaman->id }}">{{ $tanaman->nama_tanaman }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Field khusus role --}}
                            @role('admin_verifikator')
                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Jumlah Produksi</label>
                                    <input type="number" name="jumlah_produksi" class="form-control" step="0.01">
                                </div>

                                <div class="mb-3 col-md-3">
                                    <label class="form-label">Jenis Benih</label>
                                    <input type="text" name="jenis_benih" class="form-control">
                                </div>
                            @endrole

                            @role('admin_bidang_produksi')
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Kebutuhan Benih (Batang)</label>
                                    <input type="number" name="kebutuhan_benih" class="form-control" step="0.01">
                                </div>
                            @endrole
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" required>
                        </div>

                        {{-- bidang tetap otomatis di controller --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editModal');

            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const id = button.getAttribute('data-id');
                    const namaProgram = button.getAttribute('data-nama-program');
                    const namaKegiatan = button.getAttribute('data-nama-kegiatan');
                    const jenisTanamanId = button.getAttribute('data-jenis-tanaman-id');
                    const jumlahProduksi = button.getAttribute('data-jumlah-produksi');
                    const kebutuhanBenih = button.getAttribute('data-kebutuhan-benih');
                    const jenisBenih = button.getAttribute('data-jenis-benih');
                    const tahun = button.getAttribute('data-tahun');

                    const form = document.getElementById('editForm');
                    const baseUrl = "{{ url('admin/program-kegiatan') }}";
                    form.action = baseUrl + '/' + id;

                    form.querySelector('[name="nama_program"]').value = namaProgram || '';
                    form.querySelector('[name="nama_kegiatan"]').value = namaKegiatan || '';
                    form.querySelector('[name="tahun"]').value = tahun || '';

                    const selectJenis = form.querySelector('[name="jenis_tanaman_id"]');
                    if (selectJenis && jenisTanamanId) {
                        selectJenis.value = jenisTanamanId;
                    }

                    const jumlahProduksiInput = form.querySelector('[name="jumlah_produksi"]');
                    if (jumlahProduksiInput) {
                        jumlahProduksiInput.value = jumlahProduksi || '';
                    }

                    const kebutuhanBenihInput = form.querySelector('[name="kebutuhan_benih"]');
                    if (kebutuhanBenihInput) {
                        kebutuhanBenihInput.value = kebutuhanBenih || '';
                    }

                    const jenisBenihInput = form.querySelector('[name="jenis_benih"]');
                    if (jenisBenihInput) {
                        jenisBenihInput.value = jenisBenih || '';
                    }
                });
            }
        });
    </script>
@endpush
