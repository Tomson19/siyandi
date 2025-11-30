@extends('layouts.bootstrap')

@section('content')
<style>
    body{
        margin-top: -70px
    }
</style>
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="text-primary fw-bold mb-0">
                📋 Daftar Kebun Benih Sumber (KBS)
            </h3>
            @if($user->kabupaten)
                <small class="text-muted">
                    Kabupaten: <strong>{{ $user->kabupaten->nama_kabupaten }}</strong>
                </small>
            @endif
        </div>

        {{-- BTN TAMBAH KBS (buka modal) --}}
        <button type="button"
                class="btn btn-success rounded-pill shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalCreateKbs">
            <i class="bi bi-plus-circle"></i> Tambah KBS
        </button>
    </div>

    {{-- ALERT SUKSES --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ALERT ERROR VALIDASI --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTER KOMODITAS (OPSIONAL) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Filter Komoditas</label>
                    <select name="tanaman_id" class="form-select">
                        <option value="">-- Semua Komoditas --</option>
                        @foreach ($tanamanList as $t)
                            <option value="{{ $t->id }}" {{ request('tanaman_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->nama_tanaman }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary mt-auto rounded-pill">
                        <i class="bi bi-funnel"></i> Terapkan
                    </button>
                    <a href="{{ route('admin.kabupaten.kbs.index') }}" class="btn btn-light border mt-auto rounded-pill">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DAFTAR KBS (HEADER) --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-primary text-white fw-bold">
            Data Kebun Benih Sumber
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-success text-center align-middle">
                        <tr>
                            <th style="width:5%;">No</th>
                            <th style="width:20%;">Komoditas</th>
                            <th style="width:25%;">No. dan Tanggal SK</th>
                            <th style="width:25%;">Varietas</th>
                            <th style="width:15%;">Kabupaten</th>
                            <th style="width:10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kbs as $item)
                            <tr>
                                <td class="text-center">
                                    {{ ($kbs->firstItem() ?? 1) + $loop->index }}
                                </td>
                                <td class="text-center">{{ $item->tanaman->nama_tanaman ?? '-' }}</td>
                                <td class="text-center">
                                    <div><strong>{{ $item->nomor_sk ?? '-' }}</strong></div>
                                    <small class="text-muted">{{ $item->tanggal_sk ?? '-' }}</small>
                                </td>
                                <td class="text-center">{{ $item->nama_varietas }}</td>
                                <td class="text-center">{{ $item->kabupaten->nama_kabupaten ?? '-' }}</td>

                                {{-- AKSI: sejajar / satu baris --}}
                                <td class="text-center text-nowrap" style="min-width: 190px;">
                                    <div class="d-inline-flex align-items-center justify-content-center gap-1">

                                        {{-- DETAIL --}}
                                        <a href="{{ route('admin.kabupaten.kbs.show', $item->id) }}"
                                           class="btn btn-sm btn-info rounded-pill">
                                            <i class="bi bi-search"></i>
                                        </a>

                                        {{-- EDIT (modal) --}}
                                        <button type="button"
                                                class="btn btn-sm btn-warning rounded-pill"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditKbs-{{ $item->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- HAPUS --}}
                                        <form action="{{ route('admin.kabupaten.kbs.destroy', $item->id) }}"
                                              method="POST"
                                              class="d-inline-block"
                                              onsubmit="return confirm('Yakin ingin menghapus KBS ini beserta semua detailnya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-pill">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- MODAL EDIT UNTUK BARIS INI --}}
                            <div class="modal fade" id="modalEditKbs-{{ $item->id }}" tabindex="-1"
                                 aria-labelledby="modalEditKbsLabel-{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content rounded-4">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold" id="modalEditKbsLabel-{{ $item->id }}">
                                                ✏️ Edit Kebun Benih Sumber
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form action="{{ route('admin.kabupaten.kbs.update', $item->id) }}"
                                              method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body">
                                                <div class="row g-3">

                                                    {{-- Komoditas --}}
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Komoditas <span class="text-danger">*</span></label>
                                                        <select name="tanaman_id" class="form-select" required>
                                                            <option value="">-- Pilih Komoditas --</option>
                                                            @foreach($tanamanList as $t)
                                                                <option value="{{ $t->id }}"
                                                                    {{ $item->tanaman_id == $t->id ? 'selected' : '' }}>
                                                                    {{ $t->nama_tanaman }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- Varietas --}}
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Varietas <span class="text-danger">*</span></label>
                                                        <input type="text"
                                                            name="nama_varietas"
                                                            class="form-control"
                                                            value="{{ $item->nama_varietas }}"
                                                            required>
                                                    </div>

                                                    {{-- Nomor SK --}}
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Nomor SK</label>
                                                        <input type="text"
                                                            name="nomor_sk"
                                                            class="form-control"
                                                            value="{{ $item->nomor_sk }}">
                                                    </div>

                                                    {{-- Tanggal SK --}}
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Tanggal SK</label>
                                                        <input type="text"
                                                            name="tanggal_sk"
                                                            class="form-control"
                                                            value="{{ $item->tanggal_sk }}">
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button"
                                                        class="btn btn-light border rounded-pill"
                                                        data-bs-dismiss="modal">
                                                    <i class="bi bi-x-circle"></i> Batal
                                                </button>
                                                <button type="submit"
                                                        class="btn btn-primary rounded-pill">
                                                    <i class="bi bi-save"></i> Simpan Perubahan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    Belum ada data Kebun Benih Sumber.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="card-footer bg-white">
            @if($kbs instanceof \Illuminate\Contracts\Pagination\Paginator && $kbs->hasPages())
                <div class="d-flex justify-content-end mt-2">
                    {{ $kbs->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

{{-- MODAL CREATE KBS --}}
<div class="modal fade" id="modalCreateKbs" tabindex="-1" aria-labelledby="modalCreateKbsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalCreateKbsLabel">
                    ➕ Tambah Kebun Benih Sumber
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.kabupaten.kbs.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Komoditas --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Komoditas <span class="text-danger">*</span></label>
                            <select name="tanaman_id" class="form-select" required>
                                <option value="">-- Pilih Komoditas --</option>
                                @foreach($tanamanList as $t)
                                    <option value="{{ $t->id }}" {{ old('tanaman_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->nama_tanaman }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Varietas --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Varietas <span class="text-danger">*</span></label>
                            <input type="text"
                                name="nama_varietas"
                                class="form-control"
                                placeholder="Contoh: Liberoid Meranti 1"
                                value="{{ old('nama_varietas') }}"
                                required>
                        </div>

                        {{-- Nomor SK --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor SK</label>
                            <input type="text"
                                   name="nomor_sk"
                                   class="form-control"
                                   placeholder="Contoh: No. 40/Kpts/KB.020/4/2018"
                                   value="{{ old('nomor_sk') }}">
                        </div>

                        {{-- Tanggal SK --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal SK</label>
                            <input type="text"
                                   name="tanggal_sk"
                                   class="form-control"
                                   placeholder="Contoh: 30 April 2018"
                                   value="{{ old('tanggal_sk') }}">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-light border rounded-pill"
                            data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit"
                            class="btn btn-success rounded-pill">
                        <i class="bi bi-save"></i> Simpan KBS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
