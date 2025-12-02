@extends('layouts.halamanutama')

@section('title', 'Peta Varietas Tanaman Riau')

@section('content')
    <div class="container-fluid mt-3">
        <div class="row">
            <!-- 🗺️ MAP SECTION -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white fw-bold">
                        Peta Persebaran Varietas
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 400px; border-radius: 6px;"></div>
                    </div>
                </div>
            </div>

            <!-- 📋 TABLE SECTION -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Data Varietas Tanaman</span>
                        <div>
                            <a href="{{ route('export.varietas.excel', ['tanaman_id' => request('tanaman_id'), 'kabupaten_id' => request('kabupaten_id')]) }}"
                                class="btn btn-success btn-sm">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </a>
                            <a href="{{ route('export.varietas.pdf', ['tanaman_id' => request('tanaman_id'), 'kabupaten_id' => request('kabupaten_id')]) }}"
                                class="btn btn-danger btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </a>
                        </div>
                    </div>

                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <!-- 🔍 FILTER FORM -->
                        <form method="GET" action="{{ route('peta.index') }}" id="filterForm" class="row mb-3">
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

                        <!-- 📊 DATA TABLE -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle shadow-sm" id="varietasTable"
                                style="border:1px solid #999; font-size:13px; text-align:center;">
                                <thead class="table-dark text-center">
                                    <tr class="align-middle">
                                        <th style="width:4%;">No.</th>
                                        <th style="width:18%;">Nomor dan Tanggal SK</th>
                                        <th style="width:15%;">Varietas</th>
                                        <th style="width:12%;">Jenis Benih</th>
                                        <th style="width:18%;">Pemilik Varietas</th>
                                        <th style="width:16%;">Jumlah Materi Genetik<br>(Pohon/Rumpun)</th>
                                        <th style="width:12%;">Keterangan</th>
                                        <th style="width:5%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody style="background-color:#E8F5D2;">
                                    @forelse ($varietas as $v)
                                        <tr id="row-varietas-{{ $v->id }}"
                                            style="border:1px solid #999; text-align:center;">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div><strong>{{ $v->deskripsi->nomor_sk ?? '-' }}</strong></div>
                                                <small class="text-muted">{{ $v->deskripsi->tanggal ?? '-' }}</small>
                                            </td>
                                            <td>{{ $v->nama_varietas }}</td>
                                            <td>{{ $v->jenis_benih ?? '-' }}</td>
                                            <td>{{ $v->deskripsi->pemilik_varietas ?? '-' }}</td>
                                            <td>{{ $v->materiGenetik->count() ?? 0 }}</td>
                                            <td>{{ $v->keterangan ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info px-2"
                                                    onclick="showDetail({{ $v->id }})">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-3">
                                                Silakan pilih komoditi dan kabupaten terlebih dahulu.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{-- PAGINATION TABEL VARIETAS --}}
                            @if ($varietas instanceof \Illuminate\Contracts\Pagination\Paginator && $varietas->hasPages())
                                <div class="mt-2 d-flex justify-content-end">
                                    {{ $varietas->withQueryString()->links() }}
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- 📑 DETAIL SECTION -->
        <div class="row mt-4" id="detailSection" style="display: none;">
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold text-primary mb-0">Deskripsi Varietas</h5>
                    <div id="deskripsiButtons" style="display:none;">
                        <a href="#" id="downloadDeskripsiPDF" class="btn btn-danger btn-sm">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                        <a href="#" id="downloadDeskripsiExcel" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                    </div>
                </div>

                <!-- ✅ tampilkan deskripsi seperti PDF -->
                <div id="deskripsiContent" class="border p-3 rounded bg-light mt-2"
                    style="border:1px solid #ccc; font-size:14px;">
                    <table class="table table-bordered table-sm" style="border:1px solid #999;">
                        <tbody id="deskripsiTableBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 align-items-sm-center">
                    <h5 class="fw-bold text-primary mb-1 mb-sm-0 mt-2 mt-sm-0 me-sm-2 flex-grow-1">
                        Materi Genetik dan Koordinat Lokasi
                    </h5>

                    <div id="materiButtons" class="d-flex flex-wrap gap-1" style="display: none;">
                        <a href="#" id="downloadMateriExcel" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                        <a href="#" id="downloadMateriPDF" class="btn btn-danger btn-sm">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                    </div>
                </div>

                <div id="koordinatContent" class="border p-3 rounded bg-light mt-2"></div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([0.5, 102.0], 7);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const varietasData = @json($varietasMap);

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

            function showMarkers(data) {
                clearMarkers();
                if (!data || data.length === 0) return;

                const bounds = [];
                const grouped = {};

                data.forEach(v => {
                    if (v.materi_genetik && v.materi_genetik.length > 0) {
                        v.materi_genetik.forEach(m => {
                            if (m.latitude && m.longitude) {
                                const key = `${m.latitude}_${m.longitude}`;
                                if (!grouped[key]) {
                                    grouped[key] = {
                                        varietas: v.nama_varietas,
                                        kabupaten: v.kabupaten?.nama_kabupaten ?? '-',
                                        pohon: [],
                                        lat: parseFloat(m.latitude),
                                        lng: parseFloat(m.longitude),
                                        varietas_id: v.id
                                    };
                                }
                                grouped[key].pohon.push(m.nomor_pohon);
                            }
                        });
                    }
                });

                Object.values(grouped).forEach(item => {
                    const popup = `
                <b>${item.varietas}</b><br>
                <b>Kabupaten:</b> ${item.kabupaten}<br>
                <b>No. Pohon:</b> ${item.pohon.join(', ')}
            `;
                    const marker = L.circleMarker([item.lat, item.lng], {
                            radius: 6,
                            color: '#d63031',
                            fillColor: '#e74c3c',
                            fillOpacity: 0,
                            opacity: 0,
                        }).addTo(map)
                        .bindPopup(popup)
                        .on('click', () => highlightRow(item.varietas_id));

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

                if (bounds.length > 0) map.fitBounds(bounds);
            }

            showMarkers(varietasData);

            function highlightRow(id) {
                document.querySelectorAll('#varietasTable tr').forEach(tr => tr.classList.remove('table-warning'));
                const row = document.getElementById('row-varietas-' + id);
                if (row) {
                    row.classList.add('table-warning');
                    row.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            // 🧭 DETAIL
            window.showDetail = function(id) {
                fetch(`/peta/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('detailSection').style.display = 'flex';
                        const d = data.deskripsi ?? {};

                        // 🎯 Update tombol export
                        document.getElementById('downloadDeskripsiPDF').href =
                            `/export/deskripsi/${id}/pdf`;
                        document.getElementById('downloadDeskripsiExcel').href =
                            `/export/deskripsi/${id}/excel`;
                        document.getElementById('deskripsiButtons').style.display = 'inline-block';
                        document.getElementById('downloadMateriExcel').href =
                            `/export/materigenetik/${id}/excel`;
                        document.getElementById('downloadMateriPDF').href =
                            `/export/materigenetik/${id}/pdf`;
                        document.getElementById('materiButtons').style.display = 'inline-block';

                        // 🌍 Map marker
                        clearMarkers();
                        if (data.materi_genetik?.length) {
                            const bounds = [];
                            data.materi_genetik.forEach(m => {
                                const marker = L.circleMarker([m.latitude, m.longitude], {
                                        radius: 7,
                                        color: '#2c3e50',
                                        fillColor: '#3498db',
                                        fillOpacity: 0,
                                        opacity: 0
                                    }).addTo(map)
                                    .bindPopup(
                                        `<b>${data.nama_varietas}</b><br>No. Pohon: ${m.nomor_pohon}`
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
                                bounds.push([m.latitude, m.longitude]);
                            });
                            map.fitBounds(bounds);
                        }

                        // 🧾 DESKRIPSI (format tabel sejajar seperti PDF)
                        const fields = [
                            ['Keputusan Menteri Pertanian RI', ''],
                            ['Nomor', d.nomor_sk],
                            ['Tanggal', d.tanggal],
                            ['Tipe varietas', d.tipe_varietas],
                            ['Asal-usul', d.asal_usul],
                            ['Tipe pertumbuhan', d.tipe_pertumbuhan],
                            ['Bentuk tajuk', d.bentuk_tajuk],
                            ['Daun', ''],
                            ['Ukuran', d.daun_ukuran],
                            ['Warna daun muda', d.daun_warna_muda],
                            ['Warna daun tua', d.daun_warna_tua],
                            ['Bentuk ujung daun', d.daun_bentuk_ujung],
                            ['Tepi daun', d.daun_tepi],
                            ['Pangkal daun', d.daun_pangkal],
                            ['Permukaan daun', d.daun_permukaan],
                            ['Warna pucuk', d.daun_warna_pucuk],
                            ['Buah', ''],
                            ['Ukuran buah', d.buah_ukuran],
                            ['Panjang (cm)', d.buah_panjang],
                            ['Diameter (cm)', d.buah_diameter],
                            ['Bobot (gram)', d.buah_bobot],
                            ['Bentuk buah', d.buah_bentuk],
                            ['Warna buah muda', d.buah_warna_muda],
                            ['Warna buah masak', d.buah_warna_masak],
                            ['Ukuran discus', d.buah_ukuran_discus],
                            ['Biji', ''],
                            ['Bentuk', d.biji_bentuk],
                            ['Nisbah biji buah (%)', d.biji_nisbah],
                            ['Persentase biji normal (%)', d.biji_persen_normal],
                            ['Citarasa', d.citarasa],
                            ['Potensi produksi', d.potensi_produksi],
                            ['Ketahanan terhadap hama penyakit utama', ''],
                            ['Penyakit karat daun', d.penyakit_karat_daun],
                            ['Pengerek buah kopi (PBKo)', d.penggerek_buah_kopi],
                            ['Daerah adaptasi', d.daerah_adaptasi],
                            ['Pemulia', d.pemulia],
                            ['Peneliti', d.peneliti],
                            ['Pemilik varietas', d.pemilik_varietas],
                        ];

                        let html = '';
                        fields.forEach(f => {
                            if (f[1] === '' && ['Keputusan Menteri Pertanian RI', 'Daun', 'Buah',
                                    'Biji', 'Ketahanan terhadap hama penyakit utama'
                                ].includes(f[0])) {
                                html +=
                                    `<tr style="background:#f5f5f5;font-weight:bold;"><td colspan="3">${f[0]}</td></tr>`;
                            } else {
                                html += `<tr>
                            <td style="width:35%;">${f[0]}</td>
                            <td style="width:2%;text-align:center;">:</td>
                            <td>${f[1] ?? '-'}</td>
                        </tr>`;
                            }
                        });
                        document.getElementById('deskripsiTableBody').innerHTML = html;

                        // 🧬 KOORDINAT dengan Pagination (max 18 data per halaman)
                        const rowsPerPage = 18;
                        let currentPage = 1;

                        function renderKoordinatTable(data) {
                            let htmlKoordinat = `
        <table class="table table-bordered table-hover table-sm align-middle mt-2"
               style="border:1px solid #999; font-size:14px;">
            <thead class="table-dark text-center align-middle">
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:25%;">No. SK dan Tanggal</th>
                    <th style="width:25%;">Nomor Pohon</th>
                    <th style="width:22%;">Latitude</th>
                    <th style="width:23%;">Longitude</th>
                </tr>
            </thead>
            <tbody>
    `;

                            if (data && data.length > 0) {
                                const start = (currentPage - 1) * rowsPerPage;
                                const end = start + rowsPerPage;
                                const pageData = data.slice(start, end);

                                pageData.forEach((m, index) => {
                                    htmlKoordinat += `
                <tr class="text-center">
                    <td>${start + index + 1}</td>
                    <td>
                        <div><strong>${m.no_sk ?? '-'}</strong></div>
                        <small class="text-muted">${m.tanggal_sk ?? '-'}</small>
                    </td>
                    <td>${m.nomor_pohon ?? '-'}</td>
                    <td>${m.latitude ?? '-'}</td>
                    <td>${m.longitude ?? '-'}</td>
                </tr>
            `;
                                });
                            } else {
                                htmlKoordinat += `
            <tr>
                <td colspan="5" class="text-center text-muted py-3">
                    Tidak ada data materi genetik untuk varietas ini.
                </td>
            </tr>
        `;
                            }

                            htmlKoordinat += `
            </tbody>
        </table>
    `;

                            // 🧭 Tambahkan pagination jika data > 18
                            if (data && data.length > rowsPerPage) {
                                const totalPages = Math.ceil(data.length / rowsPerPage);
                                htmlKoordinat += `
            <nav class="mt-2">
                <ul class="pagination pagination-sm justify-content-center mb-0">
        `;

                                // Tombol "Prev"
                                htmlKoordinat += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" onclick="changeKoordinatPage(${currentPage - 1})">&laquo;</button>
            </li>
        `;

                                // Tombol nomor halaman
                                for (let i = 1; i <= totalPages; i++) {
                                    htmlKoordinat += `
                <li class="page-item ${currentPage === i ? 'active' : ''}">
                    <button class="page-link" onclick="changeKoordinatPage(${i})">${i}</button>
                </li>
            `;
                                }

                                // Tombol "Next"
                                htmlKoordinat += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" onclick="changeKoordinatPage(${currentPage + 1})">&raquo;</button>
            </li>
        `;

                                htmlKoordinat += `
                </ul>
            </nav>
        `;
                            }

                            document.getElementById('koordinatContent').innerHTML = htmlKoordinat;
                        }

                        window.changeKoordinatPage = function(page) {
                            if (!window._koordinatData) return;
                            const totalPages = Math.ceil(window._koordinatData.length / rowsPerPage);
                            if (page < 1 || page > totalPages) return;
                            currentPage = page;
                            renderKoordinatTable(window._koordinatData);
                        };

                        // render pertama kali
                        window._koordinatData = data.materi_genetik || [];
                        renderKoordinatTable(window._koordinatData);

                    });
            };
        });
    </script>
@endpush
