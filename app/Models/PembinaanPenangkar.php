<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembinaanPenangkar extends Model
{
    use HasFactory;

    protected $table = 'pembinaan_penangkar';

    protected $fillable = [
        'user_id',
        'pembinaan_sesi_id',

        'jenis_benih_diusahakan',

        'nama_penangkar',
        'nama_penanggung_jawab',
        'nik',
        'alamat_penanggung_jawab',
        'npwp',
        'lokasi_usaha',
        'status_kepemilikan_lahan',
        'no_hp',

        // status pembinaan
        'status',
        'alasan_status',

        // data OSS
        'nib',
        'no_sertifikat_standar',

        // status perizinan
        'status_perizinan',
        'alasan_perizinan',
    ];

    // pemohon
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // sesi pembinaan
    public function sesi()
    {
        return $this->belongsTo(PembinaanSesi::class, 'pembinaan_sesi_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper status pembinaan
    |--------------------------------------------------------------------------
    */

    public function getIsMenungguJadwalAttribute(): bool
    {
        return $this->status === 'menunggu_jadwal';
    }

    public function getIsDijadwalkanAttribute(): bool
    {
        return $this->status === 'dijadwalkan';
    }

    public function getIsSelesaiAttribute(): bool
    {
        return $this->status === 'selesai';
    }

    public function getIsBatalAttribute(): bool
    {
        return $this->status === 'batal';
    }

    /*
    |--------------------------------------------------------------------------
    | Helper status perizinan
    |--------------------------------------------------------------------------
    */

    public function getIsPerizinanMenungguAttribute(): bool
    {
        return $this->status_perizinan === 'menunggu';
    }

    public function getIsPerizinanBerhasilAttribute(): bool
    {
        return $this->status_perizinan === 'berhasil';
    }

    public function getIsPerizinanDibatalkanAttribute(): bool
    {
        return $this->status_perizinan === 'dibatalkan';
    }
}
