<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        /**
         * MASTER STOK BENIH
         * Contoh data:
         * - Kopi – Biji – Gratis – stok 100
         * - Kopi – Biji – Berbayar – stok 200
         */
        Schema::create('benih', function (Blueprint $table) {
            $table->id();

            // relasi ke jenis_tanaman
            $table->foreignId('jenis_tanaman_id')
                ->constrained('jenis_tanaman')
                ->onDelete('cascade');

            $table->enum('jenis_benih', ['Biji', 'Siap Tanam']);
            $table->enum('tipe_pembayaran', ['Gratis', 'Berbayar']);
            $table->integer('stok')->default(0);
            $table->unsignedBigInteger('harga')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });


        /**
         * PENGATURAN QRIS GLOBAL
         * Hanya admin utama yang boleh ganti.
         */
        Schema::create('pengaturan_qris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_qris')->nullable(); // misal: QRIS UPT A
            $table->string('gambar_qris');           // path gambar QRIS
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        /**
         * TABEL UTAMA: permohonan_benih
         */
        Schema::create('permohonan_benih', function (Blueprint $table) {
            $table->id();

            // Relasi ke user pemohon
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Relasi ke master stok benih (opsional tapi disarankan diisi)
            $table->foreignId('benih_id')
                ->nullable()
                ->constrained('benih')
                ->nullOnDelete();

            // Data pemohon
            $table->string('nama');
            $table->string('nik', 20);
            $table->text('alamat');
            $table->string('no_telp', 20);

            // Informasi tanaman (jika masih pakai tabel jenis_tanaman)
            $table->foreignId('jenis_tanaman_id')
                ->nullable()
                ->constrained('jenis_tanaman')
                ->nullOnDelete();

            // Jenis & tipe benih di level permohonan (redundan tapi memudahkan filter)
            $table->enum('jenis_benih', ['Biji', 'Siap Tanam'])->nullable();
            $table->enum('tipe_pembayaran', ['Gratis', 'Berbayar'])
                ->default('Gratis');
            $table->unsignedBigInteger('nominal_pembayaran')->nullable(); // harga total yang harus dibayar pemohon


            // Detail permohonan
            $table->integer('jumlah_tanaman');          // jumlah diajukan
            $table->integer('jumlah_disetujui')->nullable(); // jumlah final yang disetujui admin
            $table->decimal('luas_area', 8, 2)->nullable();

            // Lokasi
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            // Dokumen permohonan
            $table->string('scan_surat_permohonan')->nullable();
            $table->string('scan_surat_pernyataan')->nullable();
            $table->string('scan_kk')->nullable();
            $table->string('scan_ktp')->nullable();
            $table->string('scan_surat_tanah')->nullable();

            // Surat pengambilan (diupload admin)
            $table->string('scan_surat_pengambilan')->nullable();

            // Bukti pembayaran & komunikasi pembayaran
            $table->string('bukti_pembayaran')->nullable(); // upload bukti transfer
            $table->text('pesan_pemohon_pembayaran')->nullable(); // "sudah saya tf ya kak"
            $table->enum('status_pembayaran', ['Menunggu', 'Menunggu Verifikasi', 'Berhasil', 'Gagal'])
                ->nullable(); // null untuk permohonan Gratis
            $table->text('catatan_pembayaran_admin')->nullable(); // alasan berhasil/gagal
            $table->dateTime('tanggal_verifikasi_pembayaran')->nullable();

            // Simpan snapshot gambar QRIS yang dipakai (optional, kalau mau)
            $table->string('qris_image')->nullable();

            // Bukti pengambilan & bukti tanam
            $table->string('bukti_pengambilan')->nullable(); // foto saat ambil bibit
            $table->string('bukti_tanam')->nullable();       // foto bukti tanam di lahan

            /**
             * STATUS PERMOHONAN
             * - Menunggu Dokumen       : pemohon belum/lengkapin dokumen
             * - Sedang Diverifikasi    : admin lagi cek
             * - Perbaikan              : dokumen/data harus diperbaiki pemohon
             * - Disetujui              : permohonan OK, lanjut pembayaran / pengambilan
             * - Ditolak                : permohonan ditolak
             * - Dibatalkan             : misal lewat batas pembayaran 7 hari
             */
            $table->enum('status', [
                'Menunggu Dokumen',
                'Sedang Diverifikasi',
                'Perbaikan',
                'Disetujui',
                'Ditolak',
                'Dibatalkan',
            ])->default('Menunggu Dokumen');

            /**
             * STATUS PENGAMBILAN
             * - Belum Diambil
             * - Selesai       : saat ini stok akan dikurangi
             * - Dibatalkan    : tidak jadi diambil
             */
            $table->enum('status_pengambilan', ['Belum Diambil', 'Selesai', 'Dibatalkan'])
                ->default('Belum Diambil');

            // Tanggal-tanggal penting
            $table->date('tanggal_diajukan')->nullable();
            $table->date('tanggal_disetujui')->nullable();
            $table->date('tanggal_ditolak')->nullable();
            $table->date('tanggal_surat_keluar')->nullable();
            $table->date('tanggal_pengambilan')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->date('tanggal_dibatalkan')->nullable();

            // Batas pembayaran & bukti tanam
            $table->date('batas_pembayaran')->nullable();       // tanggal_disetujui + 7 hari (jika Berbayar)
            $table->date('tanggal_tanam_deadline')->nullable(); // tanggal_pengambilan + 3 bulan
            $table->date('tanggal_tanam')->nullable();          // saat pemohon upload bukti tanam

            // Keterangan tambahan
            $table->text('alasan_penolakan')->nullable();

            $table->timestamps();
        });

        /**
         * TABEL keterangan_permohonan
         * untuk mencatat riwayat dan catatan verifikasi admin
         */
        Schema::create('keterangan_permohonan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')
                ->constrained('permohonan_benih')
                ->onDelete('cascade');
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('jenis_keterangan', ['Perlu Diperbaiki', 'Sedang Diverifikasi', 'Disetujui', 'Ditolak']);
            $table->text('isi_keterangan')->nullable();
            $table->date('tanggal_keterangan')->nullable(); // null = sementara
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keterangan_permohonan');
        Schema::dropIfExists('permohonan_benih');
        Schema::dropIfExists('pengaturan_qris');
        Schema::dropIfExists('benih');
    }
};
