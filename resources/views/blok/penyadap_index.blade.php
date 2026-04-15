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
.map-thumb { height:160px; background:#f0f3f6; }
.blok-info { padding:14px 16px; }
</style>
@endpush

@section('content')

{{-- Stat ringkas --}}
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-map"></i></div>
        <p class="stat-value">{{ $blok->count() }}</p>
        <p class="stat-label">Total Blok Ditugaskan</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-check-circle"></i></div>
        <p class="stat-value">{{ $blok->filter(fn($b) => $b->blokPeta->first()?->status_mapping === 'disetujui')->count() }}</p>
        <p class="stat-label">Peta Disetujui</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-clock"></i></div>
        <p class="stat-value">{{ $blok->filter(fn($b) => $b->blokPeta->first()?->status_mapping === 'pending')->count() }}</p>
        <p class="stat-label">Peta Pending Validasi</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;">
    @forelse($blok as $b)
    @php $peta = $b->blokPeta->first(); @endphp
    <div class="blok-card">
        <div class="map-thumb" id="map-{{ $b->id }}"></div>
        <div class="blok-info">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                <div>
                    <div style="font-size:15px;font-weight:700;">{{ $b->nama_blok }}</div>
                    <div style="font-size:12px;color:#6b7a8d;">{{ $b->jenis_blok ?? 'Blok Produksi' }} • {{ number_format($b->luas,2) }} Ha</div>
                </div>
                @if($peta)
                    <span class="badge {{ match($peta->status_mapping) {
                        'disetujui' => 'badge-success',
                        'pending'   => 'badge-warning',
                        'ditolak'   => 'badge-danger',
                        default     => 'badge-gray'
                    } }}">{{ ucfirst($peta->status_mapping) }}</span>
                @else
                    <span class="badge badge-gray">Belum Dipetakan</span>
                @endif
            </div>
            <div style="display:flex;gap:6px;font-size:12px;color:#6b7a8d;margin-bottom:12px;">
                <span><i class="fas fa-tree"></i> {{ number_format($b->pohon_produktif) }} produktif</span>
                <span>•</span>
                <span><i class="fas fa-seedling"></i> {{ number_format($b->total_pohon) }} total</span>
            </div>
            <div style="display:flex;gap:8px;">
                <button onclick="openMapping({{ $b->id }}, '{{ $b->nama_blok }}')"
                        class="btn btn-primary btn-sm" style="flex:1;justify-content:center;">
                    <i class="fas fa-map-location-dot"></i>
                    {{ $peta && $peta->status_mapping !== 'ditolak' ? 'Update Peta' : 'Buat Peta' }}
                </button>
                <a href="{{ route('saya.blok.show', $b) }}" class="btn btn-outline btn-sm btn-icon">
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

{{-- Modal Mapping --}}
<div id="modal-mapping" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:700px;margin:16px;overflow:hidden;">
        <div style="padding:16px 20px;border-bottom:1px solid #e8ecf0;display:flex;justify-content:space-between;align-items:center;">
            <h4 style="margin:0;font-size:15px;" id="modal-title">Mapping Blok</h4>
            <button onclick="closeMapping()" style="background:none;border:none;font-size:18px;cursor:pointer;color:#6b7a8d;">×</button>
        </div>
        <div id="draw-map" style="height:400px;"></div>
        <div style="padding:14px 20px;border-top:1px solid #e8ecf0;display:flex;gap:10px;align-items:center;">
            <p style="font-size:12px;color:#6b7a8d;flex:1;margin:0;">
                <i class="fas fa-info-circle"></i> Gunakan tool di kiri peta untuk menggambar area blok
            </p>
            <button onclick="simpanPeta()" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Peta
            </button>
            <button onclick="closeMapping()" class="btn btn-outline">Batal</button>
        </div>
    </div>
</div>

<form id="form-peta" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="geojson" id="input-geojson">
</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
// Mini maps per blok
const blokData = @json($blok->map(fn($b) => [
    'id'      => $b->id,
    'geojson' => $b->blokPeta->first()?->geojson,
]));

blokData.forEach(b => {
    const el = document.getElementById('map-' + b.id);
    if (!el) return;
    const m = L.map('map-' + b.id, { zoomControl:false, dragging:false, scrollWheelZoom:false })
               .setView([-6.3, 107.1], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(m);
    if (b.geojson) {
        try {
            const layer = L.geoJSON(JSON.parse(b.geojson), {
                style: { color:'#1a7f4b', weight:2, fillOpacity:0.2 }
            }).addTo(m);
            m.fitBounds(layer.getBounds());
        } catch(e) {}
    }
});

// Draw map
let drawMap, drawnItems, currentBlokId;

function openMapping(blokId, nama) {
    currentBlokId = blokId;
    document.getElementById('modal-title').textContent = 'Mapping: ' + nama;
    document.getElementById('modal-mapping').style.display = 'flex';

    setTimeout(() => {
        if (drawMap) { drawMap.remove(); }
        drawMap = L.map('draw-map').setView([-6.3, 107.1], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(drawMap);
        drawnItems = new L.FeatureGroup().addTo(drawMap);

        const drawControl = new L.Control.Draw({
            draw: { polygon:true, polyline:false, rectangle:false, circle:false, marker:false, circlemarker:false },
            edit: { featureGroup: drawnItems }
        });
        drawMap.addControl(drawControl);
        drawMap.on(L.Draw.Event.CREATED, e => {
            drawnItems.clearLayers();
            drawnItems.addLayer(e.layer);
        });
    }, 100);
}

function closeMapping() {
    document.getElementById('modal-mapping').style.display = 'none';
    if (drawMap) { drawMap.remove(); drawMap = null; }
}

function simpanPeta() {
    const geojson = JSON.stringify(drawnItems.toGeoJSON());
    if (!drawnItems.getLayers().length) {
        alert('Gambar area blok terlebih dahulu!');
        return;
    }
    const form = document.getElementById('form-peta');
    form.action = '/saya/blok/' + currentBlokId + '/peta';
    document.getElementById('input-geojson').value = geojson;
    form.submit();
}
</script>
@endpush