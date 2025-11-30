<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanQris extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_qris';

    protected $fillable = [
        'nama_qris',
        'gambar_qris',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];
}
