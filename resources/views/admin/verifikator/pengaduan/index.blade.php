@extends('layouts.bootstrap')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <style>
        body {
            margin-top: -70px;
        }
    </style>
    <div class="container-fluid py-3">

        {{-- HEADER --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <h1 class="h5 mb-1">Pengaduan Masyarakat</h1>
                <small class="text-muted">
                    Total pengaduan: {{ $total }}.
                </small>
            </div>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- FILTER --}}
        <form method="GET" action="{{ route('admin.verifikator.pengaduan.index') }}" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Cari Nama / NIK / No HP / Isi Pengaduan</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control" placeholder="Ketik kata kunci..."
                            value="{{ $search }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Dari tanggal</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Sampai tanggal</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100 mb-1">
                        Terapkan
                    </button>
                    <a href="{{ route('admin.verifikator.pengaduan.index') }}" class="btn btn-sm btn-light w-100">
                        Reset
                    </a>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Daftar Pengaduan</h4>

            <div class="btn-group" role="group" aria-label="Export">
                <div class="me-1">
                <a href="{{ route('admin.verifikator.pengaduan.export_excel', request()->only(['q', 'from', 'to'])) }}"
                    class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
                </div>
                <div>
                <a href="{{ route('admin.verifikator.pengaduan.export_pdf', request()->only(['q', 'from', 'to'])) }}"
                    class="btn btn-sm btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>
            </div>
        </div>

        {{-- TABEL --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-sm mb-0 align-middle">
                        <thead class="table-light text-center small">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th style="width: 150px;">Tanggal</th>
                                <th>Nama</th>
                                <th style="width: 130px;">NIK</th>
                                <th style="width: 110px;">No HP</th>
                                <th>Alamat</th>
                                <th style="width: 280px;">Pengaduan</th>
                                <th style="width: 120px;">Lampiran</th>
                                <th style="width: 60px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse ($pengaduanList as $index => $row)
                                <tr>
                                    <td class="text-center">
                                        {{ $pengaduanList->firstItem() + $index }}
                                    </td>
                                    <td class="text-center">
                                        {{ $row->created_at?->format('d-m-Y H:i') ?? '-' }}
                                    </td>
                                    <td>{{ $row->nama }}</td>
                                    <td class="text-center">{{ $row->nik ?? '-' }}</td>
                                    <td class="text-center">{{ $row->no_hp ?? '-' }}</td>
                                    <td style="white-space: normal; max-width: 200px;">
                                        {{ $row->alamat ?? '-' }}
                                    </td>
                                    <td style="white-space: normal; max-width: 280px;">
                                        {{ $row->pengaduan }}
                                    </td>
                                    <td class="text-center">
                                        @if ($row->gambar_path && Storage::disk('public')->exists($row->gambar_path))
                                            <a href="{{ Storage::url($row->gambar_path) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                Lihat Gambar
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.verifikator.pengaduan.destroy', $row->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus pengaduan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">
                                        Belum ada pengaduan masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-top px-3 py-2">
                    {{ $pengaduanList->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
