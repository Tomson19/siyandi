<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Varietas extends Model
{
    use HasFactory;

    protected $table = 'varietas';

    protected $fillable = [
        'tanaman_id',
        'kabupaten_id',
        'user_id',
        'nomor_tanggal_sk',
        'nama_varietas',
        'jenis_benih',
        'pemilik_varietas',
        'jumlah_materi_genetik',
        'keterangan',
        'status',
    ];

    // ==========================
    // 🔗 RELASI MODEL
    // ==========================

    // Relasi ke tanaman (misal: kopi, teh, kakao)
    public function tanaman()
    {
        return $this->belongsTo(Tanaman::class);
    }

    // Relasi ke kabupaten (tempat varietas berasal)
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    // Relasi ke user/admin yang input data
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke deskripsi varietas (1 varietas punya 1 deskripsi)
    public function deskripsi()
    {
        return $this->hasOne(DeskripsiVarietas::class);
    }

    // Relasi ke materi genetik (1 varietas punya banyak titik koordinat)
    public function materiGenetik()
    {
        return $this->hasMany(MateriGenetik::class);
    }
    public function getJumlahMateriGenetikAttribute($value)
{
    return (int) $value;
}
}
