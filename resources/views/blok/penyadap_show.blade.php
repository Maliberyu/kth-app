{{-- resources/views/blok/penyadap_show.blade.php --}}
@extends('layouts.app')

@section('title', $blok->nama_blok . ' — Blok Saya')
@section('page_title', '📍 ' . $blok->nama_blok)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #map { height: 400px; border-radius: 10px; border: 2px solid #e0e0e0; background: #f8f9fa; }
    .info-card { background: #f8f9fa; padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; }
    .badge-status { font-size: 11px; padding: 4px 10px; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-purple-600">
        
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800 mb-0">📍 {{ $blok->nama_blok }}</h1>
            <a href="{{ route('saya.blok') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Info Blok --}}
        <div class="grid md:grid-cols-2 gap-4 mb-6">
            <div class="info-card">
                <p class="text-sm text-gray-600 mb-1">Jenis Blok</p>
                <p class="font-semibold">{{ $blok->jenis_blok ?? '-' }}</p>
            </div>
            <div class="info-card">
                <p class="text-sm text-gray-600 mb-1">Luas Area</p>
                <p class="font-semibold text-success">{{ number_format($blok->luas, 2) }} Ha</p>
            </div>
            <div class="info-card">
                <p class="text-sm text-gray-600 mb-1">Total Pohon</p>
                <p class="font-semibold">{{ number_format($blok->total_pohon ?? 0) }} pohon</p>
            </div>
            <div class="info-card">
                <p class="text-sm text-gray-600 mb-1">🟢 Produktif</p>
                <p class="font-semibold text-success">{{ number_format($blok->pohon_produktif ?? 0) }}</p>
            </div>
            <div class="info-card md:col-span-2">
                <p class="text-sm text-gray-600 mb-1">🔴 Tidak Produktif</p>
                <p class="font-semibold text-danger">{{ number_format($blok->pohon_tidak_produktif ?? 0) }}</p>
            </div>
        </div>

        {{-- 🗺️ PETA --}}
        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 mb-2">🗺️ Peta Blok</h3>
            
            {{-- ✅ PASS GEOJSON VIA DATA ATTRIBUTE (AMAN!) --}}
            @php
                $petaSaya = $blok->blokPeta()->where('dibuat_oleh', auth()->id())->latest()->first();
                $petaDisetujui = $blok->blokPeta()->where('status_mapping', 'disetujui')->latest()->first();
                $geojsonToShow = $petaDisetujui ? $petaDisetujui->geojson : ($petaSaya ? $petaSaya->geojson : null);
            @endphp
            
            <div id="map" 
                 data-geojson="{{ $geojsonToShow ?? '' }}"
                 data-center="-7.324426,108.0145812">
            </div>
            
            {{-- Status Badge --}}
            <div class="mt-2">
                @if($petaDisetujui)
                    <span class="badge badge-success badge-status"><i class="fas fa-check-circle"></i> Area Resmi</span>
                @elseif($petaSaya)
                    <span class="badge {{ $petaSaya->status_mapping === 'pending' ? 'badge-warning' : 'badge-danger' }} badge-status">
                        {{ ucfirst($petaSaya->status_mapping) }}
                    </span>
                @else
                    <span class="badge badge-secondary badge-status"><i class="fas fa-exclamation-circle"></i> Belum Ada Area</span>
                @endif
            </div>
            
            {{-- Info Validasi --}}
            @if($petaSaya && $petaSaya->status_mapping !== 'disetujui')
                <small class="text-muted d-block mt-2">
                    @if($petaSaya->status_mapping === 'pending')
                        <i class="fas fa-clock"></i> Menunggu validasi admin...
                    @elseif($petaSaya->status_mapping === 'ditolak' && $petaSaya->catatan)
                        <i class="fas fa-times-circle"></i> Ditolak: {{ $petaSaya->catatan }}
                    @endif
                </small>
            @endif
        </div>

        {{-- Mapping Controls --}}
        <div class="mb-4">
            <button type="button" id="btn-mapping" class="btn btn-primary" onclick="toggleMapping()">
                <i class="fas fa-pen"></i> <span id="mapping-text">Mulai Mapping</span>
            </button>
            <button type="button" id="btn-save" class="btn btn-success ml-2" onclick="saveMapping()" style="display:none;">
                <i class="fas fa-save"></i> Simpan Peta
            </button>
            <button type="button" id="btn-reset" class="btn btn-outline ml-2" onclick="resetMapping()">
                <i class="fas fa-redo"></i> Reset
            </button>
        </div>

        {{-- Form Submit --}}
        <form id="form-mapping" method="POST" action="{{ url('saya/blok', $blok->id) }}/peta" style="display:none;">
            @csrf
            <input type="hidden" name="geojson" id="input-geojson">
        </form>

        <small class="text-muted d-block">
            <i class="fas fa-lightbulb"></i> 
            <strong>Cara Mapping:</strong> Klik di peta untuk tambah titik. Dekatkan ke titik awal untuk menutup area.
        </small>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script>
// ===============================
// 🔥 INIT MAP - Baca dari data attribute
// ===============================
const mapEl = document.getElementById('map');
const geojsonStr = mapEl.getAttribute('data-geojson');
const centerStr = mapEl.getAttribute('data-center') || '-7.324426,108.0145812';
const center = centerStr.split(',').map(Number);

const map = L.map('map').setView(center, 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

// Load existing polygon jika ada
if (geojsonStr && geojsonStr.length > 10) {
    try {
        const geo = JSON.parse(geojsonStr);
        L.geoJSON(geo, {
            style: { color: '#1a7f4b', weight: 2, fillOpacity: 0.25 }
        }).addTo(map).bindPopup("Area Blok");
        map.fitBounds(L.geoJSON(geo).getBounds(), {padding: [30, 30]});
    } catch(e) {
        console.warn('Gagal load geojson:', e);
    }
}

// ===============================
// 🔥 DRAWING VARIABLES
// ===============================
let points = [];
let markers = [];
let polygon = null;
let polyline = null;
let previewCircle = null;
let isMapping = false;
let isClosed = false;

// ===============================
// 🔥 FUNGSI UTILS
// ===============================
function hitungLuas(geojson) {
    try { return (turf.area(geojson) / 10000).toFixed(2); } 
    catch(e) { return 0; }
}

function jarakMeter(a, b) { return map.distance(a, b); }

function updatePolygonDisplay(pts, closed) {
    const leafletPoints = pts.map(p => [p[1], p[0]]);
    if (polyline) map.removeLayer(polyline);
    if (polygon) map.removeLayer(polygon);
    
    polyline = L.polyline(leafletPoints, {color: '#6f42c1', dashArray: closed ? null : '5, 5'}).addTo(map);
    if (pts.length >= 3) {
        polygon = L.polygon(leafletPoints, {color: '#6f42c1', fillOpacity: 0.3}).addTo(map);
    }
}

function removePreviewCircle() {
    if (previewCircle) { map.removeLayer(previewCircle); previewCircle = null; }
}

// ===============================
// 🔥 TOGGLE MAPPING MODE
// ===============================
function toggleMapping() {
    isMapping = !isMapping;
    
    if (isMapping) {
        // Aktifkan mode mapping
        document.getElementById('mapping-text').textContent = 'Batal';
        document.getElementById('btn-save').style.display = 'inline-block';
        document.getElementById('btn-reset').style.display = 'inline-block';
        map.getContainer().style.cursor = 'crosshair';
        alert("🎯 Mode Mapping Aktif!\n• Klik di peta untuk tambah titik\n• Dekatkan ke titik awal untuk menutup area");
    } else {
        // Nonaktifkan
        resetMapping();
        document.getElementById('mapping-text').textContent = 'Mulai Mapping';
        document.getElementById('btn-save').style.display = 'none';
        document.getElementById('btn-reset').style.display = 'none';
        map.getContainer().style.cursor = '';
    }
}

function resetMapping() {
    if (polygon) map.removeLayer(polygon);
    if (polyline) map.removeLayer(polyline);
    removePreviewCircle();
    markers.forEach(m => map.removeLayer(m));
    
    points = []; markers = []; isClosed = false;
}

// ===============================
// 🔥 SAVE MAPPING
// ===============================
function saveMapping() {
    if (points.length < 3) {
        alert("⚠️ Minimal 3 titik diperlukan!"); return;
    }
    if (!isClosed) {
        alert("⚠️ Tutup dulu polygon-nya (klik dekat titik awal)!"); return;
    }
    
    // Buat GeoJSON tertutup
    let closedPoints = [...points];
    const first = points[0], last = points[points.length - 1];
    if (first[0] !== last[0] || first[1] !== last[1]) {
        closedPoints.push([...first]);
    }
    
    const geojson = {
        type: "Feature", properties: {},
        geometry: { type: "Polygon", coordinates: [closedPoints] }
    };
    
    document.getElementById('input-geojson').value = JSON.stringify(geojson);
    
    if (confirm("✅ Simpan peta untuk blok ini?\n\nStatus: Menunggu validasi admin.")) {
        document.getElementById('form-mapping').submit();
    }
}

// ===============================
// 🔥 MAP CLICK (DRAWING)
// ===============================
map.on('click', function(e) {
    if (!isMapping || isClosed) return;
    
    const lat = e.latlng.lat, lng = e.latlng.lng, latlng = [lat, lng];
    
    // Auto-close check
    if (points.length >= 3) {
        const firstLatLng = L.latLng(points[0][1], points[0][0]);
        if (jarakMeter(latlng, firstLatLng) < 30) {
            closePolygon();
            removePreviewCircle();
            return;
        }
    }
    
    // Add new point
    points.push([lng, lat]);
    markers.push(L.marker([lat, lng], {
        icon: L.divIcon({className: 'custom-marker', html: '📍', iconSize: [20, 20]})
    }).addTo(map));
    
    updatePolygonDisplay(points, false);
    removePreviewCircle();
});

// ===============================
// 🔥 MOUSE MOVE (SNAP INDICATOR)
// ===============================
map.on('mousemove', function(e) {
    if (!isMapping || isClosed || points.length < 3) return;
    
    const current = e.latlng;
    const first = L.latLng(points[0][1], points[0][0]);
    const distance = jarakMeter(current, first);
    
    if (distance < 30) {
        if (!previewCircle) {
            previewCircle = L.circle([points[0][1], points[0][0]], {
                radius: 30, color: '#6f42c1', fillOpacity: 0.2, dashArray: '3, 3'
            }).addTo(map);
        }
        map.getContainer().style.cursor = 'pointer';
    } else {
        removePreviewCircle();
        map.getContainer().style.cursor = '';
    }
});

// ===============================
// 🔥 CLOSE POLYGON
// ===============================
function closePolygon() {
    isClosed = true;
    let closedPoints = [...points];
    const first = points[0], last = points[points.length - 1];
    
    if (first[0] !== last[0] || first[1] !== last[1]) {
        closedPoints.push([...first]);
    }
    
    updatePolygonDisplay(closedPoints, true);
    
    const geojson = {
        type: "Feature", properties: {},
        geometry: { type: "Polygon", coordinates: [closedPoints] }
    };
    
    const luas = hitungLuas(geojson);
    alert(`✅ Area tertutup!\n📐 Luas: ${luas} Ha\nKlik "Simpan Peta" untuk submit.`);
    
    if (polygon) map.fitBounds(polygon.getBounds());
}
</script>
@endpush