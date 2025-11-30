<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PermohonanBenih extends Model
{
    use HasFactory;

    protected $table = 'permohonan_benih';

    protected $fillable = [
        'user_id',
        'benih_id',

        // Data pemohon
        'nama',
        'nik',
        'alamat',
        'no_telp',

        // Tanaman & benih
        'jenis_tanaman_id',
        'jenis_benih',
        'tipe_pembayaran',

        // Detail permohonan
        'jumlah_tanaman',
        'jumlah_disetujui',
        'luas_area',
        'latitude',
        'longitude',

        // Dokumen permohonan
        'scan_surat_permohonan',
        'scan_surat_pernyataan',
        'scan_kk',
        'scan_ktp',
        'scan_surat_tanah',

        // Surat & dokumen admin
        'scan_surat_pengambilan',

        // Pembayaran
        'bukti_pembayaran',
        'pesan_pemohon_pembayaran',
        'status_pembayaran',
        'catatan_pembayaran_admin',
        'tanggal_verifikasi_pembayaran',
        'qris_image',
        'nominal_pembayaran',

        // Bukti pengambilan & tanam
        'bukti_pengambilan',
        'bukti_tanam',

        // Status utama & pengambilan
        'status',
        'status_pengambilan',

        // Tanggal-tanggal utama
        'tanggal_diajukan',
        'tanggal_disetujui',
        'tanggal_ditolak',
        'tanggal_surat_keluar',
        'tanggal_pengambilan',
        'tanggal_selesai',
        'tanggal_dibatalkan',

        // Deadline pembayaran & tanam
        'batas_pembayaran',
        'tanggal_tanam_deadline',
        'tanggal_tanam',

        // Keterangan tambahan
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_diajukan'              => 'date',
        'tanggal_disetujui'             => 'date',
        'tanggal_ditolak'               => 'date',
        'tanggal_surat_keluar'          => 'date',
        'tanggal_pengambilan'           => 'date',
        'tanggal_selesai'               => 'date',
        'tanggal_dibatalkan'            => 'date',
        'batas_pembayaran'              => 'date',
        'tanggal_tanam_deadline'        => 'date',
        'tanggal_tanam'                 => 'date',
        'tanggal_verifikasi_pembayaran' => 'datetime',
        'created_at'                    => 'datetime',
        'updated_at'                    => 'datetime',
    ];

    // =============================
    // 🔗 RELATIONSHIPS
    // =============================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisTanaman()
    {
        return $this->belongsTo(JenisTanaman::class, 'jenis_tanaman_id');
    }

    public function keterangan()
    {
        return $this->hasMany(KeteranganPermohonan::class, 'permohonan_id');
    }

    public function benih()
    {
        return $this->belongsTo(Benih::class, 'benih_id');
    }

    // =============================
    // 🧠 ACCESSORS / HELPERS
    // =============================

    public function getTanggalDiajukanFormatAttribute()
    {
        return $this->tanggal_diajukan
            ? Carbon::parse($this->tanggal_diajukan)->translatedFormat('d F Y')
            : '-';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'Menunggu Dokumen'    => '<span class="badge bg-secondary">Menunggu Dokumen</span>',
            'Sedang Diverifikasi' => '<span class="badge bg-info text-dark">Sedang Diverifikasi</span>',
            'Perbaikan'           => '<span class="badge bg-warning text-dark">Perlu Perbaikan</span>',
            'Disetujui'           => '<span class="badge bg-success">Disetujui</span>',
            'Ditolak'             => '<span class="badge bg-danger">Ditolak</span>',
            'Dibatalkan'          => '<span class="badge bg-dark">Dibatalkan</span>',
            default               => '<span class="badge bg-light text-dark">Tidak Diketahui</span>',
        };
    }

    /**
     * (Opsional) Label status pembayaran untuk dipakai di Blade:
     * {!! $permohonan->status_pembayaran_label !!}
     */
    public function getStatusPembayaranLabelAttribute()
    {
        return match ($this->status_pembayaran) {
            'Menunggu'             => '<span class="badge bg-secondary">Menunggu Pembayaran</span>',
            'Menunggu Verifikasi'  => '<span class="badge bg-info text-dark">Menunggu Verifikasi</span>',
            'Berhasil'             => '<span class="badge bg-success">Pembayaran Berhasil</span>',
            'Gagal'                => '<span class="badge bg-danger">Pembayaran Gagal</span>',
            default                => '<span class="badge bg-light text-dark">-</span>',
        };
    }
}
