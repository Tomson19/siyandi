<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KbsListExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->values()->map(function ($k, $index) {
            return [
                'No'                 => $index + 1,
                'Komoditas'          => $k->tanaman->nama_tanaman ?? '-',
                'Nomor & Tanggal SK' => trim(($k->nomor_sk ?? '') . ' ' . ($k->tanggal_sk ?? '')),
                'Varietas'           => $k->nama_varietas,
                'Kabupaten'          => $k->kabupaten->nama_kabupaten ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Komoditas',
            'Nomor & Tanggal SK',
            'Varietas',
            'Kabupaten',
        ];
    }

    public function title(): string
    {
        return 'Kebun Benih Sumber';
    }

    /**
     * Styling supaya Excel rapi saat di-download.
     */
    public function styles(Worksheet $sheet)
    {
        $headerRow    = 1;
        $highestRow   = $sheet->getHighestRow();     // baris terakhir
        $highestColumn = $sheet->getHighestColumn(); // harusnya 'E'
        $fullRange    = "A1:{$highestColumn}{$highestRow}";
        $headerRange  = "A{$headerRow}:{$highestColumn}{$headerRow}";

        // 1) Header: bold, center, background abu-abu, wrap text
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

        // 3) Kolom tertentu center (No)
        $centerColumns = ['A']; // kolom "No"
        foreach ($centerColumns as $col) {
            $sheet->getStyle("{$col}1:{$col}{$highestRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // 4) Wrap text untuk kolom yang judul/isinya bisa panjang
        //    - C: Nomor & Tanggal SK
        //    - D: Varietas
        $wrapColumns = ['C', 'D'];
        foreach ($wrapColumns as $col) {
            $sheet->getStyle("{$col}1:{$col}{$highestRow}")
                ->getAlignment()
                ->setWrapText(true);
        }

        // 5) Sedikit atur tinggi header
        $sheet->getRowDimension($headerRow)->setRowHeight(20);

        return [];
    }
}
