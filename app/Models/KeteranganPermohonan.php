<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KeteranganPermohonan extends Model
{
    use HasFactory;

    protected $table = 'keterangan_permohonan';

    protected $fillable = [
        'permohonan_id',
        'admin_id',
        'jenis_keterangan',
        'isi_keterangan',
        'tanggal_keterangan',
    ];

    protected $casts = [
        'tanggal_keterangan' => 'date',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    // =============================
    // 🔗 RELATIONSHIPS
    // =============================

    public function permohonan()
    {
        return $this->belongsTo(PermohonanBenih::class, 'permohonan_id');
    }

    public function admin()
    {
        // admin tetap pakai model User
        return $this->belongsTo(User::class, 'admin_id');
    }

    // =============================
    // 🧠 ACCESSORS / HELPERS
    // =============================

    public function getTanggalKeteranganFormatAttribute()
    {
        return $this->tanggal_keterangan
            ? Carbon::parse($this->tanggal_keterangan)->translatedFormat('d F Y')
            : '-';
    }

    public function getJenisLabelAttribute()
    {
        return match ($this->jenis_keterangan) {
            'Perlu Diperbaiki'   => '<span class="badge bg-warning text-dark">Perlu Diperbaiki</span>',
            'Sedang Diverifikasi'=> '<span class="badge bg-info text-dark">Sedang Diverifikasi</span>',
            'Disetujui'          => '<span class="badge bg-success">Disetujui</span>',
            'Ditolak'            => '<span class="badge bg-danger">Ditolak</span>',
            default              => '<span class="badge bg-light text-dark">-</span>',
        };
    }
}
