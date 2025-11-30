<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeskripsiVarietas;
use App\Models\Varietas;

class DeskripsiVarietasSeeder extends Seeder
{
    public function run(): void
    {
        /* ----------------------------------------------------------------
         * ☕ LIBEROID MERANTI 1
         * ---------------------------------------------------------------- */
        $v1 = Varietas::where('nama_varietas', 'Liberoid Meranti 1')->first();

        if ($v1) {
            DeskripsiVarietas::firstOrCreate(
                ['varietas_id' => $v1->id],
                [
                    'nomor_sk' => 'No. 69/Kpts/KB.020/1/2016',
                    'tanggal' => '26 Januari 2016',
                    'tipe_varietas' => 'Komposit bersari bebas',
                    'asal_usul' => 'Berasal dari Batu Pahat, Malaysia tahun 1942 dan merupakan hasil pemilihan pada populasi kopi Liberoid di Desa Kedaburapat, Kecamatan Rangsang Pesisir, Kabupaten Kepulauan Meranti, Provinsi Riau.',

                    // Morfologi
                    'tipe_pertumbuhan' => 'Berbentuk pohon dengan habitus tinggi dan diameter tajuk berkisar antara 3–4,5 m, jika tidak dipangkas tinggi tanaman dapat mencapai 4–5,5 m.',
                    'bentuk_tajuk' => 'Piramid tumpul dan payung',

                    // Daun
                    'daun_ukuran' => 'Kecil – besar',
                    'daun_warna_muda' => 'Hijau muda – hijau',
                    'daun_warna_tua' => 'Hijau kelam/tua',
                    'daun_bentuk_ujung' => 'Tumpul dan runcing',
                    'daun_tepi' => 'Rata',
                    'daun_pangkal' => 'Meruncing',
                    'daun_permukaan' => 'Bergelombang/rata',
                    'daun_warna_pucuk' => 'Hijau, hijau kecokelatan, dan kecokelatan',

                    // Bunga
                    'bunga_warna_mahkota' => 'Putih bergaris keunguan',
                    'bunga_jumlah_mahkota' => '7–8',
                    'bunga_ukuran' => 'Besar',

                    // Buah
                    'buah_ukuran' => 'Kecil – sedang',
                    'buah_panjang' => '1.92 ± 1.09',
                    'buah_diameter' => '1.77 ± 1.11',
                    'buah_bobot' => '3.65 ± 0.50',
                    'buah_bentuk' => 'Lonjong, bulat lonjong dan bulat telur',
                    'buah_warna_muda' => 'Hijau, hijau kekuningan',
                    'buah_warna_masak' => 'Kuning oranye dan kemerahan',
                    'buah_ukuran_discus' => 'Kecil, sedang, dan besar',

                    // Biji
                    'biji_bentuk' => 'Oval',
                    'biji_nisbah' => '10.91',
                    'biji_persen_normal' => '86.67 – 92 (rata-rata 90.00)',

                    // Mutu & Produksi
                    'citarasa' => 'Nilai kesukaan (preferensi) berkisar antara 80–84.25 atau rata-rata mencapai 82.28, mutu citarasa "excellent".',
                    'potensi_produksi' => 'Rata-rata 2.37 kg kopi biji/pohon/tahun atau setara dengan 1.69 ton biji kopi/ha dengan jumlah populasi 714 tanaman.',

                    // Ketahanan & Adaptasi
                    'penyakit_karat_daun' => 'Tahan',
                    'penggerek_buah_kopi' => 'Agak tahan – tahan',
                    'daerah_adaptasi' => 'Lahan gambut, tipe iklim A.',

                    // Pemuliaan
                    'pemulia' => 'Budi Martono, Rubiyo, Rudi T. Setiyono, dan Laba Udarno',
                    'peneliti' => 'Risfaheri, Usman Daras, Rita Harni, Bedy Sudjarmoko, Efi Taufiq, Eni Randriani, Maman Herman, dan Nindyo Adhi Wibowo',
                    'pemilik_varietas' => 'Pemerintah Daerah Kabupaten Kepulauan Meranti, Provinsi Riau',
                ]
            );
        }

        /* ----------------------------------------------------------------
         * ☕ LIBEROID MERANTI 2
         * ---------------------------------------------------------------- */
        $v2 = Varietas::where('nama_varietas', 'Liberoid Meranti 2')->first();

        if ($v2) {
            DeskripsiVarietas::firstOrCreate(
                ['varietas_id' => $v2->id],
                [
                    'nomor_sk' => 'No. 70/Kpts/KB.020/1/2016',
                    'tanggal' => '26 Januari 2016',
                    'tipe_varietas' => 'Komposit bersari bebas',
                    'asal_usul' => 'Berasal dari Batu Pahat Malaysia pada tahun 1942 dan merupakan hasil pemilihan pada populasi kopi Liberoid di Desa Kedaburapat Kecamatan Rangsang Pesisir Kabupaten Kepulauan Meranti Provinsi Riau.',

                    // Morfologi
                    'tipe_pertumbuhan' => 'Berbentuk pohon dengan habitus tinggi dan diameter tajuk berkisar antara 3,0–4,0 m, jika tidak dipangkas tinggi tanaman dapat mencapai 3,5–5 m.',
                    'bentuk_tajuk' => 'Piramid tumpul dan payung',

                    // Daun
                    'daun_ukuran' => 'Sedang – besar',
                    'daun_warna_muda' => 'Hijau muda – hijau',
                    'daun_warna_tua' => 'Hijau tua',
                    'daun_bentuk_ujung' => 'Runcing',
                    'daun_tepi' => 'Rata',
                    'daun_pangkal' => 'Meruncing',
                    'daun_permukaan' => 'Bergelombang/rata',
                    'daun_warna_pucuk' => 'Hijau kecokelatan',

                    // Bunga
                    'bunga_warna_mahkota' => 'Putih bergaris keunguan',
                    'bunga_jumlah_mahkota' => '7–8',
                    'bunga_ukuran' => 'Besar',

                    // Buah
                    'buah_ukuran' => 'Besar',
                    'buah_panjang' => '2.59 ± 2.57',
                    'buah_diameter' => '1.96 ± 1.23',
                    'buah_bobot' => '5.86 ± 0.98',
                    'buah_bentuk' => 'Lonjong – bulat lonjong',
                    'buah_warna_muda' => 'Hijau',
                    'buah_warna_masak' => 'Kemerahan',
                    'buah_ukuran_discus' => 'Kecil, sedang, dan rata',

                    // Biji
                    'biji_bentuk' => 'Oval',
                    'biji_nisbah' => '8.71',
                    'biji_persen_normal' => '84 – 96 (rata-rata 88.25)',

                    // Mutu & Produksi
                    'citarasa' => 'Nilai kesukaan (preferensi) 84.50, mutu citarasa "excellent".',
                    'potensi_produksi' => 'Rata-rata 2.78 kg kopi biji/pohon/tahun atau setara dengan 1.98 ton biji kopi/ha dengan jumlah populasi 714 tanaman.',

                    // Ketahanan & Adaptasi
                    'penyakit_karat_daun' => 'Tahan',
                    'penggerek_buah_kopi' => 'Tahan',
                    'daerah_adaptasi' => 'Lahan gambut, tipe iklim A.',

                    // Pemuliaan
                    'pemulia' => 'Budi Martono, Rubiyo, Rudi T. Setiyono, dan Laba Udarno',
                    'peneliti' => 'Risfaheri, Usman Daras, Rita Harni, Bedy Sudjarmoko, dan Abdul Musi Hasibuan',
                    'pemilik_varietas' => 'Pemerintah Daerah Kabupaten Kepulauan Meranti, Provinsi Riau',
                ]
            );
        }

        $this->command->info("✅ Deskripsi varietas Liberoid Meranti 1 & 2 berhasil ditambahkan ke database.");
    }
}
