@extends('layouts.halamanutama')

@section('title', 'Peta Varietas Tanaman Riau')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>
    <script src="https://unpkg.com/@turf/turf@6.5.0/turf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html-to-image@1.11.11/dist/html-to-image.min.js"></script>


    <style>
        :root {
            --map-h: 560px;
        }

        /* tinggi peta & kartu kanan */
        .title {
            text-align: center;
            font-weight: 700;
            margin: 22px 0 14px;
            letter-spacing: .5px;
        }

        #map {
            height: var(--map-h);
            border: 3px solid #5e9b43;
        }

        .table-card {
            border: 3px solid #5e9b43;
            height: var(--map-h);
        }

        .table-scroll {
            height: var(--map-h);
            overflow: auto;
        }

        .table thead th {
            background: #8dc63f !important;
            color: #000;
            vertical-align: middle;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .table tfoot th {
            position: sticky;
            bottom: 0;
            background: #fff;
            z-index: 2;
        }

        .table th,
        .table td {
            text-align: center;
        }

        /* tabel center */

        .legend {
            background: #fff;
            padding: 8px 10px;
            border: 2px solid #777;
            border-radius: 4px;
            line-height: 1.4
        }

        .legend .swatch {
            display: inline-block;
            width: 16px;
            height: 10px;
            margin-right: 6px;
            vertical-align: middle
        }

        .legend .line {
            display: inline-block;
            width: 28px;
            height: 0;
            border-top: 3px dashed #000;
            margin-right: 6px;
            vertical-align: middle;
        }

        .legend .line.thin {
            border-top-width: 2px;
        }

        /* Label nama kabupaten di peta */
        .label-kab {
            font: 12px/1.1 "Segoe UI", Arial, sans-serif;
            color: #000;
            text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;
            white-space: nowrap;
            pointer-events: none;
        }

        .accent-title {
            display: inline-block;
            position: relative;
            padding-bottom: 0.35rem;
        }

        .accent-title::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            height: 4px;
            width: 45%;
            background: linear-gradient(90deg, #ffb703, #fb8500);
            border-radius: 4px;
        }
    </style>

    @php
        // fallback aman jika variabel belum dikirim dari controller
        $assetDir = $assetDir ?? 'mapdata';
        $availableSlugs = array_keys($shpFiles ?? []);
    @endphp

    <div class="container-fluid px-4">
        <h4 class="title">AREAL POTENSI BUDIDAYA – PROVINSI RIAU</h4>

        {{-- Filter komoditi --}}
<div class="row mb-2">
    <div class="col-12">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-end align-items-sm-center gap-1 gap-sm-2 text-center">

            {{-- Label: di atas pada mobile, sejajar pada sm+ --}}
            <div class="mb-1 mb-sm-0">
                <label for="komoditiSelect" class="form-label fw-bold mb-0">
                    Komoditi:
                </label>
            </div>

            {{-- Select + tombol: blok kedua --}}
            <div class="d-flex flex-wrap justify-content-end align-items-center gap-1">
                {{-- Select: tidak full, tapi ada min-width --}}
                <select id="komoditiSelect"
                        class="form-select form-select-sm w-auto"
                        style="min-width: 170px;"
                        aria-label="Pilih komoditi">
                    <option value="" selected disabled>— Pilih Komoditi —</option>
                    @foreach ($komoditiOptions ?? [] as $opt)
                        @php
                            $slug = $opt['slug'];
                            $name = $opt['name'];
                            $has  = in_array($slug, $availableSlugs, true);
                        @endphp
                        <option value="{{ $slug }}" {{ $has ? '' : 'disabled' }}>
                            {{ $name }} {{ $has ? '' : '(data belum tersedia)' }}
                        </option>
                    @endforeach
                </select>

                {{-- Tombol-tombol --}}
                <button id="komoditiClear"
                        type="button"
                        class="btn btn-outline-secondary btn-sm"
                        title="Hapus filter">
                    ✕
                </button>

                <button id="btnDownloadPng"
                        type="button"
                        class="btn btn-primary btn-sm"
                        title="Unduh peta sebagai PNG">
                    <i class="bi bi-image"></i>
                </button>

                <button id="btnExportExcel"
                        type="button"
                        class="btn btn-success btn-sm"
                        title="Export Excel">
                    <i class="bi bi-file-earmark-excel"></i>
                </button>
            </div>

        </div>
    </div>
</div>




        <div class="row g-3">
            <div class="col-lg-7">
                <div id="map"></div>
            </div>
            <div class="col-lg-5">
                <div class="card table-card">
                    <div class="table-scroll">
                        <table class="table table-sm mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th style="width:60px;">NO</th>
                                    <th>KOMODITI</th>
                                    <th>KABUPATEN/KOTA</th>
                                    <th style="width:160px;">LUAS (Ha)</th>
                                    <th style="width:80px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="tbl-body">
                                <tr>
                                    <td colspan="5" class="py-4">Silakan pilih komoditi terlebih dahulu.</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">TOTAL</th>
                                    <th id="total-luas">—</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <form id="exportForm" action="{{ route('budidaya.export-js') }}" method="POST" class="d-none">
                        @csrf
                        <textarea name="rows_json" id="exportRowsJson"></textarea>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        // ====== DATA DARI SERVER ======
        const SHP_FILES = @json($shpFiles ?? []); // { slug: urlZip }
        const NAME_BY_SLUG = @json($nameBySlug ?? []); // { slug: name }

        // ====== KONFIG POTENSI ======
        const FIELD_KAB = 'KABUPATEN';
        const FIELD_LUAS = 'Luas_Ha';

        // Daftar 11 kab/kota (sesuai PDF) → layer potensi & tabel dibatasi ini
        const ALLOWED = [
            'KABUPATEN BENGKALIS', 'KABUPATEN INDRAGIRI HILIR', 'KABUPATEN INDRAGIRI HULU',
            'KABUPATEN KAMPAR', 'KABUPATEN KEPULAUAN MERANTI', 'KABUPATEN PELALAWAN',
            'KABUPATEN ROKAN HILIR', 'KABUPATEN ROKAN HULU', 'KABUPATEN SIAK',
            'KOTA DUMAI', 'KOTA PEKANBARU'
        ].map(s => s.toUpperCase());
        const ALLOWED_SET = new Set(ALLOWED);

        // Label peta: tambah Kuantan Singingi (meski tidak ada di tabel 11)
        const LABEL_EXTRA = ['KUANTAN SINGINGI'];
        const ALLOWED_BASE = ALLOWED.map(s => s.replace(/^KABUPATEN\s+|^KOTA\s+/, '').trim());
        const LABEL_WHITELIST_BASE = new Set([...ALLOWED_BASE, ...LABEL_EXTRA]);
        const CITY_BASE_SET = new Set(['DUMAI', 'PEKANBARU']);

        // ====== PETA (base & overlay selalu dimuat) ======
        const map = L.map('map', {
            zoomControl: true
        }).setView([0.5, 102.3], 7);

        // Basemap (usahakan provider dengan CORS). Jika provider support CORS, aktifkan crossOrigin:true
        const tileLayerBase = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
            crossOrigin: true // jika provider tidak support CORS, pengambilan PNG penuh bisa gagal; kode kita ada fallback.
        }).addTo(map);


        // Panes
        map.createPane('potensiPane');
        map.getPane('potensiPane').style.zIndex = 410;
        map.createPane('adminPane');
        map.getPane('adminPane').style.zIndex = 420;
        map.createPane('labelPane');
        map.getPane('labelPane').style.zIndex = 430;

        // Legend
        const legend = L.control({
            position: 'topright'
        });
        legend.onAdd = function() {
            const div = L.DomUtil.create('div', 'legend');
            div.innerHTML = `
      <b>KETERANGAN :</b><br>
      <span class="swatch" style="background:#CC00FF;border:2px solid #9900CC;"></span>
      Areal Potensi Budidaya
      <br><span class="line"></span> Batas Kira‑Kira Provinsi
      <br><span class="line thin"></span> Batas Kira‑Kira Kabupaten
    `;
            return div;
        };
        legend.addTo(map);
        L.control.scale({
            imperial: false
        }).addTo(map);

        // Overlay batas & label (dimuat sekali, sejak awal)
        (function loadOverlaysOnce() {
            Promise.all([
                fetch("{{ asset($assetDir . '/prov_riau.geojson') }}").then(r => r.json()),
                fetch("{{ asset($assetDir . '/kab_riau.geojson') }}").then(r => r.json())
            ]).then(([prov, kab]) => {
                const gProv = L.geoJSON(prov, {
                    style: {
                        color: '#000',
                        weight: 1.8,
                        dashArray: '6 6',
                        fillOpacity: 0
                    },
                    pane: 'adminPane',
                    interactive: false
                }).addTo(map);
                const gKab = L.geoJSON(kab, {
                    style: {
                        color: '#000',
                        weight: 1.2,
                        dashArray: '3 3',
                        fillOpacity: 0
                    },
                    pane: 'adminPane',
                    interactive: false
                }).addTo(map);
                // Fit awal ke provinsi
                if (gProv.getLayers().length) map.fitBounds(gProv.getBounds());

                // Label 11 kab/kota + Kuantan Singingi
                const labeled = new Set();
                kab.features.forEach(f => {
                    const rawName = (f.properties?.shapeName || f.properties?.NAME_2 || f.properties
                        ?.KABUPATEN || '').toString();
                    const base = baseName(rawName);
                    if (!LABEL_WHITELIST_BASE.has(base) || labeled.has(base)) return;
                    labeled.add(base);
                    const isCity = CITY_BASE_SET.has(base);
                    const labelText = (isCity ? 'KOTA ' : 'KABUPATEN ') + base;
                    const pt = turf.pointOnFeature(f).geometry.coordinates; // [lon, lat]
                    L.marker([pt[1], pt[0]], {
                        pane: 'labelPane',
                        interactive: false,
                        icon: L.divIcon({
                            className: '',
                            html: `<span class="label-kab">${labelText}</span>`
                        })
                    }).addTo(map);
                });
            }).catch(() => {});
        })();

        // ====== UTIL ======
        const fmtID = n => Number(n || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        function parseIDNumber(v) {
            if (v == null) return 0;
            if (typeof v === 'number') return v;
            return Number(String(v).replace(/\./g, '').replace(',', '.')) || 0;
        }

        function getProp(obj, key) {
            const low = key.toLowerCase();
            for (const k in obj) {
                if (k.toLowerCase() === low) return obj[k];
            }
            return null;
        }

        function baseName(raw) {
            return String(raw || '').toUpperCase().trim().replace(/^KABUPATEN\s+|^KOTA\s+/, '').trim();
        }

        // ====== STATE & RENDER ======
        let _potensiLayer = null;
        const kabAgg = new Map(); // { kab, luasTotal, group }
        const baseStyle = {
            color: '#9900CC',
            weight: 1.2,
            opacity: 0.9,
            fillColor: '#CC00FF',
            fillOpacity: 0.35,
            pane: 'potensiPane'
        };
        const hoverStyle = {
            weight: 2,
            fillOpacity: 0.45,
            color: '#660099'
        };
        let CURRENT_KOMODITI = null;
        let CURRENT_NAME = 'Komoditi';

        function clearPotensi(message) {
            kabAgg.clear();
            if (_potensiLayer) {
                map.removeLayer(_potensiLayer);
                _potensiLayer = null;
            }
            document.getElementById('tbl-body').innerHTML =
                `<tr><td colspan="5" class="py-4">${message || 'Tidak ada data.'}</td></tr>`;
            document.getElementById('total-luas').innerText = '—';
        }

        function renderTable() {
            const tbody = document.getElementById('tbl-body');
            tbody.innerHTML = '';
            const rows = ALLOWED.map(name => kabAgg.get(name) || {
                kab: name,
                luasTotal: 0,
                group: null
            });
            let total = 0;
            rows.forEach((row, i) => {
                total += row.luasTotal;
                const tr = document.createElement('tr');
                tr.innerHTML = `
        <td>${i+1}</td>
        <td>${CURRENT_NAME}</td>
        <td>${row.kab}</td>
        <td>${fmtID(row.luasTotal)}</td>
        <td>
          <button class="btn btn-sm btn-outline-success" data-kab="${row.kab}" title="Zoom">
            <i class="bi bi-zoom-in"></i>
          </button>
        </td>
      `;
                tbody.appendChild(tr);
            });
            document.getElementById('total-luas').innerText = fmtID(total);

            // Zoom
            tbody.querySelectorAll('button[data-kab]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const kab = btn.getAttribute('data-kab');
                    const grp = kabAgg.get(kab)?.group;
                    if (grp) map.fitBounds(grp.getBounds(), {
                        maxZoom: 12
                    });
                });
            });
        }

        function loadKomoditi(slug) {
            const shpUrl = SHP_FILES[slug];
            if (!shpUrl) {
                clearPotensi(`Data komoditi "${NAME_BY_SLUG[slug] || slug}" belum tersedia.`);
                return;
            }
            CURRENT_KOMODITI = slug;
            CURRENT_NAME = NAME_BY_SLUG[slug] || 'Komoditi';

            // reset state
            kabAgg.clear();
            if (_potensiLayer) {
                map.removeLayer(_potensiLayer);
                _potensiLayer = null;
            }
            document.getElementById('tbl-body').innerHTML = '<tr><td colspan="5" class="py-2">Memuat data…</td></tr>';

            shp(shpUrl).then(geojson => {
                _potensiLayer = L.geoJSON(geojson, {
                    filter: f => {
                        const kab = String(getProp(f.properties || {}, FIELD_KAB) ?? '').trim()
                            .toUpperCase();
                        return ALLOWED_SET.has(kab); // hanya 11 kab/kota
                    },
                    style: () => baseStyle,
                    onEachFeature: (feature, layer) => {
                        const p = feature.properties || {};
                        const kab = String(getProp(p, FIELD_KAB) ?? '').trim().toUpperCase();
                        const luas = parseIDNumber(getProp(p, FIELD_LUAS));

                        if (!kabAgg.has(kab)) kabAgg.set(kab, {
                            kab,
                            luasTotal: 0,
                            group: L.featureGroup()
                        });
                        const ent = kabAgg.get(kab);
                        ent.luasTotal += Number(luas) || 0;
                        ent.group.addLayer(layer);

                        layer.on({
                            mouseover: e => e.target.setStyle(hoverStyle),
                            mouseout: e => _potensiLayer.resetStyle(e.target),
                            click: e => {
                                const totalKab = ent.luasTotal ?? 0;
                                e.target.bindPopup(
                                    `<b>${kab}</b><br>Komoditi: ${CURRENT_NAME}<br>Luas total (Ha): ${fmtID(totalKab)}`
                                ).openPopup();
                            }
                        });
                    }
                }).addTo(map);

                if (_potensiLayer.getLayers().length) map.fitBounds(_potensiLayer.getBounds());
                renderTable();
            }).catch(err => {
                console.error(err);
                clearPotensi(`Gagal memuat shapefile <code>${shpUrl}</code>.`);
            });
        }

        // ====== INIT: set awal kosong, tunggu user pilih ======
        document.getElementById('komoditiSelect').value = "";
        clearPotensi('Silakan pilih komoditi terlebih dahulu.');

        document.getElementById('komoditiSelect').addEventListener('change', (e) => {
            const slug = e.target.value;
            if (!slug) {
                clearPotensi('Silakan pilih komoditi terlebih dahulu.');
                return;
            }
            loadKomoditi(slug);
        });

        // komoditi clear button
        document.getElementById('komoditiClear').addEventListener('click', () => {
            const sel = document.getElementById('komoditiSelect');
            sel.value = "";
            sel.dispatchEvent(new Event('change'));
        });

        // ====== EXPORT PNG (client-side) ======
        (function setupPngExport() {
            const btn = document.getElementById('btnDownloadPng');
            if (!btn) return;

            btn.addEventListener('click', async () => {
                const node = document.getElementById('map');
                if (!node) return;

                const pixelRatio =
                    2; // 2x biar tajam; naikkan ke 3 jika butuh lebih tajam (dengan risiko file lebih besar)
                const filename = () => {
                    const nm = (CURRENT_NAME || 'Komoditi').replace(/\s+/g, '_');
                    const now = new Date();
                    const pad = n => String(n).padStart(2, '0');
                    const stamp =
                        `${now.getFullYear()}${pad(now.getMonth()+1)}${pad(now.getDate())}_${pad(now.getHours())}${pad(now.getMinutes())}`;
                    return `Peta_${nm}_${stamp}.png`;
                };

                // Helper: simpan Blob → file
                function saveBlobAsFile(blob, name) {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = name;
                    a.click();
                    setTimeout(() => URL.revokeObjectURL(url), 1000);
                }

                // Snapshot fungsi: toBlob (lebih hemat memori daripada dataURL)
                const snap = () => htmlToImage.toBlob(node, {
                    pixelRatio,
                    backgroundColor: '#ffffff',
                    cacheBust: true
                });

                // 1) Coba ambil "apa adanya" (dengan basemap)
                try {
                    const blob = await snap();
                    if (blob) return saveBlobAsFile(blob, filename());
                } catch (e) {
                    console.warn('Snapshot with basemap failed (kemungkinan CORS); coba overlay-only...',
                        e);
                }

                // 2) Fallback overlay-only: sembunyikan basemap → foto → tampilkan lagi
                let hadBase = false;
                try {
                    if (typeof tileLayerBase !== 'undefined' && map.hasLayer(tileLayerBase)) {
                        hadBase = true;
                        map.removeLayer(tileLayerBase);
                        // Tunggu satu frame agar reflow selesai
                        await new Promise(r => requestAnimationFrame(r));
                    }
                    const blob = await snap();
                    if (blob) return saveBlobAsFile(blob, filename().replace('.png', '_overlay_only.png'));
                } catch (e) {
                    console.error('Snapshot overlay-only juga gagal:', e);
                    alert(
                        'Gagal mengekspor PNG. Coba perkecil ukuran peta atau ganti penyedia tile yang mendukung CORS.'
                    );
                } finally {
                    if (hadBase) tileLayerBase.addTo(map);
                }
            });
        })();

        // ====== EXPORT EXCEL (kirim data tabel ke Laravel) ======
        (function setupExcelExport() {
            const btn = document.getElementById('btnExportExcel');
            const form = document.getElementById('exportForm');
            const textarea = document.getElementById('exportRowsJson');

            if (!btn || !form || !textarea) return;

            btn.addEventListener('click', () => {
                // Pastikan sudah ada komoditi yang dipilih
                if (!CURRENT_KOMODITI || !CURRENT_NAME) {
                    alert('Silakan pilih komoditi terlebih dahulu sebelum export.');
                    return;
                }

                // Bangun array data berdasarkan kabAgg & ALLOWED (supaya urut seperti tabel)
                const rows = [];
                const rowsKab = ALLOWED.map(name => kabAgg.get(name) || {
                    kab: name,
                    luasTotal: 0
                });

                let adaData = false;

                rowsKab.forEach((row, idx) => {
                    const luas = row.luasTotal || 0;
                    if (luas > 0) adaData = true;

                    rows.push({
                        no: idx + 1,
                        komoditi: CURRENT_NAME,
                        kabupaten: row.kab,
                        luas: Number(luas)
                    });
                });

                if (!adaData) {
                    if (!confirm('Semua nilai luas 0. Tetap export Excel?')) {
                        return;
                    }
                }

                // Masukkan JSON ke textarea hidden
                textarea.value = JSON.stringify(rows);

                // Submit form → Laravel balas file Excel
                form.submit();
            });
        })();
    </script>
@endsection
