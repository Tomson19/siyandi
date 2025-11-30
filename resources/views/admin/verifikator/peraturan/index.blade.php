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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h5 mb-1">Peraturan</h1>
            </div>
            <button type="button"
                    class="btn btn-sm btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createPeraturanModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Peraturan
            </button>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- FILTER & SEARCH --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('admin.verifikator.peraturan.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Pencarian</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   placeholder="Cari nomor atau tentang..."
                                   value="{{ $search }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small mb-1">Tahun Penetapan</label>
                        <select name="tahun" class="form-select form-select-sm">
                            <option value="">Semua Tahun</option>
                            @foreach ($tahunList as $th)
                                <option value="{{ $th }}" {{ (string)$selectedYear === (string)$th ? 'selected' : '' }}>
                                    {{ $th }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5 d-flex justify-content-end gap-2 mt-2 mt-md-0">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-funnel me-1"></i> Terapkan
                        </button>
                        <a href="{{ route('admin.verifikator.peraturan.index') }}" class="btn btn-sm btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-sm mb-0 align-middle text-center">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th style="width: 5%;">No</th>
                                <th style="width: 10%;">Nomor & Tahun</th>
                                <th style="width: 8%;">Tanggal Penetapan</th>
                                <th style="min-width: 15%;">Tentang</th>
                                <th style="width: 8%;">File</th>
                                <th style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($peraturanList as $index => $row)
                                <tr>
                                    <td>{{ $peraturanList->firstItem() + $index }}</td>

                                    <td class=" text-center text-wrap" style="max-width: 200px; white-space: normal; word-wrap: break-word;">
                                        <strong>{{ $row->nomor_tahun }}</strong>
                                    </td>

                                    <td>
                                        {{ $row->tanggal_penetapan?->format('d-m-Y') ?? '-' }}
                                    </td>

                                    {{-- 🔸 Kolom "Tentang" dibuat wrap & tidak melebar --}}
                                    <td class="text-center text-wrap"
                                        style="max-width: 200px; white-space: normal; word-wrap: break-word;">
                                        {{ $row->tentang }}
                                    </td>

                                    <td>
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

                                    <td>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-warning btn-edit-peraturan mb-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editPeraturanModal"
                                                data-id="{{ $row->id }}"
                                                data-nomor-tahun="{{ $row->nomor_tahun }}"
                                                data-tanggal-penetapan="{{ $row->tanggal_penetapan?->format('Y-m-d') }}"
                                                data-tentang="{{ $row->tentang }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('admin.verifikator.peraturan.destroy', $row->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus peraturan ini?')">
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
                                    <td colspan="6" class="text-center text-muted py-3">
                                        Belum ada data peraturan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="border-top px-3 py-2 d-flex justify-content-between align-items-center">
                    <small class="text-muted d-none d-md-inline">
                        Halaman {{ $peraturanList->currentPage() }} dari {{ $peraturanList->lastPage() }}
                    </small>
                    <div>
                        {{ $peraturanList->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== MODAL CREATE ========== --}}
    <div class="modal fade" id="createPeraturanModal" tabindex="-1" aria-labelledby="createPeraturanModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form action="{{ route('admin.verifikator.peraturan.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPeraturanModalLabel">Tambah Peraturan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Nomor & Tahun</label>
                                <input type="text"
                                       name="nomor_tahun"
                                       class="form-control @error('nomor_tahun') is-invalid @enderror"
                                       value="{{ old('nomor_tahun') }}"
                                       required>
                                @error('nomor_tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Tanggal Penetapan</label>
                                <input type="date"
                                       name="tanggal_penetapan"
                                       class="form-control @error('tanggal_penetapan') is-invalid @enderror"
                                       value="{{ old('tanggal_penetapan') }}"
                                       required>
                                @error('tanggal_petetapan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tentang</label>
                            <textarea name="tentang"
                                      class="form-control @error('tentang') is-invalid @enderror"
                                      rows="3"
                                      required>{{ old('tentang') }}</textarea>
                            @error('tentang')
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
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========== MODAL EDIT ========== --}}
    <div class="modal fade" id="editPeraturanModal" tabindex="-1" aria-labelledby="editPeraturanModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form id="editPeraturanForm"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPeraturanModalLabel">Edit Peraturan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Nomor & Tahun</label>
                                <input type="text" name="nomor_tahun" class="form-control" required>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Tanggal Penetapan</label>
                                <input type="date" name="tanggal_penetapan" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tentang</label>
                            <textarea name="tentang" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">File (PDF) - Opsional</label>
                            <input type="file"
                                   name="file"
                                   accept="application/pdf"
                                   class="form-control">
                            <small class="text-muted">
                                Kosongkan jika tidak ingin mengganti file.
                            </small>
                        </div>

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

@push('styles')
    <style>
        /* pastikan text-wrap bener-bener nge-wrap */
        td.text-wrap, th.text-wrap {
            white-space: normal !important;
            word-wrap: break-word;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editModal = document.getElementById('editPeraturanModal');

            if (editModal) {
                editModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;

                    const id    = button.getAttribute('data-id');
                    const nomor = button.getAttribute('data-nomor-tahun');
                    const tgl   = button.getAttribute('data-tanggal-penetapan');
                    const ttg   = button.getAttribute('data-tentang');

                    const form   = document.getElementById('editPeraturanForm');
                    const baseUrl = "{{ url('admin/verifikator/peraturan') }}";
                    form.action  = baseUrl + '/' + id;

                    form.querySelector('[name="nomor_tahun"]').value       = nomor || '';
                    form.querySelector('[name="tanggal_penetapan"]').value = tgl || '';
                    form.querySelector('[name="tentang"]').value           = ttg || '';
                });
            }
        });
    </script>
@endpush
