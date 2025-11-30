<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbsPohon extends Model
{
    protected $table = 'kbs_pohon';

    protected $fillable = [
        'kbs_pemilik_id',
        'no_pohon',
        'nomor_pohon_induk',
        'latitude',
        'longitude',
    ];

    public function pemilik()
    {
        return $this->belongsTo(KbsPemilik::class, 'kbs_pemilik_id');
    }
}
