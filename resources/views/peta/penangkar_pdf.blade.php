<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Penangkar Benih</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        h3,
        h4 {
            margin: 0;
            text-align: center;
        }

        .subtitle {
            margin-top: 4px;
            margin-bottom: 10px;
            text-align: center;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px 4px;
        }

        th {
            background: #e0e0e0;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <h3>LAPORAN DATA PENANGKAR BENIH</h3>
    <div class="subtitle">
        @if ($tanaman)
            Komoditas: <strong>{{ $tanaman->nama_tanaman }}</strong>
        @else
            Komoditas: <strong>Semua</strong>
        @endif

        &nbsp; | &nbsp;

        @if ($kabupaten)
            Kabupaten: <strong>{{ $kabupaten->nama_kabupaten }}</strong>
        @else
            Kabupaten: <strong>Semua</strong>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%;">No</th>
                <th style="width:10%;">Komoditas</th>
                <th style="width:16%;">Nama Produsen Benih<br>Perorangan/Perusahaan</th>
                <th style="width:12%;">NIB &amp;<br>Tanggal</th>
                <th style="width:16%;">Sertifikat Sandar / Izin <br>Usaha Prod. Benih Nomor &amp; Tanggal</th>
                <th style="width:8%;">Luas<br>Areal (Ha)</th>
                <th style="width:8%;">Jumlah Sertifikasi Benih<br>Tahun Berjalan(Batang)</th>
                <th style="width:12%;">Alamat</th>
                <th style="width:8%;">Desa/Kelurahan</th>
                <th style="width:8%;">Kecamatan</th>
                <th style="width:8%;">Kabupaten</th>
                <th style="width:4%;">LU/LS</th>
                <th style="width:4%;">BT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $row->tanaman->nama_tanaman ?? '-' }}</td>
                    <td class="text-center">{{ $row->nama_penangkar }}</td>
                    <td class="text-center">{{ $row->nib_dan_tanggal ?? '-' }}</td>
                    <td class="text-center">{{ $row->sertifikat_izin_usaha_nomor_dan_tanggal ?? '-' }}</td>
                    <td class="text-center">{{ $row->luas_areal_ha ?? '-' }}</td>
                    <td class="text-center">{{ $row->jumlah_sertifikasi ?? '-' }}</td>
                    <td class="text-center">{{ $row->jalan ?? '-' }}</td>
                    <td class="text-center">{{ $row->desa ?? '-' }}</td>
                    <td class="text-center">{{ $row->kecamatan ?? '-' }}</td>
                    <td class="text-center">{{ $row->kabupaten->nama_kabupaten ?? '-' }}</td>
                    <td class="text-center">{{ $row->latitude ?? '-' }}</td>
                    <td class="text-center">{{ $row->longitude ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">
                        Tidak ada data.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
