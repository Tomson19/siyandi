<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    protected $table = 'kabupaten';

    protected $fillable = [
        'nama_kabupaten',
    ];

    // Relasi ke user (satu kabupaten punya banyak user)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relasi ke varietas nanti (satu kabupaten punya banyak varietas)
    public function varietas()
    {
        return $this->hasMany(Varietas::class);
    }
}
