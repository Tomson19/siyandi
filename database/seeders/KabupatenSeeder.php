<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KabupatenSeeder extends Seeder
{
    public function run(): void
    {
        $kabupaten = [
            'Pekanbaru',
            'Kampar',
            'Bengkalis',
            'Indragiri Hulu',
            'Indragiri Hilir',
            'Kuantan Singingi',
            'Pelalawan',
            'Rokan Hilir',
            'Rokan Hulu',
            'Siak',
            'Kepulauan Meranti',
            'Dumai',
        ];

        foreach ($kabupaten as $k) {
            DB::table('kabupaten')->insert([
                'nama_kabupaten' => $k,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
