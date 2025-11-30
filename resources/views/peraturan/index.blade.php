@extends('layouts.halamanutama')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<style>
    .badge-soft-primary {
            background-color: rgba(13, 110, 253, 0.12);
            color: #0d6efd;
        }
</style>
    <div class="container my-4">

        <div class="mb-4 text-center">
                <span class="badge badge-soft-primary px-3 py-2 mb-2">
                    <i class="bi bi-journal-text me-2"></i> Peraturan
                </span>
                <h2 class="fw-bold mb-1">
                    Peraturan dan Kebijakan
                </h2>
            </div>

        {{-- FILTER & SEARCH --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('peraturan.public') }}" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small mb-1">Pencarian</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   placeholder="Cari nomor atau tentang peraturan..."
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

                    <div class="col-md-4 d-flex justify-content-end gap-2 mt-2 mt-md-0">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-funnel me-1"></i> Terapkan
                        </button>
                        <a href="{{ route('peraturan.public') }}" class="btn btn-sm btn-outline-secondary">
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
                    <table class="table table-bordered table-striped table-hover table-sm mb-0 align-middle">
                        <thead class="table-light text-center small">
                            <tr class="small text-muted">
                                <th>No</th>
                                <th>Nomor & Tahun</th>
                                <th>Tanggal Penetapan</th>
                                <th>Tentang</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse ($peraturanList as $index => $row)
                                <tr>
                                    <td class="text-center">
                                        {{ $peraturanList->firstItem() + $index }}
                                    </td>

                                    <td class="text-wrap text-center" style="max-width: 200px; white-space: normal; word-wrap: break-word;">
                                        <strong>{{ $row->nomor_tahun }}</strong>
                                    </td>

                                    <td class="text-center">
                                        {{ $row->tanggal_penetapan?->format('d-m-Y') ?? '-' }}
                                    </td>

                                    {{-- 🔸 Tentang: wrap teks, bukan melar ke samping --}}
                                    <td class="text-wrap text-center"
                                        style="max-width: 200px; white-space: normal; word-wrap: break-word;">
                                        {{ $row->tentang }}
                                    </td>

                                    <td class="text-center">
                                        @if ($row->file_path && Storage::disk('public')->exists($row->file_path))
                                            <a href="{{ Storage::url($row->file_path) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-pdf"></i> Lihat File
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        Belum ada data peraturan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="border-top px-3 py-2 d-flex justify-content-end align-items-center">
                    <div>
                        {{ $peraturanList->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        td.text-wrap, th.text-wrap {
            white-space: normal !important;
            word-wrap: break-word;
        }
    </style>
@endpush
