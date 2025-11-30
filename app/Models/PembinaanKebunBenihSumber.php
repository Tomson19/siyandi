<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembinaanKebunBenihSumber extends Model
{
    use HasFactory;

    protected $table = 'pembinaan_kebun_benih_sumber';

    // kolom yang bisa di-mass assign
    protected $fillable = [
        'user_id',
        'pembinaan_sesi_id',
        'nama',
        'nik',
        'alamat',
        'no_hp',
        'jenis_tanaman_id',
        'lokasi_kebun',
        'latitude_kebun',
        'longitude_kebun',
        'jumlah_pohon_induk',
        'status',
        'alasan_status',
    ];

    // casting kalau mau
    protected $casts = [
        'jumlah_pohon_induk' => 'integer',
    ];

    /* ================== RELASI ================== */

    // pemohon
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // sesi pembinaan yang diikuti
    public function sesi()
    {
        return $this->belongsTo(PembinaanSesi::class, 'pembinaan_sesi_id');
    }

    // komoditas / jenis tanaman
    public function jenisTanaman()
    {
        return $this->belongsTo(JenisTanaman::class, 'jenis_tanaman_id');
    }

    /* ================== SCOPE BANTUAN (opsional) ================== */

    // hanya yang status menunggu jadwal
    public function scopeMenungguJadwal($query)
    {
        return $query->where('status', 'menunggu_jadwal');
    }

    // hanya yang status selesai
    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    // hanya yang status batal
    public function scopeBatal($query)
    {
        return $query->where('status', 'batal');
    }
}
