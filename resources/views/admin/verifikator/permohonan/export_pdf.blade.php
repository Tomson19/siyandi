<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Permohonan Benih</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }
        h2 {
            margin: 0 0 4px 0;
            font-size: 14px;
            text-transform: uppercase;
        }
        .meta {
            font-size: 8px;
            color: #555;
            margin-bottom: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        th, td {
            border: 1px solid #999;
            padding: 2px 3px;
        }
        th {
            background: #f3f4f6;
            font-size: 8px;
            text-transform: uppercase;
        }
        td {
            font-size: 8px;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .wrap        { word-wrap: break-word; }
    </style>
</head>
<body>
    <h2>Laporan Permohonan Benih</h2>
    <div class="meta">
        Dibuat pada: {{ $generatedAt->format('d/m/Y H:i') }}<br>
        Total data: {{ $permohonan->count() }}
    </div>

    <table>
        <thead>
        <tr>
            <th class="text-center" style="width: 2.5%;">No</th>
            <th style="width: 9%;">Nama</th>
            <th style="width: 7%;">NIK</th>
            <th style="width: 12%;">Alamat</th>
            <th style="width: 7%;">No. Telepon</th>
            <th style="width: 8%;">Jenis Tanaman</th>
            <th style="width: 6%;">Jenis Benih</th>
            <th style="width: 4%;">Luas (Ha)</th>
            <th style="width: 5%;">Latitude</th>
            <th style="width: 5%;">Longitude</th>
            <th style="width: 5%;">Tipe</th>
            <th style="width: 4.5%;">Jml Ajukan</th>
            <th style="width: 4.5%;">Jml Setuju</th>
            <th style="width: 5.5%;">Tgl Diajukan</th>
            <th style="width: 6%;">Tgl Keputusan</th>
            <th style="width: 6%;">Status</th>
            <th style="width: 10%;">Keterangan Admin</th>
            <th style="width: 6%;">Status Bayar</th>
            <th style="width: 6%;">Status Pengambilan</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($permohonan as $i => $item)
            @php
                // Tanggal keputusan tergantung status
                if ($item->status === 'Disetujui' && $item->tanggal_disetujui) {
                    $tglKeputusan = $item->tanggal_disetujui->format('d/m/Y');
                } elseif ($item->status === 'Ditolak' && $item->tanggal_ditolak) {
                    $tglKeputusan = $item->tanggal_ditolak->format('d/m/Y');
                } else {
                    $tglKeputusan = '-';
                }

                // Ambil keterangan admin terakhir yang Disetujui / Ditolak
                $ketFinal = $item->keterangan
                    ? $item->keterangan
                        ->whereIn('jenis_keterangan', ['Disetujui', 'Ditolak'])
                        ->sortByDesc('created_at')
                        ->first()
                    : null;

                $keteranganAdmin = $ketFinal->isi_keterangan ?? '-';
            @endphp

            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item->nama }}</td>
                <td class="text-center">{{ $item->nik }}</td>
                <td class="wrap">{{ $item->alamat }}</td>
                <td class="text-center">{{ $item->no_telp }}</td>
                <td>{{ $item->jenisTanaman->nama_tanaman ?? '-' }}</td>
                <td class="text-center">{{ $item->jenis_benih ?? '-' }}</td>
                <td class="text-right">
                    {{ $item->luas_area !== null ? number_format($item->luas_area, 2, ',', '.') : '-' }}
                </td>
                <td class="text-center">{{ $item->latitude ?? '-' }}</td>
                <td class="text-center">{{ $item->longitude ?? '-' }}</td>
                <td class="text-center">{{ $item->tipe_pembayaran ?? '-' }}</td>
                <td class="text-right">{{ $item->jumlah_tanaman }}</td>
                <td class="text-right">{{ $item->jumlah_disetujui ?? '-' }}</td>
                <td class="text-center">
                    {{ $item->tanggal_diajukan
                        ? $item->tanggal_diajukan->format('d/m/Y')
                        : '-' }}
                </td>
                <td class="text-center">{{ $tglKeputusan }}</td>
                <td class="text-center">{{ $item->status ?? '-' }}</td>
                <td class="wrap">{{ $keteranganAdmin }}</td>
                <td class="text-center">{{ $item->status_pembayaran ?? '-' }}</td>
                <td class="text-center">{{ $item->status_pengambilan ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
