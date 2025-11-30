<!DOCTYPE html>
<html>
<head>
    <title>Deskripsi Varietas - {{ $data->varietas->nama_varietas }}</title>
    <style>
        body{font-family: DejaVu Sans, sans-serif;font-size:12px;line-height:1.5;margin:18px}
        h3{margin:0 0 12px;text-align:center;font-size:15pt}
        table{width:100%;border-collapse:collapse}
        td{padding:6px 6px;vertical-align:top;border:0.5px solid #999} /* border tipis di semua baris */

        /* kolom & rata */
        td.label{width:34%;padding-right:4px}
        td.colon{width:2%;text-align:center}
        td.value{width:64%;text-align:justify}

        /* baris judul bagian yang harus BOLD */
        tr.section td{
            font-weight:bold;
            background:#f3f3f3;
        }

        /* baris header halaman (judul besar) tidak pakai border */
        .title-row td{border:none;padding:0 0 12px}
    </style>
</head>
<body>
    <table>
        <tr class="title-row">
            <td colspan="3"><h3>DESKRIPSI KOPI {{ strtoupper($data->varietas->nama_varietas) }}</h3></td>
        </tr>

        {{-- === Keputusan Menteri Pertanian RI (BOLD) === --}}
        <tr class="section"><td colspan="3">Keputusan Menteri Pertanian RI</td></tr>
        <tr><td class="label">Nomor</td><td class="colon">:</td><td class="value">{{ $data->nomor_sk ?? '-' }}</td></tr>
        <tr><td class="label">Tanggal</td><td class="colon">:</td><td class="value">{{ $data->tanggal ?? '-' }}</td></tr>
        <tr><td class="label">Tipe varietas</td><td class="colon">:</td><td class="value">{{ $data->tipe_varietas ?? '-' }}</td></tr>
        <tr><td class="label">Asal-usul</td><td class="colon">:</td><td class="value">{{ $data->asal_usul ?? '-' }}</td></tr>
        <tr><td class="label">Tipe pertumbuhan</td><td class="colon">:</td><td class="value">{{ $data->tipe_pertumbuhan ?? '-' }}</td></tr>
        <tr><td class="label">Bentuk tajuk</td><td class="colon">:</td><td class="value">{{ $data->bentuk_tajuk ?? '-' }}</td></tr>

        {{-- === Daun (BOLD) === --}}
        <tr class="section"><td colspan="3">Daun</td></tr>
        <tr><td class="label">Ukuran</td><td class="colon">:</td><td class="value">{{ $data->daun_ukuran ?? '-' }}</td></tr>
        <tr><td class="label">Warna daun muda</td><td class="colon">:</td><td class="value">{{ $data->daun_warna_muda ?? '-' }}</td></tr>
        <tr><td class="label">Warna daun tua</td><td class="colon">:</td><td class="value">{{ $data->daun_warna_tua ?? '-' }}</td></tr>
        <tr><td class="label">Bentuk ujung daun</td><td class="colon">:</td><td class="value">{{ $data->daun_bentuk_ujung ?? '-' }}</td></tr>
        <tr><td class="label">Tepi daun</td><td class="colon">:</td><td class="value">{{ $data->daun_tepi ?? '-' }}</td></tr>
        <tr><td class="label">Pangkal daun</td><td class="colon">:</td><td class="value">{{ $data->daun_pangkal ?? '-' }}</td></tr>
        <tr><td class="label">Permukaan daun</td><td class="colon">:</td><td class="value">{{ $data->daun_permukaan ?? '-' }}</td></tr>
        <tr><td class="label">Warna pucuk</td><td class="colon">:</td><td class="value">{{ $data->daun_warna_pucuk ?? '-' }}</td></tr>

        {{-- === Bunga (biasa, bukan heading yang diminta bold) === --}}
        <tr><td class="label">Bunga - Warna mahkota</td><td class="colon">:</td><td class="value">{{ $data->bunga_warna_mahkota ?? '-' }}</td></tr>
        <tr><td class="label">Bunga - Jumlah mahkota</td><td class="colon">:</td><td class="value">{{ $data->bunga_jumlah_mahkota ?? '-' }}</td></tr>
        <tr><td class="label">Bunga - Ukuran bunga</td><td class="colon">:</td><td class="value">{{ $data->bunga_ukuran ?? '-' }}</td></tr>

        {{-- === Buah (BOLD) === --}}
        <tr class="section"><td colspan="3">Buah</td></tr>
        <tr><td class="label">Ukuran buah</td><td class="colon">:</td><td class="value">{{ $data->buah_ukuran ?? '-' }}</td></tr>
        <tr><td class="label">Panjang (cm)</td><td class="colon">:</td><td class="value">{{ $data->buah_panjang ?? '-' }}</td></tr>
        <tr><td class="label">Diameter (cm)</td><td class="colon">:</td><td class="value">{{ $data->buah_diameter ?? '-' }}</td></tr>
        <tr><td class="label">Bobot (gram)</td><td class="colon">:</td><td class="value">{{ $data->buah_bobot ?? '-' }}</td></tr>
        <tr><td class="label">Bentuk buah</td><td class="colon">:</td><td class="value">{{ $data->buah_bentuk ?? '-' }}</td></tr>
        <tr><td class="label">Warna buah muda</td><td class="colon">:</td><td class="value">{{ $data->buah_warna_muda ?? '-' }}</td></tr>
        <tr><td class="label">Warna buah masak</td><td class="colon">:</td><td class="value">{{ $data->buah_warna_masak ?? '-' }}</td></tr>
        <tr><td class="label">Ukuran discus</td><td class="colon">:</td><td class="value">{{ $data->buah_ukuran_discus ?? '-' }}</td></tr>

        {{-- === Biji (BOLD) === --}}
        <tr class="section"><td colspan="3">Biji</td></tr>
        <tr><td class="label">Bentuk</td><td class="colon">:</td><td class="value">{{ $data->biji_bentuk ?? '-' }}</td></tr>
        <tr><td class="label">Nisbah biji buah atau rata-rata rendemen (%)</td><td class="colon">:</td><td class="value">{{ $data->biji_nisbah ?? '-' }}</td></tr>
        <tr><td class="label">Persentase biji normal (%)</td><td class="colon">:</td><td class="value">{{ $data->biji_persen_normal ?? '-' }}</td></tr>

        {{-- === Mutu & Produksi (biasa) === --}}
        <tr><td class="label">Citarasa</td><td class="colon">:</td><td class="value">{{ $data->citarasa ?? '-' }}</td></tr>
        <tr><td class="label">Potensi produksi</td><td class="colon">:</td><td class="value">{{ $data->potensi_produksi ?? '-' }}</td></tr>

        {{-- === Ketahanan terhadap hama penyakit utama (BOLD) === --}}
        <tr class="section"><td colspan="3">Ketahanan terhadap hama penyakit utama</td></tr>
        <tr><td class="label">Penyakit karat daun</td><td class="colon">:</td><td class="value">{{ $data->penyakit_karat_daun ?? '-' }}</td></tr>
        <tr><td class="label">Pengerek buah kopi (PBKo)</td><td class="colon">:</td><td class="value">{{ $data->penggerek_buah_kopi ?? '-' }}</td></tr>
        <tr><td class="label">Daerah adaptasi</td><td class="colon">:</td><td class="value">{{ $data->daerah_adaptasi ?? '-' }}</td></tr>

        {{-- === Pemuliaan (biasa) === --}}
        <tr><td class="label">Pemulia</td><td class="colon">:</td><td class="value">{{ $data->pemulia ?? '-' }}</td></tr>
        <tr><td class="label">Peneliti</td><td class="colon">:</td><td class="value">{{ $data->peneliti ?? '-' }}</td></tr>
        <tr><td class="label">Pemilik varietas</td><td class="colon">:</td><td class="value">{{ $data->pemilik_varietas ?? '-' }}</td></tr>
    </table>
</body>
</html>
