<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PermohonanBenihExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithColumnWidths,
    WithStyles,
    WithColumnFormatting
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
            // NOTE:
            // kalau mau bener2 date Excel, kirim object tanggal (Carbon) tanpa format('Y-m-d')
            optional($item->tanggal_diajukan)?->format('Y-m-d'),
            optional($item->tanggal_disetujui)?->format('Y-m-d'),
            optional($item->tanggal_ditolak)?->format('Y-m-d'),
            optional($item->tanggal_pengambilan)?->format('Y-m-d'),
            optional($item->tanggal_selesai)?->format('Y-m-d'),
        ];
    }

    /**
     * Atur lebar kolom tertentu (yang lain auto-size).
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // ID
            'B' => 25,  // Nama Pemohon
            'C' => 18,  // NIK
            'D' => 40,  // Alamat
            'E' => 15,  // No. Telp
            'F' => 20,  // Jenis Tanaman
            'G' => 20,  // Jenis Benih
            'H' => 20,  // Tipe Permohonan
            'I' => 16,  // Jumlah Diajukan
            'J' => 16,  // Jumlah Disetujui
            'K' => 14,  // Luas Area
            'L' => 18,  // Status Utama
            'M' => 18,  // Status Pembayaran
            'N' => 18,  // Status Pengambilan
            'O' => 18,
            'P' => 18,
            'Q' => 18,
            'R' => 18,
            'S' => 18,
        ];
    }

    /**
     * Style Excel (header bold, warna, border, alignment, wrap text, dll).
     */
    public function styles(Worksheet $sheet)
    {
        // baris header (1)
        $sheet->getStyle('A1:S1')->getFont()->setBold(true);
        $sheet->getStyle('A1:S1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // warna background header
        $sheet->getStyle('A1:S1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFCCE5FF'); // biru muda

        // row height header sedikit lebih tinggi
        $sheet->getRowDimension(1)->setRowHeight(24);

        // ambil last row (buat range border & alignment)
        $lastRow = $sheet->getHighestRow();

        // border semua sel yang terisi
        $sheet->getStyle("A1:S{$lastRow}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // wrap text untuk alamat
        $sheet->getStyle("D2:D{$lastRow}")
            ->getAlignment()->setWrapText(true);

        // center ID + angka
        $sheet->getStyle("A2:A{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("I2:K{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // center status-status
        $sheet->getStyle("L2:N{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // center tanggal-tanggal
        $sheet->getStyle("O2:S{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return []; // kita styling langsung ke $sheet
    }

    /**
     * Format kolom (kalau ingin pakai format date / angka Excel).
     * NOTE: ini akan kepake maksimal kalau value tanggal dikirim sebagai object Date/Carbon.
     */
    public function columnFormats(): array
    {
        return [
            // tanggal-tanggal
            'O' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'P' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'Q' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'R' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'S' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }
}
