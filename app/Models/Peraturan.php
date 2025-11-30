<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peraturan extends Model
{
    protected $table = 'peraturan';

    protected $fillable = [
        'nomor_tahun',
        'tanggal_penetapan',
        'tentang',
        'file_path',
    ];

    protected $casts = [
        'tanggal_penetapan' => 'date',
    ];
}
