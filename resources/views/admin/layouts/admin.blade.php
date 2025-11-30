<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - {{ config('app.name', 'Siyandi') }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <div class="d-flex">
    {{-- Sidebar --}}
    <div class="bg-dark text-white p-3" style="min-width:220px; min-height:100vh;">
      <h5 class="mb-4">Panel Admin</h5>
      <ul class="nav flex-column">
        <li class="nav-item mb-2">
          <a href="{{ url()->current() }}" class="nav-link text-white">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="#" class="nav-link text-white">
            <i class="bi bi-people me-1"></i> Manajemen User
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="#" class="nav-link text-white">
            <i class="bi bi-clipboard-data me-1"></i> Data Permohonan
          </a>
        </li>
        <li class="nav-item mt-4">
          <form action="{{ route('logout') }}" method="POST">@csrf
            <button class="btn btn-outline-light w-100"><i class="bi bi-box-arrow-right me-1"></i> Logout</button>
          </form>
        </li>
      </ul>
    </div>

    {{-- Content --}}
    <div class="flex-grow-1 p-4 bg-light" style="min-height:100vh;">
      @yield('content')
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
