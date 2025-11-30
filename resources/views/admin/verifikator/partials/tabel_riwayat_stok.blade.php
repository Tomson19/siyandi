@forelse ($riwayat as $r)
<tr>
    <td class="text-center">{{ $r->created_at->format('d M Y H:i') }}</td>
    <td class="text-center">{{ $r->benih->jenisTanaman->nama_tanaman ?? '-' }} ({{ $r->benih->jenis_benih ?? '-' }})</td>
    <td class="text-center">
        <span class="badge {{ $r->tipe === 'Masuk' ? 'bg-success' : 'bg-danger' }}">{{ $r->tipe }}</span>
    </td>
    <td class="text-center fw-bold">{{ number_format($r->jumlah) }}</td>
    <td class="text-center">{{ number_format($r->stok_awal) }}</td>
    <td class="text-center">{{ number_format($r->stok_akhir) }}</td>
    <td>{{ $r->keterangan ?? '-' }}</td>
    <td class="text-center">{{ $r->admin->name ?? '-' }}</td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center text-muted fst-italic">Tidak ada riwayat stok pada rentang waktu ini.</td>
</tr>
@endforelse
