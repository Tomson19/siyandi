<?php

namespace App\Exports;

use App\Models\MateriGenetik;
use App\Models\Varietas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MateriGenetikExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $varietas_id;
    protected $varietas;

    public function __construct($varietas_id)
    {
        $this->varietas_id = $varietas_id;
        $this->varietas = Varietas::find($varietas_id);
    }

    /**
     * Ambil data materi genetik berdasarkan varietas
     */
    public function collection()
    {
        return MateriGenetik::where('varietas_id', $this->varietas_id)
            ->select('no_sk', 'tanggal_sk', 'nomor_pohon', 'latitude', 'longitude')
            ->get();
    }

    /**
     * Mapping setiap baris data
     */
    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            ($row->no_sk ?? '-') . ' / ' . ($row->tanggal_sk ?? '-'),
            $row->nomor_pohon ?? '-',
            $row->latitude ?? '-',
            $row->longitude ?? '-',
        ];
    }

    /**
     * Judul kolom Excel
     */
    public function headings(): array
    {
        return [
            ['MATERI GENETIK DAN KOORDINAT LOKASI'],
            ['Varietas: ' . ($this->varietas->nama_varietas ?? '-')],
            [],
            ['No.', 'No. SK dan Tanggal', 'Nomor Pohon', 'Latitude', 'Longitude'],
        ];
    }

    /**
     * Nama sheet
     */
    public function title(): string
    {
        return 'Materi Genetik';
    }

    /**
     * Styling tampilan Excel
     */
    public function styles(Worksheet $sheet)
    {
        // 🌿 Header utama (judul dan nama varietas)
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:A2')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('C6E0B4'); // Hijau lembut

        // 📘 Header tabel
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);
        $sheet->getStyle('A4:E4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4:E4')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D9D9D9'); // Abu muda
        $sheet->getStyle('A4:E4')->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // ✏️ Semua teks rata tengah kecuali No. SK dan Tanggal (biar enak dibaca)
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A5:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C5:E{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B5:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // 📏 Border semua data
        $sheet->getStyle("A4:E{$highestRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}
