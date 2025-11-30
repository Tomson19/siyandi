<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penangkar extends Model
{
    protected $table = 'penangkar';

    protected $fillable = [
        'tanaman_id',
        'kabupaten_id',
        'nama_penangkar',
        'nib_dan_tanggal',
        'sertifikat_izin_usaha_nomor_dan_tanggal',
        'luas_areal_ha',
        'jumlah_sertifikasi',
        'jalan',
        'desa',
        'kecamatan',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'luas_areal_ha' => 'decimal:2',
        'latitude'      => 'decimal:6',
        'longitude'     => 'decimal:6',
    ];

    public function tanaman()
    {
        return $this->belongsTo(Tanaman::class);
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }
}
