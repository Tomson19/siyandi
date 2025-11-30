<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramKegiatan extends Model
{
    // karena kita pakai nama tabel 'program_kegiatan'
    protected $table = 'program_kegiatan';

    protected $fillable = [
        'nama_program',
        'nama_kegiatan',
        'jenis_tanaman_id',
        'jumlah_produksi',
        'jenis_benih',
        'kebutuhan_benih',
        'bidang',
        'tahun',
        'user_id',
    ];

    public function jenisTanaman()
    {
        return $this->belongsTo(JenisTanaman::class, 'jenis_tanaman_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

