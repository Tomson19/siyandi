<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanStokExport implements FromArray, WithTitle, ShouldAutoSize, WithEvents
{
    protected $riwayat;
    protected $startDate;
    protected $endDate;

    public function __construct($riwayat, $startDate = null, $endDate = null)
    {
        $this->riwayat = $riwayat;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $data = [];

        // Baris 1–3: header laporan
        $periode = $this->startDate && $this->endDate
            ? 'Periode: ' . date('d M Y', strtotime($this->startDate)) . ' - ' . date('d M Y', strtotime($this->endDate))
            : 'Periode: Semua Data (10 terakhir)';

        $data[] = ['LAPORAN RIWAYAT PERUBAHAN STOK BENIH'];
        $data[] = [$periode];
        $data[] = ['Dicetak pada: ' . now()->format('d M Y, H:i')];
        $data[] = []; // baris kosong

        // Baris 5: header kolom
        $data[] = [
            'Tanggal',
            'Benih',
            'Tipe',
            'Jumlah',
            'Stok Awal',
            'Stok Akhir',
            'Keterangan',
            'Admin',
        ];

        // Data utama mulai baris 6
        foreach ($this->riwayat as $r) {
            $data[] = [
                $r->created_at->format('d M Y H:i'),
                ($r->benih->jenisTanaman->nama_tanaman ?? '-') . ' (' . ($r->benih->jenis_benih ?? '-') . ')',
                $r->tipe,
                $r->jumlah,
                $r->stok_awal,
                $r->stok_akhir,
                $r->keterangan ?? '-',
                $r->admin->name ?? '-',
            ];
        }

        return $data;
    }

  public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();

            // === Header Judul ===
            $sheet->mergeCells('A1:H1');
            $sheet->mergeCells('A2:H2');
            $sheet->mergeCells('A3:H3');

            $sheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0D6EFD']],
                'alignment' => ['horizontal' => 'center'],
            ]);
            $sheet->getStyle('A2:A3')->applyFromArray([
                'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '555555']],
                'alignment' => ['horizontal' => 'center'],
            ]);

            // === Header tabel (sekarang baris ke-5) ===
            $headerRow = 4;

            $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '0D6EFD']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                'borders' => ['allBorders' => ['borderStyle' => 'thin']],
            ]);

            // === Border seluruh tabel ===
            $highestRow = $sheet->getHighestRow();
            $sheet->getStyle("A{$headerRow}:H{$highestRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => 'thin']],
            ]);

            // === Pewarnaan Baris Data ===
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                $tipe = trim(strtolower((string) $sheet->getCell("C{$row}")->getValue()));

                // Selang-seling abu muda
                if ($row % 2 === 0) {
                    $sheet->getStyle("A{$row}:H{$row}")
                        ->getFill()->setFillType('solid')
                        ->getStartColor()->setRGB('F8F9FA');
                }

                // Warna pink lembut hanya untuk 'keluar'
                if ($tipe === 'keluar') {
                    $sheet->getStyle("A{$row}:H{$row}")
                        ->getFill()->setFillType('solid')
                        ->getStartColor()->setRGB('FFE5E5');
                }
            }

            // === Garis tebal di bawah header ===
            $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
                'borders' => [
                    'bottom' => ['borderStyle' => 'medium'],
                ],
            ]);

            // === Freeze header agar tetap terlihat ===
            $sheet->freezePane('A6');
        },
    ];
}



    public function title(): string
    {
        return 'Laporan Stok Benih';
    }
}
