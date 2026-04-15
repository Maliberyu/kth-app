{{-- resources/views/blok/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Blok — '.$blok->nama_blok)
@section('page_title', 'Detail Blok: '.$blok->nama_blok)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #map { height: 300px; border-radius: 10px; border: 2px solid #e0e0e0; }
    .info-row td { padding: 8px 0; border-bottom: 1px dashed #f0f0f0; }
    .info-row td:first-child { color: #6b7a8d; width: 45%; font-size: 13px; }
    .badge-status { font-size: 11px; padding: 4px 10px; }
    pre.geojson-preview { 
        background:#f8f9fa; padding:10px; border-radius:5px; 
        overflow-x:auto; margin-top:8px; max-height:150px; font-size:11px;
    }
</style>
@endpush

@section('content')
<div class="grid grid-2" style="margin-bottom:20px;align-items:start;">
    
    {{-- 📋 INFO BLOK --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-info-circle" style="color:#1a7f4b;margin-right:8px;"></i>Info Blok</h3>
            <a href="{{ route('blok.edit', $blok) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-pen"></i> Edit
            </a>
        </div>
        <div class="card-body">
            <table class="info-row" style="width:100%;font-size:13.5px;">
                <tr>
                    <td>Nama Blok</td>
                    <td><strong>{{ $blok->nama_blok }}</strong></td>
                </tr>
                <tr>
                    <td>Jenis</td>
                    <td>{{ $blok->jenis_blok ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Luas Area</td>
                    <td><strong class="text-success">{{ number_format($blok->luas, 2) }} Ha</strong></td>
                </tr>
                <tr>
                    <td>Jarak Tanam</td>
                    <td>{{ $blok->jarak_antar_pohon ? $blok->jarak_antar_pohon.' meter' : '-' }}</td>
                </tr>
                <tr>
                    <td>Total Pohon</td>
                    <td><strong>{{ number_format($blok->total_pohon ?? 0) }} pohon</strong></td>
                </tr>
                <tr>
                    <td>🟢 Produktif</td>
                    <td><span class="badge badge-success badge-status">{{ number_format($blok->pohon_produktif ?? 0) }}</span></td>
                </tr>
                <tr>
                    <td>🔴 Tidak Produktif</td>
                    <td><span class="badge badge-danger badge-status">{{ number_format($blok->pohon_tidak_produktif ?? 0) }}</span></td>
                </tr>
            </table>
            
            {{-- 🔍 GeoJSON Preview (Collapsible) --}}
            @php
                $petaForPreview = $blok->blokPeta()->whereIn('status_mapping', ['disetujui', 'pending'])->latest()->first();
            @endphp
            @if($petaForPreview && $petaForPreview->geojson)
            <details style="margin-top:15px;font-size:12px;">
                <summary style="cursor:pointer;color:#1a7f4b;font-weight:500;">
                    <i class="fas fa-code"></i> Lihat Data GeoJSON
                </summary>
                <pre class="geojson-preview">
{{ json_encode(json_decode($petaForPreview->geojson), JSON_PRETTY_PRINT) }}
                </pre>
            </details>
            @endif
        </div>
    </div>

    {{-- 🗺️ PETA BLOK --}}
    <div class="card">
        @php 
            $petaTerbaru = $blok->blokPeta()
                                ->whereIn('status_mapping', ['disetujui', 'pending'])
                                ->latest()
                                ->first();
            $geojson = $petaTerbaru ? json_decode($petaTerbaru->geojson, true) : null;
        @endphp
        
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-map-location-dot" style="color:#1a7f4b;margin-right:8px;"></i>Peta Area Blok</h3>
            @if($petaTerbaru)
                <span class="badge {{ $petaTerbaru->status_mapping === 'disetujui' ? 'badge-success' : ($petaTerbaru->status_mapping === 'pending' ? 'badge-warning' : 'badge-danger') }} badge-status">
                    {{ ucfirst($petaTerbaru->status_mapping) }}
                </span>
            @else
                <span class="badge badge-secondary badge-status"><i class="fas fa-exclamation-circle"></i> Belum Ada Area</span>
            @endif
        </div>
        
        <div id="map"></div>
        
        {{-- Map Controls --}}
        <div style="padding:12px 16px;border-top:1px solid #f0f3f6;display:flex;gap:8px;flex-wrap:wrap;">
            <button onclick="zoomToBlok()" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-search-location"></i> Zoom ke Area
            </button>
            @if($geojson)
            <button onclick="downloadGeoJSON()" class="btn btn-sm btn-outline-success">
                <i class="fas fa-download"></i> Download GeoJSON
            </button>
            @endif
            <small class="text-muted" style="margin-left:auto;display:flex;align-items:center;">
                <i class="fas fa-mouse-pointer"></i> Scroll/geser untuk eksplor
            </small>
        </div>
        
        {{-- Form Validasi (Hanya untuk admin & status pending) --}}
        @if($petaTerbaru && $petaTerbaru->status_mapping === 'pending' && auth()->user()->role === 'admin')
        <div style="padding:12px 16px;border-top:1px solid #f0f3f6;background:#fff9e6;">
            <form method="POST" action="{{ route('blok.peta.validasi', $petaTerbaru) }}" 
                  style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                @csrf @method('PATCH')
                <select name="status_mapping" class="form-control form-select" style="width:140px;">
                    <option value="disetujui">✅ Setujui</option>
                    <option value="ditolak">❌ Tolak</option>
                </select>
                <input type="text" name="catatan" class="form-control" placeholder="Catatan..." style="flex:1;min-width:120px;">
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- 👥 PENUGASAN PENYADAP --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="fas fa-users" style="color:#1a7f4b;margin-right:8px;"></i>Penyadap Ditugaskan</h3>
        <span class="badge badge-primary">{{ $blok->penyadap->count() }} Orang</span>
    </div>
    
    {{-- Form Tambah Penyadap --}}
    <div style="padding:16px 20px;border-bottom:1px solid #f0f3f6;background:#fafbfc;">
        <form method="POST" action="{{ route('blok.tugaskan', $blok) }}" 
              style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div style="flex:1;min-width:200px;">
                <label class="form-label" style="font-size:12px;">Tambah Penyadap</label>
                <select name="penyadap_id" class="form-control form-select" required>
                    <option value="">— Pilih Penyadap —</option>
                    @foreach($penyadapTersedia as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->nik }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tugaskan
            </button>
        </form>
    </div>
    
    {{-- Tabel Penyadap --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Penyadap</th>
                    <th>NIK</th>
                    <th>No. HP</th>
                    <th style="width:80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blok->penyadap as $p)
                <tr>
                    <td><strong>{{ $p->nama }}</strong></td>
                    <td>{{ $p->nik ?? '-' }}</td>
                    <td>{{ $p->no_hp ?? '-' }}</td>
                    <td>
                        <form method="POST" action="{{ route('blok.hapus-tugas', [$blok, $p]) }}"
                              onsubmit="return confirm('Hapus penugasan {{ $p->nama }} dari blok ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus Tugas">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:#adb5bd;padding:25px;">
                        <i class="fas fa-user-slash" style="margin-right:8px;"></i>
                        Belum ada penyadap ditugaskan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ===============================
// 🔥 INIT MAP
// ===============================
const map = L.map('map').setView([-7.324426, 108.0145812], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// ===============================
// 🔥 LOAD POLYGON DARI blok_peta
// ===============================
let blokLayer = null;

@php
    $petaTerbaru = $blok->blokPeta()
                        ->whereIn('status_mapping', ['disetujui', 'pending'])
                        ->latest()
                        ->first();
    $geojson = $petaTerbaru ? json_decode($petaTerbaru->geojson, true) : null;
@endphp

@if($geojson && isset($geojson['geometry']['coordinates']))
    const blokGeoJSON = @json($geojson);
    
    blokLayer = L.geoJSON(blokGeoJSON, {
        style: { 
            color: '#1a7f4b', 
            weight: 3, 
            fillOpacity: 0.25,
            dashArray: null
        },
        onEachFeature: function(feature, layer) {
            layer.bindPopup(`
                <strong>{{ $blok->nama_blok }}</strong><br>
                Luas: {{ number_format($blok->luas, 2) }} Ha<br>
                Pohon: {{ number_format($blok->total_pohon ?? 0) }}
            `);
        }
    }).addTo(map);
    
    // Auto zoom ke area blok
    map.fitBounds(blokLayer.getBounds(), {padding: [30, 30]});
@else
    // Tambah marker default kalau belum ada polygon
    L.marker([-7.324426, 108.0145812])
        .addTo(map)
        .bindPopup('Belum ada area yang direkam untuk blok ini.')
        .openPopup();
@endif

// ===============================
// 🔥 FUNGSI ZOOM TO BLOK
// ===============================
function zoomToBlok(){
    @if($geojson && isset($geojson['geometry']['coordinates']))
        if(blokLayer){
            map.fitBounds(blokLayer.getBounds(), {padding: [30, 30]});
        }
    @else
        map.setView([-7.324426, 108.0145812], 14);
        alert('⚠️ Belum ada area polygon untuk di-zoom.');
    @endif
}

// ===============================
// 🔥 DOWNLOAD GEOJSON
// ===============================
function downloadGeoJSON(){
    @if($geojson)
        const dataStr = "data:text/json;charset=utf-8," + 
            encodeURIComponent(JSON.stringify(blokGeoJSON, null, 2));
        const dlAnchor = document.createElement('a');
        dlAnchor.setAttribute("href", dataStr);
        dlAnchor.setAttribute("download", "{{ str_replace(' ', '_', $blok->nama_blok) }}_area.geojson");
        dlAnchor.click();
    @else
        alert('⚠️ Tidak ada data GeoJSON untuk diunduh.');
    @endif
}
</script>
@endpush