<table width="100%">
    <tr>
        <td colspan="8" align="center" style="font-size:18px;font-weight:bold;color:#0d6efd;">
            LAPORAN RIWAYAT PERUBAHAN STOK BENIH
        </td>
    </tr>
    <tr>
        <td colspan="8" align="center" style="font-size:12px;color:#444;">
            @if(!empty($startDate) && !empty($endDate))
                Periode: <b>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</b> -
                <b>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</b>
            @else
                Periode: Semua Data (10 terakhir)
            @endif
        </td>
    </tr>
    <tr>
        <td colspan="8" align="center" style="font-size:11px;color:#777;">
            Dicetak pada: {{ now()->format('d M Y, H:i') }}
        </td>
    </tr>
</table>

{{-- ===================== RIWAYAT PERUBAHAN STOK ===================== --}}
<table border="1" cellspacing="0" cellpadding="6" width="100%" style="margin-top:25px;border-collapse:collapse;font-size:12px;">
    <thead>
        <tr style="background-color:#0d6efd;color:#fff;text-align:center;">
            <th style="width:12%;">Tanggal</th>
            <th style="width:22%;">Benih</th>
            <th style="width:8%;">Tipe</th>
            <th style="width:8%;">Jumlah</th>
            <th style="width:10%;">Stok Awal</th>
            <th style="width:10%;">Stok Akhir</th>
            <th style="width:20%;">Keterangan</th>
            <th style="width:10%;">Admin</th>
        </tr>
    </thead>
    <tbody>
        @forelse($riwayat as $r)
            <tr @if($loop->even) style="background-color:#f8f9fa;" @endif>
                <td align="center">{{ $r->created_at->format('d M Y H:i') }}</td>
                <td>{{ $r->benih->jenisTanaman->nama_tanaman ?? '-' }} ({{ $r->benih->jenis_benih ?? '-' }})</td>
                <td align="center" style="font-weight:bold;color:{{ $r->tipe === 'Masuk' ? '#198754' : '#dc3545' }}">
                    {{ $r->tipe }}
                </td>
                <td align="right">{{ number_format($r->jumlah) }}</td>
                <td align="right">{{ number_format($r->stok_awal) }}</td>
                <td align="right">{{ number_format($r->stok_akhir) }}</td>
                <td>{{ $r->keterangan ?? '-' }}</td>
                <td align="center">{{ $r->admin->name ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" align="center" style="font-style:italic;color:#888;">
                    Tidak ada riwayat stok pada periode ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- FOOTER --}}
<table width="100%" style="margin-top:30px;">
    <tr>
        <td align="right" style="font-size:10px;color:#999;">
            Sistem Informasi Benih — {{ config('app.name', 'Sistem Benih') }}
        </td>
    </tr>
</table>
