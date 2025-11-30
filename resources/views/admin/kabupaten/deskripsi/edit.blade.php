@extends('layouts.bootstrap')

@section('content')
<div class="container">
    <h3 class="mb-4 text-primary">
        Edit Deskripsi Varietas: {{ $varietas->nama_varietas }}
    </h3>

    <form action="{{ route('admin.varietas.deskripsi.update', $deskripsi->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ========================================================= --}}
        {{-- 🧾 KEPUTUSAN MENTERI PERTANIAN RI --}}
        {{-- ========================================================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <strong>Keputusan Menteri Pertanian RI</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nomor SK</label>
                        <input type="text" name="nomor_sk" class="form-control" value="{{ old('nomor_sk', $deskripsi->nomor_sk) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="text" name="tanggal" class="form-control" value="{{ old('tanggal', $deskripsi->tanggal) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipe Varietas</label>
                        <input type="text" name="tipe_varietas" class="form-control" value="{{ old('tipe_varietas', $deskripsi->tipe_varietas) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Asal Usul</label>
                    <textarea name="asal_usul" class="form-control" rows="3">{{ old('asal_usul', $deskripsi->asal_usul) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe Pertumbuhan</label>
                        <input type="text" name="tipe_pertumbuhan" class="form-control" value="{{ old('tipe_pertumbuhan', $deskripsi->tipe_pertumbuhan) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bentuk Tajuk</label>
                        <input type="text" name="bentuk_tajuk" class="form-control" value="{{ old('bentuk_tajuk', $deskripsi->bentuk_tajuk) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 🍃 DAUN --}}
        {{-- ========================================================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white"><strong>Daun</strong></div>
            <div class="card-body row">
                <div class="col-md-3 mb-3">
                    <label>Ukuran</label>
                    <input type="text" name="daun_ukuran" class="form-control" value="{{ old('daun_ukuran', $deskripsi->daun_ukuran) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Warna Daun Muda</label>
                    <input type="text" name="daun_warna_muda" class="form-control" value="{{ old('daun_warna_muda', $deskripsi->daun_warna_muda) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Warna Daun Tua</label>
                    <input type="text" name="daun_warna_tua" class="form-control" value="{{ old('daun_warna_tua', $deskripsi->daun_warna_tua) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Bentuk Ujung Daun</label>
                    <input type="text" name="daun_bentuk_ujung" class="form-control" value="{{ old('daun_bentuk_ujung', $deskripsi->daun_bentuk_ujung) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Tepi Daun</label>
                    <input type="text" name="daun_tepi" class="form-control" value="{{ old('daun_tepi', $deskripsi->daun_tepi) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Pangkal Daun</label>
                    <input type="text" name="daun_pangkal" class="form-control" value="{{ old('daun_pangkal', $deskripsi->daun_pangkal) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Permukaan Daun</label>
                    <input type="text" name="daun_permukaan" class="form-control" value="{{ old('daun_permukaan', $deskripsi->daun_permukaan) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Warna Pucuk</label>
                    <input type="text" name="daun_warna_pucuk" class="form-control" value="{{ old('daun_warna_pucuk', $deskripsi->daun_warna_pucuk) }}">
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 🌸 BUNGA --}}
        {{-- ========================================================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-warning"><strong>Bunga</strong></div>
            <div class="card-body row">
                <div class="col-md-4 mb-3">
                    <label>Warna Mahkota</label>
                    <input type="text" name="bunga_warna_mahkota" class="form-control" value="{{ old('bunga_warna_mahkota', $deskripsi->bunga_warna_mahkota) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Jumlah Mahkota</label>
                    <input type="text" name="bunga_jumlah_mahkota" class="form-control" value="{{ old('bunga_jumlah_mahkota', $deskripsi->bunga_jumlah_mahkota) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Ukuran Bunga</label>
                    <input type="text" name="bunga_ukuran" class="form-control" value="{{ old('bunga_ukuran', $deskripsi->bunga_ukuran) }}">
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 🍈 BUAH --}}
        {{-- ========================================================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-danger text-white"><strong>Buah</strong></div>
            <div class="card-body row">
                <div class="col-md-3 mb-3">
                    <label>Ukuran Buah</label>
                    <input type="text" name="buah_ukuran" class="form-control" value="{{ old('buah_ukuran', $deskripsi->buah_ukuran) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Panjang (cm)</label>
                    <input type="text" name="buah_panjang" class="form-control" value="{{ old('buah_panjang', $deskripsi->buah_panjang) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Diameter (cm)</label>
                    <input type="text" name="buah_diameter" class="form-control" value="{{ old('buah_diameter', $deskripsi->buah_diameter) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Bobot (gram)</label>
                    <input type="text" name="buah_bobot" class="form-control" value="{{ old('buah_bobot', $deskripsi->buah_bobot) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Bentuk Buah</label>
                    <input type="text" name="buah_bentuk" class="form-control" value="{{ old('buah_bentuk', $deskripsi->buah_bentuk) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Warna Buah Muda</label>
                    <input type="text" name="buah_warna_muda" class="form-control" value="{{ old('buah_warna_muda', $deskripsi->buah_warna_muda) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Warna Buah Masak</label>
                    <input type="text" name="buah_warna_masak" class="form-control" value="{{ old('buah_warna_masak', $deskripsi->buah_warna_masak) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Ukuran Discus</label>
                    <input type="text" name="buah_ukuran_discus" class="form-control" value="{{ old('buah_ukuran_discus', $deskripsi->buah_ukuran_discus) }}">
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 🌰 BIJI --}}
        {{-- ========================================================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white"><strong>Biji</strong></div>
            <div class="card-body row">
                <div class="col-md-6 mb-3">
                    <label>Bentuk</label>
                    <input type="text" name="biji_bentuk" class="form-control" value="{{ old('biji_bentuk', $deskripsi->biji_bentuk) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Nisbah Biji Buah atau Rata-rata Rendemen (%)</label>
                    <input type="text" name="biji_nisbah" class="form-control" value="{{ old('biji_nisbah', $deskripsi->biji_nisbah) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Persentase Biji Normal (%)</label>
                    <input type="text" name="biji_persen_normal" class="form-control" value="{{ old('biji_persen_normal', $deskripsi->biji_persen_normal) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Citarasa</label>
                    <textarea name="citarasa" rows="2" class="form-control">{{ old('citarasa', $deskripsi->citarasa) }}</textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Potensi Produksi</label>
                    <textarea name="potensi_produksi" rows="2" class="form-control">{{ old('potensi_produksi', $deskripsi->potensi_produksi) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- 🛡️ KETAHANAN TERHADAP HAMA PENYAKIT --}}
        {{-- ========================================================= --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-secondary text-white">
                <strong>Ketahanan terhadap Hama Penyakit Utama</strong>
            </div>
            <div class="card-body row">
                <div class="col-md-4 mb-3">
                    <label>Penyakit Karat Daun</label>
                    <input type="text" name="penyakit_karat_daun" class="form-control" value="{{ old('penyakit_karat_daun', $deskripsi->penyakit_karat_daun) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Penggerek Buah Kopi (PBKo)</label>
                    <input type="text" name="penggerek_buah_kopi" class="form-control" value="{{ old('penggerek_buah_kopi', $deskripsi->penggerek_buah_kopi) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Daerah Adaptasi</label>
                    <input type="text" name="daerah_adaptasi" class="form-control" value="{{ old('daerah_adaptasi', $deskripsi->daerah_adaptasi) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Pemulia</label>
                    <input type="text" name="pemulia" class="form-control" value="{{ old('pemulia', $deskripsi->pemulia) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Peneliti</label>
                    <input type="text" name="peneliti" class="form-control" value="{{ old('peneliti', $deskripsi->peneliti) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Pemilik Varietas</label>
                    <input type="text" name="pemilik_varietas" class="form-control" value="{{ old('pemilik_varietas', $deskripsi->pemilik_varietas) }}">
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- BUTTON --}}
        {{-- ========================================================= --}}
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('admin.varietas.show', $deskripsi->varietas_id) }}" class="btn btn-secondary">Kembali</a>
            <button type="submit" class="btn btn-success">Update Deskripsi</button>
        </div>
    </form>
</div>
@endsection
