<?php

namespace App\Models;
use App\Models\PembinaanKebunBenihSumber;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PembinaanSesi extends Model
{
    use HasFactory;

    protected $table = 'pembinaan_sesi';

    protected $fillable = [
        'jenis',
        'nama_sesi',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'meet_link',
        'materi_path',
        'bukti_pembinaan_path',
        'status',
        'alasan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    // admin pembuat sesi
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // peserta (pengajuan pembinaan)
    public function peserta()
    {
        return $this->hasMany(PembinaanPenangkar::class, 'pembinaan_sesi_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper status sesi
    |--------------------------------------------------------------------------
    */

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
    | Helper waktu (mulai / selesai)
    |--------------------------------------------------------------------------
    */

    // gabungkan tanggal + jam_mulai jadi Carbon
    public function getMulaiDateTimeAttribute(): ?Carbon
    {
        if (!$this->tanggal || !$this->jam_mulai) {
            return null;
        }

        return Carbon::parse($this->tanggal->toDateString() . ' ' . $this->jam_mulai);
    }

    // gabungkan tanggal + jam_selesai jadi Carbon
    public function getSelesaiDateTimeAttribute(): ?Carbon
    {
        if (!$this->tanggal || !$this->jam_selesai) {
            return null;
        }

        return Carbon::parse($this->tanggal->toDateString() . ' ' . $this->jam_selesai);
    }

    /**
     * Cek apakah sesi sudah lewat waktu selesai.
     * Dipakai untuk logika:
     * - sebelum selesai: tampilkan form edit sesi
     * - setelah lewat: tampilkan form ubah status sesi
     */
    public function getHasEndedAttribute(): bool
    {
        $selesai = $this->selesai_date_time;

        if (!$selesai) {
            return false;
        }

        return now()->greaterThan($selesai);
    }

    public function pesertaKbs()
    {
        return $this->hasMany(PembinaanKebunBenihSumber::class, 'pembinaan_sesi_id');
    }
}
