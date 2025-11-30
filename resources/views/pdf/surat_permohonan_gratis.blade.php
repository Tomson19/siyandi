<p style="text-align:right;">………………….., {{ now()->format('d F Y') }}</p>

<p>Kepada Yth.<br>
Bapak Kepala Dinas Perkebunan Provinsi Riau<br>
Cq. Kepala UPT Produksi Benih Tanaman Perkebunan<br>
di-<br>
&nbsp;&nbsp;&nbsp;&nbsp;Pekanbaru</p>

<p><strong>Perihal : Permohonan Permintaan Benih Tanaman Perkebunan</strong></p>

<p>Dengan hormat,</p>

<p>Saya yang bertandatangan di bawah ini :</p>

<table style="width:100%;">
    <tr><td style="width:160px;">Nama</td><td>: {{ strtoupper($permohonan->nama) }}</td></tr>
    <tr><td>NIK</td><td>: {{ $permohonan->nik }}</td></tr>
    <tr><td>Alamat</td><td>: {{ $permohonan->alamat }}</td></tr>
    <tr><td>No. Telepon / HP</td><td>: {{ $permohonan->no_telp }}</td></tr>
</table>

<p>dengan ini mengajukan permohonan <strong>permintaan benih</strong> :</p>

<table style="width:100%;">
    <tr><td style="width:160px;">Jenis Tanaman</td><td>: {{ $permohonan->jenisTanaman->nama_tanaman ?? '-' }}</td></tr>
    <tr><td>Jenis Benih</td><td>: {{ ucfirst($permohonan->jenis_benih ?? '-') }}</td></tr>
    <tr><td>Jumlah</td>
        <td>: {{ $permohonan->jumlah_tanaman }} 
            {{ $permohonan->jenis_benih === 'kecambah' ? 'butir' : 'batang' }}</td>
    </tr>
    <tr><td>Luas Areal</td><td>: {{ $permohonan->luas_area }} Ha</td></tr>
    <tr><td>Koordinat Lokasi</td><td></td></tr>
    <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Lintang</td><td>: {{ $permohonan->latitude ?? '-' }}</td></tr>
    <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Bujur</td><td>: {{ $permohonan->longitude ?? '-' }}</td></tr>
</table>

<p>Sebagai bahan pertimbangan, bersama ini saya lampirkan dokumen pendukung :</p>
<ol>
    <li>Fotokopi Kartu Tanda Penduduk (KTP);</li>
    <li>Fotokopi Kartu Keluarga (KK);</li>
    <li>Fotokopi surat kepemilikan / penguasaan lahan;</li>
    <li>Surat pernyataan kesanggupan pemeliharaan benih tanaman.</li>
</ol>

<p>Demikian surat permohonan ini saya sampaikan, atas perhatian dan bantuannya diucapkan terima kasih.</p>

<br><br>
<table style="width:100%; margin-top:20px;">
    <tr>
        <td style="width:50%;"></td>
        <td style="width:50%; text-align:center;">
            <p style="text-align:center; margin-bottom:17px;">
    Hormat saya,
</p>
            <div style="margin-bottom:14px;">
                
                <span style="display:inline-block; width:100px;"></span>
            </div>
            <strong><u>{{ strtoupper($permohonan->nama) }}</u></strong><br>
        </td>
    </tr>
</table>
