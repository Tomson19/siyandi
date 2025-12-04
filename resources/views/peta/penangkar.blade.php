@extends('layouts.halamanutama')

@section('title', 'Peta Penangkar Benih')

@section('content')
    <div class="container-fluid mt-3">

        {{-- STYLE KHUSUS HALAMAN INI --}}
        <style>
            /* Kecilkan font dan rapatkan padding tabel */
            #penangkarTable {
                font-size: 11px;
            }

            #penangkarTable th,
            #penangkarTable td {
                padding: 0.20rem 0.30rem;
            }

            .card-header {
                font-size: 0.9rem;
            }
        </style>

        <div class="row">
            {{-- 🗺️ MAP SECTION --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white fw-bold">
                        Peta Persebaran Penangkar Benih
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 400px; border-radius: 6px;"></div>
                    </div>
                </div>
            </div>

            {{-- 📋 FILTER + LIST PENANGKAR --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Data Penangkar Benih</span>
                        <div class="d-flex gap-1">
                            <a href="{{ route('peta.penangkar.export.excel', [
                                'tanaman_id' => request('tanaman_id'),
                                'kabupaten_id' => request('kabupaten_id'),
                            ]) }}"
                                class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </a>

                            <a href="{{ route('peta.penangkar.export.pdf', [
                                'tanaman_id' => request('tanaman_id'),
                                'kabupaten_id' => request('kabupaten_id'),
                            ]) }}"
                                class="btn btn-danger btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </a>
                        </div>
                    </div>


                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">

                        {{-- 🔍 FILTER FORM --}}
                        <form method="GET" action="{{ route('peta.penangkar.index') }}" id="filterForm" class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Komoditas</label>
                                <select name="tanaman_id" class="form-select"
                                    onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Semua Komoditas --</option>
                                    @foreach ($tanamanList as $t)
                                        <option value="{{ $t->id }}"
                                            {{ request('tanaman_id') == $t->id ? 'selected' : '' }}>
                                            {{ $t->nama_tanaman }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Kabupaten</label>
                                <select name="kabupaten_id" class="form-select"
                                    onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Semua Kabupaten --</option>
                                    @foreach ($kabupatenList as $k)
                                        <option value="{{ $k->id }}"
                                            {{ request('kabupaten_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kabupaten }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        {{-- 📊 TABEL PENANGKAR --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle shadow-sm" id="penangkarTable"
                                style="border:1px solid #999; text-align:center;">
                                <thead class="table-dark text-center">
                                    <tr class="align-middle">
                                        <th style="width:4%;">No.</th>
                                        <th style="width:10%;">Komoditas</th>
                                        <th style="width:18%;">Nama Produsen Benih<br>Perorangan/Perusahaan</th>
                                        <th style="width:14%;">NIB &amp;<br>Tanggal</th>
                                        <th style="width:16%;">Sertifikat Sandar / Izin <br>Usaha Prod. Benih Nomor &amp; Tanggal</th>
                                        <th style="width:8%;">Luas<br>Areal (Ha)</th>
                                        <th style="width:8%;">Jumlah Sertifikasi Benih<br>Tahun Berjalan(Batang)</th>
                                        <th style="width:12%;">Alamat</th>
                                        <th style="width:10%;">Desa/Kelurahan</th>
                                        <th style="width:10%;">Kecamatan</th>
                                        <th style="width:10%;">Kabupaten</th>
                                        <th style="width:7%;">LU/LS</th>
                                        <th style="width:7%;">BT</th>
                                    </tr>
                                </thead>
                                <tbody style="background-color:#E8F5D2;">
                                    @if (!$hasFilter)
                                        {{-- Belum ada filter: tampilkan pesan --}}
                                        <tr>
                                            <td colspan="12" class="text-center text-muted py-3">
                                                Silakan pilih komoditas dan/atau kabupaten terlebih dahulu.
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Sudah ada filter: tampilkan data paginate --}}
                                        @forelse ($penangkarList as $p)
                                            <tr id="row-penangkar-{{ $p->id }}"
                                                style="border:1px solid #999; text-align:center; cursor:pointer;"
                                                onclick="focusMarker({{ $p->id }})">
                                                <td>
                                                    {{ ($penangkarList->firstItem() ?? 1) + $loop->index }}
                                                </td>
                                                <td>{{ $p->tanaman->nama_tanaman ?? '-' }}</td>
                                                <td>{{ $p->nama_penangkar }}</td>
                                                <td>{{ $p->nib_dan_tanggal ?? '-' }}</td>
                                                <td>{{ $p->sertifikat_izin_usaha_nomor_dan_tanggal ?? '-' }}</td>
                                                <td>{{ $p->luas_areal_ha ?? '-' }}</td>
                                                <td>{{ $p->jumlah_sertifikasi ?? '-' }}</td>
                                                <td>{{ $p->jalan ?? '-' }}</td>
                                                <td>{{ $p->desa ?? '-' }}</td>
                                                <td>{{ $p->kecamatan ?? '-' }}</td>
                                                <td>{{ $p->kabupaten->nama_kabupaten ?? '-' }}</td>
                                                <td>{{ $p->latitude ?? '-' }}</td>
                                                <td>{{ $p->longitude ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center text-muted py-3">
                                                    Data penangkar belum tersedia untuk filter ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    @endif
                                </tbody>
                            </table>

                        </div>

                        {{-- PAGINATION --}}
                        @if ($hasFilter && $penangkarList instanceof \Illuminate\Contracts\Pagination\Paginator && $penangkarList->hasPages())
                            <div class="mt-2">
                                {{ $penangkarList->links() }}
                            </div>
                        @endif

                    </div> {{-- /.card-body --}}
                </div>
            </div>
        </div>
    </div>

    {{-- ================== LEAFLET & SCRIPT ================== --}}
    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ---------- INIT MAP ----------
                const map = L.map('map').setView([0.5, 102.0], 7);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // 🔹 Data marker: semua penangkar sesuai filter (bukan cuma 10 baris pagination)
                const penangkarData = @json($hasFilter ? $markerData : []);

                const markersById = {};
                let activeMarkers = [];

                function fadeOutAndRemove(marker) {
                    let opacity = 1;
                    const fadeInterval = setInterval(() => {
                        opacity -= 0.1;
                        if (opacity <= 0) {
                            clearInterval(fadeInterval);
                            map.removeLayer(marker);
                        } else {
                            marker.setStyle({
                                opacity,
                                fillOpacity: opacity * 0.8
                            });
                        }
                    }, 50);
                }

                function clearMarkers() {
                    activeMarkers.forEach(marker => fadeOutAndRemove(marker));
                    activeMarkers = [];
                }

                function highlightRow(id) {
                    document.querySelectorAll('#penangkarTable tbody tr')
                        .forEach(tr => tr.classList.remove('table-warning'));

                    const row = document.getElementById('row-penangkar-' + id);
                    if (row) {
                        row.classList.add('table-warning');
                        row.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }

                function showMarkers(data) {
                    clearMarkers();
                    Object.keys(markersById).forEach(k => delete markersById[k]);

                    if (!data || !data.length) return;

                    const bounds = [];

                    data.forEach(p => {
                        if (!p.latitude || !p.longitude) {
                            return; // lewati yang tidak punya koordinat
                        }

                        const lat = parseFloat(p.latitude);
                        const lng = parseFloat(p.longitude);
                        if (isNaN(lat) || isNaN(lng)) return;

                        const popup = `
                            <b>${p.nama_penangkar ?? '-'}</b><br>
                            <b>Komoditas:</b> ${p.tanaman?.nama_tanaman ?? '-'}<br>
                            <b>Lokasi:</b> ${p.jalan ?? '-'}, ${p.desa ?? '-'}, ${p.kecamatan ?? '-'}<br>
                            <b>Kabupaten:</b> ${p.kabupaten?.nama_kabupaten ?? '-'}<br>
                            <b>Koordinat:</b> ${p.latitude ?? '-'} , ${p.longitude ?? '-'}
                        `;

                        const marker = L.circleMarker([lat, lng], {
                                radius: 6,
                                color: '#d63031',
                                fillColor: '#e74c3c',
                                fillOpacity: 0,
                                opacity: 0,
                            })
                            .addTo(map)
                            .bindPopup(popup)
                            .on('click', () => {
                                highlightRow(p.id);
                            });

                        // efek fade in
                        let opacity = 0;
                        const fadeIn = setInterval(() => {
                            opacity += 0.1;
                            if (opacity >= 1) clearInterval(fadeIn);
                            marker.setStyle({
                                opacity,
                                fillOpacity: opacity * 0.8
                            });
                        }, 50);

                        markersById[p.id] = marker;
                        activeMarkers.push(marker);
                        bounds.push([lat, lng]);
                    });

                    if (bounds.length > 0) {
                        map.fitBounds(bounds);
                    }
                }

                // Tampilkan titik awal (jika sudah ada filter)
                showMarkers(penangkarData);

                // Dipanggil saat klik baris tabel (meski tabel cuma 10 baris, marker tetap semua)
                window.focusMarker = function(id) {
                    const marker = markersById[id];
                    if (marker) {
                        map.setView(marker.getLatLng(), 14);
                        marker.openPopup();
                        highlightRow(id);
                    }
                };
            });
        </script>
    @endpush
@endsection
