<!DOCTYPE html>
<html>
<head>
    <title>Data Varietas Tanaman</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #555;
            padding: 6px 8px;
            text-align: center;
            vertical-align: middle;
        }

        /* Header hijau lembut */
        thead th {
            background-color: #A7D36E;
            color: #000;
            font-weight: bold;
        }

        /* Baris selang-seling */
        tbody tr:nth-child(odd) {
            background-color: #E8F5D2;
        }

        tbody tr:nth-child(even) {
            background-color: #F3FAE2;
        }

        /* Hover simulasi ringan (tidak berpengaruh di PDF tapi jaga konsistensi preview HTML) */
        tr:hover td {
            background-color: #D7F0B4;
        }
    </style>
</head>
<body>

<h3>Data Varietas Tanaman Kopi Provinsi Riau</h3>

<table>
    <thead>
        <tr>
            <th style="width:4%;">No</th>
            <th style="width:20%;">Nomor &amp; Tanggal SK</th>
            <th style="width:15%;">Varietas</th>
            <th style="width:12%;">Jenis Benih</th>
            <th style="width:20%;">Pemilik Varietas</th>
            <th style="width:15%;">Jumlah Materi Genetik<br>(Pohon/Rumpun)</th>
            <th style="width:14%;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $v)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div><strong>{{ $v->deskripsi->nomor_sk ?? '-' }}</strong></div>
                <small>{{ $v->deskripsi->tanggal ?? '-' }}</small>
            </td>
            <td>{{ $v->nama_varietas }}</td>
            <td>{{ $v->jenis_benih ?? '-' }}</td>
            <td>{{ $v->deskripsi->pemilik_varietas ?? '-' }}</td>
            <td>{{ $v->materiGenetik->count() ?? 0 }}</td>
            <td>{{ $v->keterangan ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
