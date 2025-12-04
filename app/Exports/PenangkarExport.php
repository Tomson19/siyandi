<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PenangkarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected Collection $data;
    protected int $rowNumber = 0;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Komoditas',
            'Nama Produsen Benih Perorangan/Perusahaan',
            'NIB & Tanggal',
            'Sertifikat Sandar/Izin Usaha Prod. Benih Nomor & Tanggal',
            'Luas Areal (Ha)',
            'Jumlah Sertifikasi Benih Tahun Berjalan(Batang)',
            'Alamat',
            'Desa/Kelurahan',
            'Kecamatan',
            'Kabupaten',
            'LU/LS',
            'BT',
        ];
    }

    public function map($row): array
    {
        return [
            ++$this->rowNumber,
            optional($row->tanaman)->nama_tanaman,
            $row->nama_penangkar,
            $row->nib_dan_tanggal,
            $row->sertifikat_izin_usaha_nomor_dan_tanggal,
            $row->luas_areal_ha,
            $row->jumlah_sertifikasi,
            $row->jalan,
            $row->desa,
            $row->kecamatan,
            optional($row->kabupaten)->nama_kabupaten,
            $row->latitude,
            $row->longitude,
        ];
    }

    /**
     * Styling Excel supaya rapi saat di-download.
     */
    public function styles(Worksheet $sheet)
    {
        // Row header = baris 1
        $headerRow    = 1;
        $highestRow   = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $fullRange    = "A1:{$highestColumn}{$highestRow}";
        $headerRange  = "A{$headerRow}:{$highestColumn}{$headerRow}";

        // 1) Header: bold, center, background abu-abu
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0'],
            ],
        ]);

        // 2) Border ke semua sel
        $sheet->getStyle($fullRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 3) Alignment kolom tertentu
        //    - No (A), Luas Areal (F), LU/LS (K), BT (L) → center
        $centerColumns = ['A', 'F', 'K', 'L'];
        foreach ($centerColumns as $col) {
            $sheet->getStyle("{$col}1:{$col}{$highestRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // 4) Wrap text untuk kolom judul yang panjang: C, D, E, G
        $wrapColumns = ['C', 'D', 'E', 'G'];
        foreach ($wrapColumns as $col) {
            $sheet->getStyle("{$col}1:{$col}{$highestRow}")
                ->getAlignment()
                ->setWrapText(true);
        }

        // 5) Sedikit perkecil tinggi header
        $sheet->getRowDimension($headerRow)->setRowHeight(24);

        return [];
    }
}
