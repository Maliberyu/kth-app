@extends('layouts.app')
@section('title', 'Buat Kegiatan Luar Ruangan')
@section('page_title', 'Buat Kegiatan Luar Ruangan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #map { height: 280px; border-radius: 10px; border: 1.5px solid #d1d9e0; z-index: 1; }
    .coord-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
</style>
@endpush

@section('content')
<div style="max-width:740px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-tree" style="color:#1a7f4b;margin-right:8px;"></i>Form Kegiatan Luar Ruangan</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('kegiatan-luar.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Judul Kegiatan <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_kegiatan" class="form-control @error('nama_kegiatan') is-error @enderror"
                           value="{{ old('nama_kegiatan') }}" placeholder="Contoh: Patroli Kawasan Blok A">
                    @error('nama_kegiatan')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tempat / Lokasi <span style="color:#d93025;">*</span></label>
                    <input type="text" name="lokasi" class="form-control @error('lokasi') is-error @enderror"
                           value="{{ old('lokasi') }}" placeholder="Contoh: Hutan Blok A, Kec. X">
                    @error('lokasi')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Waktu Mulai <span style="color:#d93025;">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai" class="form-control"
                               value="{{ old('tanggal_mulai') }}">
                        @error('tanggal_mulai')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Waktu Selesai</label>
                        <input type="datetime-local" name="tanggal_selesai" class="form-control"
                               value="{{ old('tanggal_selesai') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Uraian Kegiatan</label>
                    <textarea name="uraian_kegiatan" class="form-control" rows="5"
                              placeholder="Deskripsikan kegiatan secara lengkap: tujuan, proses, hasil, temuan, dll.">{{ old('uraian_kegiatan') }}</textarea>
                </div>

                {{-- Titik Koordinat --}}
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt" style="color:#1a7f4b;margin-right:4px;"></i>
                        Titik Koordinat
                        <button type="button" onclick="gunakanLokasiSaya()" class="btn btn-outline btn-sm" style="margin-left:8px; vertical-align:middle;">
                            <i class="fas fa-crosshairs"></i> Gunakan Lokasi Saya
                        </button>
                    </label>
                    <div id="map" style="margin-bottom:12px;"></div>
                    <p style="font-size:12px; color:#6b7a8d; margin-bottom:8px;">
                        <i class="fas fa-info-circle"></i> Klik pada peta untuk menentukan titik lokasi, atau isi koordinat manual.
                    </p>
                    <div class="coord-row">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Latitude</label>
                            <input type="number" name="latitude" id="latInput" class="form-control"
                                   value="{{ old('latitude') }}" step="any" placeholder="-6.12345678">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Longitude</label>
                            <input type="number" name="longitude" id="lngInput" class="form-control"
                                   value="{{ old('longitude') }}" step="any" placeholder="107.12345678">
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Kegiatan
                    </button>
                    <a href="{{ route('kegiatan-luar.index') }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const defaultLat = {{ old('latitude', -2.5) }};
    const defaultLng = {{ old('longitude', 118.0) }};

    const map = L.map('map').setView([defaultLat, defaultLng], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = null;
    const latInput = document.getElementById('latInput');
    const lngInput = document.getElementById('lngInput');

    @if(old('latitude') && old('longitude'))
    marker = L.marker([{{ old('latitude') }}, {{ old('longitude') }}]).addTo(map);
    map.setView([{{ old('latitude') }}, {{ old('longitude') }}], 13);
    @endif

    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        setMarker(lat, lng);
    });

    function setMarker(lat, lng) {
        lat = parseFloat(lat.toFixed(8));
        lng = parseFloat(lng.toFixed(8));
        if (marker) marker.setLatLng([lat, lng]);
        else marker = L.marker([lat, lng]).addTo(map);
        latInput.value = lat;
        lngInput.value = lng;
    }

    // Update marker saat input manual diubah
    [latInput, lngInput].forEach(el => {
        el.addEventListener('change', () => {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                setMarker(lat, lng);
                map.setView([lat, lng], 13);
            }
        });
    });

    function gunakanLokasiSaya() {
        if (!navigator.geolocation) return alert('Browser tidak mendukung geolokasi.');
        navigator.geolocation.getCurrentPosition(
            pos => {
                setMarker(pos.coords.latitude, pos.coords.longitude);
                map.setView([pos.coords.latitude, pos.coords.longitude], 15);
            },
            () => alert('Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan.')
        );
    }
</script>
@endpush
