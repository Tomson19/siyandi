<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengaduan</title>
    <style>
    * {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 11px;
    }
    body {
        margin: 10px;
    }
    h2, h4 {
        text-align: center;
        margin: 0;
        padding: 0;
    }
    .subtitle {
        text-align: center;
        margin-bottom: 10px;
        font-size: 10px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        table-layout: fixed; /* ⬅️ penting: biar kolom patuh ke width dan teks mau wrap */
    }
    table th, table td {
        border: 1px solid #000;
        padding: 4px 6px;
        word-wrap: break-word;   /* ⬅️ biar kata panjang bisa dipecah */
        word-break: break-word;  /* ⬅️ mantap untuk teks tanpa spasi */
        white-space: normal;     /* ⬅️ boleh turun ke bawah */
    }
    table th {
        background-color: #f0f0f0;
        font-weight: bold;
    }
    .text-center {
        text-align: center;
    }
    .text-right {
        text-align: right;
    }
    .small {
        font-size: 9px;
    }
</style>

</head>
<body>

    <h2>LAPORAN PENGADUAN</h2>
    <h4>Admin Verifikator</h4>

    <p class="subtitle">
        @if($from || $to)
            Periode:
            @if($from)
                {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }}
            @else
                -
            @endif
            s/d
            @if($to)
                {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
            @else
                -
            @endif
            <br>
        @endif

        @if($search)
            Pencarian: <strong>{{ $search }}</strong><br>
        @endif

        Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 80px;">Tanggal</th>
                <th>Nama</th>
                <th style="width: 110px;">NIK</th>
                <th>No HP</th>
                <th>Alamat</th>
                <th>Pengaduan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengaduanList as $index => $item)
                <tr>
                    <td class="text-center">
                        {{ $index + 1 }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                    </td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->nik }}</td>
                    <td>{{ $item->no_hp }}</td>
                    <td>{{ $item->alamat }}</td>
                    <td>{{ $item->pengaduan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        Tidak ada data pengaduan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
