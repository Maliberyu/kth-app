{{-- resources/views/blok/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Blok')
@section('page_title', 'Tambah Blok')

@section('content')
<div class="card" style="max-width:680px;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="fas fa-map" style="color:#1a7f4b;margin-right:8px;"></i>Form Tambah Blok</h3>
        <a href="{{ route('blok.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('blok.store') }}">
            @csrf

            <div class="grid grid-2">
                <div class="form-group">
                    <label>Nama Blok *</label>
                    <input type="text" name="nama_blok" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Jenis Blok</label>
                    <select name="jenis_blok" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Produksi">Produksi</option>
                        <option value="Konservasi">Konservasi</option>
                        <option value="Rehabilitasi">Rehabilitasi</option>
                    </select>
                </div>
            </div>

            {{-- 🔥 LUAS OTOMATIS --}}
            <div class="form-group">
                <label>Luas (Hektar)</label>
                <input type="number" name="luas" id="luas"
                       class="form-control fw-bold"
                       step="0.01" readonly placeholder="0.00">
                <small class="text-muted">Terhitung otomatis setelah polygon tertutup</small>
            </div>

            {{-- 🔥 JARAK ANTAR POHON --}}
            <div class="form-group">
                <label>Jarak Antar Pohon (Meter)</label>
                <input type="number" name="jarak_antar_pohon" id="jarak_antar_pohon"
                       class="form-control" min="1" step="0.5" placeholder="Contoh: 3">
                <small class="text-muted"><i class="fas fa-info-circle"></i> Jarak ideal pinus: 3-4 meter</small>
            </div>

            {{-- 🔥 TOTAL POHON --}}
            <div class="form-group">
                <label>Total Pohon (Estimasi)</label>
                <input type="number" name="total_pohon" id="total_pohon"
                       class="form-control fw-bold text-success" readonly placeholder="0">
            </div>

            {{-- 🔥 PRODUKTIF & TIDAK PRODUKTIF --}}
            <div class="grid grid-2">
                <div class="form-group">
                    <label>Pohon Produktif</label>
                    <input type="number" name="pohon_produktif" id="pohon_produktif"
                           class="form-control" placeholder="0" min="0">
                    <small class="text-muted">Default 75%. Bisa diedit manual.</small>
                </div>
                <div class="form-group">
                    <label>Pohon Tidak Produktif</label>
                    <input type="number" name="pohon_tidak_produktif" id="pohon_tidak_produktif"
                           class="form-control fw-bold text-danger" readonly>
                </div>
            </div>

            {{-- 🔥 GEOJSON HIDDEN --}}
            <input type="hidden" name="geojson" id="geojson">

            {{-- 🔥 MAP --}}
            <div class="form-group mt-3">
                <label>Gambar Area Blok</label>
                <div id="map" style="height:400px;border-radius:10px;border:2px solid #e0e0e0;"></div>
                <small class="text-muted">
                    <i class="fas fa-mouse-pointer"></i> Klik untuk tambah titik. Dekatkan ke titik awal untuk menutup area.
                </small>
            </div>

            <button type="button" onclick="resetPolygon()" class="btn btn-warning mt-2">
                <i class="fas fa-redo"></i> Reset Titik
            </button>

            <div class="mt-3">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection


@section('scripts')
{{-- Leaflet CSS & JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
{{-- Turf.js --}}
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

<script>
// ===============================
// 🔥 INIT MAP
// ===============================
var map = L.map('map').setView([-7.324426, 108.0145812], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);
map.doubleClickZoom.disable();

// ===============================
// 🔥 VARIABLE GLOBAL
// ===============================
let points = [];
let markers = [];
let polygon = null;
let polyline = null;
let previewCircle = null;
let isClosed = false;

// ===============================
// 🔥 HITUNG LUAS (Hektar)
// ===============================
function hitungLuas(geojson){
    try {
        let areaMeter = turf.area(geojson);
        let hektar = areaMeter / 10000;
        document.getElementById('luas').value = hektar.toFixed(2);
        console.log("✅ Luas:", hektar, "ha");
        
        hitungTotalPohon(); // Trigger chain hitung pohon
        return hektar;
    } catch (e) {
        console.error("❌ Error hitung luas:", e);
        return 0;
    }
}

// ===============================
// 🔥 HITUNG TOTAL POHON
// ===============================
function hitungTotalPohon(){
    const luasHa = parseFloat(document.getElementById('luas').value);
    const jarak = parseFloat(document.getElementById('jarak_antar_pohon').value);
    
    if(!luasHa || !jarak || jarak <= 0){
        document.getElementById('total_pohon').value = '';
        return;
    }
    
    const luasM2 = luasHa * 10000;
    const areaPerPohon = jarak * jarak;
    const totalPohon = Math.round(luasM2 / areaPerPohon);
    
    document.getElementById('total_pohon').value = totalPohon;
    console.log(`🌲 Total pohon: ${totalPohon}`);
    
    syncPohonProduktif(); // Update field produktif
}

// ===============================
// 🔥 SYNC POHON PRODUKTIF
// ===============================
function syncPohonProduktif() {
    const total = parseInt(document.getElementById('total_pohon').value) || 0;
    const prodInput = document.getElementById('pohon_produktif');
    
    prodInput.max = total; // Cegah input > total

    // Kalau user belum edit manual, isi default 75%
    if(!prodInput.dataset.userEdited && total > 0) {
        prodInput.value = Math.round(total * 0.75);
    }

    updateTidakProduktif();
}

function updateTidakProduktif() {
    const total = parseInt(document.getElementById('total_pohon').value) || 0;
    const prod = parseInt(document.getElementById('pohon_produktif').value) || 0;
    const tidakProd = Math.max(0, total - prod);
    
    document.getElementById('pohon_tidak_produktif').value = tidakProd;
    
    // Visual feedback jika input > total
    const inputProd = document.getElementById('pohon_produktif');
    if(prod > total) {
        inputProd.classList.add('is-invalid');
    } else {
        inputProd.classList.remove('is-invalid');
    }
}

// 🔥 Event: User edit produktif manual
document.getElementById('pohon_produktif').addEventListener('input', function() {
    this.dataset.userEdited = "true";
    updateTidakProduktif();
});

// 🔥 Event: Jarak berubah -> recalc total
document.getElementById('jarak_antar_pohon').addEventListener('input', hitungTotalPohon);

// ===============================
// 🔥 FUNGSI JARAK (Meter)
// ===============================
function jarakMeter(latlng1, latlng2){
    return map.distance(latlng1, latlng2);
}

// ===============================
// 🔥 UPDATE TAMPILAN POLYGON
// ===============================
function updatePolygonDisplay(pts, closed = false){
    let leafletPoints = pts.map(p => [p[1], p[0]]);
    if(polyline) map.removeLayer(polyline);
    if(polygon) map.removeLayer(polygon);

    polyline = L.polyline(leafletPoints, {color: '#1a7f4b', dashArray: closed ? null : '5, 5'}).addTo(map);
    if(pts.length >= 3){
        polygon = L.polygon(leafletPoints, {
            color: '#1a7f4b', fillOpacity: closed ? 0.4 : 0.2, weight: 2
        }).addTo(map);
    }
}

// ===============================
// 🔥 HAPUS INDIKATOR SNAP
// ===============================
function removePreviewCircle(){
    if(previewCircle){
        map.removeLayer(previewCircle);
        previewCircle = null;
    }
}

// ===============================
// 🔥 CLICK MAP (DRAWING)
// ===============================
map.on('click', function(e){
    if(isClosed){
        alert("Polygon sudah selesai! Silakan reset jika ingin menggambar ulang.");
        return;
    }

    let lat = e.latlng.lat, lng = e.latlng.lng, latlng = [lat, lng];

    if(points.length >= 3){
        let firstLatLng = L.latLng(points[0][1], points[0][0]);
        if(jarakMeter(latlng, firstLatLng) < 30){
            closePolygon();
            removePreviewCircle();
            return;
        }
    }

    points.push([lng, lat]);
    markers.push(L.marker([lat, lng]).addTo(map));
    updatePolygonDisplay(points);
    removePreviewCircle();
});

// ===============================
// 🔥 MOUSE MOVE (SNAP INDICATOR)
// ===============================
map.on('mousemove', function(e){
    if(isClosed || points.length < 3) return;
    let currentLatlng = e.latlng;
    let firstLatLng = L.latLng(points[0][1], points[0][0]);
    let distance = jarakMeter(currentLatlng, firstLatLng);

    if(distance < 30){
        if(!previewCircle){
            previewCircle = L.circle([points[0][1], points[0][0]], {
                radius: 30, color: 'red', fillOpacity: 0.2, dashArray: '3, 3'
            }).addTo(map);
        }
        map.getContainer().style.cursor = 'pointer';
    } else {
        removePreviewCircle();
        map.getContainer().style.cursor = '';
    }
});

// ===============================
// 🔥 TUTUP POLYGON
// ===============================
function closePolygon(){
    isClosed = true;
    let closedPoints = [...points];
    const first = points[0], last = points[points.length - 1];
    
    if(first[0] !== last[0] || first[1] !== last[1]){
        closedPoints.push([...first]);
    }

    updatePolygonDisplay(closedPoints, true);

    let geojson = {
        type: "Feature", properties: {},
        geometry: { type: "Polygon", coordinates: [closedPoints] }
    };

    document.getElementById('geojson').value = JSON.stringify(geojson);
    hitungLuas(geojson);

    alert("✅ Area berhasil ditutup!\nLuas: " + document.getElementById('luas').value + " Ha");
    if(polygon) map.fitBounds(polygon.getBounds());
}

// ===============================
// 🔥 RESET POLYGON
// ===============================
function resetPolygon(){
    if(polygon) map.removeLayer(polygon);
    if(polyline) map.removeLayer(polyline);
    removePreviewCircle();
    markers.forEach(m => map.removeLayer(m));

    points = []; markers = []; isClosed = false;
    polygon = null; polyline = null;

    // Reset semua form value & flag
    document.getElementById('geojson').value = "";
    document.getElementById('luas').value = "";
    document.getElementById('jarak_antar_pohon').value = "";
    document.getElementById('total_pohon').value = "";
    document.getElementById('pohon_produktif').value = "";
    document.getElementById('pohon_produktif').dataset.userEdited = "false";
    document.getElementById('pohon_tidak_produktif').value = "";
    
    console.log("🔄 Polygon direset");
}
</script>
@endsection