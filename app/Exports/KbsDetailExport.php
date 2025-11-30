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

class KbsDetailExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected Collection $rows;
    protected string $sheetTitle;

    public function __construct(Collection $rows, string $sheetTitle = 'Detail KBS')
    {
        $this->rows       = $rows;
        $this->sheetTitle = $sheetTitle;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Komoditas',
            'Varietas',
            'Nomor SK',
            'Tanggal SK',
            'Kabupaten',
            'Kecamatan',
            'Desa',
            'Tahun Tanam',
            'Jumlah PIT',
            'No Pemilik',
            'Nama Pemilik',
            'Luas (Ha)',
            'Jumlah Pohon Induk',
            'No Pohon',
            'No Pohon Induk',
            'Latitude',
            'Longitude',
        ];
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    /**
     * Styling supaya file Excel rapi saat di-download.
     */
    public function styles(Worksheet $sheet)
    {
        $headerRow     = 1;
        $highestRow    = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn(); // harusnya R
        $fullRange     = "A1:{$highestColumn}{$highestRow}";
        $headerRange   = "A{$headerRow}:{$highestColumn}{$headerRow}";

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

        // 3) Kolom angka / kode yang enak kalau di-center:
        //    A: No
        //    I: Tahun Tanam
        //    J: Jumlah PIT
        //    K: No Pemilik
        //    M: Luas (Ha)
        //    N: Jumlah Pohon Induk
        //    O: No Pohon
        //    P: No Pohon Induk
        //    Q: Latitude
        //    R: Longitude
        $centerColumns = ['A', 'I', 'J', 'K', 'M', 'N', 'O', 'P', 'Q', 'R'];
        foreach ($centerColumns as $col) {
            $sheet->getStyle("{$col}1:{$col}{$highestRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // 4) Wrap text untuk kolom yang bisa panjang:
        //    C: Varietas
        //    F: Kabupaten
        //    G: Kecamatan
        //    H: Desa
        //    L: Nama Pemilik
        $wrapColumns = ['C', 'F', 'G', 'H', 'L'];
        foreach ($wrapColumns as $col) {
            $sheet->getStyle("{$col}1:{$col}{$highestRow}")
                ->getAlignment()
                ->setWrapText(true);
        }

        // 5) Sedikit atur tinggi header
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        return [];
    }
}
