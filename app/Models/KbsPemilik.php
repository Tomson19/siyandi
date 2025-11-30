<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbsPemilik extends Model
{
    protected $table = 'kbs_pemilik';

    protected $fillable = [
        'kbs_id',
        'kecamatan',
        'desa',
        'tahun_tanam',
        'jumlah_pit',
        'no_pemilik',
        'nama_pemilik',
        'luas_ha',
        'jumlah_pohon_induk',
    ];

    public function kbs()
    {
        return $this->belongsTo(KebunBenihSumber::class, 'kbs_id');
    }

    public function pohon()
    {
        return $this->hasMany(KbsPohon::class, 'kbs_pemilik_id');
    }
}
