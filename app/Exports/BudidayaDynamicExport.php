<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BudidayaDynamicExport implements FromArray, WithHeadings, WithColumnWidths, WithEvents, WithColumnFormatting
{
    /**
     * Setiap row: [NO, KOMODITI, KABUPATEN/KOTA, LUAS]
     * (sudah dinormalisasi di constructor)
     */
    protected $rows = [];

    /**
     * Total luas (Ha)
     */
    protected float $totalLuas = 0.0;

    /**
     * @param array $rows  // boleh associative (['no'=>..]) atau index [0=>..]
     */
    public function __construct(array $rows)
    {
        $normalized = [];
        $total = 0.0;

        foreach ($rows as $row) {
            // Support dua bentuk: associative & numeric index
            if (isset($row['no']) || isset($row['komoditi']) || isset($row['luas'])) {
                $no        = (int)   ($row['no'] ?? 0);
                $komoditi  = (string)($row['komoditi'] ?? '');
                $kabupaten = (string)($row['kabupaten'] ?? ($row['kabupaten_kota'] ?? ''));
                $luas      = (float) ($row['luas'] ?? 0);
            } else {
                $no        = (int)   ($row[0] ?? 0);
                $komoditi  = (string)($row[1] ?? '');
                $kabupaten = (string)($row[2] ?? '');
                $luas      = (float) ($row[3] ?? 0);
            }

            $normalized[] = [
                $no,
                $komoditi,
                $kabupaten,
                $luas,
            ];

            $total += $luas;
        }

        $this->rows = $normalized;
        $this->totalLuas = $total;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'NO',
            'KOMODITI',
            'KABUPATEN/KOTA',
            'LUAS (Ha)',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // NO
            'B' => 25,  // KOMODITI
            'C' => 30,  // KABUPATEN/KOTA
            'D' => 18,  // LUAS
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Format angka ribuan untuk kolom luas
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // --- Hitung posisi baris ---
                $dataCount   = count($this->rows); // jumlah baris data
                $lastDataRow = $dataCount + 1;     // header di baris 1
                $totalRow    = $lastDataRow + 1;   // baris setelah data

                // ====== ISI BARIS TOTAL (pakai nilai dari PHP, bukan formula) ======
                // B + C di-merge untuk tulisan TOTAL
                $sheet->setCellValue("C{$totalRow}", 'TOTAL');
                $sheet->mergeCells("B{$totalRow}:C{$totalRow}");

                // D = total luas dari PHP
                $sheet->setCellValue("D{$totalRow}", $this->totalLuas);

                // Style baris TOTAL
                $sheet->getStyle("B{$totalRow}:D{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2F0D9'], // hijau muda
                    ],
                ]);

                // Alignment TOTAL
                $sheet->getStyle("C{$totalRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("D{$totalRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // ====== RANGE SELURUH TABEL (HEADER + DATA + TOTAL) ======
                $tableRange = "A1:D{$totalRow}";

                // HEADER (A1:D1)
                $sheet->getStyle('A1:D1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F9D5D'], // hijau
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // BORDER semua
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // ALIGNMENT DATA (kalau ada data)
                if ($dataCount > 0) {
                    // NO
                    $sheet->getStyle("A2:A{$lastDataRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // KOMODITI + KABUPATEN
                    $sheet->getStyle("B2:C{$lastDataRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    // LUAS
                    $sheet->getStyle("D2:D{$lastDataRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                // Vertikal tengah semua
                $sheet->getStyle($tableRange)->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Wrap text kolom kabupaten
                $sheet->getStyle("C1:C{$totalRow}")->getAlignment()
                    ->setWrapText(true);
            },
        ];
    }
}
