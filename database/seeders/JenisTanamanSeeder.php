<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisTanamanSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk menambahkan data jenis tanaman
     */
    public function run(): void
    {
        DB::table('jenis_tanaman')->insert([
            ['nama_tanaman' => 'Kopi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tanaman' => 'Lada', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tanaman' => 'Rosela', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tanaman' => 'Kelor', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
