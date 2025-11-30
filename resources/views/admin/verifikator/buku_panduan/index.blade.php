@extends('layouts.bootstrap')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<style>
    body{
        margin-top: -70px;
    }
</style>
    <div class="container-fluid py-3">

        {{-- HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h1 class="h5 mb-1">Buku Panduan</h1>
            </div>

            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createBukuPanduanModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Buku Panduan
            </button>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif

        {{-- FILTER & SEARCH --}}
        <form method="GET" action="{{ route('admin.verifikator.buku_panduan.index') }}" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Cari</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text"
                               name="q"
                               class="form-control"
                               placeholder="Cari nama buku panduan..."
                               value="{{ $search }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Tahun</label>
                    <select name="year" class="form-select form-select-sm">
                        <option value="">Semua Tahun</option>
                        @foreach ($years as $y)
                            <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-outline-secondary me-1">
                        Terapkan
                    </button>
                    <a href="{{ route('admin.verifikator.buku_panduan.index') }}"
                       class="btn btn-sm btn-light">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- TABEL --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-sm mb-0 align-middle">
                        <thead class="table-light text-center small">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th>Nama Buku Panduan</th>
                                <th style="width: 140px;">Dibuat</th>
                                <th style="width: 120px;">File</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse ($bukuList as $index => $row)
                                <tr>
                                    <td class="text-center">
                                        {{ $bukuList->firstItem() + $index }}
                                    </td>
                                    <td class="text-center text-wrap" style="white-space: normal; max-width: 420px;">
                                        <strong>{{ $row->nama_buku }}</strong>
                                    </td>
                                    <td class="text-center">
                                        {{ $row->created_at?->format('d-m-Y') ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($row->file_path && Storage::disk('public')->exists($row->file_path))
                                            <a href="{{ Storage::url($row->file_path) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-file-earmark-pdf"></i> Lihat
                                            </a>
                                        @else
                                            <span class="text-muted">Tidak ada file</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- Edit --}}
                                        <button type="button"
                                                class="btn btn-sm btn-outline-warning btn-edit-buku mb-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editBukuPanduanModal"
                                                data-id="{{ $row->id }}"
                                                data-nama-buku="{{ $row->nama_buku }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- Hapus --}}
                                        <form action="{{ route('admin.verifikator.buku_panduan.destroy', $row->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus buku panduan ini?')">
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
                                    <td colspan="5" class="text-center text-muted py-3">
                                        Belum ada data buku panduan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-top px-3 py-2">
                    {{ $bukuList->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- ========== MODAL CREATE ========== --}}
    <div class="modal fade" id="createBukuPanduanModal" tabindex="-1"
         aria-labelledby="createBukuPanduanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('admin.verifikator.buku_panduan.store') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createBukuPanduanModalLabel">Tambah Buku Panduan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Buku Panduan</label>
                            <textarea name="nama_buku"
                                      class="form-control @error('nama_buku') is-invalid @enderror"
                                      rows="2"
                                      required>{{ old('nama_buku') }}</textarea>
                            @error('nama_buku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">File (PDF)</label>
                            <input type="file"
                                   name="file"
                                   accept="application/pdf"
                                   class="form-control @error('file') is-invalid @enderror"
                                   required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Hanya file PDF. Maksimal 10MB.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit"
                                class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========== MODAL EDIT ========== --}}
    <div class="modal fade" id="editBukuPanduanModal" tabindex="-1"
         aria-labelledby="editBukuPanduanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form id="editBukuPanduanForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBukuPanduanModalLabel">Edit Buku Panduan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Buku Panduan</label>
                            <textarea name="nama_buku" class="form-control" rows="2" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">File (PDF) - Opsional</label>
                            <input type="file" name="file" accept="application/pdf" class="form-control">
                            <small class="text-muted">
                                Kosongkan jika tidak ingin mengganti file.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary btn-sm"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit"
                                class="btn btn-primary btn-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table td.text-wrap {
            white-space: normal !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editModal = document.getElementById('editBukuPanduanModal');

            if (!editModal) return;

            editModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                const id   = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama-buku');

                const form = document.getElementById('editBukuPanduanForm');
                const baseUrl = "{{ url('admin/verifikator/buku-panduan') }}";

                form.action = baseUrl + '/' + id;
                form.querySelector('[name="nama_buku"]').value = nama || '';
            });
        });
    </script>
@endpush
