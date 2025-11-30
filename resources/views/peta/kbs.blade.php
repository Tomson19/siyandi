@extends('layouts.halamanutama')

@section('title', 'Peta Kebun Benih Sumber')

@section('content')
    <div class="container-fluid mt-3">
        <div class="row">
            {{-- 🗺️ MAP SECTION --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white fw-bold">
                        Peta Persebaran Kebun Benih Sumber
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 400px; border-radius: 6px;"></div>
                    </div>
                </div>
            </div>

            {{-- 📋 FILTER + LIST KBS --}}
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Data Kebun Benih Sumber</span>
                        <div>
                            <a href="{{ route('peta.kbs.export.excel', [
                                        'tanaman_id'   => request('tanaman_id'),
                                        'kabupaten_id' => request('kabupaten_id'),
                                    ]) }}"
                                class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </a>
                            <a href="{{ route('peta.kbs.export.pdf', [
                                        'tanaman_id'   => request('tanaman_id'),
                                        'kabupaten_id' => request('kabupaten_id'),
                                    ]) }}"
                                class="btn btn-danger btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </a>
                        </div>
                    </div>

                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">

                        {{-- 🔍 FILTER FORM --}}
                        <form method="GET" action="{{ route('peta.kbs.index') }}" id="filterForm" class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pilih Komoditi</label>
                                <select name="tanaman_id" class="form-select"
                                    onchange="document.getElementById('filterForm').submit()">
                                    <option value="">-- Pilih Komoditi --</option>
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
                                    <option value="">-- Pilih Kabupaten --</option>
                                    @foreach ($kabupatenList as $k)
                                        <option value="{{ $k->id }}"
                                            {{ request('kabupaten_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kabupaten }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        {{-- 📊 TABEL HEADER KBS --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle shadow-sm" id="kbsTable"
                                style="border:1px solid #999; font-size:13px; text-align:center;">
                                <thead class="table-dark text-center">
                                    <tr class="align-middle">
                                        <th style="width:5%;">No.</th>
                                        <th style="width:12%;">Komoditas</th>
                                        <th style="width:18%;">No & Tanggal SK</th>
                                        <th style="width:20%;">Varietas</th>
                                        <th style="width:20%;">Kabupaten</th>
                                        <th style="width:10%;">Jumlah Lokasi</th>
                                        <th style="width:10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody style="background-color:#E8F5D2;">
                                    @if (!$hasFilter)
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-3">
                                                Silakan pilih komoditi dan/atau kabupaten terlebih dahulu.
                                            </td>
                                        </tr>
                                    @else
                                        @forelse ($kbsList as $k)
                                            <tr id="row-kbs-{{ $k->id }}"
                                                style="border:1px solid #999; text-align:center;">
                                                <td>
                                                    {{ ($kbsList->firstItem() ?? 1) + $loop->index }}
                                                </td>
                                                <td>{{ $k->tanaman->nama_tanaman ?? '-' }}</td>
                                                <td>
                                                    <div><strong>{{ $k->nomor_sk ?? '-' }}</strong></div>
                                                    <small class="text-muted">{{ $k->tanggal_sk ?? '-' }}</small>
                                                </td>
                                                <td>{{ $k->nama_varietas }}</td>
                                                <td>{{ $k->kabupaten->nama_kabupaten ?? '-' }}</td>
                                                <td>{{ $k->pemilik->count() }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info px-2"
                                                        onclick="showDetail({{ $k->id }})">
                                                        <i class="bi bi-search"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-3">
                                                    Data KBS belum tersedia untuk filter ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- PAGINATION --}}
                        @if ($hasFilter && $kbsList instanceof \Illuminate\Contracts\Pagination\Paginator && $kbsList->hasPages())
                            <div class="mt-2">
                                {{ $kbsList->links() }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- 📑 DETAIL SECTION --}}
        <div class="row mt-4" id="detailSection" style="display: none;">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <span>Detail Kebun Benih Sumber</span>
                        <div class="d-flex align-items-center gap-2">
                            <span id="detailTitle" class="fw-bold me-2"></span>
                            <div id="detailExportButtons" style="display:none;">
                                <a href="#" id="detailExportExcel" class="btn btn-sm btn-success">
                                    <i class="bi bi-file-earmark-excel"></i>
                                </a>
                                <a href="#" id="detailExportPdf" class="btn btn-sm btn-danger">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 450px; overflow-y:auto;">
                            <table class="table table-bordered table-sm align-middle"
                                style="border:1px solid #999; font-size:12px;">
                                <thead class="table-dark text-center align-middle">
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
                                <tbody id="detailTableBody">
                                    {{-- Diisi via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
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

                // 🔹 Semua data marker (tanpa pagination)
                const kbsData = @json($hasFilter ? $markerData : []);

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

                function highlightRow(kbsId) {
                    document.querySelectorAll('#kbsTable tbody tr')
                        .forEach(tr => tr.classList.remove('table-warning'));

                    const row = document.getElementById('row-kbs-' + kbsId);
                    if (row) {
                        row.classList.add('table-warning');
                        row.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }

                function showMarkersForAll(data) {
                    clearMarkers();
                    if (!data || !data.length) return;

                    const bounds = [];
                    const grouped = {};

                    data.forEach(k => {
                        if (!k.pemilik) return;
                        k.pemilik.forEach(p => {
                            if (!p.pohon) return;
                            p.pohon.forEach(ph => {
                                if (ph.latitude && ph.longitude) {
                                    const key = `${ph.latitude}_${ph.longitude}`;
                                    if (!grouped[key]) {
                                        grouped[key] = {
                                            komoditas: k.tanaman?.nama_tanaman ?? '',
                                            varietas: k.nama_varietas,
                                            kabupaten: k.kabupaten?.nama_kabupaten ?? '',
                                            pemilik: new Set(),
                                            pohon: [],
                                            lat: parseFloat(ph.latitude),
                                            lng: parseFloat(ph.longitude),
                                            kbs_id: k.id,
                                        };
                                    }
                                    if (p.nama_pemilik) {
                                        grouped[key].pemilik.add(p.nama_pemilik);
                                    }
                                    grouped[key].pohon.push(ph.no_pohon ?? ph.nomor_pohon_induk ?? '?');
                                }
                            });
                        });
                    });

                    Object.values(grouped).forEach(item => {
                        const popup = `
                            <b>${item.varietas}</b><br>
                            <b>Komoditas:</b> ${item.komoditas}<br>
                            <b>Kabupaten:</b> ${item.kabupaten}<br>
                            <b>Pemilik:</b> ${Array.from(item.pemilik).join(', ') || '-'}<br>
                            <b>No. Pohon:</b> ${item.pohon.join(', ')}
                        `;
                        const marker = L.circleMarker([item.lat, item.lng], {
                                radius: 6,
                                color: '#d63031',
                                fillColor: '#e74c3c',
                                fillOpacity: 0,
                                opacity: 0,
                            })
                            .addTo(map)
                            .bindPopup(popup)
                            .on('click', () => highlightRow(item.kbs_id));

                        let opacity = 0;
                        const fadeIn = setInterval(() => {
                            opacity += 0.1;
                            if (opacity >= 1) clearInterval(fadeIn);
                            marker.setStyle({
                                opacity,
                                fillOpacity: opacity * 0.8
                            });
                        }, 50);

                        activeMarkers.push(marker);
                        bounds.push([item.lat, item.lng]);
                    });

                    if (bounds.length > 0) {
                        map.fitBounds(bounds);
                    }
                }

                // Tampilkan semua titik awal (sesuai filter server)
                showMarkersForAll(kbsData);

                // ---------- DETAIL ----------
                window.showDetail = function(id) {
                    fetch("{{ url('/peta-kbs') }}/" + id)
                        .then(res => res.json())
                        .then(data => {
                            // Tampilkan section detail
                            const detailSection = document.getElementById('detailSection');
                            const detailBody    = document.getElementById('detailTableBody');
                            const detailTitle   = document.getElementById('detailTitle');

                            const exportButtons = document.getElementById('detailExportButtons');
                            const exportExcel   = document.getElementById('detailExportExcel');
                            const exportPdf     = document.getElementById('detailExportPdf');

                            detailSection.style.display = 'block';
                            detailTitle.innerHTML = `${data.komoditas ?? '-'} &mdash; ${data.varietas ?? '-'}`;

                            // set URL export detail berdasarkan id
                            exportExcel.href = "{{ url('/peta-kbs') }}/" + id + "/export/excel";
                            exportPdf.href   = "{{ url('/peta-kbs') }}/" + id + "/export/pdf";
                            exportButtons.style.display = 'inline-block';

                            // Bersihkan highlight di tabel list
                            highlightRow(data.id);

                            // Update marker hanya untuk KBS ini
                            clearMarkers();
                            const bounds = [];

                            if (data.pemilik && data.pemilik.length) {
                                data.pemilik.forEach(p => {
                                    if (!p.pohon) return;
                                    p.pohon.forEach(ph => {
                                        if (!ph.latitude || !ph.longitude) return;

                                        const marker = L.circleMarker([ph.latitude, ph.longitude], {
                                                radius: 7,
                                                color: '#2c3e50',
                                                fillColor: '#3498db',
                                                fillOpacity: 0,
                                                opacity: 0,
                                            })
                                            .addTo(map)
                                            .bindPopup(
                                                `<b>${data.varietas}</b><br>` +
                                                `<b>Pemilik:</b> ${p.nama_pemilik ?? '-'}<br>` +
                                                `<b>No. Pohon:</b> ${ph.no_pohon ?? '-'}`
                                            );

                                        let opacity = 0;
                                        const fadeIn = setInterval(() => {
                                            opacity += 0.1;
                                            if (opacity >= 1) clearInterval(fadeIn);
                                            marker.setStyle({
                                                opacity,
                                                fillOpacity: opacity * 0.8
                                            });
                                        }, 50);

                                        activeMarkers.push(marker);
                                        bounds.push([ph.latitude, ph.longitude]);
                                    });
                                });

                                if (bounds.length > 0) {
                                    map.fitBounds(bounds);
                                }
                            }

                            // Bangun tabel detail (sama seperti sebelumnya)
                            let html = '';
                            let kbsRowShown = false;
                            let totalLuas = 0;
                            let totalPohonInduk = 0;

                            if (data.pemilik && data.pemilik.length) {
                                data.pemilik.forEach(p => {
                                    const luas = parseFloat(p.luas_ha);
                                    if (!isNaN(luas)) totalLuas += luas;

                                    const jmlInduk = parseInt(p.jumlah_pohon_induk);
                                    if (!isNaN(jmlInduk)) totalPohonInduk += jmlInduk;

                                    let pemilikRowShown = false;

                                    if (p.pohon && p.pohon.length) {
                                        p.pohon.forEach(ph => {
                                            html += `<tr>`;

                                            html += `<td class="text-center">${kbsRowShown ? '' : '1'}</td>`;
                                            html += `<td>${kbsRowShown ? '' : (data.komoditas ?? '-')}</td>`;
                                            html += `<td>${kbsRowShown ? '' : (data.varietas ?? '-')}</td>`;

                                            if (!kbsRowShown) {
                                                html += `<td>
                                                    <div><strong>${data.nomor_sk ?? '-'}</strong></div>
                                                    <small class="text-muted">${data.tanggal_sk ?? '-'}</small>
                                                </td>`;
                                            } else {
                                                html += `<td></td>`;
                                            }

                                            html += `<td>${pemilikRowShown ? '' : (p.kecamatan ?? '-')}</td>`;
                                            html += `<td>${pemilikRowShown ? '' : (p.desa ?? '-')}</td>`;
                                            html += `<td>${pemilikRowShown ? '' : (p.tahun_tanam ?? '-')}</td>`;
                                            html += `<td class="text-center">${pemilikRowShown ? '' : (p.jumlah_pit ?? '-')}</td>`;

                                            html += `<td class="text-center">${pemilikRowShown ? '' : (p.no_pemilik ?? '-')}</td>`;
                                            html += `<td>${pemilikRowShown ? '' : (p.nama_pemilik ?? '-')}</td>`;
                                            html += `<td class="text-center">${pemilikRowShown ? '' : (p.luas_ha ?? '-')}</td>`;
                                            html += `<td class="text-center">${pemilikRowShown ? '' : (p.jumlah_pohon_induk ?? '-')}</td>`;

                                            html += `<td class="text-center">${ph.no_pohon ?? '-'}</td>`;
                                            html += `<td class="text-center">${ph.nomor_pohon_induk ?? '-'}</td>`;
                                            html += `<td class="text-center">${ph.latitude ?? '-'}</td>`;
                                            html += `<td class="text-center">${ph.longitude ?? '-'}</td>`;

                                            html += `</tr>`;

                                            kbsRowShown = true;
                                            pemilikRowShown = true;
                                        });
                                    } else {
                                        html += `<tr>`;
                                        html += `<td class="text-center">${kbsRowShown ? '' : '1'}</td>`;
                                        html += `<td>${kbsRowShown ? '' : (data.komoditas ?? '-')}</td>`;
                                        html += `<td>${kbsRowShown ? '' : (data.varietas ?? '-')}</td>`;
                                        if (!kbsRowShown) {
                                            html += `<td>
                                                <div><strong>${data.nomor_sk ?? '-'}</strong></div>
                                                <small class="text-muted">${data.tanggal_sk ?? '-'}</small>
                                            </td>`;
                                        } else {
                                            html += `<td></td>`;
                                        }

                                        html += `<td>${p.kecamatan ?? '-'}</td>`;
                                        html += `<td>${p.desa ?? '-'}</td>`;
                                        html += `<td>${p.tahun_tanam ?? '-'}</td>`;
                                        html += `<td class="text-center">${p.jumlah_pit ?? '-'}</td>`;

                                        html += `<td class="text-center">${p.no_pemilik ?? '-'}</td>`;
                                        html += `<td>${p.nama_pemilik ?? '-'}</td>`;
                                        html += `<td class="text-center">${p.luas_ha ?? '-'}</td>`;
                                        html += `<td class="text-center">${p.jumlah_pohon_induk ?? '-'}</td>`;

                                        html += `<td class="text-center">-</td>`;
                                        html += `<td class="text-center">-</td>`;
                                        html += `<td class="text-center">-</td>`;
                                        html += `<td class="text-center">-</td>`;

                                        html += `</tr>`;

                                        kbsRowShown = true;
                                    }
                                });

                                html += `
                                    <tr class="fw-bold bg-light">
                                        <td colspan="9"></td>
                                        <td class="text-end">Jumlah</td>
                                        <td class="text-center">${totalLuas > 0 ? totalLuas : '-'}</td>
                                        <td class="text-center">${totalPohonInduk > 0 ? totalPohonInduk : '-'}</td>
                                        <td colspan="4"></td>
                                    </tr>
                                `;
                            } else {
                                html = `
                                    <tr>
                                        <td colspan="16" class="text-center text-muted">
                                            Belum ada data pemilik / pohon untuk kebun benih sumber ini.
                                        </td>
                                    </tr>
                                `;
                            }

                            detailBody.innerHTML = html;
                        });
                };
            });
        </script>
    @endpush
@endsection
