@extends('layouts.bootstrap')

@section('title', 'Manajemen Benih')

@section('content')
<div class="container py-3">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 mt-2">
        <div>
            <h4 class="fw-bold mb-1" style="font-size: 1.05rem;">
                <i class="bi bi-box-seam text-primary me-2"></i> Manajemen Benih
            </h4>
            <div class="small text-muted">
                Kelola data, stok, dan harga benih untuk setiap jenis tanaman.
            </div>
        </div>
        <button type="button"
                class="btn btn-primary btn-sm shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalBenihCreate">
            <i class="bi bi-plus-circle me-1"></i> Tambah Benih
        </button>
    </div>

    {{-- FLASH MESSAGE --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show small shadow-sm" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ERROR GLOBAL --}}
    @if ($errors->any())
        <div class="alert alert-danger small shadow-sm">
            <strong><i class="bi bi-x-circle me-1"></i>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TABEL --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3 p-md-4">
            @if ($benih->isEmpty())
                <div class="text-center text-muted small py-3">
                    Belum ada data benih.  
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalBenihCreate">Tambah sekarang</a>.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light small text-center align-middle">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Jenis Tanaman</th>
                                <th>Jenis Benih</th>
                                <th>Tipe</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Gambar</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach ($benih as $i => $row)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td class="text-center">{{ $row->jenisTanaman->nama_tanaman ?? '-' }}</td>
                                    <td class="text-center">{{ ucfirst($row->jenis_benih) }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $row->tipe_pembayaran == 'Gratis' ? 'bg-secondary' : 'bg-success' }}">
                                            {{ $row->tipe_pembayaran }}
                                        </span>
                                    </td>
                                    <td class="text-end text-center">
                                        @if ($row->harga)
                                            Rp {{ number_format($row->harga, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $row->stok }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($row->gambar)
                                            <img src="{{ asset('storage/' . $row->gambar) }}" width="55" class="rounded shadow-sm">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            {{-- EDIT --}}
                                            <button type="button"
                                                    class="btn btn-outline-primary btn-sm btn-edit-benih"
                                                    data-id="{{ $row->id }}"
                                                    data-jenis-tanaman-id="{{ $row->jenis_tanaman_id }}"
                                                    data-jenis-benih="{{ $row->jenis_benih }}"
                                                    data-tipe-pembayaran="{{ $row->tipe_pembayaran }}"
                                                    data-stok="{{ $row->stok }}"
                                                    data-harga="{{ $row->harga ?? 0 }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalBenihEdit"
                                                    title="Edit Data">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            {{-- HAPUS --}}
                                            <form action="{{ route('admin.verifikator.benih.destroy', $row->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Hapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus Data">
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

<style>
    .container {
        margin-top: -4.3rem !important;
    }
    table td, table th {
        vertical-align: middle !important;
    }
    .d-flex.gap-2 .btn {
        min-width: 32px;
    }
</style>

{{-- ========== MODAL CREATE ========== --}}
<div class="modal fade" id="modalBenihCreate" tabindex="-1" aria-labelledby="modalBenihCreateLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"> {{-- scroll natural --}}
        <div class="modal-content">
            <form action="{{ route('admin.verifikator.benih.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header py-2 border-0 shadow-sm">
                    <h5 class="modal-title small fw-semibold mb-0">
                        <i class="bi bi-plus-circle text-primary me-1"></i> Tambah Benih
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body small py-3 px-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tanaman</label>
                        <select name="jenis_tanaman_id" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Jenis Tanaman --</option>
                            @foreach ($jenisTanaman as $jt)
                                <option value="{{ $jt->id }}">{{ $jt->nama_tanaman }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Benih</label>
                        <select name="jenis_benih" class="form-select form-select-sm" required>
                            <option value="Biji">Biji</option>
                            <option value="Siap Tanam">Siap Tanam</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Pembayaran</label>
                        <select name="tipe_pembayaran" class="form-select form-select-sm" required>
                            <option value="Gratis">Gratis</option>
                            <option value="Berbayar">Berbayar</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga (opsional)</label>
                        <input type="number" name="harga" min="0" class="form-control form-control-sm" placeholder="Contoh: 10000">
                        <div class="form-text">Kosongkan atau isi 0 jika gratis.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stok</label>
                        <input type="number" name="stok" min="0" class="form-control form-control-sm" value="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Gambar (opsional)</label>
                        <input type="file" name="gambar" class="form-control form-control-sm" accept="image/*">
                        <div class="form-text">Format: JPG/PNG, maksimal 2MB</div>
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

{{-- ========== MODAL EDIT ========== --}}
<div class="modal fade" id="modalBenihEdit" tabindex="-1" aria-labelledby="modalBenihEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"> {{-- scroll natural --}}
        <div class="modal-content">
            <form id="formBenihEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header py-2 border-0 shadow-sm">
                    <h5 class="modal-title small fw-semibold mb-0">
                        <i class="bi bi-pencil-square text-primary me-1"></i> Edit Benih
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body small py-3 px-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Tanaman</label>
                        <select name="jenis_tanaman_id" id="edit_jenis_tanaman_id" class="form-select form-select-sm" required>
                            @foreach ($jenisTanaman as $jt)
                                <option value="{{ $jt->id }}">{{ $jt->nama_tanaman }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Benih</label>
                        <select name="jenis_benih" id="edit_jenis_benih" class="form-select form-select-sm" required>
                            <option value="Biji">Biji</option>
                            <option value="Siap Tanam">Siap Tanam</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Pembayaran</label>
                        <select name="tipe_pembayaran" id="edit_tipe_pembayaran" class="form-select form-select-sm" required>
                            <option value="Gratis">Gratis</option>
                            <option value="Berbayar">Berbayar</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga (opsional)</label>
                        <input type="number" name="harga" id="edit_harga" min="0" class="form-control form-control-sm" placeholder="Contoh: 10000">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stok</label>
                        <input type="number" name="stok" id="edit_stok" min="0" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Gambar Baru (opsional)</label>
                        <input type="file" name="gambar" class="form-control form-control-sm" accept="image/*">
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

<style>
/* Scroll aktif dan tetap natural */
.modal-dialog-scrollable {
    max-height: 90vh; /* modal tidak lebih tinggi dari layar */
}

.modal-dialog-scrollable .modal-body {
    max-height: calc(90vh - 130px); /* kurangi tinggi header + footer */
    overflow-y: auto !important;
    padding-bottom: 1rem;
}

/* Tambahan styling biar enak dilihat */
.modal-content {
    border-radius: 0.5rem;
    overflow: hidden;
}

.modal-header {
    background: #fff;
    border-bottom: 1px solid #f1f1f1;
}

.modal-footer {
    background: #fff;
    border-top: 1px solid #f1f1f1;
}
</style>





{{-- SCRIPT UNTUK MODAL EDIT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.btn-edit-benih');
    const formEdit = document.getElementById('formBenihEdit');

    const jenisTanamanSelect = document.getElementById('edit_jenis_tanaman_id');
    const jenisBenihSelect = document.getElementById('edit_jenis_benih');
    const tipePembayaranSelect = document.getElementById('edit_tipe_pembayaran');
    const stokInput = document.getElementById('edit_stok');
    const hargaInput = document.getElementById('edit_harga');

    const updateUrlTemplate = "{{ route('admin.verifikator.benih.update', ['benih' => '__ID__']) }}";

    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            formEdit.action = updateUrlTemplate.replace('__ID__', id);

            jenisTanamanSelect.value = this.dataset.jenisTanamanId || '';
            jenisBenihSelect.value = this.dataset.jenisBenih || 'Biji';
            tipePembayaranSelect.value = this.dataset.tipePembayaran || 'Gratis';
            stokInput.value = this.dataset.stok || 0;
            hargaInput.value = this.dataset.harga || '';
        });
    });
});
</script>
@endsection
