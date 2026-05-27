@extends('layouts.app')
@section('title', 'Tambah Sumber Air')
@section('page_title', 'Tambah Sumber Air')

@section('content')

<div style="margin-bottom:16px;font-size:13px;color:#6b7a8d;">
    <a href="{{ route('sumber-air.index') }}" style="color:var(--primary);text-decoration:none;">Sumber Air</a>
    <span style="margin:0 6px;">/</span> Tambah
</div>

<div class="card" style="max-width:600px;">
    <div class="card-header">
        <h3><i class="fas fa-plus" style="color:#1a7f4b;margin-right:8px;"></i>Tambah Sumber Air</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('sumber-air.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Sumber Air <span style="color:#d93025;">*</span></label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}"
                       placeholder="Contoh: Sumber Cibulao" required>
                @error('nama')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tipe <span style="color:#d93025;">*</span></label>
                <select name="tipe" class="form-control" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="air_bersih" {{ old('tipe') === 'air_bersih' ? 'selected' : '' }}>Air Bersih</option>
                    <option value="air_baku"   {{ old('tipe') === 'air_baku'   ? 'selected' : '' }}>Air Baku</option>
                </select>
                @error('tipe')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"
                          placeholder="Deskripsi singkat sumber air...">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Koordinat GPS</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px;">
                    <div>
                        <input type="number" name="latitude" id="lat" class="form-control"
                               value="{{ old('latitude') }}" placeholder="Latitude" step="any">
                    </div>
                    <div>
                        <input type="number" name="longitude" id="lng" class="form-control"
                               value="{{ old('longitude') }}" placeholder="Longitude" step="any">
                    </div>
                </div>
                <button type="button" onclick="ambilGps('lat','lng','gps-status')"
                        class="btn btn-outline btn-sm">
                    <i class="fas fa-map-marker-alt"></i> Ambil Lokasi GPS
                </button>
                <span id="gps-status" style="font-size:12px;color:#6b7a8d;margin-left:8px;"></span>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="aktif"       {{ old('status','aktif') === 'aktif'       ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ old('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <div style="display:flex;gap:8px;margin-top:8px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('sumber-air.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function ambilGps(latId, lngId, statusId) {
    const status = document.getElementById(statusId);
    status.textContent = 'Mengambil lokasi...';
    if (!navigator.geolocation) {
        status.textContent = 'Browser tidak mendukung GPS.';
        return;
    }
    navigator.geolocation.getCurrentPosition(
        pos => {
            document.getElementById(latId).value = pos.coords.latitude.toFixed(8);
            document.getElementById(lngId).value = pos.coords.longitude.toFixed(8);
            status.textContent = '✓ Lokasi didapat (akurasi ±' + Math.round(pos.coords.accuracy) + 'm)';
            status.style.color = '#1a7f4b';
        },
        err => {
            status.textContent = 'Gagal: ' + err.message;
            status.style.color = '#d93025';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}
</script>
@endpush
