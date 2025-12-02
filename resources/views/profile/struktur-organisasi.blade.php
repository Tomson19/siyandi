@extends('layouts.halamanutama')
@section('title', 'Struktur Organisasi - SIYANDI')

@section('content')
    <section class="container my-5 py-4 fade-page">
        {{-- Header judul --}}
        <div class="text-center mb-5">
            <h2 class="fw-bold text-success display-6 mb-2 text-uppercase">Struktur Organisasi</h2>
            <div class="mx-auto mb-3" style="width: 80px; height: 4px; background: #198754; border-radius: 2px;"></div>
            <p class="text-muted lead mb-0 text-uppercase">
                UPT Produksi Benih Tanaman Perkebunan Provinsi Riau
            </p>
        </div>

        {{-- Gambar struktur --}}
        <div class="container my-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 text-center">
                    <img src="{{ asset('images/struktur-organisasi.jpeg') }}" alt="Struktur Organisasi"
                        class="img-fluid w-100 shadow-lg rounded-4 zoom-hover fade-in struktur-img">
                </div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Efek fade-in
            const fadeElems = document.querySelectorAll('.fade-in');
            fadeElems.forEach(el => {
                setTimeout(() => el.classList.add('show'), 300);
            });

            // Efek fade seluruh halaman konten
            document.querySelector('.fade-page')?.classList.add('show');
        });
    </script>
@endpush
