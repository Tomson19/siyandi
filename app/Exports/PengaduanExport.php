<?php

namespace App\Exports;

use App\Models\Pengaduan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PengaduanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithEvents,
    WithColumnWidths
{
    protected $search;
    protected $from;
    protected $to;

    /**
     * Terima filter dari controller (q, from, to).
     */
    public function __construct($search = null, $from = null, $to = null)
    {
        $this->search = $search;
        $this->from   = $from;
        $this->to     = $to;
    }

    /**
     * Data yang akan diexport.
     */
    public function collection()
    {
        $query = Pengaduan::query();

        if ($this->search) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('pengaduan', 'like', "%{$search}%");
            });
        }

        if ($this->from) {
            $query->whereDate('created_at', '>=', $this->from);
        }

        if ($this->to) {
            $query->whereDate('created_at', '<=', $this->to);
        }

        return $query
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Judul kolom Excel.
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pengaduan',
            'Nama',
            'NIK',
            'Alamat',
            'No HP',
            'Isi Pengaduan',
        ];
    }

    /**
     * Mapping setiap baris data -> kolom Excel.
     */
    public function map($pengaduan): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            optional($pengaduan->created_at)->format('d-m-Y H:i'),
            $pengaduan->nama,
            $pengaduan->nik,
            $pengaduan->alamat,
            $pengaduan->no_hp,
            $pengaduan->pengaduan,
        ];
    }

    /**
     * Lebar kolom agar teks bisa wrap ke bawah, bukan melebar.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 18,  // Tanggal
            'C' => 20,  // Nama
            'D' => 18,  // NIK
            'E' => 25,  // Alamat
            'F' => 15,  // No HP
            'G' => 40,  // Pengaduan (panjang, sengaja lebar & wrap)
            'H' => 18,  // IP
            'I' => 35,  // User Agent (juga panjang)
        ];
    }

    /**
     * Style dasar (misal header).
     */
    public function styles(Worksheet $sheet)
    {
        // Header row (row 1)
        return [
            1 => [
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
                    'startColor' => ['argb' => 'FFE0E0E0'], // abu-abu muda
                ],
            ],
        ];
    }

    /**
     * Event tambahan: border, wrap text, freeze header, auto filter, dll.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();

                $dataRange = 'A1:' . $highestCol . $highestRow;

                // Border untuk semua sel
                $sheet->getStyle($dataRange)->getBorders()->applyFromArray([
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FF000000'],
                    ],
                ]);

                // Wrap text + vertical top untuk semua data
                $sheet->getStyle($dataRange)->getAlignment()->setWrapText(true);
                $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

                // Center untuk kolom No, Tanggal, NIK, IP
                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D2:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Freeze header (baris 1)
                $sheet->freezePane('A2');

                // Auto filter di row 1
                $sheet->setAutoFilter('A1:' . $highestCol . '1');

                // Tinggi baris header
                $sheet->getRowDimension(1)->setRowHeight(22);
            },
        ];
    }
}
