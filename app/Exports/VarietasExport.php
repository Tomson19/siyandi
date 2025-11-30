<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;

class VarietasExport implements 
    FromCollection, 
    WithHeadings, 
    WithStyles, 
    WithColumnWidths, 
    ShouldAutoSize, 
    WithTitle,
    WithCustomStartCell,
    WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    // 🔢 Data utama
    public function collection()
    {
        return $this->data->values();
    }

    // 📊 Mapping kolom (biar bisa tambahkan nomor urut otomatis)
    public function map($v): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            ($v->deskripsi->nomor_sk ?? '-') . ' / ' . ($v->deskripsi->tanggal ?? '-'),
            $v->nama_varietas,
            $v->jenis_benih ?? '-',
            $v->deskripsi->pemilik_varietas ?? '-',
            $v->materiGenetik->count() ?? 0,
            $v->keterangan ?? '-',
        ];
    }

    // 🏷️ Header kolom
    public function headings(): array
    {
        return [
            'No',
            'Nomor & Tanggal SK',
            'Varietas',
            'Jenis Benih',
            'Pemilik Varietas',
            'Jumlah Materi Genetik (Pohon/Rumpun)',
            'Keterangan'
        ];
    }

    // 📍 Mulai isi tabel dari baris ke-3 (judul di baris 1)
    public function startCell(): string
    {
        return 'A3';
    }

    // 📄 Judul sheet
    public function title(): string
    {
        return 'Data Varietas Tanaman Kopi';
    }

    // 🎨 Styling keseluruhan
    public function styles(Worksheet $sheet)
    {
        $rowCount = $this->data->count() + 3; // 3 karena mulai dari baris 3

        // 🟩 Judul utama di baris pertama
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'Data Varietas Tanaman Kopi Provinsi Riau');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '000000']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // 🟢 Header tabel (baris ke-3)
        $sheet->getStyle('A3:G3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'A7D36E'], // hijau lembut
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '999999'],
                ],
            ],
        ]);

        // 📗 Isi tabel (mulai dari A4)
        $sheet->getStyle('A4:G' . $rowCount)->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '999999'],
                ],
            ],
        ]);

        // 🌿 Pewarnaan baris ganjil-genap (zebra striping)
        for ($i = 4; $i <= $rowCount; $i++) {
            $color = ($i % 2 == 0) ? 'F3FAE2' : 'E8F5D2';
            $sheet->getStyle("A{$i}:G{$i}")->getFill()->applyFromArray([
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => $color],
            ]);
        }

        return [];
    }

    // ⚙️ Atur lebar kolom
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 25,  // Nomor & Tanggal SK
            'C' => 20,  // Varietas
            'D' => 15,  // Jenis Benih
            'E' => 25,  // Pemilik Varietas
            'F' => 25,  // Jumlah Materi Genetik
            'G' => 20,  // Keterangan
        ];
    }
}
