<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\QueueableVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail // 👈 tambahin interface
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Atribut yang dapat diisi massal.
     */
    protected $fillable = [
        'name',
        'nik',          // 👈 baru
        'phone',        // 👈 baru
        'email',
        'password',
        'kabupaten_id',
        // NOTE:
        // is_locked, locked_at, alasan_lock
        // TIDAK dimasukkan ke fillable supaya hanya diubah oleh sistem/admin,
        // bukan oleh mass assignment dari form user.
    ];

    /**
     * Atribut yang harus disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konversi atribut.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_locked'         => 'boolean',
            'locked_at'         => 'datetime',
        ];
    }

    // ==============================
    // ✉️ Kirim email verifikasi via QUEUE
    // ==============================
    public function sendEmailVerificationNotification()
    {
        $this->notify(new QueueableVerifyEmail());
    }

    // ==============================
    // 🔗 Relasi ke tabel kabupaten
    // ==============================
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class);
    }

    // ==============================
    // 🔗 Relasi ke permohonan benih
    // ==============================
    public function permohonanBenih()
    {
        return $this->hasMany(PermohonanBenih::class, 'user_id');
    }

    // ==============================
    // 🔗 Relasi ke keterangan permohonan (sebagai admin)
    // ==============================
    public function keteranganPermohonanDibuat()
    {
        return $this->hasMany(KeteranganPermohonan::class, 'admin_id');
    }

    public function pembinaanPenangkar()
    {
        return $this->hasMany(\App\Models\PembinaanPenangkar::class);
    }
}
