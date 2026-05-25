@extends('layouts.app')
@section('title', 'Edit Kegiatan Luar Ruangan')
@section('page_title', 'Edit Kegiatan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #map { height: 280px; border-radius: 10px; border: 1.5px solid #d1d9e0; z-index:1; }
    .coord-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
</style>
@endpush

@section('content')
<div style="max-width:740px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-pen" style="color:#1a7f4b;margin-right:8px;"></i>Edit: {{ $kegiatanLuar->nama_kegiatan }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('kegiatan-luar.update', $kegiatanLuar) }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Judul Kegiatan <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_kegiatan" class="form-control"
                           value="{{ old('nama_kegiatan', $kegiatanLuar->nama_kegiatan) }}">
                    @error('nama_kegiatan')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tempat / Lokasi <span style="color:#d93025;">*</span></label>
                    <input type="text" name="lokasi" class="form-control"
                           value="{{ old('lokasi', $kegiatanLuar->lokasi) }}">
                    @error('lokasi')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Waktu Mulai <span style="color:#d93025;">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai" class="form-control"
                               value="{{ old('tanggal_mulai', $kegiatanLuar->tanggal_mulai->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Waktu Selesai</label>
                        <input type="datetime-local" name="tanggal_selesai" class="form-control"
                               value="{{ old('tanggal_selesai', $kegiatanLuar->tanggal_selesai?->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control form-select">
                            <option value="aktif"   {{ old('status',$kegiatanLuar->status)==='aktif'   ? 'selected':'' }}>Aktif</option>
                            <option value="selesai" {{ old('status',$kegiatanLuar->status)==='selesai' ? 'selected':'' }}>Selesai</option>
                            <option value="batal"   {{ old('status',$kegiatanLuar->status)==='batal'   ? 'selected':'' }}>Batal</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Uraian Kegiatan</label>
                    <textarea name="uraian_kegiatan" class="form-control" rows="6">{{ old('uraian_kegiatan', $kegiatanLuar->uraian_kegiatan) }}</textarea>
                </div>

                {{-- Koordinat --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt" style="color:#1a7f4b;margin-right:4px;"></i>
                        Titik Koordinat
                        <button type="button" onclick="gunakanLokasiSaya()" class="btn btn-outline btn-sm" style="margin-left:8px; vertical-align:middle;">
                            <i class="fas fa-crosshairs"></i> Lokasi Saya
                        </button>
                    </label>
                    <div id="map" style="margin-bottom:12px;"></div>
                    <div class="coord-row">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Latitude</label>
                            <input type="number" name="latitude" id="latInput" class="form-control"
                                   value="{{ old('latitude', $kegiatanLuar->latitude) }}" step="any">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Longitude</label>
                            <input type="number" name="longitude" id="lngInput" class="form-control"
                                   value="{{ old('longitude', $kegiatanLuar->longitude) }}" step="any">
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="{{ route('kegiatan-luar.show', $kegiatanLuar) }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const initLat = {{ $kegiatanLuar->latitude ?? -2.5 }};
    const initLng = {{ $kegiatanLuar->longitude ?? 118.0 }};
    const hasCoord = {{ ($kegiatanLuar->latitude && $kegiatanLuar->longitude) ? 'true' : 'false' }};

    const map = L.map('map').setView([initLat, initLng], hasCoord ? 13 : 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap' }).addTo(map);

    const latInput = document.getElementById('latInput');
    const lngInput = document.getElementById('lngInput');
    let marker = hasCoord ? L.marker([initLat, initLng]).addTo(map) : null;

    map.on('click', e => setMarker(e.latlng.lat, e.latlng.lng));

    function setMarker(lat, lng) {
        lat = parseFloat(parseFloat(lat).toFixed(8));
        lng = parseFloat(parseFloat(lng).toFixed(8));
        if (marker) marker.setLatLng([lat, lng]);
        else marker = L.marker([lat, lng]).addTo(map);
        latInput.value = lat;
        lngInput.value = lng;
    }

    [latInput, lngInput].forEach(el => {
        el.addEventListener('change', () => {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) { setMarker(lat, lng); map.setView([lat, lng], 13); }
        });
    });

    function gunakanLokasiSaya() {
        if (!navigator.geolocation) return alert('Browser tidak mendukung geolokasi.');
        navigator.geolocation.getCurrentPosition(
            pos => { setMarker(pos.coords.latitude, pos.coords.longitude); map.setView([pos.coords.latitude, pos.coords.longitude], 15); },
            () => alert('Gagal mendapatkan lokasi.')
        );
    }
</script>
@endpush
