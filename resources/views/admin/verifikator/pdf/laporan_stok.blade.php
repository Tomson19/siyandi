<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Stok Benih</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2, h4 {
            text-align: center;
            margin: 0;
        }
        .subtitle {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table th, table td {
            border: 1px solid #999;
            padding: 6px 8px;
        }
        table th {
            background: #e9ecef;
            text-align: center;
        }
        table td {
            vertical-align: middle;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 4px;
            color: #fff;
            font-size: 11px;
        }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }

        .footer {
            text-align: right;
            font-size: 10px;
            color: #888;
            margin-top: 20px;
        }

        .section-title {
            margin: 20px 0 10px;
            font-weight: bold;
            border-bottom: 1px solid #999;
            padding-bottom: 4px;
        }
    </style>
</head>
<body>

    {{-- ================= HEADER ================= --}}
    <h2>Laporan Stok Benih</h2>
    <div class="subtitle">
        @if(!empty($startDate) && !empty($endDate))
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
            {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        @else
            Periode: Semua Data (10 terakhir)
        @endif
        <br>
        Dicetak pada: {{ now()->format('d M Y, H:i') }}
    </div>

    {{-- ================= RIWAYAT STOK ================= --}}
    <h4 class="section-title">Riwayat Perubahan Stok</h4>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Benih</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Stok Awal</th>
                <th>Stok Akhir</th>
                <th>Keterangan</th>
                <th>Admin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat ?? [] as $r)
                <tr>
                    <td class="text-center">{{ $r->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $r->benih->jenisTanaman->nama_tanaman ?? '-' }} ({{ $r->benih->jenis_benih ?? '-' }})</td>
                    <td class="text-center">
                        <span class="badge {{ $r->tipe === 'Masuk' ? 'bg-success' : 'bg-danger' }}">{{ $r->tipe }}</span>
                    </td>
                    <td class="text-end">{{ number_format($r->jumlah) }}</td>
                    <td class="text-end">{{ number_format($r->stok_awal) }}</td>
                    <td class="text-end">{{ number_format($r->stok_akhir) }}</td>
                    <td>{{ $r->keterangan ?? '-' }}</td>
                    <td class="text-center">{{ $r->admin->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted fst-italic">
                        Tidak ada data riwayat stok pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistem Informasi Benih — {{ config('app.name', 'Sistem Benih') }}
    </div>

</body>
</html>
