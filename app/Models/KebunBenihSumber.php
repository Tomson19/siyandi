<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KebunBenihSumber extends Model
{
    // Karena nama tabel tidak jamak "kebun_benih_sumbers", kita set manual
    protected $table = 'kebun_benih_sumber';

    protected $fillable = [
        'tanaman_id',
        'kabupaten_id',
        'nama_varietas',
        'nomor_sk',
        'tanggal_sk',
        'kecamatan',
        'desa',
        'tahun_tanam',
        'jumlah_pit',
        'nama_pemilik',
        'luas_ha',
        'jumlah_pohon_induk',
        'nomor_pohon_induk',
        'latitude',
        'longitude',
    ];

    // Relasi
    public function tanaman()
    {
        return $this->belongsTo(Tanaman::class);
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }
    public function pemilik()
{
    return $this->hasMany(\App\Models\KbsPemilik::class, 'kbs_id');
}

}
