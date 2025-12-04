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

  {{-- Style CSS utama --}}
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">

  {{-- CSS tambahan dari partial (sidebar, dll) --}}
  @stack('styles')

  {{-- Leaflet Maps --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  {{-- Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  {{-- ======== TAMBAHAN UNTUK FIXED SIDEBAR ======== --}}
  <style>
    /* Pastikan body penuh layar */
    html, body {
      height: 100%;
      margin: 0;
      background-color: #f8f9fa;
      padding-top: 56px; /* tinggi navbar Bootstrap */
      overflow-x: hidden;
    }

    /* Wrapper utama untuk sidebar + konten */
    .app-wrapper {
      display: flex;
      min-height: calc(100vh - 56px); /* dikurangi tinggi navbar */
    }

    /* Geser main ke kanan biar nggak ketimpa sidebar */
    .app-main {
      flex-grow: 1;
      padding: 1.5rem;
      background-color: #f8f9fa;
      transition: margin-left 0.25s ease-in-out;
    }

    @media (min-width: 992px) {
      .app-main {
        margin-left: 260px; /* lebar sidebar */
      }
    }

    @media (max-width: 991.98px) {
      .app-main {
        margin-left: 0; /* di mobile sidebar slide-in */
      }
    }

    /* Biar footer di bawah */
    footer {
      margin-top: auto;
    }
  </style>
</head>

<body>
  {{-- Navbar --}}
  @include('partials.navbar')

  {{-- Wrapper: Sidebar + Konten --}}
  <div class="app-wrapper">
    {{-- Sidebar (tampil kalau user login) --}}
    @auth
      @include('partials.sidebar')
      {{-- Backdrop untuk mobile sidebar --}}
      <div id="sidebarBackdrop" class="sidebar-backdrop d-lg-none"></div>
    @endauth

    {{-- Konten utama halaman --}}
    <main class="app-main">
      @yield('content')
    </main>
  </div>



  {{-- Script --}}
  <script>
    document.getElementById('year')?.append(new Date().getFullYear())
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
 @include('partials.survei_kepuasan_modal')
 @include('partials.pengaduan_modal')
  {{-- Script tambahan dari partial (sidebar, dll) --}}
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
