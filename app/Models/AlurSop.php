<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlurSop extends Model
{
    protected $table = 'alur_sop';

    protected $fillable = [
        'judul',
        'file_path',
    ];
}
