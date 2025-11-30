<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';

    protected $fillable = [
        'nama',
        'nik',
        'alamat',
        'no_hp',
        'pengaduan',
        'gambar_path',
        'ip_address',
        'user_agent',
    ];
}
