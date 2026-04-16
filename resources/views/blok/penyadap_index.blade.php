{{-- resources/views/blok/penyadap_index.blade.php --}}
@extends('layouts.app')
@section('title', 'Blok Saya')
@section('page_title', 'Blok Saya')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css"/>
<style>
.blok-card {
    background:#fff; border:1px solid #e8ecf0; border-radius:12px; overflow:hidden;
    transition: box-shadow .2s;
}
.blok-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
.map-thumb { height:160px; background:#f0f3f6; position:relative; }
.blok-info { padding:14px 16px; }
.badge { font-size:11px; padding:4px 10px; }
</style>
@endpush

@section('content')

{{-- 📊 Stat Ringkas --}}
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-map"></i></div>
        <p class="stat-value">{{ $blok->count() }}</p>
        <p class="stat-label">Total Blok Ditugaskan</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-check-circle"></i></div>
        <p class="stat-value">
            {{ $blok->filter(fn($b) => $b->blokPeta->first()?->status_mapping === 'disetujui')->count() }}
        </p>
        <p class="stat-label">Peta Disetujui</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-clock"></i></div>
        <p class="stat-value">
            {{ $blok->filter(fn($b) => $b->blokPeta->first()?->status_mapping === 'pending')->count() }}
        </p>
        <p class="stat-label">Peta Pending</p>
    </div>
</div>

{{-- 🗂️ List Blok --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
    @forelse($blok as $b)
    @php $peta = $b->blokPeta->first(); @endphp
    <div class="blok-card">
        {{-- Mini Map Thumbnail --}}
      {{-- 🔥 MINI MAP DENGAN DATA ATTRIBUTE --}}
        <div class="map-thumb" 
            id="map-{{ $b->id }}" 
            data-geojson="{{ $peta?->geojson ?? '' }}"
            data-center="-7.324426,108.0145812">
        </div>
        <div class="blok-info">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                <div>
                    <div style="font-size:15px;font-weight:700;">{{ $b->nama_blok }}</div>
                    <div style="font-size:12px;color:#6b7a8d;">
                        {{ $b->jenis_blok ?? 'Blok Produksi' }} • {{ number_format($b->luas,2) }} Ha
                    </div>
                </div>
                @if($peta)
                    <span class="badge {{ match($peta->status_mapping) {
                        'disetujui' => 'badge-success',
                        'pending'   => 'badge-warning', 
                        'ditolak'   => 'badge-danger',
                        default     => 'badge-secondary'
                    } }}">{{ ucfirst($peta->status_mapping) }}</span>
                @else
                    <span class="badge badge-secondary">Belum Dipetakan</span>
                @endif
            </div>
            
            <div style="display:flex;gap:6px;font-size:12px;color:#6b7a8d;margin-bottom:12px;">
                <span><i class="fas fa-tree"></i> {{ number_format($b->pohon_produktif ?? 0) }} produktif</span>
                <span>•</span>
                <span><i class="fas fa-seedling"></i> {{ number_format($b->total_pohon ?? 0) }} total</span>
            </div>
            
            <div style="display:flex;gap:8px;">
                {{-- ✅ Tombol Mapping pakai data attribute --}}
                <button onclick="openMapping({{ $b->id }}, '{{ addslashes($b->nama_blok) }}')"
                        class="btn btn-primary btn-sm" style="flex:1;justify-content:center;"
                        data-blok-id="{{ $b->id }}">
                    <i class="fas fa-map-location-dot"></i>
                    {{ $peta && $peta->status_mapping !== 'ditolak' ? 'Update Peta' : 'Buat Peta' }}
                </button>
                <a href="{{ route('saya.blok.show', $b) }}" class="btn btn-outline btn-sm btn-icon" title="Detail">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:48px;color:#adb5bd;">
        <i class="fas fa-map" style="font-size:48px;display:block;margin-bottom:12px;"></i>
        <p>Belum ada blok yang ditugaskan ke Anda.</p>
    </div>
    @endforelse
</div>

{{-- 🗺️ Modal Mapping --}}
<div id="modal-mapping" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:700px;margin:16px;overflow:hidden;max-height:90vh;display:flex;flex-direction:column;">
        <div style="padding:16px 20px;border-bottom:1px solid #e8ecf0;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:15px;" id="modal-title">Mapping Blok</h4>
            <button onclick="closeMapping()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#6b7a8d;line-height:1;">&times;</button>
        </div>
        
        <div id="draw-map" style="flex:1;min-height:400px;"></div>
        
        <div style="padding:14px 20px;border-top:1px solid #e8ecf0;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <p style="font-size:12px;color:#6b7a8d;flex:1;margin:0;">
                <i class="fas fa-info-circle"></i> Gunakan tool polygon di kiri untuk gambar area
            </p>
            <button onclick="resetDraw()" class="btn btn-outline btn-sm">
                <i class="fas fa-redo"></i> Reset
            </button>
            <button onclick="simpanPeta()" class="btn btn-primary btn-sm">
                <i class="fas fa-save"></i> Simpan Peta
            </button>
            <button onclick="closeMapping()" class="btn btn-outline btn-sm">Batal</button>
        </div>
    </div>
</div>

{{-- ✅ Form Submit dengan Route Laravel --}}
<form id="form-peta" method="POST" style="display:none;" action="{{ route('saya.blok.peta.store', '__BLOCK_ID__') }}">
    @csrf
    <input type="hidden" name="geojson" id="input-geojson">
</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
// ===============================
// 🔥 AMBIL URL DARI META TAG (AMAN!)
// ===============================
const APP_URL = document.querySelector('meta[name="app-url"]')?.content || '';
const BLOK_PETA_BASE = document.querySelector('meta[name="blok-peta-route"]')?.content || '/saya/blok';

// ===============================
// 🔥 MINI MAPS: Baca dari data attribute
// ===============================
document.addEventListener('DOMContentLoaded', function() {
    const mapThumbs = document.querySelectorAll('[id^="map-"]');
    
    mapThumbs.forEach(function(el) {
        const blokId = el.id.replace('map-', '');
        const geojsonStr = el.getAttribute('data-geojson');
        const centerStr = el.getAttribute('data-center') || '-7.324426,108.0145812';
        const center = centerStr.split(',').map(Number);
        
        // Skip kalau element sudah di-init
        if (el._leaflet_id) return;
        
        const m = L.map(el, { 
            zoomControl:false, dragging:false, scrollWheelZoom:false,
            touchZoom:false, doubleClickZoom:false, boxZoom:false
        }).setView(center, 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(m);
        
        if (geojsonStr && geojsonStr.length > 10) {
            try {
                const geo = JSON.parse(geojsonStr);
                const layer = L.geoJSON(geo, {
                    style: { color:'#1a7f4b', weight:2, fillOpacity:0.2 }
                }).addTo(m);
                m.fitBounds(layer.getBounds(), {padding: [20, 20]});
            } catch(e) {
                console.warn('Gagal load geojson blok #' + blokId, e);
            }
        }
    });
});

// ===============================
// 🔥 DRAW MAP (MODAL) - GLOBAL VARS
// ===============================
let drawMap = null;
let drawnItems = null;
let currentBlokId = null;
let drawControl = null;

function openMapping(blokId, nama) {
    currentBlokId = blokId;
    document.getElementById('modal-title').textContent = 'Mapping: ' + nama;
    document.getElementById('modal-mapping').style.display = 'flex';
    
    setTimeout(function() { initDrawMap(); }, 150);
}

function initDrawMap() {
    if (drawMap) {
        if (drawControl) drawMap.removeControl(drawControl);
        drawMap.remove();
    }
    if (drawnItems) drawnItems.clearLayers();
    
    drawMap = L.map('draw-map').setView([-7.324426, 108.0145812], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(drawMap);
    
    drawnItems = new L.FeatureGroup();
    drawMap.addLayer(drawnItems);
    
    drawControl = new L.Control.Draw({
        draw: {
            polygon: { allowIntersection: false, showArea: true, shapeOptions: { color: '#6f42c1' } },
            polyline: false, rectangle: false, circle: false, marker: false, circlemarker: false
        },
        edit: { featureGroup: drawnItems, remove: true }
    });
    drawMap.addControl(drawControl);
    
    drawMap.on(L.Draw.Event.CREATED, function(e) {
        drawnItems.clearLayers();
        drawnItems.addLayer(e.layer);
    });
}

function closeMapping() {
    document.getElementById('modal-mapping').style.display = 'none';
    if (drawMap) {
        if (drawControl) drawMap.removeControl(drawControl);
        drawMap.remove(); drawMap = null;
    }
    if (drawnItems) { drawnItems.clearLayers(); drawnItems = null; }
    currentBlokId = null;
}

function resetDraw() { if (drawnItems) drawnItems.clearLayers(); }

function simpanPeta() {
    if (!drawnItems || drawnItems.getLayers().length === 0) {
        alert('⚠️ Gambar area blok terlebih dahulu!'); return;
    }
    
    const geojson = JSON.stringify(drawnItems.toGeoJSON());
    const form = document.getElementById('form-peta');
    
    // ✅ Build URL dari meta tag + ID (tanpa Blade di JS!)
    form.action = BLOK_PETA_BASE + '/' + currentBlokId + '/peta';
    
    document.getElementById('input-geojson').value = geojson;
    
    if (confirm('✅ Simpan peta untuk blok ini?')) { form.submit(); }
}

// Close modal klik outside
document.getElementById('modal-mapping')?.addEventListener('click', function(e) {
    if (e.target === this) closeMapping();
});

// ESC to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('modal-mapping')?.style.display === 'flex') {
        closeMapping();
    }
});
</script>
@endpush