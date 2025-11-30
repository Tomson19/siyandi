@php
use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
    </style>
</head>
<body>

<p style="text-align:center; font-weight:bold; text-decoration:underline; font-size:14pt;">
    SURAT PERNYATAAN PETANI
</p>

<br>

<p>Saya yang bertandatangan di bawah ini :</p>

<table style="line-height:1.6;">
    <tr><td style="width:150px;">Nama</td><td>: {{ strtoupper($permohonan->nama) }}</td></tr>
    <tr><td>NIK</td><td>: {{ $permohonan->nik }}</td></tr>
    <tr><td>Alamat</td><td>: {{ $permohonan->alamat }}</td></tr>
    <tr><td>No. HP</td><td>: {{ $permohonan->no_telp }}</td></tr>
</table>

<br>

<p>dengan ini menyatakan bahwa:</p>

<ol style="text-align:justify;">
    <li>Data/dokumen kelengkapan persyaratan permohonan benih yang disampaikan adalah benar tanpa adanya rekayasa ataupun manipulasi;</li>
    <li>Lahan yang akan ditanami Benih Tanaman {{ $permohonan->jenisTanaman->nama_tanaman ?? '-' }} adalah benar milik saya sendiri sebagaimana surat kepemilikan/penguasaan tanah terlampir;</li>
    <li>Lahan yang akan ditanami benih dimaksud tidak dalam status konflik dan sengketa dengan pihak manapun;</li>
    <li>Benih {{ $permohonan->jenis_benih ?? 'kecambah/siap tanam' }} untuk tanaman {{ $permohonan->jenisTanaman->nama_tanaman ?? '-' }}
        yang dimohon sebanyak {{ $permohonan->jumlah_tanaman }}
        {{ $permohonan->jenis_benih === 'kecambah' ? 'butir' : 'batang' }},
        benar-benar akan ditanam pada lahan kebun sebagaimana dimaksud pada point (2) di atas dan tidak akan dialihkan dan/atau tidak akan diperjualbelikan kepada pihak lain;</li>
    <li>Bersedia dan sanggup mengikuti semua petunjuk budidaya yang disampaikan oleh petugas dan akan memelihara tanaman tersebut.</li>
</ol>

<p style="text-align:justify;">
Demikian surat pernyataan ini saya buat dengan kesadaran penuh tanpa adanya tekanan ataupun paksaan dari pihak manapun.
Apabila di kemudian hari terbukti pernyataan ini tidak benar, maka saya bersedia mempertanggungjawabkannya sesuai ketentuan yang berlaku.
</p>

<br><br>
<p style="text-align:right;">
    …………….., {{ Carbon::parse(now())->translatedFormat('d F Y') }}
</p>



<table style="width:100%; margin-top:20px;">
    <tr>
        <td style="width:50%;"></td>
        <td style="width:50%; text-align:center;">
            <p style="text-align:center; margin-bottom:17px;">
    Yang membuat pernyataan,
</p>
            <div style="margin-bottom:14px;">
                <div style="margin-left:-40px; border:1px dashed #999; width:90px; height:50px; display:inline-block; vertical-align:middle;">
                    <small><em>Materai<br>Rp 10.000</em></small>
                </div>
                <span style="display:inline-block; width:100px;"></span>
            </div>
            <strong><u>{{ strtoupper($permohonan->nama) }}</u></strong><br>
        </td>
    </tr>
</table>

</body>
</html>
