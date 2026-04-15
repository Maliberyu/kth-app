{{-- resources/views/blok/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Blok — '.$blok->nama_blok)
@section('page_title', 'Detail Blok: '.$blok->nama_blok)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@section('content')
<div class="grid grid-2" style="margin-bottom:20px;">
    {{-- Info Blok --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle" style="color:#1a7f4b;margin-right:8px;"></i>Info Blok</h3>
            <a href="{{ route('blok.edit', $blok) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-pen"></i> Edit
            </a>
        </div>
        <div class="card-body">
            <table style="width:100%;font-size:13.5px;">
                <tr>
                    <td style="color:#6b7a8d;padding:6px 0;width:40%;">Nama Blok</td>
                    <td><strong>{{ $blok->nama_blok }}</strong></td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:6px 0;">Jenis</td>
                    <td>{{ $blok->jenis_blok ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:6px 0;">Luas</td>
                    <td>{{ number_format($blok->luas, 2) }} Ha</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:6px 0;">Total Pohon</td>
                    <td>{{ number_format($blok->total_pohon) }}</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:6px 0;">Produktif</td>
                    <td><span class="badge badge-success">{{ number_format($blok->pohon_produktif) }}</span></td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:6px 0;">Tidak Produktif</td>
                    <td><span class="badge badge-danger">{{ number_format($blok->pohon_tidak_produktif) }}</span></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Peta --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-map-location-dot" style="color:#1a7f4b;margin-right:8px;"></i>Peta Blok</h3>
            @php $petaTerbaru = $blok->blokPeta->first(); @endphp
            @if($petaTerbaru)
                <span class="badge {{ $petaTerbaru->status_mapping === 'disetujui' ? 'badge-success' : ($petaTerbaru->status_mapping === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                    {{ ucfirst($petaTerbaru->status_mapping) }}
                </span>
            @endif
        </div>
        <div id="map" style="height:220px;"></div>
        @if($petaTerbaru && $petaTerbaru->status_mapping === 'pending')
        <div style="padding:12px 16px;border-top:1px solid #f0f3f6;">
            <form method="POST" action="{{ route('blok.peta.validasi', $petaTerbaru) }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                @csrf @method('PATCH')
                <select name="status_mapping" class="form-control form-select" style="width:160px;">
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

{{-- Penugasan Penyadap --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users" style="color:#1a7f4b;margin-right:8px;"></i>Penyadap Ditugaskan</h3>
    </div>
    <div style="padding:16px 20px;border-bottom:1px solid #f0f3f6;background:#fafbfc;">
        <form method="POST" action="{{ route('blok.tugaskan', $blok) }}" style="display:flex;gap:10px;align-items:flex-end;">
            @csrf
            <div style="flex:1;">
                <label class="form-label" style="font-size:12px;">Tambah Penyadap</label>
                <select name="penyadap_id" class="form-control form-select" required>
                    <option value="">— Pilih Penyadap —</option>
                    @foreach($penyadapTersedia as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tugaskan
            </button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Nama Penyadap</th><th>NIK</th><th>No. HP</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($blok->penyadap as $p)
                <tr>
                    <td><strong>{{ $p->nama }}</strong></td>
                    <td>{{ $p->nik ?? '-' }}</td>
                    <td>{{ $p->no_hp ?? '-' }}</td>
                    <td>
                        <form method="POST" action="{{ route('blok.hapus-tugas', [$blok, $p]) }}"
                              onsubmit="return confirm('Hapus penugasan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#adb5bd;padding:20px;">Belum ada penyadap ditugaskan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([-6.3, 107.1], 12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

@if($petaTerbaru && $petaTerbaru->status_mapping !== 'ditolak')
try {
    const geojson = {!! $petaTerbaru->geojson !!};
    const layer = L.geoJSON(geojson, {
        style: { color: '#1a7f4b', weight: 2, fillOpacity: 0.2 }
    }).addTo(map);
    map.fitBounds(layer.getBounds());
} catch(e) {}
@endif
</script>
@endpush