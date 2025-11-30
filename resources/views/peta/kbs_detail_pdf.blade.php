<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Kebun Benih Sumber</title>
    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
        }

        th {
            background: #e0e0e0;
        }

        .text-center {
            text-align: center;
        }

        .mb-2 {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <h3 class="mb-2">Detail Kebun Benih Sumber</h3>
    <p class="mb-2">
        Komoditas : {{ $kbs->tanaman->nama_tanaman ?? '-' }} <br>
        Varietas : {{ $kbs->nama_varietas ?? '-' }} <br>
        Kab/Kota : {{ $kbs->kabupaten->nama_kabupaten ?? '-' }} <br>
        Nomor SK : {{ $kbs->nomor_sk ?? '-' }} <br>
        Tanggal SK: {{ $kbs->tanggal_sk ?? '-' }} <br>
        Dicetak : {{ $generatedAt->format('d-m-Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width:4%;">No</th>
                <th rowspan="2" style="width:10%;">Komoditas</th>
                <th rowspan="2" style="width:14%;">Varietas</th>
                <th rowspan="2" style="width:15%;">No & Tgl SK</th>
                <th colspan="3" style="width:18%;">Lokasi</th>
                <th rowspan="2" style="width:6%;">Jml PIT</th>
                <th colspan="4" style="width:18%;">Pemilik</th>
                <th colspan="4" style="width:18%;">Pohon & Koordinat</th>
            </tr>
            <tr>
                <th>Kecamatan</th>
                <th>Desa</th>
                <th>Tahun Tanam</th>

                <th>No</th>
                <th>Nama Pemilik</th>
                <th>Luas (Ha)</th>
                <th>Jml Pohon Induk</th>

                <th>No</th>
                <th>No Pohon Induk</th>
                <th>Latitude</th>
                <th>Longitude</th>
            </tr>
        </thead>
        <tbody>
            @php
                $kbsRowShown = false;
                $rowNo = 1;
                $totalLuas = 0;
                $totalPohonInduk = 0;
            @endphp

            @forelse($kbs->pemilik as $p)
                @php
                    // hitung total per pemilik (sekali per pemilik)
                    $totalLuas += (float) ($p->luas_ha ?? 0);
                    $totalPohonInduk += (int) ($p->jumlah_pohon_induk ?? 0);

                    $pemilikRowShown = false;
                    $pohonCount = $p->pohon->count();
                @endphp

                @if ($pohonCount)
                    @foreach ($p->pohon as $ph)
                        <tr>
                            {{-- No (global) --}}
                            <td class="text-center">
                                @if (!$kbsRowShown)
                                    {{ $rowNo }}
                                @endif
                            </td>

                            {{-- Komoditas --}}
                            <td>
                                @if (!$kbsRowShown)
                                    {{ $kbs->tanaman->nama_tanaman ?? '-' }}
                                @endif
                            </td>

                            {{-- Varietas --}}
                            <td>
                                @if (!$kbsRowShown)
                                    {{ $kbs->nama_varietas ?? '-' }}
                                @endif
                            </td>

                            {{-- No & Tgl SK --}}
                            <td>
                                @if (!$kbsRowShown)
                                    <div><strong>{{ $kbs->nomor_sk ?? '-' }}</strong></div>
                                    <div>{{ $kbs->tanggal_sk ?? '-' }}</div>
                                @endif
                            </td>

                            {{-- Lokasi --}}
                            <td>
                                @if (!$pemilikRowShown)
                                    {{ $p->kecamatan ?? '-' }}
                                @endif
                            </td>
                            <td>
                                @if (!$pemilikRowShown)
                                    {{ $p->desa ?? '-' }}
                                @endif
                            </td>
                            <td>
                                @if (!$pemilikRowShown)
                                    {{ $p->tahun_tanam ?? '-' }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if (!$pemilikRowShown)
                                    {{ $p->jumlah_pit ?? '-' }}
                                @endif
                            </td>

                            {{-- Pemilik --}}
                            <td class="text-center">
                                @if (!$pemilikRowShown)
                                    {{ $p->no_pemilik ?? '-' }}
                                @endif
                            </td>
                            <td>
                                @if (!$pemilikRowShown)
                                    {{ $p->nama_pemilik ?? '-' }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if (!$pemilikRowShown)
                                    {{ $p->luas_ha ?? '-' }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if (!$pemilikRowShown)
                                    {{ $p->jumlah_pohon_induk ?? '-' }}
                                @endif
                            </td>

                            {{-- Pohon & koordinat --}}
                            <td class="text-center">{{ $ph->no_pohon ?? '-' }}</td>
                            <td class="text-center">{{ $ph->nomor_pohon_induk ?? '-' }}</td>
                            <td class="text-center">{{ $ph->latitude ?? '-' }}</td>
                            <td class="text-center">{{ $ph->longitude ?? '-' }}</td>
                        </tr>

                        @php
                            $kbsRowShown = true;
                            $pemilikRowShown = true;
                        @endphp
                    @endforeach

                    @php
                        $rowNo++;
                    @endphp
                @else
                    {{-- Pemilik tanpa pohon --}}
                    <tr>
                        <td class="text-center">
                            @if (!$kbsRowShown)
                                {{ $rowNo }}
                            @endif
                        </td>
                        <td>
                            @if (!$kbsRowShown)
                                {{ $kbs->tanaman->nama_tanaman ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @if (!$kbsRowShown)
                                {{ $kbs->nama_varietas ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @if (!$kbsRowShown)
                                <div><strong>{{ $kbs->nomor_sk ?? '-' }}</strong></div>
                                <div>{{ $kbs->tanggal_sk ?? '-' }}</div>
                            @endif
                        </td>

                        <td>{{ $p->kecamatan ?? '-' }}</td>
                        <td>{{ $p->desa ?? '-' }}</td>
                        <td>{{ $p->tahun_tanam ?? '-' }}</td>
                        <td class="text-center">{{ $p->jumlah_pit ?? '-' }}</td>

                        <td class="text-center">{{ $p->no_pemilik ?? '-' }}</td>
                        <td>{{ $p->nama_pemilik ?? '-' }}</td>
                        <td class="text-center">{{ $p->luas_ha ?? '-' }}</td>
                        <td class="text-center">{{ $p->jumlah_pohon_induk ?? '-' }}</td>

                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                    </tr>

                    @php
                        $kbsRowShown = true;
                        $rowNo++;
                    @endphp
                @endif
            @empty
                <tr>
                    <td colspan="16" class="text-center">
                        Tidak ada data pemilik / pohon untuk kebun benih sumber ini.
                    </td>
                </tr>
            @endforelse
            @if ($totalLuas > 0 || $totalPohonInduk > 0)
                <tr>
                    {{-- 9 kolom pertama kosong --}}
                    <td colspan="9"></td>

                    {{-- kolom "Nama Pemilik" --}}
                    <td class="text-right"><strong>Jumlah</strong></td>

                    {{-- Luas (Ha) --}}
                    <td class="text-center">
                        <strong>{{ rtrim(rtrim(number_format($totalLuas, 2, ',', '.'), '0'), ',') }}</strong>
                    </td>

                    {{-- Jumlah Pohon Induk --}}
                    <td class="text-center">
                        <strong>{{ $totalPohonInduk }}</strong>
                    </td>

                    {{-- 4 kolom terakhir (No, No Pohon Induk, Lat, Long) --}}
                    <td colspan="4"></td>
                </tr>
            @endif
        </tbody>

        </tbody>
    </table>
</body>

</html>
