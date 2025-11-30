<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTanaman extends Model
{
    use HasFactory;

    protected $table = 'jenis_tanaman';

    protected $fillable = ['nama_tanaman'];

    // Semua permohonan yang memilih jenis tanaman ini
    public function permohonanBenih()
    {
        return $this->hasMany(PermohonanBenih::class, 'jenis_tanaman_id');
    }

    // Semua stok benih (Biji / Siap Tanam, Gratis / Berbayar) untuk tanaman ini
    public function benih()
    {
        return $this->hasMany(Benih::class, 'jenis_tanaman_id');
    }

    public function programKegiatan()
    {
        return $this->hasMany(ProgramKegiatan::class, 'jenis_tanaman_id');
    }
}
