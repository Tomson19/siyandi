{{-- resources/views/partials/sidebar.blade.php --}}
<style>
    :root {
        --sidebar-bg-top: #0f5132;
        --sidebar-bg-bottom: #198754;
        --sidebar-accent: #20c997;
        --sidebar-text: #e9f7ef;
        --sidebar-muted: #b6d8c0;
    }

    /* ==== SIDEBAR (FIXED) ==== */
    .app-sidebar {
        width: 260px;
        background: linear-gradient(180deg, var(--sidebar-bg-top), var(--sidebar-bg-bottom));
        color: var(--sidebar-text);
        border-right: 1px solid rgba(0, 0, 0, 0.06);
        padding: 1.1rem 1.1rem 0.9rem;
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 56px;
        left: 0;
        bottom: 0;
        z-index: 1010;
    }

    .app-sidebar * {
        box-sizing: border-box;
    }

    /* ==== HEADER ==== */
    .app-sidebar-header {
        gap: 0.75rem;
    }

    .app-sidebar-logo {
        width: 42px;
        height: 42px;
        border-radius: 1.1rem;
        background: radial-gradient(circle at 30% 20%, #ffffff, var(--sidebar-accent));
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 18px rgba(0, 0, 0, .18);
    }

    .app-sidebar-title {
        font-weight: 700;
        letter-spacing: .06em;
        font-size: 0.95rem;
        margin-bottom: 2px;
    }

    .app-sidebar-subtitle {
        font-size: 0.75rem;
        color: var(--sidebar-muted);
    }

    /* ==== NAMA USER DI HEADER ==== */
    .app-sidebar-userinfo {
        margin-top: 0.5rem;
        padding: 0.6rem 0.8rem;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 0.6rem;
        text-align: left;
        color: var(--sidebar-text);
    }

    .app-sidebar-userinfo .name {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .app-sidebar-userinfo .role {
        font-size: 0.72rem;
        color: var(--sidebar-muted);
        letter-spacing: 0.04em;
    }

    .app-sidebar-divider {
        border-color: rgba(255, 255, 255, 0.18);
        margin: 0.9rem 0 1rem;
    }

    .app-sidebar-section-title {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--sidebar-muted);
        margin-bottom: 0.35rem;
        margin-top: 0.35rem;
    }

    .app-sidebar-nav {
        overflow-y: auto;
        padding-right: 0.15rem;
        flex: 1 1 auto;
    }

    .app-sidebar-nav::-webkit-scrollbar {
        width: 4px;
    }

    .app-sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.28);
        border-radius: 999px;
    }

    .app-sidebar .nav-link.sidebar-link {
        color: var(--sidebar-text) !important;
        font-size: 0.9rem;
        padding: 0.5rem 0.55rem;
        border-radius: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.18s ease-out;
        position: relative;
    }

    .app-sidebar .nav-link.sidebar-link i {
        font-size: 1.05rem;
        width: 18px;
        text-align: center;
    }

    .app-sidebar .nav-link.sidebar-link:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateX(2px);
    }

    .app-sidebar .nav-link.sidebar-link.active {
        background: rgba(255, 255, 255, 0.95);
        color: #155724 !important;
        font-weight: 600;
        box-shadow: 0 8px 18px rgba(0, 0, 0, .25);
    }

    .app-sidebar .nav-link.sidebar-link.active i {
        color: var(--sidebar-bg-top);
    }

    .app-sidebar-footer {
        font-size: 0.82rem;
        color: var(--sidebar-muted);
        padding-top: 0.65rem;
        margin-top: 0.75rem;
        border-top: 1px dashed rgba(255, 255, 255, 0.18);
    }

    .app-sidebar-user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
    }

    @media (max-width: 991.98px) {
        .app-sidebar {
            left: -270px;
            transition: left 0.25s ease-out;
            width: 260px;
        }

        .app-sidebar.sidebar-open {
            left: 0;
        }
    }
</style>

@auth
    <aside id="app-sidebar" class="app-sidebar">
        {{-- HEADER --}}
        <div class="app-sidebar-header d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center">
                <div class="app-sidebar-logo me-2">
                    <i class="bi bi-grid-1x2-fill"></i>
                </div>
                <div>
                    <div class="app-sidebar-title">SIYANDI</div>
                    <div class="app-sidebar-subtitle">Layanan Benih</div>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-light d-lg-none" id="sidebarCloseBtn">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- INFO USER (HEADER SECTION) --}}
        <div class="app-sidebar-userinfo">
            <div class="name text-uppercase">
                {{ auth()->user()->name ?? 'USER' }}
            </div>
        </div>

        {{-- MENU --}}
        <nav class="app-sidebar-nav" id="appSidebarNav">
            <ul class="nav flex-column">

                {{-- Laporan: Verifikator + Manager + Operator --}}
                @hasanyrole('admin_verifikator|admin_manager|admin_operator')
                    <li class="nav-item mt-3">
                        <p class="app-sidebar-section-title">Laporan</p>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.laporan_penjualan') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.laporan_penjualan')) active @endif">
                            <i class="bi bi-cash-coin"></i>
                            <span>Laporan Penjualan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.laporan_stok') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.laporan_stok')) active @endif">
                            <i class="bi bi-box-seam"></i>
                            <span>Laporan Stok</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.laporan.pembinaan.index') }}"
                            class="nav-link sidebar-link {{ request()->routeIs('admin.laporan.pembinaan.*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up"></i>
                            <span>Laporan Pembinaan</span>
                        </a>
                    </li>
                @endhasanyrole

                {{-- Admin Kabupaten --}}
                @hasanyrole('admin_pekanbaru|admin_kampar|admin_bengkalis|admin_indragiri_hulu|admin_indragiri_hilir|admin_kuantan_singingi|admin_pelalawan|admin_rokan_hilir|admin_rokan_hulu|admin_siak|admin_kepulauan_meranti|admin_dumai')
                    <li class="nav-item mt-3">
                        <p class="app-sidebar-section-title">Data dan Informasi</p>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.kabupaten.dashboard') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.kabupaten.dashboard')) active @endif">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.varietas.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.varietas.*')) active @endif">
                            <i class="bi bi-folder2-open"></i>
                            <span>Pelepasan Varietas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.kabupaten.kbs.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.kabupaten.kbs.*')) active @endif">
                            <i class="bi bi-tree"></i>
                            <span>Kebun Benih Sumber</span>
                        </a>
                    </li>
                @endhasanyrole

                {{-- Super Admin --}}
                @role('admin_super')
                    <li class="nav-item mt-3">
                        <p class="app-sidebar-section-title">Super Admin</p>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.super.dashboard') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.super.dashboard')) active @endif">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.super.users.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.super.users.*')) active @endif">
                            <i class="bi bi-people"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>
                @endrole

                {{-- Admin UPT Sertifikasi --}}
                @role('admin_upt_sertifikasi')
                    <li class="nav-item mt-3">
                        <p class="app-sidebar-section-title">UPT Sertifikasi</p>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.upt_sertifikasi.penangkar.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.upt_sertifikasi.penangkar.*')) active @endif">
                            <i class="bi bi-geo-alt"></i>
                            <span>Penangkar</span>
                        </a>
                    </li>
                @endrole

                {{-- Pemohon --}}
                @role('pemohon')
                    <li class="nav-item mt-3">
                        <p class="app-sidebar-section-title">Layanan</p>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pemohon.permohonan.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('pemohon.permohonan.*')) active @endif">
                            <i class="bi bi-file-earmark-plus"></i>
                            <span>Usulan Permohonan Benih</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pemohon.pembinaan.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('pemohon.pembinaan.*')) active @endif">
                            <i class="bi bi-mortarboard"></i>
                            <span>Pembinaan Penangkar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pemohon.pembinaan-kbs.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('pemohon.pembinaan-kbs.*')) active @endif">
                            <i class="bi bi-tree"></i>
                            <span>Pembinaan KBS</span>
                        </a>
                    </li>
                @endrole

                {{-- Verifikator --}}
                @role('admin_verifikator')
                    <li class="nav-item mt-3">
                        <p class="app-sidebar-section-title">Layanan</p>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.permohonan.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.permohonan.*')) active @endif">
                            <i class="bi bi-card-checklist"></i>
                            <span>Permohonan Benih</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.pembinaan.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.pembinaan.*')) active @endif">
                            <i class="bi bi-mortarboard"></i>
                            <span>Pembinaan Penangkar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.pembinaan_kbs.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.pembinaan_kbs.*')) active @endif">
                            <i class="bi bi-tree"></i>
                            <span>Pembinaan KBS</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.benih.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.benih.*')) active @endif">
                            <i class="bi bi-box-seam"></i>
                            <span>Stock Tanaman</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.qris.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.qris.*')) active @endif">
                            <i class="bi bi-qr-code-scan"></i>
                            <span>QRIS Pembayaran</span>
                        </a>
                    </li>

                    <li class="nav-item mt-3">
                        <p class="app-sidebar-section-title">Peraturan dan Panduan</p>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.peraturan.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.peraturan.*')) active @endif">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>Peraturan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.alur_sop.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.alur_sop.*')) active @endif">
                            <i class="bi bi-diagram-3"></i>
                            <span>Alur & SOP</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.buku_panduan.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.buku_panduan.*')) active @endif">
                            <i class="bi bi-journal-bookmark"></i>
                            <span>Buku Panduan</span>
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <p class="app-sidebar-section-title">Kontak</p>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.survei_kepuasan.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.survei_kepuasan.*')) active @endif">
                            <i class="bi bi-emoji-smile"></i>
                            <span>Survei Kepuasan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifikator.pengaduan.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.verifikator.pengaduan.*')) active @endif">
                            <i class="bi bi-exclamation-diamond"></i>
                            <span>Pengaduan</span>
                        </a>
                    </li>
                @endrole

                {{-- Program & Kegiatan (Verifikator + Bidang Produksi) --}}
                @hasanyrole('admin_verifikator|admin_bidang_produksi')
                    <li class="nav-item mt-3">
                        <p class="app-sidebar-section-title">Data dan Informasi</p>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.program_kegiatan.index') }}"
                            class="nav-link sidebar-link @if (request()->routeIs('admin.program_kegiatan.*')) active @endif">
                            <i class="bi bi-list-check"></i>
                            <span>Program & Kegiatan</span>
                        </a>
                    </li>
                @endhasanyrole

                {{-- PENGATURAN (SELALU TAMPIL, TANPA COLLAPSE) --}}
                <li class="nav-item mt-3">
                    <p class="app-sidebar-section-title">Pengaturan</p>
                </li>

                <li class="nav-item">
                    <a href="{{ route('settings.profile.edit') }}"
                        class="nav-link sidebar-link @if (request()->routeIs('settings.profile.edit')) active @endif">
                        <i class="bi bi-person-circle"></i>
                        <span>Profile</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('settings.password.edit') }}"
                        class="nav-link sidebar-link @if (request()->routeIs('settings.password.edit')) active @endif">
                        <i class="bi bi-key"></i>
                        <span>Ubah Password</span>
                    </a>
                </li>
            </ul>
        </nav>

        {{-- FOOTER (Tetap dengan tombol logout) --}}
        <div class="app-sidebar-footer">
            <div class="d-flex align-items-center">
                <div class="app-sidebar-user-avatar me-2">
                    <i class="bi bi-person"></i>
                </div>
                <div class="flex-grow-1">© {{ date('Y') }}</div>
                <form action="{{ route('logout') }}" method="POST" class="ms-1">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>
@endauth

@push('scripts')
<script>
(function() {
    const sidebar        = document.getElementById('app-sidebar');
    const sidebarCloseBtn= document.getElementById('sidebarCloseBtn');
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarNav     = document.getElementById('appSidebarNav');
    const sidebarBackdrop= document.getElementById('sidebarBackdrop');

    // === SIMPAN & RESTORE SCROLL SIDEBAR ===
    if (sidebarNav) {
        const savedScroll = sessionStorage.getItem('sidebarScrollTop');
        if (savedScroll !== null) {
            sidebarNav.scrollTop = parseInt(savedScroll, 10) || 0;
        }
        sidebarNav.addEventListener('scroll', () => {
            sessionStorage.setItem('sidebarScrollTop', sidebarNav.scrollTop);
        });
    }

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('sidebar-open');
        sidebarBackdrop?.classList.add('show');
    }

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('sidebar-open');
        sidebarBackdrop?.classList.remove('show');
    }

    sidebarCloseBtn?.addEventListener('click', () => closeSidebar());

    sidebarToggleBtn?.addEventListener('click', e => {
        e.stopPropagation();
        if (sidebar.classList.contains('sidebar-open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    // Klik backdrop -> tutup
    sidebarBackdrop?.addEventListener('click', () => closeSidebar());

    // Klik di luar sidebar (mobile) -> tutup
    document.addEventListener('click', e => {
        if (!sidebar || window.innerWidth >= 992) return;
        if (!sidebar.classList.contains('sidebar-open')) return;

        const clickInside = sidebar.contains(e.target);
        const clickToggle = sidebarToggleBtn && sidebarToggleBtn.contains(e.target);

        if (!clickInside && !clickToggle) {
            closeSidebar();
        }
    });

    // Esc untuk tutup sidebar (mobile)
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    });
})();
</script>
@endpush

