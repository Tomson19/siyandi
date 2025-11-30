<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benih extends Model
{
    use HasFactory;

    protected $table = 'benih';

    protected $fillable = [
        'jenis_tanaman_id',
        'jenis_benih',
        'tipe_pembayaran',
        'stok',
        'harga',
        'gambar',
    ];

    // 🔗 Relasi: satu jenis tanaman bisa punya banyak benih
    public function jenisTanaman()
    {
        return $this->belongsTo(JenisTanaman::class, 'jenis_tanaman_id');
    }

    // 🔗 Relasi: satu benih bisa muncul di banyak permohonan
    public function permohonanBenih()
    {
        return $this->hasMany(PermohonanBenih::class, 'benih_id');
    }

    // 💰 Helper: apakah berbayar?
    public function getIsBerbayarAttribute(): bool
    {
        return $this->tipe_pembayaran === 'Berbayar';
    }

    // 📉 Helper: kurangi stok aman
    public function kurangiStok(int $jumlah)
    {
        if ($this->stok >= $jumlah) {
            $this->decrement('stok', $jumlah);
        } else {
            \Log::warning("Stok tidak cukup untuk benih ID {$this->id}");
        }
    }
}
