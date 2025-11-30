@php
    $avgTransaksi        = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;
    $totalJenisTerjual   = $penjualan->count();
    $totalPendapatanSafe = $totalPendapatan > 0 ? $totalPendapatan : 1;
@endphp

{{-- HEADER LAPORAN --}}
<table>
    <tr>
        <td colspan="7"
            style="background-color:#0d6efd;color:#ffffff;font-weight:bold;
                   font-size:13px;padding:6px 8px;">
            Laporan Penjualan Benih
        </td>
    </tr>

    <tr>
        <td style="background:#e9f2ff;font-weight:bold;border:1px solid #d0d7e2;">Mode</td>
        <td colspan="2" style="border:1px solid #d0d7e2;">
            {{ ucfirst($mode) }}
        </td>
        <td style="background:#e9f2ff;font-weight:bold;border:1px solid #d0d7e2;">Tipe</td>
        <td colspan="3" style="border:1px solid #d0d7e2;">
            {{ $tipe ?: 'Semua' }}
        </td>
    </tr>

    @if($mode === 'bulan' || $mode === 'hari')
        <tr>
            <td style="background:#e9f2ff;font-weight:bold;border:1px solid #d0d7e2;">Tahun</td>
            <td colspan="2" style="border:1px solid #d0d7e2;">
                {{ $year }}
            </td>

            @if($mode === 'hari')
                <td style="background:#e9f2ff;font-weight:bold;border:1px solid #d0d7e2;">Bulan</td>
                <td colspan="3" style="border:1px solid #d0d7e2;">
                    {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                </td>
            @else
                <td style="border:1px solid #d0d7e2;"></td>
                <td colspan="2" style="border:1px solid #d0d7e2;"></td>
            @endif
        </tr>
    @endif
</table>

<br>

{{-- RINGKASAN PENJUALAN --}}
<table>
    <tr>
        <td colspan="7"
            style="background:#f3f4f6;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Ringkasan Penjualan
        </td>
    </tr>

    <tr>
        <td style="background:#e9f2ff;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Total Pendapatan
        </td>
        <td colspan="6" style="border:1px solid #999;padding:4px 5px;">
            Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
        </td>
    </tr>
    <tr>
        <td style="background:#e9f2ff;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Total Transaksi Berhasil
        </td>
        <td colspan="6" style="border:1px solid #999;padding:4px 5px;">
            {{ number_format($totalTransaksi) }}
        </td>
    </tr>
    <tr>
        <td style="background:#e9f2ff;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Rata-rata Nilai Transaksi
        </td>
        <td colspan="6" style="border:1px solid #999;padding:4px 5px;">
            Rp{{ number_format($avgTransaksi, 0, ',', '.') }}
        </td>
    </tr>
    <tr>
        <td style="background:#e9f2ff;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Jumlah Jenis Benih Terjual
        </td>
        <td colspan="6" style="border:1px solid #999;padding:4px 5px;">
            {{ $totalJenisTerjual }}
        </td>
    </tr>
</table>

<br>

{{-- DATA PENJUALAN PER BENIH --}}
<table>
    <tr>
        <td colspan="7"
            style="background:#f3f4f6;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Data Penjualan Benih (Transaksi Berhasil)
        </td>
    </tr>

    <tr>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">#</th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">Tanaman</th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">Jenis Benih</th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Harga Satuan (Rp)
        </th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Jumlah Terjual
        </th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Total Pendapatan (Rp)
        </th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Sisa Stok
        </th>
    </tr>

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
            <td style="border:1px solid #999;padding:4px 5px;text-align:center;">
                {{ $loop->iteration }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;">
                {{ $benih->jenisTanaman->nama_tanaman ?? '-' }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;">
                {{ $benih->jenis_benih ?? '-' }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;text-align:right;">
                {{ $hargaSatuan }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;text-align:right;">
                {{ $jumlahTerjual }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;text-align:right;">
                {{ $pendapatan }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;text-align:right;">
                {{ $sisaStok }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" style="border:1px solid #999;padding:4px 5px;text-align:center;">
                Belum ada penjualan.
            </td>
        </tr>
    @endforelse

    <tr>
        <th colspan="5"
            style="border:1px solid #999;padding:4px 5px;text-align:right;font-weight:bold;">
            Total Pendapatan:
        </th>
        <th style="border:1px solid #999;padding:4px 5px;text-align:right;font-weight:bold;">
            {{ $grandTotal }}
        </th>
        <th style="border:1px solid #999;padding:4px 5px;"></th>
    </tr>
</table>

<br>

{{-- TOP 5 BENIH TERLARIS --}}
<table>
    <tr>
        <td colspan="6"
            style="background:#f3f4f6;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Top 5 Benih Terlaris
        </td>
    </tr>

    <tr>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">#</th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">Tanaman</th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">Jenis Benih</th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Jumlah Terjual
        </th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Total Pendapatan (Rp)
        </th>
        <th style="background:#f1f1f1;font-weight:bold;border:1px solid #999;padding:4px 5px;">
            Kontribusi (%)
        </th>
    </tr>

    @forelse ($topBenih as $index => $item)
        @php
            $benih      = $item->benih;
            $persentase = ($item->pendapatan / $totalPendapatanSafe) * 100;
        @endphp
        <tr>
            <td style="border:1px solid #999;padding:4px 5px;text-align:center;">
                {{ $index + 1 }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;">
                {{ $benih->jenisTanaman->nama_tanaman ?? '-' }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;">
                {{ $benih->jenis_benih ?? '-' }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;text-align:right;">
                {{ $item->jumlah_terjual }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;text-align:right;">
                {{ $item->pendapatan }}
            </td>
            <td style="border:1px solid #999;padding:4px 5px;text-align:right;">
                {{ number_format($persentase, 1, ',', '.') }}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" style="border:1px solid #999;padding:4px 5px;text-align:center;">
                Belum ada data Top 5.
            </td>
        </tr>
    @endforelse
</table>
