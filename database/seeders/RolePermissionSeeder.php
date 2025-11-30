<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Kabupaten;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ====== ROLE DASAR ======
        $roles = [
            'admin_super',
            'admin_operator',
            'admin_verifikator',
            'admin_keuangan',
            'admin_manager',
            'admin_bidang_produksi',
            'admin_upt_sertifikasi',
            'pemohon',
        ];

        // ====== ROLE KABUPATEN RIAU ======
        $kabupatenRiau = [
            'admin_pekanbaru',
            'admin_kampar',
            'admin_bengkalis',
            'admin_indragiri_hulu',
            'admin_indragiri_hilir',
            'admin_kuantan_singingi',
            'admin_pelalawan',
            'admin_rokan_hilir',
            'admin_rokan_hulu',
            'admin_siak',
            'admin_kepulauan_meranti',
            'admin_dumai',
        ];

        // Gabungkan semua role
        $roles = array_merge($roles, $kabupatenRiau);

        // ====== BUAT SEMUA ROLE ======
        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        // ====== BUAT USER UNTUK ROLE UTAMA ======
        $admins = [
            ['name' => 'Super Admin',     'email' => 'superadmin@gmail.com',      'role' => 'admin_super'],
            ['name' => 'Kepala Seksi',  'email' => 'kepalaseksi@gmail.com',   'role' => 'admin_operator'],
            ['name' => 'Verifikator Admin', 'email' => 'verifikator@gmail.com', 'role' => 'admin_verifikator'],
            ['name' => 'Keuangan Admin',  'email' => 'keuangan@siyandi.local',   'role' => 'admin_keuangan'],
            ['name' => 'Kepala upt',   'email' => 'kepalaupt@gmail.com',    'role' => 'admin_manager'],
            ['name' => 'Admin UPT Sertifikasi', 'email' => 'uptsertifikasi@gmail.com', 'role' => 'admin_upt_sertifikasi'],
            ['name' => 'Admin Bidang Produksi','email' => 'bidangproduksi@siyandi.local','role' => 'admin_bidang_produksi'],
        ];

        // ====== BUAT USER UNTUK SETIAP KABUPATEN ======
        foreach ($kabupatenRiau as $kab) {
            $kabName = ucfirst(str_replace('_', ' ', str_replace('admin_', '', $kab))); 
            $admins[] = [
                'name' => 'Admin ' . $kabName,
                'email' => $kab . '@siyandi.local', 
                'role' => $kab,
            ];
        }

        // ====== BUAT USER DAN ASSIGN ROLE + HUBUNGKAN DENGAN KABUPATEN ======
        foreach ($admins as $a) {
            $user = User::firstOrCreate(
                ['email' => $a['email']],
                [
                    'name' => $a['name'],
                    'password' => Hash::make('password123'),
                ]
            );

            $user->assignRole($a['role']);

            // 🔗 Hubungkan user dengan kabupaten-nya (kalau termasuk admin kabupaten)
            if (str_starts_with($a['role'], 'admin_') && !in_array($a['role'], [
                'admin_super', 'admin_operator', 'admin_verifikator', 'admin_keuangan', 'admin_manager','admin_upt_sertifikasi',
            ])) {
                // Ambil nama kabupaten dari nama role (misal: admin_kampar → Kampar)
                $kabupatenName = ucfirst(str_replace('_', ' ', str_replace('admin_', '', $a['role'])));

                // Perbaiki nama-nama khusus agar cocok
                $kabupatenName = str_replace(
                    ['Indragiri hulu', 'Indragiri hilir', 'Kuantan singingi', 'Kepulauan meranti'],
                    ['Indragiri Hulu', 'Indragiri Hilir', 'Kuantan Singingi', 'Kepulauan Meranti'],
                    $kabupatenName
                );

                // Cari kabupaten di tabel kabupaten (tidak peka huruf besar)
                $kabupaten = Kabupaten::whereRaw('LOWER(nama_kabupaten) = ?', [strtolower($kabupatenName)])->first();

                if ($kabupaten) {
                    $user->kabupaten_id = $kabupaten->id;
                    $user->save();
                } else {
                    $this->command->warn("⚠️ Kabupaten '$kabupatenName' tidak ditemukan di tabel kabupaten!");
                }
            }
        }

        // ====== USER PEMOHON ======
        if (!User::where('email', 'pemohon@siyandi.local')->exists()) {
            $pemohon = User::create([
                'name' => 'User Pemohon',
                'email' => 'pemohon@siyandi.local',
                'password' => Hash::make('password123'),
            ]);
            $pemohon->assignRole('pemohon');
        }

        // ====== INFO DI TERMINAL ======
        $this->command->info('✅ Semua role dan user berhasil dibuat!');
        $this->command->info('📧 Password default: password123');
        $this->command->info('🔗 Admin kabupaten otomatis terhubung ke tabel kabupaten (cek warning jika ada).');
    }
}
