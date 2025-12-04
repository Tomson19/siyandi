@extends('layouts.bootstrap')

@section('title', 'Daftar Permohonan Saya')

@section('content')
    <style>
        body {
            margin-top: -50px;
        }
    </style>
    <div class="container py-4">

        {{-- HEADER --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3 gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-primary text-uppercase" style="letter-spacing: .05em;">
                    <i class="bi bi-journal-text me-1"></i> Daftar Permohonan Benih
                </h5>
            </div>

            <div class="d-grid d-sm-inline-block">
                <button type="button" class="btn btn-primary btn-sm shadow-sm rounded-pill px-3" data-bs-toggle="modal"
                    data-bs-target="#modalCreatePermohonan">
                    <i class="bi bi-plus-circle me-1"></i> Permohonan Baru
                </button>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @foreach (['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'] as $key => $color)
            @if (session($key))
                <div class="alert alert-{{ $color }} small py-2 mb-2 shadow-sm border-0">
                    {!! session($key) !!}
                </div>
            @endif
        @endforeach

        {{-- ERROR VALIDASI --}}
        @if ($errors->any())
            <div class="alert alert-danger small py-2 mb-3 shadow-sm border-0">
                <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- CARD TABLE --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3 p-md-3">

                @if ($permohonan->isEmpty())
                    <div class="alert alert-info small text-center text-uppercase mb-0">
                        Anda belum memiliki permohonan.
                    </div>
                @else
                    {{-- Info atas tabel --}}
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-2 gap-1">
                        <span class="small text-muted">
                            Total permohonan: <strong>{{ $permohonan->count() }}</strong>
                        </span>
                    </div>

                    {{-- TABLE RESPONSIVE WRAPPER --}}
                    <div class="table-wrapper table-responsive">
                        <table class="table table-hover align-middle text-center small mb-0" id="permohonanTable">
                            <thead class="table-light align-middle text-uppercase">
                                <tr>
                                    <th style="width: 4%;">No</th>
                                    <th style="width: 18%;" class="text-nowrap">Nama Pemohon</th>
                                    <th style="width: 15%;" class="text-nowrap d-none d-md-table-cell">Jenis <br>Tanaman</th>
                                    <th style="width: 10%;" class="text-nowrap d-none d-lg-table-cell">Jenis <br>Benih</th>
                                    <th style="width: 8%;" class="text-nowrap">Jumlah</th>
                                    <th style="width: 12%;" class="text-nowrap d-none d-md-table-cell">Status <br>Utama</th>
                                    <th style="width: 10%;" class="text-nowrap d-none d-lg-table-cell">Tipe <br>Permohonan</th>
                                    <th style="width: 13%;" class="text-nowrap d-none d-lg-table-cell">Status <br> Pembayaran
                                    </th>
                                    <th style="width: 10%;" class="text-nowrap d-none d-md-table-cell">Status <br>Pengambilan
                                    </th>
                                    <th style="width: 10%;" class="text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permohonan as $index => $item)
                                    @php
                                        $isLocked = in_array($item->status, [
                                            'Sedang Diverifikasi',
                                            'Disetujui',
                                            'Ditolak',
                                            'Dibatalkan',
                                        ]);

                                        // badge status utama
                                        $statusClass = 'bg-secondary';
                                        if ($item->status === 'Menunggu Dokumen') {
                                            $statusClass = 'bg-secondary';
                                        } elseif ($item->status === 'Sedang Diverifikasi') {
                                            $statusClass = 'bg-warning text-dark';
                                        } elseif ($item->status === 'Perbaikan') {
                                            $statusClass = 'bg-info text-dark';
                                        } elseif ($item->status === 'Disetujui') {
                                            $statusClass = 'bg-success';
                                        } elseif (in_array($item->status, ['Ditolak', 'Dibatalkan'])) {
                                            $statusClass = 'bg-danger';
                                        }

                                        // badge tipe permohonan (Gratis / Berbayar)
                                        $tipeClass = $item->tipe_pembayaran === 'Berbayar' ? 'bg-danger' : 'bg-success';

                                        // badge status pembayaran
                                        $statusPembayaranClass = 'bg-secondary';
                                        if ($item->status_pembayaran === 'Menunggu') {
                                            $statusPembayaranClass = 'bg-secondary';
                                        } elseif ($item->status_pembayaran === 'Menunggu Verifikasi') {
                                            $statusPembayaranClass = 'bg-warning text-dark';
                                        } elseif ($item->status_pembayaran === 'Berhasil') {
                                            $statusPembayaranClass = 'bg-success';
                                        } elseif ($item->status_pembayaran === 'Gagal') {
                                            $statusPembayaranClass = 'bg-danger';
                                        }

                                        // badge status pengambilan
                                        $statusAmbilClass = 'bg-secondary';
                                        if ($item->status_pengambilan === 'Belum Diambil') {
                                            $statusAmbilClass = 'bg-secondary';
                                        } elseif ($item->status_pengambilan === 'Selesai') {
                                            $statusAmbilClass = 'bg-success';
                                        } elseif ($item->status_pengambilan === 'Dibatalkan') {
                                            $statusAmbilClass = 'bg-danger';
                                        }
                                    @endphp

                                    <tr>
                                        {{-- No --}}
                                        <td>{{ $index + 1 }}</td>

                                        {{-- Nama Pemohon (tetap tampil di semua device) --}}
                                        <td class="text-start">
                                            <div class="fw-semibold text-uppercase" style="font-size: .78rem;">
                                                {{ $item->nama }}
                                            </div>
                                            <div class="text-muted small">
                                                NIK: {{ $item->nik }}
                                            </div>

                                            {{-- Info penting yang disembunyikan dari kolom lain di mobile, bisa dirangkum di sini --}}
                                            <div class="mt-1 d-md-none small">
                                                <div>
                                                    <span class="text-muted">Tanaman:</span>
                                                    <strong>{{ strtoupper($item->jenisTanaman->nama_tanaman ?? '-') }}</strong>
                                                </div>
                                                <div>
                                                    <span class="text-muted">Status:</span>
                                                    <span class="badge {{ $statusClass }} small">
                                                        {{ $item->status }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-muted">Tipe:</span>
                                                    <span class="badge {{ $tipeClass }} small">
                                                        {{ $item->tipe_pembayaran ?? '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Jenis Tanaman (desktop / md ke atas) --}}
                                        <td class="d-none d-md-table-cell">
                                            <span class="fw-semibold text-dark" style="font-size: .78rem;">
                                                {{ strtoupper($item->jenisTanaman->nama_tanaman ?? '-') }}
                                            </span>
                                        </td>

                                        {{-- Jenis Benih (lg ke atas) --}}
                                        <td class="d-none d-lg-table-cell">
                                            <span class="badge bg-light text-dark border small">
                                                {{ $item->jenis_benih ?? '-' }}
                                            </span>
                                        </td>

                                        {{-- Jumlah --}}
                                        <td>{{ $item->jumlah_tanaman }}</td>

                                        {{-- Status Utama (md ke atas) --}}
                                        <td class="d-none d-md-table-cell">
                                            <span class="badge {{ $statusClass }} small px-3">
                                                {{ $item->status }}
                                            </span>
                                        </td>

                                        {{-- Tipe Permohonan (Gratis / Berbayar) - lg ke atas --}}
                                        <td class="d-none d-lg-table-cell">
                                            <span class="badge {{ $tipeClass }} small px-3">
                                                {{ $item->tipe_pembayaran ?? '-' }}
                                            </span>
                                        </td>

                                        {{-- Status Pembayaran - lg ke atas --}}
                                        <td class="d-none d-lg-table-cell">
                                            @if ($item->tipe_pembayaran === 'Berbayar')
                                                <span class="badge {{ $statusPembayaranClass }} small px-3">
                                                    {{ $item->status_pembayaran ?? '–' }}
                                                </span>
                                            @else
                                                <span class="text-muted small fst-italic">Tidak Berlaku</span>
                                            @endif
                                        </td>

                                        {{-- Status Pengambilan - md ke atas --}}
                                        <td class="d-none d-md-table-cell">
                                            <span class="badge {{ $statusAmbilClass }} small px-3">
                                                {{ $item->status_pengambilan ?? '-' }}
                                            </span>
                                        </td>

                                        {{-- Aksi --}}
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <a href="{{ route('pemohon.permohonan.show', $item->id) }}"
                                                    class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>

                                                <button type="button"
                                                    class="btn btn-outline-warning btn-sm rounded-pill px-3 btn-edit-permohonan {{ $isLocked ? 'disabled' : '' }}"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditPermohonan"
                                                    data-id="{{ $item->id }}" data-nama="{{ $item->nama }}"
                                                    data-nik="{{ $item->nik }}" data-alamat="{{ $item->alamat }}"
                                                    data-no_telp="{{ $item->no_telp }}"
                                                    data-jenis_tanaman_id="{{ $item->jenis_tanaman_id }}"
                                                    data-jenis_benih="{{ $item->jenis_benih }}"
                                                    data-tipe_pembayaran="{{ $item->tipe_pembayaran }}"
                                                    data-jumlah_tanaman="{{ $item->jumlah_tanaman }}"
                                                    data-luas_area="{{ $item->luas_area }}"
                                                    data-latitude="{{ $item->latitude }}"
                                                    data-longitude="{{ $item->longitude }}"
                                                    {{ $isLocked ? 'disabled' : '' }}>
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @endif
            </div>
        </div>
    </div>


    {{-- ===========================
         MODAL: CREATE PERMOHONAN
    ============================ --}}
    <div class="modal fade" id="modalCreatePermohonan" tabindex="-1" aria-labelledby="modalCreatePermohonanLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <form id="permohonanCreateForm" action="{{ route('pemohon.permohonan.store') }}" method="POST">
                    @csrf
                    <div class="modal-header py-2 border-0 border-bottom">
                        <div>
                            <h6 class="modal-title fw-bold text-primary mb-0" id="modalCreatePermohonanLabel">
                                🌱 Formulir Permohonan Benih
                            </h6>
                            <p class="mb-0 text-muted small">Isi data dengan lengkap untuk mengajukan permohonan benih.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">

                        {{-- Data Pemohon --}}
                        <div class="section-block mb-3">
                            <h6
                                class="fw-semibold text-primary mb-3 border-start border-4 border-primary ps-2 small text-uppercase">
                                🧍 Data Pemohon
                            </h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Nama Lengkap</label>
                                    <input type="text" name="nama" value="{{ old('nama') }}"
                                        class="form-control form-control-sm shadow-sm" placeholder="Masukkan nama lengkap"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">NIK</label>
                                    <input type="text" name="nik" value="{{ old('nik') }}"
                                        class="form-control form-control-sm shadow-sm"
                                        placeholder="Nomor Induk Kependudukan" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Alamat Lengkap</label>
                                    <input type="text" name="alamat" value="{{ old('alamat') }}"
                                        class="form-control form-control-sm shadow-sm"
                                        placeholder="Tulis alamat lengkap di sini..." required>
                                </div>
                            </div>


                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="no_telp" value="{{ old('no_telp') }}"
                                        class="form-control form-control-sm shadow-sm" placeholder="Contoh: 0812xxxxxxx"
                                        required>
                                </div>


                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Jenis Tanaman</label>
                                    <select name="jenis_tanaman_id" id="create_jenis_tanaman_id"
                                        class="form-select form-select-sm shadow-sm" required>
                                        <option value="">-- Pilih Jenis Tanaman --</option>
                                        @foreach ($jenisTanaman as $tanaman)
                                            <option value="{{ $tanaman->id }}"
                                                {{ old('jenis_tanaman_id') == $tanaman->id ? 'selected' : '' }}>
                                                {{ $tanaman->nama_tanaman }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Jumlah Tanaman</label>
                                    <input type="number" name="jumlah_tanaman" value="{{ old('jumlah_tanaman') }}"
                                        class="form-control form-control-sm shadow-sm" required min="1">
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Luas Area (Ha)</label>
                                    <input type="number" name="luas_area" step="0.01"
                                        value="{{ old('luas_area') }}" class="form-control form-control-sm shadow-sm"
                                        required min="0.1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Jenis Benih</label>
                                    <select name="jenis_benih" class="form-select form-select-sm shadow-sm" required>
                                        <option value="">-- Pilih Jenis Benih --</option>
                                        <option value="Biji" {{ old('jenis_benih') == 'Biji' ? 'selected' : '' }}>
                                            Biji
                                        </option>
                                        <option value="Siap Tanam"
                                            {{ old('jenis_benih') == 'Siap Tanam' ? 'selected' : '' }}>Siap Tanam
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold d-flex justify-content-between">
                                        <span>Tipe Permohonan</span>
                                    </label>

                                    {{-- hidden real value untuk dikirim ke server --}}
                                    <input type="hidden" name="tipe_pembayaran" id="create_tipe_pembayaran"
                                        value="">

                                    <div
                                        class="border rounded-3 px-3 py-2 bg-white shadow-sm d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-lock-fill text-muted"></i>
                                            <span class="small text-muted">Tipe permohonan:</span>
                                        </div>
                                        <span id="create_tipe_label" class="badge bg-secondary small px-3">
                                            -
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Latitude (opsional)</label>
                                    <input type="text" name="latitude" value="{{ old('latitude') }}"
                                        class="form-control form-control-sm shadow-sm" placeholder="Contoh: -6.200000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Longitude (opsional)</label>
                                    <input type="text" name="longitude" value="{{ old('longitude') }}"
                                        class="form-control form-control-sm shadow-sm" placeholder="Contoh: 106.816666">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm small mb-0">
                            <strong>💡 Catatan:</strong> Setelah permohonan dibuat, sistem akan otomatis menghasilkan
                            <strong>dua dokumen</strong>: <b>Surat Permohonan</b> dan <b>Surat Pernyataan</b>.<br>
                            Silakan download dan tanda tangani kedua surat tersebut, kemudian unggah kembali bersama
                            dokumen pendukung melalui menu <b>"Upload Dokumen"</b>.
                        </div>
                    </div>
                    <div class="modal-footer py-2 border-0 border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill"
                            data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" id="btnSubmitCreate" class="btn btn-primary btn-sm rounded-pill">
                            <span id="btnTextCreate"><i class="bi bi-send-fill me-1"></i> Kirim Permohonan</span>
                            <div id="loadingSpinnerCreate" class="spinner-border spinner-border-sm text-light d-none"
                                role="status"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===========================
         MODAL: EDIT PERMOHONAN
    ============================ --}}
    <div class="modal fade" id="modalEditPermohonan" tabindex="-1" aria-labelledby="modalEditPermohonanLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <form id="permohonanEditForm" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- HEADER --}}
                    <div class="modal-header py-2 border-0 border-bottom">
                        <div>
                            <h6 class="modal-title fw-bold text-primary mb-0" id="modalEditPermohonanLabel">
                                ✏️ Edit Permohonan Benih
                            </h6>
                            <p class="mb-0 text-muted small">Ubah data permohonan sebelum diverifikasi admin.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    {{-- BODY --}}
                    <div class="modal-body">
                        {{-- Data Pemohon --}}
                        <div class="section-block mb-3">
                            <h6
                                class="fw-semibold text-primary mb-3 border-start border-4 border-primary ps-2 small text-uppercase">
                                🧍 Data Pemohon
                            </h6>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Nama Lengkap</label>
                                    <input type="text" name="nama" id="edit_nama"
                                        class="form-control form-control-sm shadow-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">NIK</label>
                                    <input type="text" name="nik" id="edit_nik"
                                        class="form-control form-control-sm shadow-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Alamat Lengkap</label>
                                    <input type="text" name="alamat" id="edit_alamat"
                                        class="form-control form-control-sm shadow-sm" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="no_telp" id="edit_no_telp"
                                        class="form-control form-control-sm shadow-sm" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Jenis Tanaman</label>
                                    <select name="jenis_tanaman_id" id="edit_jenis_tanaman_id"
                                        class="form-select form-select-sm shadow-sm">
                                        <option value="">-- Pilih Jenis Tanaman --</option>
                                        @foreach ($jenisTanaman as $tanaman)
                                            <option value="{{ $tanaman->id }}">{{ $tanaman->nama_tanaman }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Jumlah Tanaman</label>
                                    <input type="number" name="jumlah_tanaman" id="edit_jumlah_tanaman"
                                        class="form-control form-control-sm shadow-sm" min="1">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Luas Area (Ha)</label>
                                    <input type="number" name="luas_area" id="edit_luas_area" step="0.01"
                                        class="form-control form-control-sm shadow-sm" min="0.1">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Jenis Benih</label>
                                    <select name="jenis_benih" id="edit_jenis_benih"
                                        class="form-select form-select-sm shadow-sm">
                                        <option value="">-- Pilih Jenis Benih --</option>
                                        <option value="Biji">Biji</option>
                                        <option value="Siap Tanam">Siap Tanam</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold d-flex justify-content-between">
                                        <span>Tipe Permohonan</span>
                                    </label>

                                    {{-- hidden real value --}}
                                    <input type="hidden" name="tipe_pembayaran" id="edit_tipe_pembayaran"
                                        value="">

                                    <div
                                        class="border rounded-3 px-3 py-2 bg-white shadow-sm d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-lock-fill text-muted"></i>
                                            <span class="small text-muted">Tipe permohonan:</span>
                                        </div>
                                        <span id="edit_tipe_label" class="badge bg-secondary small px-3">
                                            -
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Latitude (opsional)</label>
                                    <input type="text" name="latitude" id="edit_latitude"
                                        class="form-control form-control-sm shadow-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Longitude (opsional)</label>
                                    <input type="text" name="longitude" id="edit_longitude"
                                        class="form-control form-control-sm shadow-sm">
                                </div>
                            </div>

                            <p class="text-muted small mb-0">
                                Setelah data diubah, sistem akan menghasilkan ulang surat permohonan &amp; pernyataan.
                                Anda harus mengunduh, menandatangani, dan mengunggah ulang dokumen tersebut.
                            </p>
                        </div> {{-- end section-block --}}
                    </div> {{-- end modal-body --}}

                    {{-- FOOTER --}}
                    <div class="modal-footer py-2 border-0 border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill"
                            data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" id="btnSubmitEdit" class="btn btn-primary btn-sm rounded-pill">
                            <span id="btnTextEdit"><i class="bi bi-save me-1"></i> Simpan Perubahan</span>
                            <div id="loadingSpinnerEdit" class="spinner-border spinner-border-sm text-light d-none"
                                role="status"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <style>
        .table-wrapper {
            overflow-x: auto;
        }

        #permohonanTable {
            width: 100%;
            font-size: 0.8rem;
            border-collapse: collapse;
        }

        #permohonanTable th,
        #permohonanTable td {
            vertical-align: middle !important;
            padding: 6px 8px !important;
        }

        td:last-child {
            text-align: center;
            white-space: nowrap;
        }

        .btn-sm {
            font-size: 0.7rem;
            padding: 3px 10px;
        }

        .btn.disabled {
            pointer-events: none;
            opacity: 0.6;
        }

        .section-block {
            background-color: #f8fafc;
            border-radius: 0.6rem;
            padding: 0.75rem 0.75rem 0.5rem 0.75rem;
        }

        .modal-dialog.modal-lg {
            max-width: 900px;
        }

        .modal-dialog-scrollable .modal-body {
            max-height: calc(100vh - 180px);
            overflow-y: auto;
        }
    </style>

    <script>
        // Helper: tentukan tipe_pembayaran dari nama tanaman
        function resolveTipeByNamaTanaman(namaTanaman) {
            if (!namaTanaman) return 'Gratis';
            const n = namaTanaman.toLowerCase();

            // Kalau mengandung "kopi" atau "lada" -> berbayar
            if (n.includes('kopi') || n.includes('lada')) {
                return 'Berbayar';
            }

            // Selain itu gratis
            return 'Gratis';
        }

        function updateTipePembayaran(selectElement, hiddenInput, labelSpan) {
            if (!selectElement || !hiddenInput || !labelSpan) return;

            const selected = selectElement.options[selectElement.selectedIndex];
            const namaTanaman = selected ? selected.text : '';

            const tipe = resolveTipeByNamaTanaman(namaTanaman);
            hiddenInput.value = tipe;
            labelSpan.textContent = tipe.toUpperCase();

            // update warna badge
            labelSpan.classList.remove('bg-danger', 'bg-success', 'bg-secondary');
            if (tipe === 'Berbayar') {
                labelSpan.classList.add('bg-danger');
            } else {
                labelSpan.classList.add('bg-success');
            }
        }

        // Animasi submit create
        document.getElementById('permohonanCreateForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmitCreate');
            const text = document.getElementById('btnTextCreate');
            const spinner = document.getElementById('loadingSpinnerCreate');

            btn.disabled = true;
            text.textContent = 'Mengirim...';
            spinner.classList.remove('d-none');
        });

        // Animasi submit edit
        document.getElementById('permohonanEditForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmitEdit');
            const text = document.getElementById('btnTextEdit');
            const spinner = document.getElementById('loadingSpinnerEdit');

            btn.disabled = true;
            text.textContent = 'Menyimpan...';
            spinner.classList.remove('d-none');
        });

        document.addEventListener('DOMContentLoaded', function() {
            // ===== CREATE: auto set tipe_pembayaran =====
            const createSelect = document.getElementById('create_jenis_tanaman_id');
            const createHidden = document.getElementById('create_tipe_pembayaran');
            const createLabel = document.getElementById('create_tipe_label');

            if (createSelect) {
                createSelect.addEventListener('change', function() {
                    updateTipePembayaran(createSelect, createHidden, createLabel);
                });

                // inisialisasi jika ada old value
                if (createSelect.value) {
                    updateTipePembayaran(createSelect, createHidden, createLabel);
                } else {
                    createHidden.value = '';
                    createLabel.textContent = '-';
                    createLabel.classList.remove('bg-danger', 'bg-success');
                    createLabel.classList.add('bg-secondary');
                }
            }

            // ===== EDIT: isi data ke modal + auto tipe_pembayaran =====
            const editButtons = document.querySelectorAll('.btn-edit-permohonan');
            const formEdit = document.getElementById('permohonanEditForm');
            const editSelect = document.getElementById('edit_jenis_tanaman_id');
            const editHidden = document.getElementById('edit_tipe_pembayaran');
            const editLabel = document.getElementById('edit_tipe_label');

            // pas user GANTI jenis tanaman di modal edit
            if (editSelect) {
                editSelect.addEventListener('change', function() {
                    updateTipePembayaran(editSelect, editHidden, editLabel);
                });
            }

            editButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;

                    formEdit.action = "{{ url('pemohon/permohonan') }}/" + id;

                    document.getElementById('edit_nama').value = this.dataset.nama || '';
                    document.getElementById('edit_nik').value = this.dataset.nik || '';
                    document.getElementById('edit_alamat').value = this.dataset.alamat || '';
                    document.getElementById('edit_no_telp').value = this.dataset.no_telp || '';
                    document.getElementById('edit_jumlah_tanaman').value = this.dataset
                        .jumlah_tanaman || '';
                    document.getElementById('edit_luas_area').value = this.dataset.luas_area || '';
                    document.getElementById('edit_latitude').value = this.dataset.latitude || '';
                    document.getElementById('edit_longitude').value = this.dataset.longitude || '';

                    // set jenis tanaman & jenis benih
                    if (editSelect) {
                        editSelect.value = this.dataset.jenis_tanaman_id || '';
                        // setelah value diset, langsung hitung tipe_pembayaran awal
                        updateTipePembayaran(editSelect, editHidden, editLabel);
                    }

                    document.getElementById('edit_jenis_benih').value = this.dataset.jenis_benih ||
                        '';
                });
            });
        });
    </script>

@endsection
