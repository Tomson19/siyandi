<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Materi Genetik - {{ $varietas->nama_varietas }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }

        h3 {
            text-align: center;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        h4 {
            text-align: center;
            margin-top: 0;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #555;
            padding: 6px 8px;
        }

        th {
            background: #d9d9d9;
            text-align: center;
            font-weight: bold;
        }

        td {
            vertical-align: middle;
        }

        td.num, th.num {
            text-align: center;
            width: 5%;
        }

        td.center {
            text-align: center;
        }

        td.sk {
            text-align: left;
            width: 30%;
        }

        /* garis batas halus antar baris */
        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        /* header judul di atas tabel */
        .title-header {
            background-color: #c6e0b4;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #777;
        }

        .varietas-name {
            text-align: center;
            font-size: 13px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="title-header">
        MATERI GENETIK DAN KOORDINAT LOKASI
    </div>
    <div class="varietas-name">
        Varietas: <strong>{{ strtoupper($varietas->nama_varietas) }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th class="num">No</th>
                <th>No. SK dan Tanggal</th>
                <th>Nomor Pohon</th>
                <th>Latitude</th>
                <th>Longitude</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $m)
                <tr>
                    <td class="num">{{ $loop->iteration }}</td>
                    <td class="sk">
                        <div><strong>{{ $m->no_sk ?? '-' }}</strong></div>
                        <small>{{ $m->tanggal_sk ?? '-' }}</small>
                    </td>
                    <td class="center">{{ $m->nomor_pohon ?? '-' }}</td>
                    <td class="center">{{ $m->latitude ?? '-' }}</td>
                    <td class="center">{{ $m->longitude ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center text-muted">Tidak ada data materi genetik</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
