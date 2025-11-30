<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanPenjualanExport implements FromView
{
    protected array $data;

    public function __construct(array $data)
    {
        // kirim semua data laporan (penjualan, topBenih, total, dll)
        $this->data = $data;
    }

    public function view(): View
    {
        // pakai view khusus Excel
        return view('admin.verifikator.exports.laporan_penjualan_excel', $this->data);
    }
}
