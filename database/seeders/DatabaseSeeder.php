<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // 1️⃣ Data dasar (harus ada dulu)
    $this->call(KabupatenSeeder::class);
    $this->call(JenisTanamanSeeder::class);

    // 2️⃣ Data user + role (bergantung pada kabupaten)
    $this->call(RolePermissionSeeder::class);

    // 3️⃣ Data domain (varietas, deskripsi, materi genetik)
    $this->call(VarietasSeeder::class);
    $this->call(DeskripsiVarietasSeeder::class);
    $this->call(MateriGenetikSeeder::class);
}

}
