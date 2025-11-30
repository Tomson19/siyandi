<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kebun Benih Sumber</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background: #e0e0e0; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .mb-2 { margin-bottom: 8px; }
    </style>
</head>
<body>
    <h3 class="mb-2">Data Kebun Benih Sumber</h3>
    <p class="mb-2">
        Dicetak pada: {{ $generatedAt->format('d-m-Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th style="width:4%;">No</th>
                <th style="width:12%;">Komoditas</th>
                <th style="width:20%;">Nomor & Tanggal SK</th>
                <th style="width:22%;">Varietas</th>
                <th style="width:18%;">Kabupaten</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kbsList as $index => $k)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $k->tanaman->nama_tanaman ?? '-' }}</td>
                    <td class="text-center">
                        <div><strong>{{ $k->nomor_sk ?? '-' }}</strong></div>
                        <div>{{ $k->tanggal_sk ?? '-' }}</div>
                    </td>
                    <td class="text-center">{{ $k->nama_varietas }}</td>
                    <td class="text-center">{{ $k->kabupaten->nama_kabupaten ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        Tidak ada data untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
