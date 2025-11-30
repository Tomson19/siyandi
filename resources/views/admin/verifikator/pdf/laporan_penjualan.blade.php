<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Benih</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2, h3, h4 { margin: 0; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 4px 6px; }
        th { background: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .small { font-size: 10px; }
    </style>
</head>
<body>

    <h2 class="mb-1">Laporan Penjualan Benih</h2>
    <div class="small mb-3">
        Mode: {{ ucfirst($mode) }}
        @if($mode === 'bulan' || $mode === 'hari')
            · Tahun: {{ $year }}
        @endif
        @if($mode === 'hari')
            · Bulan: {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
        @endif
        @if($tipe)
            · Tipe: {{ $tipe }}
        @else
            · Tipe: Semua
        @endif
    </div>

    {{-- Ringkasan --}}
    <table class="mb-3">
        <tr>
            <th>Total Pendapatan</th>
            <th>Total Transaksi Berhasil</th>
            <th>Rata-rata Nilai Transaksi</th>
            <th>Jumlah Jenis Benih Terjual</th>
        </tr>
        @php
            $avgTransaksi = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;
            $totalJenisTerjual = $penjualan->count();
        @endphp
        <tr>
            <td class="text-right">
                Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
            </td>
            <td class="text-right">
                {{ number_format($totalTransaksi) }}
            </td>
            <td class="text-right">
                Rp{{ number_format($avgTransaksi, 0, ',', '.') }}
            </td>
            <td class="text-right">
                {{ $totalJenisTerjual }}
            </td>
        </tr>
    </table>

    {{-- Tabel penjualan per benih --}}
    <h4 class="mb-1">Data Penjualan Benih (Transaksi Berhasil)</h4>
    <table class="mb-3">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Tanaman</th>
                <th>Jenis Benih</th>
                <th class="text-right">Harga Satuan (Rp)</th>
                <th class="text-right">Jumlah Terjual</th>
                <th class="text-right">Total Pendapatan (Rp)</th>
                <th class="text-right">Sisa Stok</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp

            @forelse ($penjualan as $benihId => $group)
                @php
                    $benih         = $group->first()->benih ?? null;
                    $jumlahTerjual = $group->sum('jumlah_disetujui');
                    $pendapatan    = $group->sum('nominal_pembayaran');
                    $grandTotal   += $pendapatan;
                    $hargaSatuan   = $benih->harga ?? 0;
                    $sisaStok      = $benih->stok ?? 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $benih->jenisTanaman->nama_tanaman ?? '-' }}</td>
                    <td>{{ $benih->jenis_benih ?? '-' }}</td>
                    <td class="text-right">
                        {{ number_format($hargaSatuan, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($jumlahTerjual) }}
                    </td>
                    <td class="text-right">
                        Rp{{ number_format($pendapatan, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($sisaStok) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        Belum ada penjualan.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">Total Pendapatan:</th>
                <th class="text-right">
                    Rp{{ number_format($grandTotal, 0, ',', '.') }}
                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    {{-- Top 5 Benih Terlaris --}}
    <h4 class="mb-1">Top 5 Benih Terlaris</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th>Tanaman</th>
                <th>Jenis Benih</th>
                <th class="text-right">Jumlah Terjual</th>
                <th class="text-right">Total Pendapatan (Rp)</th>
                <th class="text-right">Kontribusi (%)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPendapatanSafe = $totalPendapatan > 0 ? $totalPendapatan : 1;
            @endphp

            @forelse ($topBenih as $index => $item)
                @php
                    $benih      = $item->benih;
                    $persentase = ($item->pendapatan / $totalPendapatanSafe) * 100;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $benih->jenisTanaman->nama_tanaman ?? '-' }}</td>
                    <td>{{ $benih->jenis_benih ?? '-' }}</td>
                    <td class="text-right">
                        {{ number_format($item->jumlah_terjual) }}
                    </td>
                    <td class="text-right">
                        Rp{{ number_format($item->pendapatan, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($persentase, 1, ',', '.') }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        Belum ada data Top 5.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
