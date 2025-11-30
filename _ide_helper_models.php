<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $varietas_id
 * @property string|null $nomor_sk
 * @property string|null $tanggal
 * @property string|null $tipe_varietas
 * @property string|null $asal_usul
 * @property string|null $tipe_pertumbuhan
 * @property string|null $bentuk_tajuk
 * @property string|null $daun_ukuran
 * @property string|null $daun_warna_muda
 * @property string|null $daun_warna_tua
 * @property string|null $daun_bentuk_ujung
 * @property string|null $daun_tepi
 * @property string|null $daun_pangkal
 * @property string|null $daun_permukaan
 * @property string|null $daun_warna_pucuk
 * @property string|null $bunga_warna_mahkota
 * @property string|null $bunga_jumlah_mahkota
 * @property string|null $bunga_ukuran
 * @property string|null $buah_ukuran
 * @property string|null $buah_panjang
 * @property string|null $buah_diameter
 * @property string|null $buah_bobot
 * @property string|null $buah_bentuk
 * @property string|null $buah_warna_muda
 * @property string|null $buah_warna_masak
 * @property string|null $buah_ukuran_discus
 * @property string|null $biji_bentuk
 * @property string|null $biji_nisbah
 * @property string|null $biji_persen_normal
 * @property string|null $citarasa
 * @property string|null $potensi_produksi
 * @property string|null $penyakit_karat_daun
 * @property string|null $penggerek_buah_kopi
 * @property string|null $daerah_adaptasi
 * @property string|null $pemulia
 * @property string|null $peneliti
 * @property string|null $pemilik_varietas
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Varietas $varietas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereAsalUsul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBentukTajuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBijiBentuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBijiNisbah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBijiPersenNormal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBuahBentuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBuahBobot($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBuahDiameter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBuahPanjang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBuahUkuran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBuahUkuranDiscus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBuahWarnaMasak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBuahWarnaMuda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBungaJumlahMahkota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBungaUkuran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereBungaWarnaMahkota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereCitarasa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereDaerahAdaptasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereDaunBentukUjung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereDaunPangkal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereDaunPermukaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereDaunTepi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereDaunUkuran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereDaunWarnaMuda($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereDaunWarnaPucuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereDaunWarnaTua($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereNomorSk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas wherePemilikVarietas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas wherePemulia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas wherePeneliti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas wherePenggerekBuahKopi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas wherePenyakitKaratDaun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas wherePotensiProduksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereTipePertumbuhan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereTipeVarietas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeskripsiVarietas whereVarietasId($value)
 */
	class DeskripsiVarietas extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_tanaman
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PermohonanBenih> $permohonanBenih
 * @property-read int|null $permohonan_benih_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisTanaman newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisTanaman newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisTanaman query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisTanaman whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisTanaman whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisTanaman whereNamaTanaman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JenisTanaman whereUpdatedAt($value)
 */
	class JenisTanaman extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_kabupaten
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Varietas> $varietas
 * @property-read int|null $varietas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kabupaten newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kabupaten newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kabupaten query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kabupaten whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kabupaten whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kabupaten whereNamaKabupaten($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kabupaten whereUpdatedAt($value)
 */
	class Kabupaten extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $permohonan_id
 * @property int|null $admin_id
 * @property string $jenis_keterangan
 * @property string|null $isi_keterangan
 * @property \Illuminate\Support\Carbon|null $tanggal_keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $admin
 * @property-read \App\Models\PermohonanBenih $permohonan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan whereIsiKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan whereJenisKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan wherePermohonanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan whereTanggalKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeteranganPermohonan whereUpdatedAt($value)
 */
	class KeteranganPermohonan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $varietas_id
 * @property string|null $no_sk
 * @property string|null $tanggal_sk
 * @property int|null $nomor_pohon
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Varietas $varietas
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik whereNoSk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik whereNomorPohon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik whereTanggalSk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MateriGenetik whereVarietasId($value)
 */
	class MateriGenetik extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $tanggal_diajukan
 * @property int $user_id
 * @property string $nama
 * @property string $nik
 * @property string $alamat
 * @property string $no_telp
 * @property int|null $jenis_tanaman_id
 * @property int $jumlah_tanaman
 * @property string $luas_area
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $scan_surat_permohonan
 * @property string|null $scan_kk
 * @property string|null $scan_ktp
 * @property string|null $scan_surat_tanah
 * @property string|null $scan_surat_pengambilan
 * @property string $status
 * @property string $status_pengambilan
 * @property string|null $alasan_penolakan
 * @property string|null $tanggal_disetujui
 * @property string|null $tanggal_ditolak
 * @property string|null $tanggal_surat_keluar
 * @property string|null $tanggal_pengambilan
 * @property string|null $tanggal_selesai
 * @property string|null $tanggal_dibatalkan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\JenisTanaman|null $jenisTanaman
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\KeteranganPermohonan> $keterangan
 * @property-read int|null $keterangan_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereAlasanPenolakan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereJenisTanamanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereJumlahTanaman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereLuasArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereNik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereNoTelp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereScanKk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereScanKtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereScanSuratPengambilan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereScanSuratPermohonan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereScanSuratTanah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereStatusPengambilan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereTanggalDiajukan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereTanggalDibatalkan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereTanggalDisetujui($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereTanggalDitolak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereTanggalPengambilan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereTanggalSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereTanggalSuratKeluar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermohonanBenih whereUserId($value)
 */
	class PermohonanBenih extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_tanaman
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Varietas> $varietas
 * @property-read int|null $varietas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tanaman newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tanaman newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tanaman query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tanaman whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tanaman whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tanaman whereNamaTanaman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tanaman whereUpdatedAt($value)
 */
	class Tanaman extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $kabupaten_id
 * @property-read \App\Models\Kabupaten|null $kabupaten
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereKabupatenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tanaman_id
 * @property int|null $kabupaten_id
 * @property int|null $user_id
 * @property string|null $nomor_tanggal_sk
 * @property string $nama_varietas
 * @property string|null $jenis_benih
 * @property string|null $pemilik_varietas
 * @property string|null $jumlah_materi_genetik
 * @property string|null $keterangan
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DeskripsiVarietas|null $deskripsi
 * @property-read \App\Models\Kabupaten|null $kabupaten
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MateriGenetik> $materiGenetik
 * @property-read int|null $materi_genetik_count
 * @property-read \App\Models\Tanaman $tanaman
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereJenisBenih($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereJumlahMateriGenetik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereKabupatenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereNamaVarietas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereNomorTanggalSk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas wherePemilikVarietas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereTanamanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Varietas whereUserId($value)
 */
	class Varietas extends \Eloquent {}
}

