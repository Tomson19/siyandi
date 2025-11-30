@extends('layouts.bootstrap')

@section('content')
<style>
    body{
        margin-top: -70px;
    }
</style>
    <div class="container py-4">

        {{-- ✅ ALERTS --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm d-flex align-items-start" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-primary fw-bold">
                🌿 Detail Varietas: <span class="text-dark">{{ $varietas->nama_varietas }}</span>
            </h3>
            <a href="{{ route('admin.varietas.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left-circle"></i> Kembali
            </a>
        </div>

        {{-- ========================= --}}
        {{-- INFORMASI VARIETAS --}}
        {{-- ========================= --}}
        <div class="card mb-4 border-0 shadow-sm rounded-4">
            <div class="card-header bg-success text-white fw-semibold rounded-top-4">
                <i class="bi bi-info-circle me-2"></i> Data Varietas
            </div>
            <div class="card-body p-4">
                <table class="table table-bordered table-hover align-middle">
                    <tr>
                        <th class="bg-light" width="30%">Nomor & Tanggal SK</th>
                        <td>{{ $varietas->nomor_tanggal_sk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jenis Benih</th>
                        <td>{{ $varietas->jenis_benih ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Pemilik Varietas</th>
                        <td>{{ $varietas->pemilik_varietas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jumlah Materi Genetik</th>
                        <td>{{ $varietas->jumlah_materi_genetik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Keterangan</th>
                        <td>{{ $varietas->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status</th>
                        <td><span class="badge bg-info">{{ $varietas->status ?? 'Tidak diketahui' }}</span></td>
                    </tr>
                    <tr>
                        <th class="bg-light">Kabupaten</th>
                        <td>{{ $varietas->kabupaten->nama_kabupaten ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tanaman</th>
                        <td>{{ $varietas->tanaman->nama_tanaman ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            {{-- ========================= --}}
            {{-- DESKRIPSI VARIETAS --}}
            {{-- ========================= --}}
            <div class="col-md-7 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-primary text-white fw-semibold rounded-top-4">
                        <i class="bi bi-journal-text me-2"></i> Deskripsi Varietas
                    </div>
                    <div class="card-body p-4">
                        @if ($varietas->deskripsi)
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th colspan="2" class="fw-bold">Keputusan Menteri Pertanian RI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th width="35%">Nomor</th>
                                            <td>{{ $varietas->deskripsi->nomor_sk ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal</th>
                                            <td>{{ $varietas->deskripsi->tanggal ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tipe Varietas</th>
                                            <td>{{ $varietas->deskripsi->tipe_varietas ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Asal-usul</th>
                                            <td>{{ $varietas->deskripsi->asal_usul ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tipe Pertumbuhan</th>
                                            <td>{{ $varietas->deskripsi->tipe_pertumbuhan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bentuk Tajuk</th>
                                            <td>{{ $varietas->deskripsi->bentuk_tajuk ?? '-' }}</td>
                                        </tr>

                                        <tr class="table-success fw-bold">
                                            <th colspan="2">🍃 Daun</th>
                                        </tr>
                                        <tr>
                                            <th>Ukuran</th>
                                            <td>{{ $varietas->deskripsi->daun_ukuran ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Warna Daun Muda</th>
                                            <td>{{ $varietas->deskripsi->daun_warna_muda ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Warna Daun Tua</th>
                                            <td>{{ $varietas->deskripsi->daun_warna_tua ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bentuk Ujung</th>
                                            <td>{{ $varietas->deskripsi->daun_bentuk_ujung ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tepi Daun</th>
                                            <td>{{ $varietas->deskripsi->daun_tepi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pangkal Daun</th>
                                            <td>{{ $varietas->deskripsi->daun_pangkal ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Permukaan</th>
                                            <td>{{ $varietas->deskripsi->daun_permukaan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Warna Pucuk</th>
                                            <td>{{ $varietas->deskripsi->daun_warna_pucuk ?? '-' }}</td>
                                        </tr>

                                        <tr class="table-success fw-bold">
                                            <th colspan="2">🍈 Buah</th>
                                        </tr>
                                        <tr>
                                            <th>Ukuran</th>
                                            <td>{{ $varietas->deskripsi->buah_ukuran ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Panjang (cm)</th>
                                            <td>{{ $varietas->deskripsi->buah_panjang ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Diameter (cm)</th>
                                            <td>{{ $varietas->deskripsi->buah_diameter ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bobot (gram)</th>
                                            <td>{{ $varietas->deskripsi->buah_bobot ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bentuk Buah</th>
                                            <td>{{ $varietas->deskripsi->buah_bentuk ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Warna Buah Muda</th>
                                            <td>{{ $varietas->deskripsi->buah_warna_muda ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Warna Buah Masak</th>
                                            <td>{{ $varietas->deskripsi->buah_warna_masak ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ukuran Discus</th>
                                            <td>{{ $varietas->deskripsi->buah_ukuran_discus ?? '-' }}</td>
                                        </tr>

                                        <tr class="table-success fw-bold">
                                            <th colspan="2">🌰 Biji</th>
                                        </tr>
                                        <tr>
                                            <th>Bentuk</th>
                                            <td>{{ $varietas->deskripsi->biji_bentuk ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nisbah Biji Buah (%)</th>
                                            <td>{{ $varietas->deskripsi->biji_nisbah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Persentase Normal (%)</th>
                                            <td>{{ $varietas->deskripsi->biji_persen_normal ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Citarasa</th>
                                            <td>{{ $varietas->deskripsi->citarasa ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Potensi Produksi</th>
                                            <td>{{ $varietas->deskripsi->potensi_produksi ?? '-' }}</td>
                                        </tr>

                                        <tr class="table-success fw-bold">
                                            <th colspan="2">🛡️ Ketahanan</th>
                                        </tr>
                                        <tr>
                                            <th>Penyakit Karat Daun</th>
                                            <td>{{ $varietas->deskripsi->penyakit_karat_daun ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Penggerek Buah Kopi (PBKo)</th>
                                            <td>{{ $varietas->deskripsi->penggerek_buah_kopi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Daerah Adaptasi</th>
                                            <td>{{ $varietas->deskripsi->daerah_adaptasi ?? '-' }}</td>
                                        </tr>

                                        <tr class="table-success fw-bold">
                                            <th colspan="2">👨‍🔬 Pemuliaan</th>
                                        </tr>
                                        <tr>
                                            <th>Pemulia</th>
                                            <td>{{ $varietas->deskripsi->pemulia ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Peneliti</th>
                                            <td>{{ $varietas->deskripsi->peneliti ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pemilik Varietas</th>
                                            <td>{{ $varietas->deskripsi->pemilik_varietas ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end mt-3">
                                <a href="{{ route('admin.varietas.deskripsi.edit', $varietas->deskripsi->id) }}"
                                    class="btn btn-warning rounded-pill">
                                    <i class="bi bi-pencil-square"></i> Edit Deskripsi
                                </a>
                            </div>
                        @else
                            <div class="alert alert-light border text-center py-4">
                                <p class="mb-2">Belum ada deskripsi untuk varietas ini.</p>
                                <a href="{{ route('admin.varietas.deskripsi.create', $varietas->id) }}"
                                    class="btn btn-success rounded-pill">
                                    <i class="bi bi-plus-circle"></i> Tambah Deskripsi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ========================= --}}
            {{-- MATERI GENETIK --}}
            {{-- ========================= --}}
            <div class="col-md-5 mb-4">
    <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center rounded-top-4">
            <span><i class="bi bi-diagram-3 me-2"></i> Materi Genetik</span>
            <button class="btn btn-sm btn-light rounded-pill" id="btnTambahMateri"
                data-bs-toggle="modal" data-bs-target="#modalMateriGenetik">
                <i class="bi bi-plus-circle"></i> Tambah
            </button>
        </div>

        <div class="card-body p-3">
            @if ($materiGenetik->count())
                <table class="table table-bordered table-hover table-sm align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>No</th>
                            <th>No. Pohon</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materiGenetik as $index => $m)
                            <tr>
                                <td>{{ $materiGenetik->firstItem() + $index }}</td>
                                <td>{{ $m->nomor_pohon }}</td>
                                <td>{{ $m->latitude }}</td>
                                <td>{{ $m->longitude }}</td>
                                <td class="text-center">
                                    {{-- Tombol Edit --}}
                                    <button class="btn btn-sm btn-warning btnEditMateri me-1 rounded-circle"
                                        data-id="{{ $m->id }}"
                                        data-no_sk="{{ $m->no_sk }}"
                                        data-tanggal_sk="{{ $m->tanggal_sk }}"
                                        data-nomor_pohon="{{ $m->nomor_pohon }}"
                                        data-latitude="{{ $m->latitude }}"
                                        data-longitude="{{ $m->longitude }}"
                                        data-keterangan="{{ $m->keterangan }}"
                                        data-bs-toggle="modal" data-bs-target="#modalMateriGenetik">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.varietas.materi.destroy', [$varietas->id, $m->id]) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger rounded-circle"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
               <div class="d-flex justify-content-center mt-4">
    <nav aria-label="Materi Genetik Pagination" class="shadow-sm rounded-pill px-3 py-2 bg-light">
        {{ $materiGenetik->links() }}
    </nav>
</div>

            @else
                <div class="alert alert-light border text-center py-3 mb-0">
                    Belum ada data materi genetik.
                </div>
            @endif
        </div>
    </div>
</div>

        </div>
    </div>

    {{-- ========================= --}}
    {{-- MODAL TAMBAH / EDIT MATERI GENETIK --}}
    {{-- ========================= --}}
    <div class="modal fade" id="modalMateriGenetik" tabindex="-1" aria-labelledby="modalMateriLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form id="formMateriGenetik" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="methodField" value="POST">
                    <div class="modal-header bg-secondary text-white rounded-top-4">
                        <h5 class="modal-title" id="modalMateriLabel">Tambah Materi Genetik</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">No SK</label>
                                <input type="text" name="no_sk" id="no_sk" class="form-control"
                                    placeholder="Nomor SK">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal SK</label>
                                <input type="text" name="tanggal_sk" id="tanggal_sk" class="form-control"
                                    placeholder="Tanggal SK">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nomor Pohon</label>
                                <input type="number" name="nomor_pohon" id="nomor_pohon" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Latitude</label>
                                <input type="text" name="latitude" id="latitude" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Longitude</label>
                                <input type="text" name="longitude" id="longitude" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="2"
                                    placeholder="Keterangan (opsional)"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-pill" id="btnSubmitMateri">
                            <i class="bi bi-check-circle"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('formMateriGenetik');
                const methodField = document.getElementById('methodField');
                const title = document.getElementById('modalMateriLabel');
                const btnSubmit = document.getElementById('btnSubmitMateri');
                const baseStoreUrl = "{{ route('admin.varietas.materi.store', ['varietas' => $varietas->id]) }}";
                const baseUpdateUrl =
                    "{{ route('admin.varietas.materi.update', ['varietas' => $varietas->id, 'id' => 'ID_PLACEHOLDER']) }}";

                document.getElementById('btnTambahMateri').addEventListener('click', function() {
                    title.textContent = 'Tambah Materi Genetik';
                    form.action = baseStoreUrl;
                    methodField.value = 'POST';
                    btnSubmit.innerHTML = '<i class="bi bi-check-circle"></i> Simpan';
                    ['no_sk', 'tanggal_sk', 'nomor_pohon', 'latitude', 'longitude', 'keterangan'].forEach(id =>
                        document.getElementById(id).value = '');
                });

                document.querySelectorAll('.btnEditMateri').forEach(button => {
                    button.addEventListener('click', function() {
                        title.textContent = 'Edit Materi Genetik';
                        const updateUrl = baseUpdateUrl.replace('ID_PLACEHOLDER', this.dataset.id);
                        form.action = updateUrl;
                        methodField.value = 'PUT';
                        btnSubmit.innerHTML = '<i class="bi bi-arrow-repeat"></i> Update';
                        document.getElementById('no_sk').value = this.dataset.no_sk || '';
                        document.getElementById('tanggal_sk').value = this.dataset.tanggal_sk || '';
                        document.getElementById('nomor_pohon').value = this.dataset.nomor_pohon || '';
                        document.getElementById('latitude').value = this.dataset.latitude || '';
                        document.getElementById('longitude').value = this.dataset.longitude || '';
                        document.getElementById('keterangan').value = this.dataset.keterangan || '';
                    });
                });
            });
        </script>
    @endpush
@endsection
