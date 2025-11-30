<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriGenetik extends Model
{
    use HasFactory;

    protected $table = 'materi_genetik';

    protected $fillable = [
        'varietas_id',
        'no_sk',
        'tanggal_sk',
        'nomor_pohon',
        'latitude',   // LU/LS
        'longitude',  // BT
        'keterangan',
    ];

    // ==========================
    // 🔗 RELASI MODEL
    // ==========================

    // Relasi ke varietas (setiap titik koordinat milik satu varietas)
    public function varietas()
    {
        return $this->belongsTo(Varietas::class);
    }
}
