<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PermohonanBenihExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Pemohon',
            'NIK',
            'Alamat',
            'No. Telp',
            'Jenis Tanaman',
            'Jenis Benih',
            'Tipe Permohonan',
            'Jumlah Diajukan',
            'Jumlah Disetujui',
            'Luas Area (Ha)',
            'Status Utama',
            'Status Pembayaran',
            'Status Pengambilan',
            'Tanggal Diajukan',
            'Tanggal Disetujui',
            'Tanggal Ditolak',
            'Tanggal Pengambilan',
            'Tanggal Selesai',
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->nama,
            $item->nik,
            $item->alamat,
            $item->no_telp,
            $item->jenisTanaman->nama_tanaman ?? '-',
            $item->jenis_benih ?? '-',
            $item->tipe_pembayaran ?? '-',
            $item->jumlah_tanaman,
            $item->jumlah_disetujui ?? '-',
            $item->luas_area ?? '-',
            $item->status ?? '-',
            $item->status_pembayaran ?? '-',
            $item->status_pengambilan ?? '-',
            optional($item->tanggal_diajukan)->format('Y-m-d'),
            optional($item->tanggal_disetujui)->format('Y-m-d'),
            optional($item->tanggal_ditolak)->format('Y-m-d'),
            optional($item->tanggal_pengambilan)->format('Y-m-d'),
            optional($item->tanggal_selesai)->format('Y-m-d'),
        ];
    }
}
