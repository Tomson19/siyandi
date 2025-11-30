<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'SIYANDI')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- Style CSS --}}
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
@stack('styles')
  {{-- Leaflet Maps --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  {{-- Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <style>
  body {
    padding-top: 60px; /* tinggi navbar kamu, bisa disesuaikan */
  }
</style>

  {{-- Navbar --}}
  @include('partials.navbar')


  {{-- Konten utama halaman --}}
  <main>
    @yield('content')
  </main>

  {{-- Footer --}}
  @include('partials.footer')

  {{-- Script --}}
  <script>document.getElementById('year')?.append(new Date().getFullYear())</script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  @include('partials.survei_kepuasan_modal')
  @include('partials.pengaduan_modal')
  @stack('scripts')

  {{-- Efek scroll navbar --}}
  <script>
  document.addEventListener("scroll", () => {
    const navbar = document.querySelector(".navbar");
    if (window.scrollY > 10) {
      navbar?.classList.add("scrolled");
    } else {
      navbar?.classList.remove("scrolled");
    }
  });
  </script>
</body>
</html>