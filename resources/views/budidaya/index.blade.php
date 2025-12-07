@extends('layouts.halamanutama')

{{-- Judul halaman di layout (sesuai contohmu) --}}
@section('title', 'Peta Varietas Tanaman Riau')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/shpjs@latest/dist/shp.min.js"></script>
<script src="https://unpkg.com/@turf/turf@6.5.0/turf.min.js"></script>

<style>
  :root { --map-h: 560px; } /* tinggi peta & kartu kanan */
  .title { text-align:center; font-weight:700; margin:22px 0 14px; letter-spacing:.5px; }
  #map { height: var(--map-h); border: 3px solid #5e9b43; }
  .table-card { border: 3px solid #5e9b43; height: var(--map-h); }
  .table-scroll { height: var(--map-h); overflow:auto; }
  .table thead th { background:#8dc63f !important; color:#000; vertical-align:middle; position:sticky; top:0; z-index:2; }
  .table tfoot th { position:sticky; bottom:0; background:#fff; z-index:2; }
  .table th, .table td { text-align:center; } /* tabel center */

  .legend{background:#fff;padding:8px 10px;border:2px solid #777;border-radius:4px;line-height:1.4}
  .legend .swatch{display:inline-block;width:16px;height:10px;margin-right:6px;vertical-align:middle}
  .legend .line {display:inline-block; width:28px; height:0; border-top:3px dashed #000; margin-right:6px; vertical-align:middle;}
  .legend .line.thin { border-top-width:2px; }

  /* Label nama kabupaten di peta */
  .label-kab {
    font: 12px/1.1 "Segoe UI", Arial, sans-serif;
    color:#000;
    text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;
    white-space:nowrap; pointer-events: none;
  }
</style>

<div class="container-fluid px-4">
  <h4 class="title">AREAL POTENSI BUDIDAYA KOPI – PROVINSI RIAU</h4>

  <div class="row g-4">
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
              <tr><td colspan="5" class="py-4">Memuat data…</td></tr>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="3" class="">TOTAL</th>
                <th id="total-luas">0</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // ====== KONFIG UTAMA ======
  const SHP_URL  = "{{ asset('mapdata/FINAL_POTENSI_BUDIDAYA_KOPI_OK_KAB.zip') }}";
  const URL_PROV = "{{ asset('mapdata/prov_riau.geojson') }}";
  const URL_KAB  = "{{ asset('mapdata/kab_riau.geojson') }}";

  // Nama field di DBF shapefile potensi
  const FIELD_KAB  = 'KABUPATEN';
  const FIELD_LUAS = 'Luas_Ha';
  const KOMODITI   = 'Kopi';

  // 11 kab/kota dari PDF (untuk data potensi & urutan tabel)
  const ALLOWED = [
    'KABUPATEN BENGKALIS',
    'KABUPATEN INDRAGIRI HILIR',
    'KABUPATEN INDRAGIRI HULU',
    'KABUPATEN KAMPAR',
    'KABUPATEN KEPULAUAN MERANTI',
    'KABUPATEN PELALAWAN',
    'KABUPATEN ROKAN HILIR',
    'KABUPATEN ROKAN HULU',
    'KABUPATEN SIAK',
    'KOTA DUMAI',
    'KOTA PEKANBARU'
  ].map(s => s.toUpperCase());

  // Label peta: tampilkan ke-11 di atas + "KABUPATEN KUANTAN SINGINGI"
  const LABEL_EXTRA = ['KUANTAN SINGINGI'];
  const ALLOWED_BASE = ALLOWED.map(s => s.replace(/^KABUPATEN\s+|^KOTA\s+/,'').trim());
  const LABEL_WHITELIST_BASE = new Set([...ALLOWED_BASE, ...LABEL_EXTRA]);
  const ALLOWED_SET = new Set(ALLOWED);
  const CITY_BASE_SET = new Set(['DUMAI','PEKANBARU']); // kab/kota yang bertipe "kota"

  // ====== PETA ======
  const map = L.map('map', { zoomControl:true }).setView([0.5, 102.3], 7);

  // Laut biru (OSM standard)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // Panes untuk kontrol tumpukan layer
  map.createPane('potensiPane'); map.getPane('potensiPane').style.zIndex = 410;
  map.createPane('adminPane');   map.getPane('adminPane').style.zIndex   = 420;
  map.createPane('labelPane');   map.getPane('labelPane').style.zIndex   = 430;

  // Legend
  const legend = L.control({position: 'topright'});
  legend.onAdd = function() {
    const div = L.DomUtil.create('div', 'legend');
    div.innerHTML = `
      <b>KETERANGAN :</b><br>
      <span class="swatch" style="background:#CC00FF;border:2px solid #9900CC;"></span>
      Areal Potensi Budidaya Kopi
      <br><span class="line"></span> Batas Kira‑Kira Provinsi
      <br><span class="line thin"></span> Batas Kira‑Kira Kabupaten
    `;
    return div;
  };
  legend.addTo(map);
  L.control.scale({imperial:false}).addTo(map);

  // Util
  const fmtID = n => Number(n||0).toLocaleString('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2});
  function parseIDNumber(v){ if (v==null) return 0; if (typeof v==='number') return v; return Number(String(v).replace(/\./g,'').replace(',','.'))||0; }
  function getProp(obj, key){ const low = key.toLowerCase(); for (const k in obj){ if (k.toLowerCase()===low) return obj[k]; } return null; }
  function baseName(raw){
    return String(raw||'').toUpperCase().trim().replace(/^KABUPATEN\s+|^KOTA\s+/, '').trim();
  }

  // Agregasi per kab/kota (untuk tabel & zoom)
  const kabAgg = new Map(); // {kab, luasTotal, group}

  // Style & interaksi layer potensi
  const baseStyle  = { color:'#9900CC', weight:1.2, opacity:0.9, fillColor:'#CC00FF', fillOpacity:0.35, pane:'potensiPane' };
  const hoverStyle = { weight:2, fillOpacity:0.45, color:'#660099' };

  // ====== Muat shapefile potensi (hanya 11 kab/kota) ======
  shp(SHP_URL).then(geojson => {
    const potensiLayer = L.geoJSON(geojson, {
      filter: f => {
        const kab = String(getProp(f.properties||{}, FIELD_KAB) ?? '').trim().toUpperCase();
        return ALLOWED_SET.has(kab); // TAMPILKAN HANYA 11 kab/kota di layer potensi
      },
      style: () => baseStyle,
      onEachFeature: (feature, layer) => {
        const p   = feature.properties || {};
        const kab = String(getProp(p, FIELD_KAB) ?? '').trim().toUpperCase();
        const luas = parseIDNumber(getProp(p, FIELD_LUAS));

        if (!kabAgg.has(kab)) kabAgg.set(kab, { kab, luasTotal:0, group: L.featureGroup() });
        kabAgg.get(kab).luasTotal += Number(luas)||0;
        kabAgg.get(kab).group.addLayer(layer);

        // Popup (nama + total luas kab/kota)
        layer.on({
          mouseover: e => e.target.setStyle(hoverStyle),
          mouseout:  e => potensiLayer.resetStyle(e.target),
          click:     e => {
            const totalKab = kabAgg.get(kab)?.luasTotal ?? 0;
            e.target.bindPopup(`<b>${kab}</b><br>Komoditi: ${KOMODITI}<br>Luas total (Ha): ${fmtID(totalKab)}`).openPopup();
          }
        });
      }
    }).addTo(map);

    if (potensiLayer.getLayers().length) map.fitBounds(potensiLayer.getBounds());

    // ====== Overlay batas & LABEL (khususkan ke whitelist + tambahan Kuantan Singingi) ======
    Promise.all([fetch(URL_PROV).then(r=>r.json()), fetch(URL_KAB).then(r=>r.json())])
      .then(([prov, kab]) => {
        L.geoJSON(prov, { style:{ color:'#000', weight:1.8, dashArray:'6 6', fillOpacity:0 }, pane:'adminPane', interactive:false }).addTo(map);
        L.geoJSON(kab,  { style:{ color:'#000', weight:1.2, dashArray:'3 3', fillOpacity:0 }, pane:'adminPane', interactive:false }).addTo(map);

        const labeled = new Set();
        kab.features.forEach(f => {
          const raw = (f.properties?.shapeName || f.properties?.NAME_2 || f.properties?.KABUPATEN || '').toString();
          const base = baseName(raw); // >>>> NORMALISASI NAMA DI SINI <<<<
          if (!LABEL_WHITELIST_BASE.has(base) || labeled.has(base)) return;

          labeled.add(base);
          const isCity = CITY_BASE_SET.has(base);
          const labelText = (isCity ? 'KOTA ' : 'KABUPATEN ') + base;

          const pt = turf.pointOnFeature(f).geometry.coordinates; // [lon, lat]
          L.marker([pt[1], pt[0]], {
            pane:'labelPane', interactive:false,
            icon: L.divIcon({ className:'', html:`<span class="label-kab">${labelText}</span>` })
          }).addTo(map);
        });
      })
      .catch(()=>{ /* jika overlay gagal dimuat, lanjut saja */ });

    // Render tabel agregasi (tetap 11 kab/kota; urutan sesuai PDF)
    renderTable();

  }).catch(err => {
    console.error(err);
    document.querySelector('#tbl-body').innerHTML =
      `<tr><td colspan="5" class="text-danger py-4">Gagal memuat shapefile. Cek file ZIP di <code>public/peta/</code>.</td></tr>`;
  });

  function renderTable(){
    const tbody = document.getElementById('tbl-body');
    tbody.innerHTML = '';

    // Susun baris sesuai urutan ALLOWED (11 entri)
    const rows = ALLOWED.map(name => kabAgg.get(name) || { kab:name, luasTotal:0, group:null });

    let total = 0;
    rows.forEach((row, i) => {
      total += row.luasTotal;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${i+1}</td>
        <td>${KOMODITI}</td>
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

    // Zoom aksi ikon
    tbody.querySelectorAll('button[data-kab]').forEach(btn => {
      btn.addEventListener('click', () => {
        const kab = btn.getAttribute('data-kab');
        const grp = kabAgg.get(kab)?.group;
        if (grp) map.fitBounds(grp.getBounds(), { maxZoom:12 });
      });
    });
  }
</script>
@endsection
