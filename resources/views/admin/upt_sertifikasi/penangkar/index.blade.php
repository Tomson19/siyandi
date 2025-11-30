@extends('layouts.bootstrap')

@section('content')
    <style>
        body {
            margin-top: -70px;
        }

        /* ==== MODAL PENANGKAR: SCROLLABLE + FOOTER SELALU TERLIHAT ==== */
        .penangkar-modal .modal-dialog {
            max-width: 960px;
            margin: 1.5rem auto;
        }

        .penangkar-modal .modal-content {
            max-height: calc(100vh - 2rem);
            display: flex;
            flex-direction: column;
        }

        .penangkar-modal .modal-body {
            overflow-y: auto;
            padding: 1rem 1rem;
        }

        .penangkar-modal .modal-header,
        .penangkar-modal .modal-footer {
            flex-shrink: 0;
        }

        .penangkar-modal .modal-footer {
            border-top: 1px solid #dee2e6;
            background-color: #fff;
        }

        /* ==== TABEL PENANGKAR ==== */

        .table-penangkar-wrapper {
            max-height: 70vh;
            overflow-x: auto;
            overflow-y: auto;
        }

        .table-penangkar {
            font-size: 0.65rem;
            /* PERHATIAN: jangan pakai white-space: nowrap di sini lagi */
        }

        .table-penangkar th,
        .table-penangkar td {
            text-align: center;
            vertical-align: middle;
        }

        .table-penangkar thead th {
            padding-top: 0.4rem;
            padding-bottom: 0.4rem;
        }

        .table-penangkar tbody td {
            padding-top: 0.2rem;
            padding-bottom: 0.2rem;
        }

        /* Kolom yang boleh teksnya turun ke bawah */
        .table-penangkar .wrap-text {
            white-space: normal;
            /* izinkan teks membungkus */
            word-break: break-word;
            /* pecah kata panjang kalau perlu */
        }
    </style>


    <div class="container py-4">

        {{-- HEADER + TOMBOL TAMBAH --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-primary mb-0">Data Penangkar Benih</h3>

            <button type="button" class="btn btn-success btn-sm rounded-pill" data-bs-toggle="modal"
                data-bs-target="#modalCreatePenangkar">
                <i class="bi bi-plus-circle"></i> Tambah Penangkar
            </button>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                {{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                {{ session('error') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- TABEL PENANGKAR --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-success text-white fw-bold d-flex justify-content-between align-items-center">
                <span>Tabel Penangkar Benih</span>

                {{-- FILTER --}}
                <form method="GET" action="{{ route('admin.upt_sertifikasi.penangkar.index') }}" class="row g-1">
                    <div class="col-auto">
                        <select name="tanaman_id" class="form-select form-select-sm">
                            <option value="">Semua Komoditas</option>
                            @foreach ($tanamanList as $t)
                                <option value="{{ $t->id }}" {{ request('tanaman_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->nama_tanaman }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FILTER KABUPATEN --}}
                    <div class="col-auto">
                        <select name="kabupaten_id" class="form-select form-select-sm">
                            <option value="">Semua Kabupaten</option>
                            @foreach ($kabupatenList as $kab)
                                <option value="{{ $kab->id }}"
                                    {{ request('kabupaten_id') == $kab->id ? 'selected' : '' }}>
                                    {{ $kab->nama_kabupaten }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto">
                        <input type="text" name="q" class="form-control form-control-sm"
                            placeholder="Cari nama / desa / kecamatan" value="{{ request('q') }}">
                    </div>
                    <div class="col-auto d-flex align-items-center gap-1">
                        <button type="submit" class="btn btn-light btn-sm">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('admin.upt_sertifikasi.penangkar.index') }}"
                            class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-repeat"></i>
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive table-penangkar-wrapper">
                    <table class="table table-bordered table-sm align-middle table-penangkar">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:4%;">No</th>
                                <th style="width:10%;">Komoditas</th>
                                <th style="width:18%;" class="wrap-text">Nama Produsen Benih Perorangan/Perusahaan</th>
                                <th style="width:14%;">NIB &<br> Tanggal</th>
                                <th style="width:16%;">Sertifikat/Izin Usaha Nomor & Tanggal</th>
                                <th style="width:8%;">Luas<br> Areal (Ha)</th>
                                <th style="width:8%;">Jumlah Sertifikasi Benih<br>Tahun Berjalan(Batang)</th>
                                <th style="width:14%;">Jalan/<br>Tempat</th>
                                <th style="width:10%;">Desa/<br>Kelurahan</th>
                                <th style="width:10%;">Kecamatan</th>
                                <th style="width:10%;">Kabupaten</th>
                                <th style="width:6%;">LU/LS</th>
                                <th style="width:6%;">BT</th>
                                <th style="width:8%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penangkarList as $row)
                                <tr>
                                    <td>
                                        {{ ($penangkarList->firstItem() ?? 1) + $loop->index }}
                                    </td>
                                    <td>{{ $row->tanaman->nama_tanaman ?? '-' }}</td>
                                    <td>{{ $row->nama_penangkar }}</td>
                                    <td>{{ $row->nib_dan_tanggal ?? '-' }}</td>
                                    <td>{{ $row->sertifikat_izin_usaha_nomor_dan_tanggal ?? '-' }}</td>
                                    <td>{{ $row->luas_areal_ha ?? '-' }}</td>
                                    <td>{{ $row->jumlah_sertifikasi ?? '-' }}</td>
                                    <td>{{ $row->jalan ?? '-' }}</td>
                                    <td>{{ $row->desa ?? '-' }}</td>
                                    <td>{{ $row->kecamatan ?? '-' }}</td>
                                    <td>{{ $row->kabupaten->nama_kabupaten ?? '-' }}</td>
                                    <td>{{ $row->latitude ?? '-' }}</td>
                                    <td>{{ $row->longitude ?? '-' }}</td>
                                    <td>
                                        <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditPenangkar{{ $row->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <form action="{{ route('admin.upt_sertifikasi.penangkar.destroy', $row->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Hapus data penangkar ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted">
                                        Belum ada data penangkar. Tambahkan melalui tombol di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if ($penangkarList instanceof \Illuminate\Contracts\Pagination\Paginator && $penangkarList->hasPages())
                    <div class="mt-2">
                        {{ $penangkarList->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ================== MODAL CREATE PENANGKAR ================== --}}
    <div class="modal fade penangkar-modal" id="modalCreatePenangkar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Tambah Penangkar Benih</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('admin.upt_sertifikasi.penangkar.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-2">

                            {{-- KOMODITAS --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Komoditas</label>
                                <select name="tanaman_id" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Komoditas --</option>
                                    @foreach ($tanamanList as $t)
                                        <option value="{{ $t->id }}"
                                            {{ old('tanaman_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->nama_tanaman }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- KABUPATEN --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kabupaten</label>
                                <select name="kabupaten_id" id="create-kabupaten" class="form-select form-select-sm"
                                    required>
                                    <option value="">-- Pilih Kabupaten --</option>
                                    @foreach ($kabupatenList as $kab)
                                        <option value="{{ $kab->id }}" data-nama="{{ $kab->nama_kabupaten }}"
                                            {{ old('kabupaten_id') == $kab->id ? 'selected' : '' }}>
                                            {{ $kab->nama_kabupaten }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- KECAMATAN (API) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kecamatan</label>
                                <select name="kecamatan" id="create-kecamatan" class="form-select form-select-sm">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>

                            {{-- DESA (API) --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Desa / Kelurahan</label>
                                <select name="desa" id="create-desa" class="form-select form-select-sm">
                                    <option value="">-- Pilih Desa --</option>
                                </select>
                            </div>
                            {{-- NAMA PENANGKAR --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Nama Produsen Benih Perorangan/Perusahaan
                                </label>
                                <input type="text" name="nama_penangkar" class="form-control form-control-sm"
                                    value="{{ old('nama_penangkar') }}" required>
                            </div>

                            {{-- NIB & TANGGAL --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIB & Tanggal</label>
                                <input type="text" name="nib_dan_tanggal" class="form-control form-control-sm"
                                    value="{{ old('nib_dan_tanggal') }}" placeholder="Contoh: 1234567890 / 12-01-2024">
                            </div>

                            {{-- SERTIFIKAT / IZIN USAHA NOMOR & TANGGAL --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Sertifikat / Izin Usaha Prod. Benih Nomor & Tanggal
                                </label>
                                <input type="text" name="sertifikat_izin_usaha_nomor_dan_tanggal"
                                    class="form-control form-control-sm"
                                    value="{{ old('sertifikat_izin_usaha_nomor_dan_tanggal') }}"
                                    placeholder="Contoh: 123/ABC/2024 / 20-02-2024">
                            </div>

                            {{-- LUAS AREAL (HA) --}}
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Luas Areal (Ha)</label>
                                <input type="number" step="0.01" name="luas_areal_ha"
                                    class="form-control form-control-sm" value="{{ old('luas_areal_ha') }}"
                                    placeholder="Contoh: 2.50">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Jumlah Sertifikasi Benih</label>
                                <input type="number" step="0.01" name="jumlah_sertifikasi"
                                    class="form-control form-control-sm" value="{{ old('jumlah_sertifikasi') }}"
                                    placeholder="Contoh: 1500">
                            </div>

                            {{-- JALAN / TEMPAT --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jalan / Tempat Pembibitan</label>
                                <input type="text" name="jalan" class="form-control form-control-sm"
                                    value="{{ old('jalan') }}">
                            </div>

                            {{-- KOORDINAT --}}
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Latitude (LU/LS)</label>
                                <input type="text" name="latitude" class="form-control form-control-sm"
                                    value="{{ old('latitude') }}" placeholder="1.1501">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Longitude (BT)</label>
                                <input type="text" name="longitude" class="form-control form-control-sm"
                                    value="{{ old('longitude') }}" placeholder="102.7587">
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

    {{-- ================== MODAL EDIT PENANGKAR (PER BARIS) ================== --}}
    @foreach ($penangkarList as $row)
        <div class="modal fade penangkar-modal" id="modalEditPenangkar{{ $row->id }}" tabindex="-1"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            Edit Penangkar ({{ $row->nama_penangkar }})
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('admin.upt_sertifikasi.penangkar.update', $row->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <div class="row g-2">

                                {{-- KOMODITAS --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Komoditas</label>
                                    <select name="tanaman_id" class="form-select form-select-sm" required>
                                        <option value="">-- Pilih Komoditas --</option>
                                        @foreach ($tanamanList as $t)
                                            <option value="{{ $t->id }}"
                                                {{ $t->id == $row->tanaman_id ? 'selected' : '' }}>
                                                {{ $t->nama_tanaman }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- KABUPATEN --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kabupaten</label>
                                    <select name="kabupaten_id" class="form-select form-select-sm edit-kabupaten"
                                        id="edit-kabupaten-{{ $row->id }}" data-row-id="{{ $row->id }}"
                                        required>
                                        <option value="">-- Pilih Kabupaten --</option>
                                        @foreach ($kabupatenList as $kab)
                                            <option value="{{ $kab->id }}" data-nama="{{ $kab->nama_kabupaten }}"
                                                {{ $kab->id == $row->kabupaten_id ? 'selected' : '' }}>
                                                {{ $kab->nama_kabupaten }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- KECAMATAN (EDIT: SELECT + API) --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kecamatan</label>
                                    <select name="kecamatan" id="edit-kecamatan-{{ $row->id }}"
                                        class="form-select form-select-sm edit-kecamatan"
                                        data-row-id="{{ $row->id }}">
                                        @if ($row->kecamatan)
                                            <option value="{{ $row->kecamatan }}">{{ $row->kecamatan }}</option>
                                        @else
                                            <option value="">-- Pilih Kecamatan --</option>
                                        @endif
                                    </select>
                                </div>

                                {{-- DESA (EDIT: SELECT + API) --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Desa / Kelurahan</label>
                                    <select name="desa" id="edit-desa-{{ $row->id }}"
                                        class="form-select form-select-sm edit-desa" data-row-id="{{ $row->id }}">
                                        @if ($row->desa)
                                            <option value="{{ $row->desa }}">{{ $row->desa }}</option>
                                        @else
                                            <option value="">-- Pilih Desa --</option>
                                        @endif
                                    </select>
                                </div>


                                {{-- NAMA PENANGKAR --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Nama Produsen Benih Perorangan/Perusahaan
                                    </label>
                                    <input type="text" name="nama_penangkar" class="form-control form-control-sm"
                                        value="{{ $row->nama_penangkar }}" required>
                                </div>

                                {{-- NIB & TANGGAL --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIB & Tanggal</label>
                                    <input type="text" name="nib_dan_tanggal" class="form-control form-control-sm"
                                        value="{{ $row->nib_dan_tanggal }}">
                                </div>

                                {{-- SERTIFIKAT / IZIN USAHA NOMOR & TANGGAL --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Sertifikat / Izin Usaha Prod. Benih Nomor & Tanggal
                                    </label>
                                    <input type="text" name="sertifikat_izin_usaha_nomor_dan_tanggal"
                                        class="form-control form-control-sm"
                                        value="{{ $row->sertifikat_izin_usaha_nomor_dan_tanggal }}">
                                </div>

                                {{-- LUAS AREAL (HA) --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Luas Areal (Ha)</label>
                                    <input type="number" step="0.01" name="luas_areal_ha"
                                        class="form-control form-control-sm" value="{{ $row->luas_areal_ha }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Jumlah Sertifikasi Benih</label>
                                    <input type="number" step="0.01" name="jumlah_sertifikasi"
                                        class="form-control form-control-sm" value="{{ $row->jumlah_sertifikasi }}">
                                </div>

                                {{-- JALAN / TEMPAT --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Jalan / Tempat Pembibitan</label>
                                    <input type="text" name="jalan" class="form-control form-control-sm"
                                        value="{{ $row->jalan }}">
                                </div>

                                {{-- KOORDINAT --}}
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Latitude (LU/LS)</label>
                                    <input type="text" name="latitude" class="form-control form-control-sm"
                                        value="{{ $row->latitude }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Longitude (BT)</label>
                                    <input type="text" name="longitude" class="form-control form-control-sm"
                                        value="{{ $row->longitude }}">
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

    {{-- ================== SCRIPT: LOAD KECAMATAN & DESA (CREATE MODAL) ================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Helper umum
            function createOption(value, text) {
                const opt = document.createElement('option');
                opt.value = text; // nama yang disimpan ke DB
                opt.textContent = text;
                opt.dataset.id = value; // simpan id district di data-id
                return opt;
            }

            // ================== CREATE ==================
            const selectKabupatenCreate = document.getElementById('create-kabupaten');
            const selectKecamatanCreate = document.getElementById('create-kecamatan');
            const selectDesaCreate = document.getElementById('create-desa');

            if (selectKabupatenCreate && selectKecamatanCreate && selectDesaCreate) {

                function resetKecamatanCreate() {
                    selectKecamatanCreate.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                }

                function resetDesaCreate() {
                    selectDesaCreate.innerHTML = '<option value="">-- Pilih Desa --</option>';
                }

                async function loadKecamatanForKabupatenCreate() {
                    resetKecamatanCreate();
                    resetDesaCreate();

                    const selectedOpt = selectKabupatenCreate.options[selectKabupatenCreate.selectedIndex];
                    if (!selectedOpt) return;

                    let kabupatenName = selectedOpt.dataset.nama || selectedOpt.textContent.trim();
                    if (!kabupatenName) {
                        console.warn('Nama kabupaten kosong (create), tidak bisa load kecamatan.');
                        return;
                    }

                    try {
                        const url = "{{ route('wilayah.kecamatan') }}?kabupaten=" + encodeURIComponent(
                            kabupatenName);
                        const res = await fetch(url);
                        if (!res.ok) {
                            console.error('Gagal fetch kecamatan (create):', res.status, res.statusText);
                            return;
                        }

                        const list = await res.json();
                        list.forEach(dist => {
                            selectKecamatanCreate.appendChild(createOption(dist.id, dist.name));
                        });
                    } catch (e) {
                        console.error('Error load kecamatan (create):', e);
                    }
                }

                async function loadDesaForKecamatanCreate() {
                    resetDesaCreate();

                    const selectedOpt = selectKecamatanCreate.options[selectKecamatanCreate.selectedIndex];
                    const districtId = selectedOpt ? selectedOpt.dataset.id : null;
                    if (!districtId) return;

                    try {
                        const url = "{{ url('/wilayah/desa') }}/" + districtId;
                        const res = await fetch(url);
                        if (!res.ok) {
                            console.error('Gagal fetch desa (create):', res.status, res.statusText);
                            return;
                        }

                        const list = await res.json();
                        list.forEach(vill => {
                            const opt = document.createElement('option');
                            opt.value = vill.name;
                            opt.textContent = vill.name;
                            selectDesaCreate.appendChild(opt);
                        });
                    } catch (e) {
                        console.error('Error load desa (create):', e);
                    }
                }

                selectKabupatenCreate.addEventListener('change', loadKecamatanForKabupatenCreate);
                selectKecamatanCreate.addEventListener('change', loadDesaForKecamatanCreate);

                // kalau sudah ada kabupaten terpilih (old value), load kecamatan awal
                if (selectKabupatenCreate.value) {
                    loadKecamatanForKabupatenCreate();
                }
            }

            // ================== EDIT (SEMUA MODAL) ==================
            document.querySelectorAll('.edit-kabupaten').forEach(function(selectKabupatenEdit) {
                const rowId = selectKabupatenEdit.dataset.rowId;
                const selectKecamatanEdit = document.getElementById('edit-kecamatan-' + rowId);
                const selectDesaEdit = document.getElementById('edit-desa-' + rowId);

                if (!selectKecamatanEdit || !selectDesaEdit) {
                    console.warn('Element kecamatan/desa edit tidak lengkap untuk row', rowId);
                    return;
                }

                function resetKecamatanEdit() {
                    selectKecamatanEdit.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                }

                function resetDesaEdit() {
                    selectDesaEdit.innerHTML = '<option value="">-- Pilih Desa --</option>';
                }

                async function loadKecamatanForKabupatenEdit() {
                    resetKecamatanEdit();
                    resetDesaEdit();

                    const selectedOpt = selectKabupatenEdit.options[selectKabupatenEdit.selectedIndex];
                    if (!selectedOpt) return;

                    let kabupatenName = selectedOpt.dataset.nama || selectedOpt.textContent.trim();
                    if (!kabupatenName) {
                        console.warn('Nama kabupaten kosong (edit), tidak bisa load kecamatan.');
                        return;
                    }

                    console.log('Load kecamatan (edit) untuk kabupaten:', kabupatenName);

                    try {
                        const url = "{{ route('wilayah.kecamatan') }}?kabupaten=" + encodeURIComponent(
                            kabupatenName);
                        const res = await fetch(url);
                        if (!res.ok) {
                            console.error('Gagal fetch kecamatan (edit):', res.status, res.statusText);
                            return;
                        }

                        const list = await res.json();
                        list.forEach(dist => {
                            selectKecamatanEdit.appendChild(createOption(dist.id, dist.name));
                        });
                    } catch (e) {
                        console.error('Error load kecamatan (edit):', e);
                    }
                }

                async function loadDesaForKecamatanEdit() {
                    resetDesaEdit();

                    const selectedOpt = selectKecamatanEdit.options[selectKecamatanEdit.selectedIndex];
                    const districtId = selectedOpt ? selectedOpt.dataset.id : null;
                    if (!districtId) return;

                    try {
                        const url = "{{ url('/wilayah/desa') }}/" + districtId;
                        const res = await fetch(url);
                        if (!res.ok) {
                            console.error('Gagal fetch desa (edit):', res.status, res.statusText);
                            return;
                        }

                        const list = await res.json();
                        list.forEach(vill => {
                            const opt = document.createElement('option');
                            opt.value = vill.name;
                            opt.textContent = vill.name;
                            selectDesaEdit.appendChild(opt);
                        });
                    } catch (e) {
                        console.error('Error load desa (edit):', e);
                    }
                }

                // Event saat ganti kabupaten / kecamatan di modal edit
                selectKabupatenEdit.addEventListener('change', loadKecamatanForKabupatenEdit);
                selectKecamatanEdit.addEventListener('change', loadDesaForKecamatanEdit);

                // Catatan:
                // - Saat pertama kali buka modal edit, select kecamatan/desa berisi nilai lama (dari Blade).
                // - Begitu kabupaten diganti, list kecamatan & desa akan di-reset dan di-load baru dari API.
            });
        });
    </script>
@endsection
