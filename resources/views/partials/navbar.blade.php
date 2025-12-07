<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top shadow-sm">
    <div class="container-fluid">
        @auth
            {{-- Tombol toggle sidebar (mobile) --}}
            <button class="btn btn-light btn-sm d-lg-none me-2" id="sidebarToggleBtn">
                <i class="bi bi-list"></i>
            </button>
        @endauth
        {{-- LOGO / HOME --}}
        @php
            use Illuminate\Support\Facades\Auth;

            $isLoggedIn = Auth::check();
            $isOnPublicPage =
                request()->is('/') ||
                request()->is('profile/*') ||
                request()->is('data*') ||
                request()->is('layanan*') ||
                request()->is('peraturan*') ||
                request()->is('kontak*') ||
                request()->is('peta*');

            // default
            $brandText = 'SIYANDI';
            $brandUrl = url('/');

            if ($isLoggedIn) {
                $user = Auth::user();
                $role = $user->getRoleNames()->first();

                // Tentukan dashboard route berdasarkan role user
                switch ($role) {
                    case 'admin_super':
                        $dashboardUrl = route('admin.super.dashboard');
                        break;
                    case 'admin_operator':
                        $dashboardUrl = route('admin.verifikator.laporan_penjualan');
                        break;
                    case 'admin_utama':
                    case 'admin_verifikator':
                        $dashboardUrl = route('admin.verifikator.dashboard');
                        break;
                    case 'admin_keuangan':
                        $dashboardUrl = route('admin.keuangan.dashboard');
                        break;
                    case 'admin_manager':
                        $dashboardUrl = route('admin.verifikator.laporan_penjualan');
                        break;
                    case 'pemohon':
                        $dashboardUrl = route('pemohon.dashboard');
                        break;
                    case 'admin_upt_sertifikasi':
                        $dashboardUrl = route('admin.upt_sertifikasi.penangkar.index');
                        break;
                    case 'admin_bidang_produksi':
                        $dashboardUrl = route('admin.program_kegiatan.index');
                        break;
                    default:
                        // fallback untuk semua admin kabupaten
                        $dashboardUrl = route('admin.kabupaten.dashboard');
                        break;
                }

                if ($isOnPublicPage) {
                    // Sudah login tapi sedang di halaman publik → tombol jadi DASHBOARD
                    $brandText = 'DASHBOARD';
                    $brandUrl = $dashboardUrl;
                } else {
                    // Sedang di area dashboard (sidebar aktif) → tombol jadi HOME
                    $brandText = 'HOME';
                    $brandUrl = url('/');
                }
            }
        @endphp

        <a class="navbar-brand fw-bold {{ request()->is('/') ? 'active-home text-light border-bottom border-2 border-light' : '' }}"
            href="{{ $brandUrl }}">
            {{ $brandText }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav equalize w-100 mx-lg-3">

                {{-- PROFILE --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('profile/*') ? 'active' : '' }}" href="#"
                        data-bs-toggle="dropdown">
                        Profile
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ request()->is('profile/struktur-organisasi') ? 'active text-success fw-bold' : '' }}"
                                href="{{ route('profile.struktur') }}">
                                Struktur Organisasi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->is('profile/tugas-fungsi') ? 'active text-success fw-bold' : '' }}"
                                href="{{ route('profile.tugas') }}">
                                Tugas & Fungsi
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- DATA & INFORMASI --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('data*') ? 'active' : '' }}" href="#"
                        data-bs-toggle="dropdown">
                        Data dan Informasi
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('budidaya.index') }}">Areal Potensi Budidaya</a></li>
                        <li><a class="dropdown-item" href="{{ route('peta.index') }}">Pelepasan Varietas</a></li>
                        <li><a class="dropdown-item" href="{{ route('peta.kbs.index') }}">Kebun Benih Sumber (KBS)</a>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('peta.penangkar.index') }}">Penangkar</a></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('program_kegiatan.public') }}">
                                Program dan Kegiatan
                            </a>
                        </li>

                    </ul>
                </li>

                {{-- LAYANAN --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('layanan*') ? 'active' : '' }}" href="#"
                        data-bs-toggle="dropdown">
                        Layanan
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ request()->is('data-benih') ? 'active text-success fw-bold' : '' }}"
                                href="{{ route('public.benih.index') }}">
                                Ketersediaan Benih
                            </a>
                        </li>

                        {{-- TAMPIL UNTUK GUEST (BELUM LOGIN) --}}
                        @guest
                            <li>
                                <a class="dropdown-item {{ request()->is('layanan/permohonan-benih') ? 'active text-success fw-bold' : '' }}"
                                    href="{{ route('pemohon.permohonan.index') }}">
                                    Permohonan Benih
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('pemohon.pembinaan-kbs.index') }}">Usulan Pembinaan
                                    Calon KBS</a></li>
                            <li><a class="dropdown-item" href="{{ route('pemohon.pembinaan.index') }}">Usulan Pembinaan
                                    Calon Penangkar</a></li>
                        @endguest

                        {{-- TAMPIL UNTUK USER LOGIN DENGAN ROLE "pemohon" --}}
                        @auth
                            @role('pemohon')
                                <li>
                                    <a class="dropdown-item {{ request()->is('layanan/permohonan-benih') ? 'active text-success fw-bold' : '' }}"
                                        href="{{ route('pemohon.permohonan.index') }}">
                                        Permohonan Benih
                                    </a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('pemohon.pembinaan-kbs.index') }}">Usulan Pembinaan
                                        Calon KBS</a></li>
                                <li><a class="dropdown-item" href="{{ route('pemohon.pembinaan.index') }}">Usulan Pembinaan
                                        Calon Penangkar</a></li>
                            @endrole
                        @endauth

                    </ul>
                </li>

                {{-- PERATURAN & PANDUAN --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('peraturan*') ? 'active' : '' }}"
                        href="#" data-bs-toggle="dropdown">
                        Peraturan dan Panduan
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('peraturan.public') }}">
                                Peraturan
                            </a>
                        </li>

                        <li><a class="dropdown-item" href="{{ route('alur_sop.public') }}">Alur dan SOP</a></li>
                        <li><a class="dropdown-item" href="{{ route('buku_panduan.public') }}">Buku Panduan</a></li>
                    </ul>
                </li>

                {{-- KONTAK --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('kontak*') ? 'active' : '' }}" href="#"
                        data-bs-toggle="dropdown">
                        Kontak
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#surveiKepuasanModal">
                                Survei Kepuasan Masyarakat
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                data-bs-target="#pengaduanModal">
                                Pengaduan
                            </a>
                        </li>

                        <li><a class="dropdown-item" href="#">Konsultasi</a></li>
                    </ul>
                </li>
            </ul>

            {{-- LOGIN BUTTON --}}
            <ul class="navbar-nav ms-lg-3">
                <li class="nav-item">
                    <div class="d-grid d-lg-block">
                        @auth
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">@csrf
                                <button
                                    class="btn btn-light btn-sm fw-semibold px-3 text-success border border-success w-100 w-lg-auto"
                                    type="submit">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a class="btn btn-light btn-sm fw-semibold px-3 w-100 w-lg-auto
                    {{ request()->is('login') ? 'text-success border border-success' : '' }}"
                                href="{{ route('login') }}">
                                Login
                            </a>
                        @endauth
                    </div>
                </li>
            </ul>

        </div>
    </div>
</nav>
