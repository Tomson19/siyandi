@extends('layouts.halamanutama')
@section('title', 'SIYANDI - Home')

@section('content')

@php
$slides = [
  ['src'=>'images/upt.jpeg','title'=>''],
  ['src'=>'images/kopi.png','title'=>'Kopi (<em>Coffea spp.</em>)'],
  ['src'=>'images/rosela.jpeg','title'=>'Rosela (<em>Hibiscus sabdariffa</em>)'],
  ['src'=>'images/serai.jpg','title'=>'Sereh Wangi (<em>Andropogon nardus</em> L.)'],
  ['src'=>'images/lada.jpeg','title'=>'Lada (<em>Piper Nigrum</em> L.)'],
  ['src'=>'images/kelor.jpeg','title'=>'Kelor (<em>Moringa oleifera</em> L. <em>Folium</em>)'],
];
@endphp

<header class="container-fluid px-0">
  <div id="heroCarousel" class="carousel slide carousel-hero" data-bs-ride="carousel">
    <div class="carousel-inner">
      @foreach ($slides as $i => $s)
      <div class="carousel-item {{ $i===0?'active':'' }}">
        <img src="{{ asset($s['src']) }}" class="d-block w-100" alt="Slide {{ $i+1 }}">
        <div class="carousel-caption centered text-center">
          <h2 class="fw-bold text-light mb-2">{!! $s['title'] !!}</h2>
        </div>
      </div>
      @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Sebelumnya</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Berikutnya</span>
    </button>
  </div>
</header>

<section class="section-address">
  <div class="container my-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
      <div class="card-body p-4">
        <div class="row g-4 align-items-center">
          <div class="col-lg-5">
            <h2 class="fw-bold text-success mb-3"><i class="bi bi-geo-alt-fill me-2"></i> ALAMAT KANTOR UPT PRODUKSI BENIH TANAMAN PERKEBUNAN</h2>
            <p class="mb-2">Jl. Raya Pekanbaru-Bangkinang KM 28<br>Desa Kualu Nenas, Kecamatan Tambang,<br>Kabupaten Kampar, Provinsi Riau</p>
          </div>
          <div class="col-lg-7">
            <div class="ratio ratio-16x9 rounded-3">
              <iframe title="Lokasi Kantor UPT" src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3989.7166026404366!2d101.26870300000002!3d0.40912899999999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMMKwMjQnMzIuOSJOIDEwMcKwMTYnMDcuMyJF!5e0!3m2!1sid!2sid!4v1759488968845!5m2!1sid!2sid" style="border:0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
