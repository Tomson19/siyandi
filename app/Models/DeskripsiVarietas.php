<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeskripsiVarietas extends Model
{
    use HasFactory;

    protected $table = 'deskripsi_varietas';

    protected $fillable = [
        'varietas_id',
        'nomor_sk',
        'tanggal',
        'tipe_varietas',
        'asal_usul',

        // Morfologi
        'tipe_pertumbuhan',
        'bentuk_tajuk',

        // Daun
        'daun_ukuran',
        'daun_warna_muda',
        'daun_warna_tua',
        'daun_bentuk_ujung',
        'daun_tepi',
        'daun_pangkal',
        'daun_permukaan',
        'daun_warna_pucuk',

        // Bunga
        'bunga_warna_mahkota',
        'bunga_jumlah_mahkota',
        'bunga_ukuran',

        // Buah
        'buah_ukuran',
        'buah_panjang',
        'buah_diameter',
        'buah_bobot',
        'buah_bentuk',
        'buah_warna_muda',
        'buah_warna_masak',
        'buah_ukuran_discus',

        // Biji
        'biji_bentuk',
        'biji_nisbah',
        'biji_persen_normal',
        'citarasa',
        'potensi_produksi',

        // Ketahanan & Adaptasi
        'penyakit_karat_daun',
        'penggerek_buah_kopi',
        'daerah_adaptasi',

        // Pemuliaan
        'pemulia',
        'peneliti',
        'pemilik_varietas',
    ];

    // Relasi balik ke varietas
    public function varietas()
    {
        return $this->belongsTo(Varietas::class);
    }
}
