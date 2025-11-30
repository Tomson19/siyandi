@extends('layouts.bootstrap')

@section('content')
    <div class="container py-4">

        {{-- STYLE KHUSUS HALAMAN INI --}}
        <style>
            body{
                margin-top: -70px;
            }
            /* Header info KBS (Komoditas, Varietas, dst) */
            .kbs-meta-table th {
                width: 140px;
                font-weight: 600;
            }

            .kbs-meta-table th,
            .kbs-meta-table td {
                font-size: 0.875rem;
                padding: 0.15rem 0.25rem;
            }

            /* Tabel detail utama */
            .kbs-detail-table {
                font-size: 11px;
            }

            .kbs-detail-table th,
            .kbs-detail-table td {
                padding: 0.25rem 0.35rem;
            }

            .kbs-detail-table .aksi-cell {
                min-width: 170px;
                white-space: nowrap;
            }
        </style>

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h3 class="fw-bold text-primary mb-2">
                    Detail Kebun Benih Sumber
                </h3>

                {{-- Info KBS dibuat seperti tabel agar titik dua sejajar --}}
                <div class="bg-light border rounded-3 px-3 py-2">
                    <table class="table table-borderless table-sm mb-0 kbs-meta-table">
                        <tbody>
                            <tr>
                                <th class="text-end text-muted">Komoditas :</th>
                                <td>
                                    <strong>{{ $kbs->tanaman->nama_tanaman ?? '-' }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-end text-muted">Varietas :</th>
                                <td>
                                    <strong>{{ $kbs->nama_varietas }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-end text-muted">No &amp; Tgl SK :</th>
                                <td>
                                    <strong>{{ $kbs->nomor_sk ?? '-' }}</strong>,
                                    <span>{{ $kbs->tanggal_sk ?? '-' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-end text-muted">Kabupaten :</th>
                                <td>
                                    <strong>{{ $kbs->kabupaten->nama_kabupaten ?? '-' }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <a href="{{ route('admin.kabupaten.kbs.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left-circle"></i> Kembali ke Daftar
            </a>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- TOMBOL ACTION (CREATE) --}}
        <div class="d-flex justify-content-end gap-2 mb-3">
            <button type="button" class="btn btn-success btn-sm rounded-pill" data-bs-toggle="modal"
                data-bs-target="#modalCreatePemilik">
                <i class="bi bi-person-plus"></i> Tambah Lokasi & Pemilik
            </button>

            <button type="button" class="btn btn-warning btn-sm rounded-pill" data-bs-toggle="modal"
                data-bs-target="#modalCreatePohon">
                <i class="bi bi-tree"></i> Tambah Pohon & Koordinat
            </button>
        </div>

        {{-- TABEL DETAIL GABUNG --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-success text-white fw-bold">
                Tabel Detail Kebun Benih Sumber
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 500px; overflow-y:auto;">
                    <table class="table table-bordered table-sm align-middle kbs-detail-table">
                        <thead class="table-dark text-center align-middle">
                            <tr>
                                <th rowspan="2" style="width:4%;">No</th>
                                <th rowspan="2" style="width:10%;">Komoditas</th>
                                <th rowspan="2" style="width:12%;">Varietas</th>
                                <th rowspan="2" style="width:15%;">No &amp; Tgl SK</th>
                                <th colspan="3" style="width:20%;">Lokasi</th>
                                <th rowspan="2" style="width:6%;">Jml PIT</th>
                                <th colspan="4" style="width:18%;">Pemilik</th>
                                <th colspan="4" style="width:20%;">Pohon &amp; Koordinat</th>
                                <th rowspan="2" style="width:7%;">Aksi</th>
                            </tr>
                            <tr>
                                <th>Kecamatan</th>
                                <th>Desa</th>
                                <th>Tahun Tanam</th>

                                <th>No</th>
                                <th>Nama Pemilik</th>
                                <th>Luas (Ha)</th>
                                <th>Jml Pohon Induk</th>

                                <th>No</th>
                                <th>No Pohon Induk</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Flag untuk tahu apakah data KBS (komoditas, varietas, SK) sudah pernah ditampilkan
                                $kbsRowShown = false;
                            @endphp

                            @forelse($pemilik as $p)
                                @php
                                    // Flag untuk tahu apakah data pemilik ini (lokasi & pemilik) sudah ditampilkan di baris pertama
                                    $pemilikRowShown = false;
                                    $pohonCount = $p->pohon->count();
                                @endphp

                                @if ($pohonCount)
                                    {{-- Jika pemilik punya banyak pohon --}}
                                    @foreach ($p->pohon as $ph)
                                        <tr>
                                            {{-- No. (per KBS) --}}
                                            <td class="text-center">
                                                @if (!$kbsRowShown)
                                                    1
                                                @endif
                                            </td>

                                            {{-- Komoditas --}}
                                            <td>
                                                @if (!$kbsRowShown)
                                                    {{ $kbs->tanaman->nama_tanaman ?? '-' }}
                                                @endif
                                            </td>

                                            {{-- Varietas --}}
                                            <td>
                                                @if (!$kbsRowShown)
                                                    {{ $kbs->nama_varietas }}
                                                @endif
                                            </td>

                                            {{-- No & Tgl SK --}}
                                            <td>
                                                @if (!$kbsRowShown)
                                                    <div><strong>{{ $kbs->nomor_sk ?? '-' }}</strong></div>
                                                    <small class="text-muted">{{ $kbs->tanggal_sk ?? '-' }}</small>
                                                @endif
                                            </td>

                                            {{-- Lokasi: Kecamatan, Desa, Tahun Tanam, Jml PIT --}}
                                            <td>
                                                @if (!$pemilikRowShown)
                                                    {{ $p->kecamatan ?? '-' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (!$pemilikRowShown)
                                                    {{ $p->desa ?? '-' }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (!$pemilikRowShown)
                                                    {{ $p->tahun_tanam ?? '-' }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (!$pemilikRowShown)
                                                    {{ $p->jumlah_pit ?? '-' }}
                                                @endif
                                            </td>

                                            {{-- Pemilik: No, Nama, Luas, Jumlah Pohon Induk --}}
                                            <td class="text-center">
                                                @if (!$pemilikRowShown)
                                                    {{ $p->no_pemilik ?? '-' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if (!$pemilikRowShown)
                                                    {{ $p->nama_pemilik ?? '-' }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (!$pemilikRowShown)
                                                    {{ $p->luas_ha ?? '-' }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (!$pemilikRowShown)
                                                    {{ $p->jumlah_pohon_induk ?? '-' }}
                                                @endif
                                            </td>

                                            {{-- Pohon & Koordinat (SELALU TAMPIL DI SETIAP BARIS) --}}
                                            <td class="text-center">{{ $ph->no_pohon ?? '-' }}</td>
                                            <td class="text-center">{{ $ph->nomor_pohon_induk ?? '-' }}</td>
                                            <td class="text-center">{{ $ph->latitude ?? '-' }}</td>
                                            <td class="text-center">{{ $ph->longitude ?? '-' }}</td>

                                            {{-- Aksi (sejajar / satu baris) --}}
                                            <td class="text-start aksi-cell">
                                                <div
                                                    class="d-inline-flex align-items-center justify-content-center gap-1 flex-wrap">

                                                    {{-- EDIT PEMILIK: hanya di baris pertama pemilik ini --}}
                                                    @if (!$pemilikRowShown)
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEditPemilik{{ $p->id }}">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                    @endif

                                                    {{-- EDIT POHON --}}
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEditPohon{{ $ph->id }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>

                                                    {{-- HAPUS POHON --}}
                                                    <form
                                                        action="{{ route('admin.kabupaten.kbs.pohon.destroy', [$kbs->id, $ph->id]) }}"
                                                        method="POST" class="d-inline-block"
                                                        onsubmit="return confirm('Hapus data pohon ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        @php
                                            // Setelah baris pertama ditampilkan:
                                            $kbsRowShown = true; // komoditas/varietas/SK tidak ditampilkan lagi di bawah
                                            $pemilikRowShown = true; // lokasi & pemilik ini tidak diulang
                                        @endphp
                                    @endforeach
                                @else
                                    {{-- Pemilik tanpa pohon --}}
                                    <tr>
                                        <td class="text-center">
                                            @if (!$kbsRowShown)
                                                1
                                            @endif
                                        </td>

                                        <td>
                                            @if (!$kbsRowShown)
                                                {{ $kbs->tanaman->nama_tanaman ?? '-' }}
                                            @endif
                                        </td>

                                        <td>
                                            @if (!$kbsRowShown)
                                                {{ $kbs->nama_varietas }}
                                            @endif
                                        </td>

                                        <td>
                                            @if (!$kbsRowShown)
                                                <div><strong>{{ $kbs->nomor_sk ?? '-' }}</strong></div>
                                                <small class="text-muted">{{ $kbs->tanggal_sk ?? '-' }}</small>
                                            @endif
                                        </td>

                                        <td>{{ $p->kecamatan ?? '-' }}</td>
                                        <td>{{ $p->desa ?? '-' }}</td>
                                        <td class="text-center">{{ $p->tahun_tanam ?? '-' }}</td>
                                        <td class="text-center">{{ $p->jumlah_pit ?? '-' }}</td>

                                        <td class="text-center">{{ $p->no_pemilik ?? '-' }}</td>
                                        <td>{{ $p->nama_pemilik ?? '-' }}</td>
                                        <td class="text-center">{{ $p->luas_ha ?? '-' }}</td>
                                        <td class="text-center">{{ $p->jumlah_pohon_induk ?? '-' }}</td>

                                        {{-- Tidak ada pohon --}}
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>
                                        <td class="text-center">-</td>

                                        {{-- Aksi (sejajar) --}}
                                        <td class="text-start aksi-cell">
                                            <div
                                                class="d-inline-flex align-items-center justify-content-center gap-1 flex-wrap">

                                                {{-- EDIT PEMILIK --}}
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditPemilik{{ $p->id }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                {{-- HAPUS PEMILIK --}}
                                                <form
                                                    action="{{ route('admin.kabupaten.kbs.pemilik.destroy', [$kbs->id, $p->id]) }}"
                                                    method="POST" class="d-inline-block"
                                                    onsubmit="return confirm('Hapus pemilik ini beserta semua pohonnya (jika ada)?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    @php
                                        $kbsRowShown = true;
                                    @endphp
                                @endif
                            @empty
                                <tr>
                                    <td colspan="17" class="text-center text-muted">
                                        Belum ada data pemilik / pohon. Tambahkan melalui tombol di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- ================== MODAL CREATE PEMILIK ================== --}}
    <div class="modal fade" id="modalCreatePemilik" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Tambah Lokasi & Pemilik</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.kabupaten.kbs.pemilik.store', $kbs->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2">
                            {{-- LOKASI (select dari API wilayah) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kecamatan</label>
                                <select name="kecamatan" id="create-kecamatan" class="form-select form-select-sm">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Desa</label>
                                <select name="desa" id="create-desa" class="form-select form-select-sm">
                                    <option value="">-- Pilih Desa --</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tahun Tanam</label>
                                <input type="text" name="tahun_tanam" class="form-control form-control-sm"
                                    placeholder="mis: 1990-2000" value="{{ old('tahun_tanam') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jumlah PIT (Pohon)</label>
                                <input type="number" name="jumlah_pit" class="form-control form-control-sm"
                                    value="{{ old('jumlah_pit') }}">
                            </div>

                            {{-- DATA PEMILIK --}}
                            <div class="col-md-2">
                                <label class="form-label fw-semibold">No.</label>
                                <input type="number" name="no_pemilik" class="form-control form-control-sm"
                                    value="{{ old('no_pemilik') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nama Pemilik</label>
                                <input type="text" name="nama_pemilik" class="form-control form-control-sm"
                                    value="{{ old('nama_pemilik') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Luas (Ha)</label>
                                <input type="number" step="0.01" name="luas_ha" class="form-control form-control-sm"
                                    value="{{ old('luas_ha') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Jumlah Pohon Induk</label>
                                <input type="number" name="jumlah_pohon_induk"
                                    class="form-control form-control-sm" value="{{ old('jumlah_pohon_induk') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================== MODAL CREATE POHON ================== --}}
    <div class="modal fade" id="modalCreatePohon" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Tambah Pohon & Koordinat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.kabupaten.kbs.pohon.store', $kbs->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Pemilik</label>
                                <select name="kbs_pemilik_id" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Pemilik --</option>
                                    @foreach ($pemilik as $p)
                                        <option value="{{ $p->id }}">
                                            No {{ $p->no_pemilik ?? '-' }} - {{ $p->nama_pemilik ?? '-' }}
                                            ({{ $p->kecamatan ?? '-' }} / {{ $p->desa ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">No</label>
                                <input type="number" name="no_pohon" class="form-control form-control-sm"
                                    placeholder="1, 2, 3...">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold">No Pohon Induk</label>
                                <input type="text" name="nomor_pohon_induk" class="form-control form-control-sm"
                                    placeholder="mis: 1, 2, 130">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Latitude (LU/LS)</label>
                                <input type="text" name="latitude" class="form-control form-control-sm"
                                    placeholder="1.1501">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Longitude (BT)</label>
                                <input type="text" name="longitude" class="form-control form-control-sm"
                                    placeholder="102.7587">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ================== MODAL EDIT PEMILIK ================== --}}
    @foreach ($pemilik as $p)
        <div class="modal fade" id="modalEditPemilik{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Edit Data Pemilik (No {{ $p->no_pemilik ?? '-' }})</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.kabupaten.kbs.pemilik.update', [$kbs->id, $p->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kecamatan</label>
                                    <input type="text" name="kecamatan" class="form-control form-control-sm"
                                        value="{{ $p->kecamatan }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Desa</label>
                                    <input type="text" name="desa" class="form-control form-control-sm"
                                        value="{{ $p->desa }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tahun Tanam</label>
                                    <input type="text" name="tahun_tanam" class="form-control form-control-sm"
                                        value="{{ $p->tahun_tanam }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jumlah PIT (Pohon)</label>
                                    <input type="number" name="jumlah_pit" class="form-control form-control-sm"
                                        value="{{ $p->jumlah_pit }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">No.</label>
                                    <input type="number" name="no_pemilik" class="form-control form-control-sm"
                                        value="{{ $p->no_pemilik }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Nama Pemilik</label>
                                    <input type="text" name="nama_pemilik" class="form-control form-control-sm"
                                        value="{{ $p->nama_pemilik }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Luas (Ha)</label>
                                    <input type="number" step="0.01" name="luas_ha"
                                        class="form-control form-control-sm" value="{{ $p->luas_ha }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Jumlah Pohon Induk</label>
                                    <input type="number" name="jumlah_pohon_induk"
                                        class="form-control form-control-sm" value="{{ $p->jumlah_pohon_induk }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- ================== MODAL EDIT POHON ================== --}}
    @foreach ($pemilik as $p)
        @foreach ($p->pohon as $ph)
            <div class="modal fade" id="modalEditPohon{{ $ph->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">Edit Data Pohon (No {{ $ph->no_pohon ?? '-' }})</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.kabupaten.kbs.pohon.update', [$kbs->id, $ph->id]) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Pemilik</label>
                                        <select name="kbs_pemilik_id" class="form-select form-select-sm">
                                            @foreach ($pemilik as $p2)
                                                <option value="{{ $p2->id }}"
                                                    {{ $p2->id == $ph->kbs_pemilik_id ? 'selected' : '' }}>
                                                    No {{ $p2->no_pemilik ?? '-' }} - {{ $p2->nama_pemilik ?? '-' }}
                                                    ({{ $p2->kecamatan ?? '-' }}/{{ $p2->desa ?? '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">No</label>
                                        <input type="number" name="no_pohon" class="form-control form-control-sm"
                                            value="{{ $ph->no_pohon }}">
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">No Pohon Induk</label>
                                        <input type="text" name="nomor_pohon_induk"
                                            class="form-control form-control-sm" value="{{ $ph->nomor_pohon_induk }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Latitude</label>
                                        <input type="text" name="latitude" class="form-control form-control-sm"
                                            value="{{ $ph->latitude }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Longitude</label>
                                        <input type="text" name="longitude" class="form-control form-control-sm"
                                            value="{{ $ph->longitude }}">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light border"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach

    {{-- ================== SCRIPT: LOAD KECAMATAN & DESA (CREATE MODAL) ================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectKecamatan = document.getElementById('create-kecamatan');
            const selectDesa = document.getElementById('create-desa');

            const currentKabupatenName = @json($kbs->kabupaten->nama_kabupaten ?? '');

            if (!selectKecamatan || !selectDesa || !currentKabupatenName) {
                console.warn('Element select atau nama kabupaten tidak ada, script wilayah berhenti.');
                return;
            }

            function createOption(value, text) {
                const opt = document.createElement('option');
                opt.value = text; // nama yang disimpan ke DB
                opt.textContent = text;
                opt.dataset.id = value; // simpan id district di data-id
                return opt;
            }

            async function loadKecamatan() {
                selectKecamatan.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                selectDesa.innerHTML = '<option value="">-- Pilih Desa --</option>';

                try {
                    const url = "{{ route('wilayah.kecamatan') }}" + '?kabupaten=' +
                        encodeURIComponent(
                            currentKabupatenName);
                    const res = await fetch(url);
                    if (!res.ok) {
                        console.error('Gagal fetch kecamatan:', res.status, res.statusText);
                        return;
                    }

                    const list = await res.json();
                    list.forEach(dist => {
                        selectKecamatan.appendChild(createOption(dist.id, dist.name));
                    });
                } catch (e) {
                    console.error('Error load kecamatan:', e);
                }
            }

            async function loadDesaForKecamatan() {
                selectDesa.innerHTML = '<option value="">-- Pilih Desa --</option>';

                const selectedOpt = selectKecamatan.options[selectKecamatan.selectedIndex];
                const districtId = selectedOpt ? selectedOpt.dataset.id : null;

                if (!districtId) return;

                try {
                    const url = "{{ url('/wilayah/desa') }}/" + districtId;
                    const res = await fetch(url);
                    if (!res.ok) {
                        console.error('Gagal fetch desa:', res.status, res.statusText);
                        return;
                    }

                    const list = await res.json();
                    list.forEach(vill => {
                        const opt = document.createElement('option');
                        opt.value = vill.name; // simpan nama desa
                        opt.textContent = vill.name;
                        selectDesa.appendChild(opt);
                    });
                } catch (e) {
                    console.error('Error load desa:', e);
                }
            }

            selectKecamatan.addEventListener('change', loadDesaForKecamatan);

            // Load kecamatan ketika halaman pertama dibuka
            loadKecamatan();
        });
    </script>
@endsection
