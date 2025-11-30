@extends('layouts.bootstrap')

@section('content')
    <style>
        body {
            margin-top: -70px;
        }
    </style>
    <div class="container py-4">

        {{-- ALERT VALIDASI (GLOBAL) --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-primary fw-bold mb-0">
                🌾 Data Varietas Kabupaten <span class="text-dark">{{ auth()->user()->kabupaten->nama_kabupaten }}</span>
            </h3>
            <div class="d-flex gap-2">
                {{-- TOMBOL TAMBAH (BUKA MODAL) --}}
                <button type="button" class="btn btn-success rounded-pill shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#modalCreateVarietas">
                    <i class="bi bi-plus-circle"></i> Tambah Varietas
                </button>
            </div>
        </div>

        {{-- ALERT SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center mb-4"
                role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- FILTER VARIETAS --}}
        <form method="GET" class="mb-4 row g-2 align-items-end">

            {{-- Filter Tanaman --}}
            <div class="col-md-3">
                <label for="tanaman_id" class="form-label fw-semibold text-secondary mb-1">
                    🌿 Tanaman
                </label>
                <select name="tanaman_id" id="tanaman_id" class="form-select shadow-sm">
                    <option value="">Semua Tanaman</option>
                    @foreach ($tanamanList as $tanaman)
                        <option value="{{ $tanaman->id }}" {{ request('tanaman_id') == $tanaman->id ? 'selected' : '' }}>
                            {{ $tanaman->nama_tanaman }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="col-md-3">
                <label for="status" class="form-label fw-semibold text-secondary mb-1">
                    📢 Status
                </label>
                <select name="status" id="status" class="form-select shadow-sm">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publish</option>
                </select>
            </div>

            {{-- Pencarian Umum --}}
            <div class="col-md-4">
                <label for="q" class="form-label fw-semibold text-secondary mb-1">
                    🔍 Pencarian (Nama Varietas / No SK / Pemilik)
                </label>
                <input type="text" name="q" id="q" class="form-control shadow-sm"
                    placeholder="Ketik kata kunci..." value="{{ request('q') }}">
            </div>

            {{-- Tombol --}}
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary mt-auto w-100 rounded-pill">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('admin.varietas.index') }}" class="btn btn-light border mt-auto rounded-pill">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>


        {{-- TABEL VARIETAS --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-success text-center align-middle">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Nomor & Tanggal SK</th>
                            <th>Nama Varietas</th>
                            <th>Jenis Benih</th>
                            <th>Pemilik</th>
                            <th width="10%">Jml Materi Genetik</th>
                            <th>Keterangan</th>
                            <th width="25%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($varietas as $v)
                            <tr>
                                <td class="text-center fw-semibold">
                                    {{ ($varietas->firstItem() ?? 1) + $loop->index }}
                                </td>

                                <td>{{ $v->nomor_tanggal_sk ?? '-' }}</td>
                                <td class="fw-semibold text-dark">{{ $v->nama_varietas ?? '-' }}</td>
                                <td>{{ $v->jenis_benih ?? '-' }}</td>
                                <td>{{ $v->pemilik_varietas ?? '-' }}</td>
                                <td class="text-center">{{ $v->jumlah_materi_genetik ?? 0 }}</td>
                                <td>{{ $v->keterangan ?? '-' }}</td>
                                <td>
                                    <div class="d-flex flex-wrap justify-content-center gap-2">

                                        {{-- DETAIL --}}
                                        <a href="{{ route('admin.varietas.show', $v->id) }}"
                                            class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- EDIT (MODAL) --}}
                                        <button type="button"
                                            class="btn btn-sm btn-outline-warning rounded-circle btn-edit-varietas"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Data"
                                            data-id="{{ $v->id }}"
                                            data-update_url="{{ route('admin.varietas.update', $v->id) }}"
                                            data-tanaman_id="{{ $v->tanaman_id }}"
                                            data-nomor_tanggal_sk="{{ $v->nomor_tanggal_sk }}"
                                            data-nama_varietas="{{ $v->nama_varietas }}"
                                            data-jenis_benih="{{ $v->jenis_benih }}"
                                            data-pemilik_varietas="{{ $v->pemilik_varietas }}"
                                            data-jumlah_materi_genetik="{{ $v->jumlah_materi_genetik }}"
                                            data-keterangan="{{ $v->keterangan }}" data-status="{{ $v->status }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- DESKRIPSI --}}
                                        @if ($v->deskripsi)
                                            <a href="{{ route('admin.varietas.deskripsi.edit', $v->deskripsi->id) }}"
                                                class="btn btn-sm btn-outline-info rounded-circle" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Edit Deskripsi">
                                                <i class="bi bi-journal-text"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.varietas.deskripsi.create', $v->id) }}"
                                                class="btn btn-sm btn-outline-success rounded-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Tambah Deskripsi">
                                                <i class="bi bi-plus-circle"></i>
                                            </a>
                                        @endif

                                        {{-- HAPUS --}}
                                        <form action="{{ route('admin.varietas.destroy', $v->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus varietas ini?')"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus Varietas">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inboxes fs-4 d-block mb-2"></i>
                                    Tidak ada data varietas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $varietas->withQueryString()->links() }}
        </div>


    </div>

    {{-- ========================= MODAL CREATE (2 KOLOM + SCROLL) ========================= --}}
    <div class="modal fade" id="modalCreateVarietas" tabindex="-1" aria-labelledby="modalCreateVarietasLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalCreateVarietasLabel">🌱 Tambah Data Varietas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="{{ route('admin.varietas.store') }}" method="POST">
                    @csrf
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row g-3">
                            {{-- KOLOM KIRI --}}
                            <div class="col-md-6">
                                {{-- PILIH TANAMAN --}}
                                <div class="mb-3">
                                    <label for="create_tanaman_id" class="form-label fw-semibold">🌿 Tanaman <span
                                            class="text-danger">*</span></label>
                                    <select name="tanaman_id" id="create_tanaman_id" class="form-select shadow-sm"
                                        required>
                                        <option value="">-- Pilih Tanaman --</option>
                                        @foreach ($tanamanList as $tanaman)
                                            <option value="{{ $tanaman->id }}"
                                                {{ old('tanaman_id') == $tanaman->id ? 'selected' : '' }}>
                                                {{ $tanaman->nama_tanaman }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- NOMOR & TANGGAL SK --}}
                                <div class="mb-3">
                                    <label for="create_nomor_tanggal_sk" class="form-label fw-semibold">📄 Nomor & Tanggal
                                        SK</label>
                                    <input type="text" name="nomor_tanggal_sk" id="create_nomor_tanggal_sk"
                                        class="form-control shadow-sm" placeholder="Contoh: No. 12/Kpts/KB.020/2025"
                                        value="{{ old('nomor_tanggal_sk') }}">
                                </div>

                                {{-- NAMA VARIETAS --}}
                                <div class="mb-3">
                                    <label for="create_nama_varietas" class="form-label fw-semibold">🏷️ Nama Varietas
                                        <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_varietas" id="create_nama_varietas"
                                        class="form-control shadow-sm" required
                                        placeholder="Contoh: Kopi Liberika Rangsang Meranti"
                                        value="{{ old('nama_varietas') }}">
                                </div>
                            </div>

                            {{-- KOLOM KANAN --}}
                            <div class="col-md-6">
                                {{-- JENIS BENIH --}}
                                <div class="mb-3">
                                    <label for="create_jenis_benih" class="form-label fw-semibold">🌾 Jenis Benih</label>
                                    <input type="text" name="jenis_benih" id="create_jenis_benih"
                                        class="form-control shadow-sm" placeholder="Contoh: Bersari bebas, Hibrida, Lokal"
                                        value="{{ old('jenis_benih') }}">
                                </div>

                                {{-- PEMILIK VARIETAS --}}
                                <div class="mb-3">
                                    <label for="create_pemilik_varietas" class="form-label fw-semibold">👤 Pemilik
                                        Varietas</label>
                                    <input type="text" name="pemilik_varietas" id="create_pemilik_varietas"
                                        class="form-control shadow-sm" placeholder="Contoh: Dinas Pertanian Kab. Meranti"
                                        value="{{ old('pemilik_varietas') }}">
                                </div>

                                {{-- JUMLAH MATERI GENETIK --}}
                                <div class="mb-3">
                                    <label for="create_jumlah_materi_genetik" class="form-label fw-semibold">🧬 Jumlah
                                        Materi Genetik</label>
                                    <input type="number" name="jumlah_materi_genetik" id="create_jumlah_materi_genetik"
                                        class="form-control shadow-sm" placeholder="Masukkan jumlah pohon/rumpun"
                                        value="{{ old('jumlah_materi_genetik') }}">
                                </div>
                            </div>

                            {{-- KETERANGAN (FULL WIDTH) --}}
                            <div class="col-12">
                                <div class="mb-1">
                                    <label for="create_keterangan" class="form-label fw-semibold">📝 Keterangan</label>
                                    <textarea name="keterangan" id="create_keterangan" rows="3" class="form-control shadow-sm"
                                        placeholder="Tulis keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border rounded-pill" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success rounded-pill shadow-sm">
                            <i class="bi bi-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================= MODAL EDIT (2 KOLOM + SCROLL) ========================= --}}
    <div class="modal fade" id="modalEditVarietas" tabindex="-1" aria-labelledby="modalEditVarietasLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalEditVarietasLabel">✏️ Edit Data Varietas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="formEditVarietas" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row g-3">
                            {{-- KOLOM KIRI --}}
                            <div class="col-md-6">
                                {{-- PILIH TANAMAN --}}
                                <div class="mb-3">
                                    <label for="edit_tanaman_id" class="form-label fw-semibold">🌿 Tanaman <span
                                            class="text-danger">*</span></label>
                                    <select name="tanaman_id" id="edit_tanaman_id" class="form-select shadow-sm"
                                        required>
                                        <option value="">-- Pilih Tanaman --</option>
                                        @foreach ($tanamanList as $tanaman)
                                            <option value="{{ $tanaman->id }}">{{ $tanaman->nama_tanaman }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- NOMOR & TANGGAL SK --}}
                                <div class="mb-3">
                                    <label for="edit_nomor_tanggal_sk" class="form-label fw-semibold">📄 Nomor & Tanggal
                                        SK</label>
                                    <input type="text" name="nomor_tanggal_sk" id="edit_nomor_tanggal_sk"
                                        class="form-control shadow-sm" placeholder="Contoh: No. 12/Kpts/KB.020/2025">
                                </div>

                                {{-- NAMA VARIETAS --}}
                                <div class="mb-3">
                                    <label for="edit_nama_varietas" class="form-label fw-semibold">🏷️ Nama Varietas <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nama_varietas" id="edit_nama_varietas"
                                        class="form-control shadow-sm" required
                                        placeholder="Contoh: Kopi Liberika Rangsang Meranti">
                                </div>
                            </div>

                            {{-- KOLOM KANAN --}}
                            <div class="col-md-6">
                                {{-- JENIS BENIH --}}
                                <div class="mb-3">
                                    <label for="edit_jenis_benih" class="form-label fw-semibold">🌾 Jenis Benih</label>
                                    <input type="text" name="jenis_benih" id="edit_jenis_benih"
                                        class="form-control shadow-sm"
                                        placeholder="Contoh: Bersari bebas, Hibrida, Lokal">
                                </div>

                                {{-- PEMILIK VARIETAS --}}
                                <div class="mb-3">
                                    <label for="edit_pemilik_varietas" class="form-label fw-semibold">👤 Pemilik
                                        Varietas</label>
                                    <input type="text" name="pemilik_varietas" id="edit_pemilik_varietas"
                                        class="form-control shadow-sm" placeholder="Contoh: Dinas Pertanian Kab. Meranti">
                                </div>

                                {{-- JUMLAH MATERI GENETIK --}}
                                <div class="mb-3">
                                    <label for="edit_jumlah_materi_genetik" class="form-label fw-semibold">🧬 Jumlah
                                        Materi Genetik</label>
                                    <input type="number" name="jumlah_materi_genetik" id="edit_jumlah_materi_genetik"
                                        class="form-control shadow-sm" placeholder="Masukkan jumlah pohon/rumpun">
                                </div>
                            </div>

                            {{-- KETERANGAN (FULL WIDTH) --}}
                            <div class="col-12">
                                <div class="mb-1">
                                    <label for="edit_keterangan" class="form-label fw-semibold">📝 Keterangan</label>
                                    <textarea name="keterangan" id="edit_keterangan" rows="3" class="form-control shadow-sm"
                                        placeholder="Tulis keterangan tambahan (opsional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border rounded-pill" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-warning rounded-pill shadow-sm">
                            <i class="bi bi-save"></i> Update Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT TOOLTIP + MODAL EDIT --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Inisialisasi Tooltip
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });

                // Handle tombol EDIT
                const editButtons = document.querySelectorAll('.btn-edit-varietas');
                const editForm = document.getElementById('formEditVarietas');

                editButtons.forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const updateUrl = this.dataset.update_url;

                        // Set action form
                        editForm.action = updateUrl;

                        // Set nilai field dari data-attribute
                        document.getElementById('edit_tanaman_id').value = this.dataset.tanaman_id ||
                            '';
                        document.getElementById('edit_nomor_tanggal_sk').value = this.dataset
                            .nomor_tanggal_sk || '';
                        document.getElementById('edit_nama_varietas').value = this.dataset
                            .nama_varietas || '';
                        document.getElementById('edit_jenis_benih').value = this.dataset.jenis_benih ||
                            '';
                        document.getElementById('edit_pemilik_varietas').value = this.dataset
                            .pemilik_varietas || '';
                        document.getElementById('edit_jumlah_materi_genetik').value = this.dataset
                            .jumlah_materi_genetik || '';
                        document.getElementById('edit_keterangan').value = this.dataset.keterangan ||
                            '';
                        
                        // Buka modal edit
                        const editModal = new bootstrap.Modal(document.getElementById(
                            'modalEditVarietas'));
                        editModal.show();
                    });
                });
            });
        </script>
    @endpush
@endsection
