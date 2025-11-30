<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveiKepuasan extends Model
{
    protected $table = 'survei_kepuasan';

    protected $fillable = [
        'q1_tampilan',
        'q2_fitur',
        'q3_informasi',
        'q4_sukai',
        'q5_kinerja',
        'q6_rekomendasi',
        'ip_address',
        'user_agent',
    ];
}
