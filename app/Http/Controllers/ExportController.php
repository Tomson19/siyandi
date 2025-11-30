<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Varietas;
use App\Models\DeskripsiVarietas;
use App\Models\MateriGenetik;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VarietasExport;
use App\Exports\MateriGenetikExport;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportController extends Controller
{
    /* ============================================================
     * 📊 EXPORT VARIETAS
     * ============================================================ */
    public function exportVarietasExcel(Request $request)
    {
        $query = Varietas::with(['kabupaten', 'tanaman', 'deskripsi']);

        if ($request->tanaman_id) {
            $query->where('tanaman_id', $request->tanaman_id);
        }

        if ($request->kabupaten_id) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }

        $data = $query->get();
        return Excel::download(new VarietasExport($data), 'data_varietas.xlsx');
    }

    public function exportVarietasPDF(Request $request)
    {
        $query = Varietas::with(['kabupaten', 'tanaman', 'deskripsi']);

        if ($request->tanaman_id) {
            $query->where('tanaman_id', $request->tanaman_id);
        }

        if ($request->kabupaten_id) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }

        $data = $query->get();
        $pdf = Pdf::loadView('exports.varietas_pdf', compact('data'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('data_varietas.pdf');
    }

    /* ============================================================
     * 📄 EXPORT DESKRIPSI (PDF & EXCEL TEMPLATE LENGKAP)
     * ============================================================ */
    public function exportDeskripsiPDF($id)
    {
        $data = DeskripsiVarietas::with('varietas')
            ->where('varietas_id', $id)
            ->firstOrFail();

        $pdf = Pdf::loadView('exports.deskripsi_pdf', compact('data'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('deskripsi_' . $data->varietas->nama_varietas . '.pdf');
    }

    public function exportDeskripsiExcel($id)
{
    $d = \App\Models\DeskripsiVarietas::with('varietas')->where('varietas_id', $id)->firstOrFail();
    $nama = strtoupper($d->varietas->nama_varietas ?? '-');
    $fileName = 'Deskripsi_' . $nama . '.xlsx';

    return \Maatwebsite\Excel\Facades\Excel::download(new class($d, $nama) implements \Maatwebsite\Excel\Concerns\WithEvents {
        protected $d;
        protected $nama;

        public function __construct($data, $nama)
        {
            $this->d = $data;
            $this->nama = $nama;
        }

        public function registerEvents(): array
        {
            return [
                \Maatwebsite\Excel\Events\AfterSheet::class => function ($event) {
                    /** @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $s */
                    $s = $event->sheet->getDelegate();

                    // Judul utama
                    $s->mergeCells('A1:C1');
                    $s->setCellValue('A1', 'DESKRIPSI KOPI ' . $this->nama);
                    $s->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                    $s->getStyle('A1')->getAlignment()->setHorizontal('center');

                    $rows = [
                        ['Keputusan Menteri Pertanian RI', ''],
                        ['Nomor', $this->d->nomor_sk],
                        ['Tanggal', $this->d->tanggal],
                        ['Tipe varietas', $this->d->tipe_varietas],
                        ['Asal-usul', $this->d->asal_usul],
                        ['Tipe pertumbuhan', $this->d->tipe_pertumbuhan],
                        ['Bentuk tajuk', $this->d->bentuk_tajuk],
                        ['Daun', ''],
                        ['Ukuran', $this->d->daun_ukuran],
                        ['Warna daun muda', $this->d->daun_warna_muda],
                        ['Warna daun tua', $this->d->daun_warna_tua],
                        ['Bentuk ujung daun', $this->d->daun_bentuk_ujung],
                        ['Tepi daun', $this->d->daun_tepi],
                        ['Pangkal daun', $this->d->daun_pangkal],
                        ['Permukaan daun', $this->d->daun_permukaan],
                        ['Warna pucuk', $this->d->daun_warna_pucuk],
                        ['Bunga', ''],
                        ['Warna mahkota', $this->d->bunga_warna_mahkota],
                        ['Jumlah mahkota', $this->d->bunga_jumlah_mahkota],
                        ['Ukuran bunga', $this->d->bunga_ukuran],
                        ['Buah', ''],
                        ['Ukuran buah', $this->d->buah_ukuran],
                        ['Panjang (cm)', $this->d->buah_panjang],
                        ['Diameter (cm)', $this->d->buah_diameter],
                        ['Bobot (gram)', $this->d->buah_bobot],
                        ['Bentuk buah', $this->d->buah_bentuk],
                        ['Warna buah muda', $this->d->buah_warna_muda],
                        ['Warna buah masak', $this->d->buah_warna_masak],
                        ['Ukuran discus', $this->d->buah_ukuran_discus],
                        ['Biji', ''],
                        ['Bentuk', $this->d->biji_bentuk],
                        ['Nisbah biji buah atau rata-rata rendemen (%)', $this->d->biji_nisbah],
                        ['Persentase biji normal (%)', $this->d->biji_persen_normal],
                        ['Citarasa', $this->d->citarasa],
                        ['Potensi produksi', $this->d->potensi_produksi],
                        ['Ketahanan terhadap hama penyakit utama', ''],
                        ['Penyakit karat daun', $this->d->penyakit_karat_daun],
                        ['Pengerek buah kopi (PBKo)', $this->d->penggerek_buah_kopi],
                        ['Daerah adaptasi', $this->d->daerah_adaptasi],
                        ['Pemulia', $this->d->pemulia],
                        ['Peneliti', $this->d->peneliti],
                        ['Pemilik varietas', $this->d->pemilik_varietas],
                    ];

                    // Isi ke Excel
                    $r = 3;
                    foreach ($rows as $row) {
                        $s->setCellValue("A{$r}", $row[0]);
                        $s->setCellValue("B{$r}", ':');
                        $s->setCellValue("C{$r}", $row[1] ?? '-');

                        // Highlight section titles
                        if (empty($row[1])) {
                            $s->mergeCells("A{$r}:C{$r}");
                            $s->getStyle("A{$r}")->getFont()->setBold(true);
                            $s->getStyle("A{$r}")->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFEFEFEF');
                        }

                        $r++;
                    }

                    // Styling border dan wrap
                    $s->getStyle("A3:C" . ($r - 1))->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ]
                        ],
                        'alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ]
                    ]);

                    foreach (['A', 'B', 'C'] as $col) {
                        $s->getColumnDimension($col)->setAutoSize(true);
                    }
                }
            ];
        }
    }, $fileName);
}


    /* ============================================================
     * 🧬 EXPORT MATERI GENETIK
     * ============================================================ */
    public function exportMateriExcel($id)
    {
        return Excel::download(new MateriGenetikExport($id), 'materi_genetik.xlsx');
    }

    public function exportMateriPDF($id)
    {
        $data = MateriGenetik::where('varietas_id', $id)->get();
        $varietas = Varietas::findOrFail($id);

        $pdf = Pdf::loadView('exports.materi_pdf', compact('data', 'varietas'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('materi_genetik_' . $varietas->nama_varietas . '.pdf');
    }
}
