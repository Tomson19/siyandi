@extends('layouts.bootstrap')

@section('title', 'Manajemen QRIS')

@section('content')
<style>
    body{
        margin-top: -70px;
    }
</style>
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold text-success mb-1">
                <i class="bi bi-qr-code-scan me-2"></i> Pengaturan QRIS Pembayaran
            </h5>
            <small class="text-muted">Atur barcode QRIS yang digunakan untuk transaksi pembayaran benih berbayar.</small>
        </div>
        <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddQris">
            <i class="bi bi-plus-circle me-1"></i> Tambah QRIS
        </button>
    </div>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="alert alert-success small shadow-sm alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- DATA --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
            @if ($data->isEmpty())
                <div class="text-center text-muted py-4">
                    <i class="bi bi-qr-code display-6 d-block mb-2"></i>
                    Belum ada data QRIS yang tersimpan.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle text-center">
                        <thead class="table-light small">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama QRIS</th>
                                <th>Gambar</th>
                                <th>Status</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach ($data as $i => $q)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $q->nama_qris ?? '-' }}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $q->gambar_qris) }}" 
                                             alt="QRIS" class="rounded shadow-sm" width="100">
                                    </td>
                                    <td>
                                        <span class="badge {{ $q->aktif ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $q->aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            {{-- EDIT --}}
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm btn-edit-qris"
                                                data-id="{{ $q->id }}"
                                                data-nama="{{ $q->nama_qris }}"
                                                data-aktif="{{ $q->aktif ? '1' : '0' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditQris">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            {{-- HAPUS --}}
                                            <form action="{{ route('admin.verifikator.qris.destroy', $q->id) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Yakin ingin menghapus QRIS ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalAddQris" tabindex="-1" aria-labelledby="modalAddQrisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('admin.verifikator.qris.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header py-2 border-0 shadow-sm">
                    <h6 class="modal-title fw-semibold mb-0">
                        <i class="bi bi-plus-circle text-primary me-1"></i> Tambah QRIS Baru
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body small py-3 px-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama QRIS</label>
                        <input type="text" name="nama_qris" class="form-control form-control-sm" placeholder="Contoh: QRIS UPT A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Gambar QRIS</label>
                        <input type="file" name="gambar_qris" class="form-control form-control-sm" accept="image/*" required>
                        <div class="form-text">Unggah gambar barcode dalam format JPG/PNG, maksimal 2MB.</div>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktifAdd">
                        <label class="form-check-label" for="aktifAdd">Jadikan QRIS ini aktif</label>
                    </div>
                </div>

                <div class="modal-footer py-2 border-0 bg-white">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal fade" id="modalEditQris" tabindex="-1" aria-labelledby="modalEditQrisLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-scrollable">
        <div class="modal-content">
            <form id="formEditQris" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header py-2 border-0 shadow-sm">
                    <h6 class="modal-title fw-semibold mb-0">
                        <i class="bi bi-pencil-square text-primary me-1"></i> Edit QRIS
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body small py-3 px-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama QRIS</label>
                        <input type="text" name="nama_qris" id="edit_nama_qris" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ganti Gambar (opsional)</label>
                        <input type="file" name="gambar_qris" class="form-control form-control-sm" accept="image/*">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="aktif" value="1" id="edit_aktif">
                        <label class="form-check-label" for="edit_aktif">Jadikan QRIS ini aktif</label>
                    </div>
                </div>

                <div class="modal-footer py-2 border-0 bg-white">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.btn-edit-qris');
    const formEdit = document.getElementById('formEditQris');
    const editNama = document.getElementById('edit_nama_qris');
    const editAktif = document.getElementById('edit_aktif');

    const urlTemplate = "{{ route('admin.verifikator.qris.update', ['id' => '__ID__']) }}";

    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            formEdit.action = urlTemplate.replace('__ID__', id);

            editNama.value = this.dataset.nama || '';
            editAktif.checked = this.dataset.aktif === '1';
        });
    });
});
</script>

{{-- STYLE TAMBAHAN --}}
<style>
    .modal-content {
        border-radius: 0.6rem;
        overflow: hidden;
    }

    .table td, .table th {
        vertical-align: middle !important;
    }

    .btn-outline-primary, .btn-outline-danger {
        padding: 0.25rem 0.5rem;
    }

    .btn-outline-primary:hover, .btn-outline-danger:hover {
        transform: scale(1.05);
        transition: 0.2s ease-in-out;
    }
</style>
@endsection
