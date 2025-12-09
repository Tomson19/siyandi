<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Verifikator\VerifikatorPermohonanController;
use App\Http\Controllers\Pemohon\PermohonanController as PemohonPermohonanController;
use App\Http\Controllers\Pemohon\PembinaanPenangkarController as PemohonPembinaanPenangkarController;
use App\Http\Controllers\Pemohon\PembinaanKebunBenihSumberController as PemohonPembinaanKbsController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Admin\Kabupaten\DashboardKabupatenController;
use App\Http\Controllers\Admin\Kabupaten\VarietasController;
use App\Http\Controllers\Admin\Kabupaten\DeskripsiVarietasController;
use App\Http\Controllers\Admin\Kabupaten\MateriGenetikController;
use App\Http\Controllers\Admin\Verifikator\BenihController;
use App\Http\Controllers\Admin\Verifikator\PengaturanQrisController;
use App\Http\Controllers\Admin\Verifikator\ProgramKegiatanController;
use App\Http\Controllers\Admin\Verifikator\AlurSopController;
use App\Http\Controllers\Admin\Verifikator\BukuPanduanController;
use App\Http\Controllers\Admin\Verifikator\SurveiKepuasanController;
use App\Http\Controllers\Admin\Verifikator\PengaduanController;
use App\Http\Controllers\Admin\Verifikator\PembinaanPenangkarAdminController;
use App\Http\Controllers\Admin\Verifikator\PembinaanKbsAdminController;
use App\Http\Controllers\Admin\Kabupaten\KebunBenihSumberController;
use App\Http\Controllers\Admin\UptSertifikasi\PenangkarController;
use App\Http\Controllers\Admin\LaporanPembinaanController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PetaKbsController;
use App\Http\Controllers\PetaPenangkarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\Super\UserManagementController;
use App\Http\Controllers\Admin\Super\DashboardController;
use App\Http\Controllers\Admin\Verifikator\PeraturanController;
use App\Http\Controllers\BudidayaController;




// ========================
// HALAMAN UTAMA
// ========================


Route::middleware(['auth'])->group(function () {
    Route::get('/settings/profile', [ProfileController::class, 'edit'])
        ->name('settings.profile.edit');

    Route::put('/settings/profile', [ProfileController::class, 'update'])
        ->name('settings.profile.update');

    Route::get('/settings/password', function () {
        return view('settings.password');
    })->name('settings.password.edit');
    Route::get('/settings/email/confirm/{id}', [ProfileController::class, 'confirmEmailChange'])
        ->name('settings.email.confirm')
        ->middleware('signed');
});

Route::get('/peraturan', [PeraturanController::class, 'publicIndex'])
    ->name('peraturan.public');

Route::get('/alur-sop', [AlurSopController::class, 'publicIndex'])
    ->name('alur_sop.public');

Route::get('/buku-panduan', [BukuPanduanController::class, 'publicIndex'])
    ->name('buku_panduan.public');

Route::post('/survei-kepuasan', [SurveiKepuasanController::class, 'store'])
    ->name('survei_kepuasan.store');

Route::post('/pengaduan', [PengaduanController::class, 'store'])
    ->name('pengaduan.store');



Route::get('/wilayah/kecamatan', [WilayahController::class, 'kecamatan'])
    ->name('wilayah.kecamatan');

Route::get('/wilayah/desa/{districtId}', [WilayahController::class, 'desa'])
    ->name('wilayah.desa');

Route::get('/peta-penangkar', [PetaPenangkarController::class, 'index'])
    ->name('peta.penangkar.index');
Route::get('/peta-penangkar/export/excel', [PetaPenangkarController::class, 'exportExcel'])
    ->name('peta.penangkar.export.excel');

Route::get('/peta-penangkar/export/pdf', [PetaPenangkarController::class, 'exportPdf'])
    ->name('peta.penangkar.export.pdf');

Route::get('/potensi-budidaya', [BudidayaController::class, 'index'])
->name('budidaya.index');
Route::post('/budidaya/export-js', [BudidayaController::class, 'exportFromJs'])
    ->name('budidaya.export-js');


Route::get('/peta-kbs', [PetaKbsController::class, 'index'])->name('peta.kbs.index');
Route::get('/peta-kbs/{kbs}', [PetaKbsController::class, 'show'])->name('peta.kbs.show');
Route::get('/peta-kbs/export/excel', [PetaKbsController::class, 'exportExcel'])->name('peta.kbs.export.excel');
Route::get('/peta-kbs/export/pdf',   [PetaKbsController::class, 'exportPdf'])->name('peta.kbs.export.pdf');
Route::get('/peta-kbs/{kbs}/export/excel', [PetaKbsController::class, 'exportDetailExcel'])
    ->name('peta.kbs.detail.export.excel');
Route::get('/peta-kbs/{kbs}/export/pdf', [PetaKbsController::class, 'exportDetailPdf'])
    ->name('peta.kbs.detail.export.pdf');


Route::get('/peta', [PetaController::class, 'index'])->name('peta.index');
Route::get('/peta/{id}', [PetaController::class, 'detail'])->name('peta.detail');
Route::get('/export/varietas/excel', [ExportController::class, 'exportVarietasExcel'])->name('export.varietas.excel');
Route::get('/export/varietas/pdf', [ExportController::class, 'exportVarietasPDF'])->name('export.varietas.pdf');

Route::get('/export/deskripsi/{id}/pdf', [ExportController::class, 'exportDeskripsiPDF'])->name('export.deskripsi.pdf');
Route::get('/export/deskripsi/{id}/excel', [ExportController::class, 'exportDeskripsiExcel'])
    ->name('export.deskripsi.excel');

Route::get('/export/materigenetik/{id}/excel', [ExportController::class, 'exportMateriExcel'])->name('export.materigenetik.excel');
Route::get('/export/materigenetik/{id}/pdf', [ExportController::class, 'exportMateriPDF'])->name('export.materigenetik.pdf');
Route::get('/data-benih', [BenihController::class, 'publicIndex'])
    ->name('public.benih.index');


Route::get('/program-kegiatan', [ProgramKegiatanController::class, 'publicIndex'])
    ->name('program_kegiatan.public');

Route::middleware(['auth', 'role:admin_verifikator|admin_bidang_produksi'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('program-kegiatan', ProgramKegiatanController::class)->names('program_kegiatan');
});

Route::get('/', fn() => view('welcome'))->name('home');

// ========================
// PROFIL
// ========================
Route::prefix('profile')->group(function () {
    Route::get('/struktur-organisasi', fn() => view('profile.struktur-organisasi'))->name('profile.struktur');
    Route::get('/tugas-fungsi', fn() => view('profile.tugas-fungsi'))->name('profile.tugas');
});

// ========================
// DASHBOARD ADMIN
// ========================

// SUPER ADMIN

Route::middleware(['auth', 'role:admin_super'])
    ->prefix('admin/super')
    ->as('admin.super.')
    ->group(function () {

        // Manajemen User
        Route::get('/users', [UserManagementController::class, 'index'])
            ->name('users.index');

        Route::put('/users/{user}', [UserManagementController::class, 'update'])
            ->name('users.update');
    });

Route::middleware(['auth', 'role:admin_verifikator|admin_manager|admin_operator'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/laporan/pembinaan', [LaporanPembinaanController::class, 'index'])
            ->name('laporan.pembinaan.index');
    });


Route::middleware(['auth', 'role:admin_super'])
    ->prefix('admin/super')
    ->as('admin.super.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/users', [UserManagementController::class, 'index'])
            ->name('users.index');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])
            ->name('users.update');
    });


Route::middleware(['auth', 'role:admin_bidang_produksi'])
    ->prefix('admin/produksi')
    ->as('admin.produksi.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('admin.produksi.dashboard'))->name('dashboard');
    });


// OPERATOR
Route::middleware(['auth', 'role:admin_operator'])
    ->prefix('admin/operator')
    ->as('admin.operator.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('admin.operator.index'))->name('dashboard');
    });

// ========================
// VERIFIKATOR
// ========================

Route::middleware(['auth', 'role:admin_verifikator'])
    ->prefix('admin')
    ->group(function () {

        // ================== GROUP VERIFIKATOR ==================
        Route::prefix('verifikator')
            ->as('admin.verifikator.')
            ->group(function () {

                // Dashboard
                Route::get('/', [VerifikatorPermohonanController::class, 'index'])
                    ->name('index');

                Route::get('/dashboard', [VerifikatorPermohonanController::class, 'dashboard'])
                    ->name('dashboard');

                // 📤 Export Permohonan (Excel & PDF)
                Route::get('/permohonan/export-excel', [VerifikatorPermohonanController::class, 'exportExcel'])
                    ->name('permohonan.export_excel');

                Route::get('/permohonan/export-pdf', [VerifikatorPermohonanController::class, 'exportPdf'])
                    ->name('permohonan.export_pdf');

                // 📄 Daftar & detail permohonan
                Route::get('/permohonan', [VerifikatorPermohonanController::class, 'index'])
                    ->name('permohonan.index');

                Route::get('/permohonan/{id}', [VerifikatorPermohonanController::class, 'show'])
                    ->name('permohonan.show');

                // ✅ Aksi verifikasi permohonan
                Route::post('/permohonan/{id}/approve', [VerifikatorPermohonanController::class, 'approve'])
                    ->name('permohonan.approve');

                Route::post('/permohonan/{id}/reject', [VerifikatorPermohonanController::class, 'reject'])
                    ->name('permohonan.reject');

                Route::post('/permohonan/{id}/perbaiki', [VerifikatorPermohonanController::class, 'perbaiki'])
                    ->name('permohonan.perbaiki');

                // 📎 Upload surat persetujuan / penolakan (PDF hasil tanda tangan)
                Route::post('/permohonan/{id}/upload-keputusan', [VerifikatorPermohonanController::class, 'uploadKeputusan'])
                    ->name('permohonan.uploadKeputusan');

                // 💳 Verifikasi pembayaran (Berhasil / Gagal)
                Route::post('/permohonan/{id}/verifikasi-pembayaran', [VerifikatorPermohonanController::class, 'verifikasiPembayaran'])
                    ->name('permohonan.verifikasi_pembayaran');

                // 🚚 Update status pengambilan + stok + deadline tanam
                Route::post('/permohonan/{id}/update-pengambilan', [VerifikatorPermohonanController::class, 'updatePengambilan'])
                    ->name('permohonan.updatePengambilan');

                // ⏰ Auto cancel pembayaran lewat 7 hari (permohonan berbayar)
                Route::get('/auto-cancel', [VerifikatorPermohonanController::class, 'autoCancel'])
                    ->name('autoCancel');

                Route::resource('peraturan', PeraturanController::class)
                    ->names('peraturan')
                    ->only(['index', 'store', 'update', 'destroy']);
                // ALUR & SOP
                Route::resource('alur-sop', AlurSopController::class)
                    ->except(['show', 'create', 'edit'])
                    ->names('alur_sop');

                // BUKU PANDUAN
                Route::resource('buku-panduan', BukuPanduanController::class)
                    ->except(['show', 'create', 'edit'])
                    ->names('buku_panduan');

                Route::get('survei-kepuasan', [SurveiKepuasanController::class, 'index'])
                    ->name('survei_kepuasan.index');

                Route::get('pengaduan', [PengaduanController::class, 'index'])
                    ->name('pengaduan.index');

                Route::delete('pengaduan/{pengaduan}', [PengaduanController::class, 'destroy'])
                    ->name('pengaduan.destroy');
                
                // 📤 Export Pengaduan (Excel & PDF)
                Route::get('pengaduan/export-excel', [PengaduanController::class, 'exportExcel'])
                    ->name('pengaduan.export_excel');

                Route::get('pengaduan/export-pdf', [PengaduanController::class, 'exportPdf'])
                    ->name('pengaduan.export_pdf');


                // Pembinaan Calon Penangkar
                Route::get('/pembinaan', [PembinaanPenangkarAdminController::class, 'index'])
                    ->name('pembinaan.index');

                // Buat sesi pembinaan baru + pasang beberapa pengajuan
                Route::post('/pembinaan/sesi', [PembinaanPenangkarAdminController::class, 'storeSesi'])
                    ->name('pembinaan.sesi.store');

                // Detail sesi (lihat peserta, dll)
                Route::get('/pembinaan/sesi/{sesi}', [PembinaanPenangkarAdminController::class, 'showSesi'])
                    ->name('pembinaan.sesi.show');

                // Update data sesi (status, bukti pembinaan, alasan)
                // dipakai untuk form "Ubah Status Sesi" & update bukti
                Route::put('/pembinaan/sesi/{sesi}', [PembinaanPenangkarAdminController::class, 'updateSesi'])
                    ->name('pembinaan.sesi.update');

                // Update status perizinan salah satu pengajuan (per pemohon)
                // misal: menunggu -> berhasil / dibatalkan (+ alasan kalau dibatalkan)
                Route::put('/pembinaan/{pembinaan}/perizinan', [PembinaanPenangkarAdminController::class, 'updatePerizinan'])
                    ->name('pembinaan.perizinan.update');
                Route::patch('pembinaan/peserta/{pembinaan}/status', [PembinaanPenangkarAdminController::class, 'updatePesertaStatus'])
                    ->name('pembinaan.peserta.status');

                // Pembinaan Kebun Benih Sumber (KBS)
                Route::get('/pembinaan-kbs', [PembinaanKbsAdminController::class, 'index'])
                    ->name('pembinaan_kbs.index');

                // Buat sesi pembinaan baru + pasang beberapa pengajuan KBS
                Route::post('/pembinaan-kbs/sesi', [PembinaanKbsAdminController::class, 'storeSesi'])
                    ->name('pembinaan_kbs.sesi.store');

                // Detail sesi KBS (lihat peserta KBS, dll)
                Route::get('/pembinaan-kbs/sesi/{sesi}', [PembinaanKbsAdminController::class, 'showSesi'])
                    ->name('pembinaan_kbs.sesi.show');

                // Update data sesi KBS (status, bukti, dll)
                Route::put('/pembinaan-kbs/sesi/{sesi}', [PembinaanKbsAdminController::class, 'updateSesi'])
                    ->name('pembinaan_kbs.sesi.update');

                // Update status pembinaan untuk satu peserta KBS
                Route::patch('/pembinaan-kbs/{pembinaanKbs}/status', [PembinaanKbsAdminController::class, 'updatePesertaStatus'])
                    ->name('pembinaan_kbs.peserta.status');



                // ================== GROUP BENIH (pengganti stok-benih) ==================
                Route::prefix('benih')
                    ->as('benih.')
                    ->group(function () {
                        Route::get('/', [BenihController::class, 'index'])->name('index');
                        Route::post('/', [BenihController::class, 'store'])->name('store');
                        Route::put('/{benih}', [BenihController::class, 'update'])->name('update');
                        Route::delete('/{benih}', [BenihController::class, 'destroy'])->name('destroy');
                    });

                // ================== GROUP QRIS ==================
                Route::prefix('qris')
                    ->as('qris.')
                    ->group(function () {
                        Route::get('/', [PengaturanQrisController::class, 'index'])->name('index');
                        Route::post('/', [PengaturanQrisController::class, 'store'])->name('store');
                        Route::put('/{id}', [PengaturanQrisController::class, 'update'])->name('update');
                        Route::delete('/{id}', [PengaturanQrisController::class, 'destroy'])->name('destroy');
                    });
            });
    });


Route::middleware(['auth', 'role:admin_utama|admin_verifikator|admin_manager|admin_operator'])
    ->prefix('admin/verifikator')
    ->as('admin.verifikator.')
    ->group(function () {

        Route::get('/laporan-penjualan', [VerifikatorPermohonanController::class, 'laporanPenjualan'])
            ->name('laporan_penjualan');

        Route::get('/laporan-penjualan/export/excel', [VerifikatorPermohonanController::class, 'exportPenjualanExcel'])
            ->name('laporan_penjualan.export.excel');

        Route::get('/laporan-penjualan/export/pdf', [VerifikatorPermohonanController::class, 'exportPenjualanPdf'])
            ->name('laporan_penjualan.export.pdf');

        Route::get('/laporan-stok', [VerifikatorPermohonanController::class, 'laporanStok'])
            ->name('laporan_stok');

        Route::get('/laporan-stok/export/excel', [VerifikatorPermohonanController::class, 'exportStokExcel'])
            ->name('laporan_stok.export.excel');

        Route::get('/laporan-stok/export/pdf', [VerifikatorPermohonanController::class, 'exportStokPdf'])
            ->name('laporan_stok.export.pdf');
    });



// KEUANGAN
Route::middleware(['auth', 'role:admin_keuangan'])
    ->prefix('admin/keuangan')
    ->as('admin.keuangan.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('admin.keuangan.index'))->name('dashboard');
    });

// ========================
// ADMIN KABUPATEN (DINAMIS)
// ========================
Route::middleware([
    'auth',
    'role:admin_pekanbaru|admin_kampar|admin_bengkalis|admin_indragiri_hulu|admin_indragiri_hilir|admin_kuantan_singingi|admin_pelalawan|admin_rokan_hilir|admin_rokan_hulu|admin_siak|admin_kepulauan_meranti|admin_dumai'
])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        // Dashboard umum untuk semua admin kabupaten
        Route::get('/kabupaten/dashboard', [DashboardKabupatenController::class, 'index'])
            ->name('kabupaten.dashboard');


        // ✅ CRUD Varietas
        Route::resource('varietas', VarietasController::class)
            ->parameters(['varietas' => 'id'])
            ->names('varietas');

        // ✅ CRUD Deskripsi Varietas
        Route::resource('varietas.deskripsi', DeskripsiVarietasController::class)
            ->shallow()
            ->names('varietas.deskripsi');

        // ✅ CRUD Materi Genetik (inline di halaman varietas)
        Route::prefix('varietas/{varietas}/materi-genetik')
            ->as('varietas.materi.')
            ->middleware('auth')
            ->group(function () {
                Route::post('/', [MateriGenetikController::class, 'store'])->name('store');
                Route::put('/{id}', [MateriGenetikController::class, 'update'])->name('update');
                Route::delete('/{id}', [MateriGenetikController::class, 'destroy'])->name('destroy');
            });

        // ✅ Halaman detail varietas
        // Route::get('varietas/{id}/detail', [VarietasController::class, 'show'])
        //     ->name('varietas.show');
    });



Route::middleware([
    'auth',
    'role:admin_pekanbaru|admin_kampar|admin_bengkalis|admin_indragiri_hulu|admin_indragiri_hilir|admin_kuantan_singingi|admin_pelalawan|admin_rokan_hilir|admin_rokan_hulu|admin_siak|admin_kepulauan_meranti|admin_dumai|admin_super'
])
    ->prefix('admin/kabupaten')
    ->name('admin.kabupaten.')
    ->group(function () {



        // INDEX KBS (ini sudah ada di kamu)
        Route::get('kbs', [KebunBenihSumberController::class, 'index'])->name('kbs.index');
        Route::post('kbs', [KebunBenihSumberController::class, 'store'])->name('kbs.store');
        Route::put('kbs/{kbs}', [KebunBenihSumberController::class, 'update'])->name('kbs.update');
        Route::delete('kbs/{kbs}', [KebunBenihSumberController::class, 'destroy'])->name('kbs.destroy');

        // ➕ DETAIL (SHOW) + CRUD PEMILIK & POHON
        Route::get('kbs/{kbs}', [KebunBenihSumberController::class, 'show'])->name('kbs.show');

        // Pemilik
        Route::post('kbs/{kbs}/pemilik', [KebunBenihSumberController::class, 'storePemilik'])
            ->name('kbs.pemilik.store');
        Route::put('kbs/{kbs}/pemilik/{pemilik}', [KebunBenihSumberController::class, 'updatePemilik'])
            ->name('kbs.pemilik.update');  // 🔹 TAMBAH INI
        Route::delete('kbs/{kbs}/pemilik/{pemilik}', [KebunBenihSumberController::class, 'destroyPemilik'])
            ->name('kbs.pemilik.destroy');

        // Pohon
        Route::post('kbs/{kbs}/pohon', [KebunBenihSumberController::class, 'storePohon'])
            ->name('kbs.pohon.store');
        Route::put('kbs/{kbs}/pohon/{pohon}', [KebunBenihSumberController::class, 'updatePohon'])
            ->name('kbs.pohon.update');    // 🔹 TAMBAH INI
        Route::delete('kbs/{kbs}/pohon/{pohon}', [KebunBenihSumberController::class, 'destroyPohon'])
            ->name('kbs.pohon.destroy');
    });

Route::middleware([
    'auth',
    'role:admin_upt_sertifikasi|admin_super'
])
    ->prefix('admin/upt-sertifikasi')
    ->name('admin.upt_sertifikasi.')
    ->group(function () {

        // ================== ROUTE PENANGKAR (Admin UPT Sertifikasi) ==================
        Route::get('penangkar', [PenangkarController::class, 'index'])
            ->name('penangkar.index');

        Route::get('penangkar/create', [PenangkarController::class, 'create'])
            ->name('penangkar.create');

        Route::post('penangkar', [PenangkarController::class, 'store'])
            ->name('penangkar.store');

        Route::get('penangkar/{penangkar}/edit', [PenangkarController::class, 'edit'])
            ->name('penangkar.edit');

        Route::put('penangkar/{penangkar}', [PenangkarController::class, 'update'])
            ->name('penangkar.update');

        Route::delete('penangkar/{penangkar}', [PenangkarController::class, 'destroy'])
            ->name('penangkar.destroy');
    });



// ========================
// PEMOHON
// ========================

Route::middleware(['auth', 'verified', 'role:pemohon'])
    ->prefix('pemohon')
    ->as('pemohon.')
    ->group(function () {
        // Dashboard pemohon langsung ke daftar permohonan
        Route::get('/dashboard', fn() => redirect()->route('pemohon.permohonan.index'))
            ->name('dashboard');

        // CRUD Permohonan Benih
        Route::get('/permohonan', [PemohonPermohonanController::class, 'index'])
            ->name('permohonan.index');

        Route::get('/permohonan/create', [PemohonPermohonanController::class, 'create'])
            ->name('permohonan.create');

        Route::post('/permohonan', [PemohonPermohonanController::class, 'store'])
            ->name('permohonan.store');

        Route::get('/permohonan/{id}', [PemohonPermohonanController::class, 'show'])
            ->name('permohonan.show');

        Route::get('/permohonan/{id}/edit', [PemohonPermohonanController::class, 'edit'])
            ->name('permohonan.edit');

        Route::put('/permohonan/{id}', [PemohonPermohonanController::class, 'update'])
            ->name('permohonan.update');

        Route::delete('/permohonan/{id}', [PemohonPermohonanController::class, 'destroy'])
            ->name('permohonan.destroy');

        // 🔹 Upload Dokumen setelah surat ditandatangani
        Route::get('/permohonan/{id}/upload', [PemohonPermohonanController::class, 'uploadForm'])
            ->name('permohonan.upload');

        Route::post('/permohonan/{id}/upload', [PemohonPermohonanController::class, 'uploadStore'])
            ->name('permohonan.uploadStore');

        Route::get('/permohonan/{id}/download', [PemohonPermohonanController::class, 'downloadSurat'])
            ->name('permohonan.download');

        // 🔹 Upload Bukti Pembayaran (BERBAYAR saja)
        Route::get('/permohonan/{id}/pembayaran', [PemohonPermohonanController::class, 'pembayaranForm'])
            ->name('permohonan.pembayaran.form');

        Route::post('/permohonan/{id}/pembayaran', [PemohonPermohonanController::class, 'pembayaranStore'])
            ->name('permohonan.pembayaran.store');

        // 🔹 Upload Bukti Tanam (setelah pengambilan Selesai)
        Route::get('/permohonan/{id}/bukti-tanam', [PemohonPermohonanController::class, 'buktiTanamForm'])
            ->name('permohonan.bukti_tanam.form');

        Route::post('/permohonan/{id}/bukti-tanam', [PemohonPermohonanController::class, 'buktiTanamStore'])
            ->name('permohonan.bukti_tanam.store');
        // Pembinaan Calon Penangkar
        Route::get('/pembinaan-penangkar', [PemohonPembinaanPenangkarController::class, 'index'])
            ->name('pembinaan.index');

        // simpan pengajuan baru
        Route::post('/pembinaan-penangkar', [PemohonPembinaanPenangkarController::class, 'store'])
            ->name('pembinaan.store');

        // update pengajuan (hanya boleh kalau status = menunggu_jadwal)
        Route::put('/pembinaan-penangkar/{pembinaan}', [PemohonPembinaanPenangkarController::class, 'update'])
            ->name('pembinaan.update');
        Route::get('/pembinaan-penangkar/{pembinaan}', [PemohonPembinaanPenangkarController::class, 'show'])
            ->name('pembinaan.show');


        // simpan data OSS (NIB & Sertifikat Standar) setelah selesai
        Route::post('/pembinaan-penangkar/{pembinaan}/oss', [PemohonPembinaanPenangkarController::class, 'storeOssData'])
            ->name('pembinaan.oss.store');

        // 🔹 Pembinaan Kebun Benih Sumber
        Route::get('/pembinaan-kbs', [PemohonPembinaanKbsController::class, 'index'])
            ->name('pembinaan-kbs.index');

        // simpan pengajuan baru
        Route::post('/pembinaan-kbs', [PemohonPembinaanKbsController::class, 'store'])
            ->name('pembinaan-kbs.store');

        // update pengajuan (hanya boleh kalau status = menunggu_jadwal)
        Route::put('/pembinaan-kbs/{pembinaanKbs}', [PemohonPembinaanKbsController::class, 'update'])
            ->name('pembinaan-kbs.update');

        // detail pengajuan
        Route::get('/pembinaan-kbs/{pembinaanKbs}', [PemohonPembinaanKbsController::class, 'show'])
            ->name('pembinaan-kbs.show');
    });


// ========================
// ROUTE AUTENTIKASI
// ========================
require __DIR__ . '/auth.php';
