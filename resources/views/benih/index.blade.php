@extends('layouts.halamanutama')

@section('title', 'Data Stok Benih')

@section('content')
<div class="container py-5">

    {{-- HEADER --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold text-success mb-2">
            <i class="bi bi-box-seam me-2"></i> Data Ketersediaan Benih
        </h2>
        <p class="text-muted mb-0 small">
            Pilih jenis benih yang tersedia dan ajukan permohonan langsung secara online.
        </p>
    </div>

    {{-- DATA --}}
    @if ($benih->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox display-6 d-block mb-2"></i>
            Belum ada data benih yang tersedia.
        </div>
    @else
        <div class="row g-4 justify-content-center">
            @foreach ($benih as $row)
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <div class="card h-100 border-0 rounded-4 overflow-hidden shadow benih-card">

                        {{-- Gambar --}}
                        <div class="position-relative">
                            @if ($row->gambar)
                                <img src="{{ asset('storage/' . $row->gambar) }}" 
                                     class="card-img-top benih-image" 
                                     alt="Gambar Benih">
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-light text-muted benih-image">
                                    <i class="bi bi-image fs-4"></i>
                                </div>
                            @endif

                            {{-- Badge tipe pembayaran --}}
                            <span class="position-absolute top-0 end-0 m-2 badge rounded-pill px-2 py-1 small {{ $row->tipe_pembayaran == 'Gratis' ? 'bg-secondary' : 'bg-success' }}">
                                {{ $row->tipe_pembayaran }}
                            </span>
                        </div>

                        {{-- Isi --}}
                        <div class="card-body text-center py-3 px-3">
                            <h6 class="fw-semibold text-success mb-1 text-truncate" style="font-size: 1rem;">
                                {{ $row->jenisTanaman->nama_tanaman ?? '-' }}
                            </h6>
                            <p class="text-muted small mb-2">{{ $row->jenis_benih }}</p>

                            {{-- Harga --}}
                            <div class="mb-2">
                                @if ($row->tipe_pembayaran === 'Berbayar' && $row->harga > 0)
                                    <span class="fw-bold text-danger" style="font-size: 0.95rem;">
                                        Rp {{ number_format($row->harga, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted small">Gratis</span>
                                @endif
                            </div>

                            {{-- Stok --}}
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <span class="badge bg-primary px-3 py-2 rounded-pill small">
                                    {{ $row->stok }} stok
                                </span>
                            </div>
                        </div>

                        {{-- Tombol --}}
                        <div class="card-footer bg-transparent border-0 text-center pb-4">
                            <a href="{{ route('pemohon.permohonan.index') }}" 
                               class="btn btn-success btn-sm rounded-pill px-4 py-2 fw-semibold shadow-sm btn-aju">
                                <i class="bi bi-seedling me-1"></i> Ajukan Permohonan
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- STYLE --}}
<style>
    .benih-card {
        transition: all 0.3s ease;
        background: #fff;
        min-height: 340px;
    }

    .benih-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, .08);
    }

    .benih-image {
        height: 160px;
        width: 100%;
        object-fit: cover;
        background: #f8f9fa;
    }

    .btn-aju {
        font-size: 0.85rem;
        transition: all 0.2s ease-in-out;
    }

    .btn-aju:hover {
        background-color: #198754 !important;
        transform: scale(1.05);
    }

    @media (max-width: 768px) {
        .benih-image {
            height: 140px;
        }
        .benih-card {
            min-height: 300px;
        }
    }
</style>
@endsection
