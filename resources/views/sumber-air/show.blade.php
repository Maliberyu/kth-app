@extends('layouts.app')
@section('title', $sumberAir->nama)
@section('page_title', $sumberAir->nama)

@push('styles')
<style>
    .trend-naik  { color:#1a7f4b; font-weight:700; }
    .trend-turun { color:#d93025; font-weight:700; }
    .trend-stabil{ color:#6b7a8d; font-weight:700; }
    .foto-thumb  { width:80px;height:60px;object-fit:cover;border-radius:6px;cursor:pointer; }
    #mapDebit    { height:260px;border-radius:8px;overflow:hidden; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- Breadcrumb --}}
<div style="margin-bottom:16px;font-size:13px;color:#6b7a8d;">
    <a href="{{ route('sumber-air.index') }}" style="color:var(--primary);text-decoration:none;">Sumber Air</a>
    <span style="margin:0 6px;">/</span>
    {{ $sumberAir->nama }}
</div>

@php
    $debitTerakhir = $sumberAir->pengukuran->first();
    $trend = $sumberAir->trend;
    $isBersih = $sumberAir->tipe === 'air_bersih';
    $trendIcon = $trend === 'naik' ? '↑ Naik' : ($trend === 'turun' ? '↓ Turun' : '→ Stabil');
@endphp

{{-- Stat cards --}}
<div class="grid grid-4" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon {{ $trend === 'naik' ? 'icon-green' : ($trend === 'turun' ? 'icon-red' : 'icon-blue') }}">
            <i class="fas fa-tachometer-alt"></i>
        </div>
        <p class="stat-value" style="font-size:15px;">
            {{ $debitTerakhir ? $debitTerakhir->debit_format : '-' }}
        </p>
        <p class="stat-label">Debit Terakhir</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon {{ $trend === 'naik' ? 'icon-green' : ($trend === 'turun' ? 'icon-red' : 'icon-blue') }}">
            <i class="fas fa-chart-line"></i>
        </div>
        <p class="stat-value {{ 'trend-'.$trend }}">{{ $trendIcon }}</p>
        <p class="stat-label">Tren Debit</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-clipboard-list"></i></div>
        <p class="stat-value">{{ $sumberAir->pengukuran->count() }}</p>
        <p class="stat-label">Total Pengukuran</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber">
            <i class="fas fa-{{ $isBersih ? 'home' : 'seedling' }}"></i>
        </div>
        <p class="stat-value">
            @if($isBersih)
                {{ $sumberAir->bakTampung?->kampungLayanan->count() ?? 0 }}
            @else
                {{ $sumberAir->lahanLayanan->count() }}
            @endif
        </p>
        <p class="stat-label">{{ $isBersih ? 'Kampung' : 'Lahan' }} Terlayani</p>
    </div>
</div>

{{-- Info KUPS --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-info-circle" style="color:#1a7f4b;margin-right:8px;"></i>Info Sumber Air</h3>
        <div style="display:flex;gap:6px;">
            <span style="font-size:12px;font-weight:600;padding:4px 10px;border-radius:6px;
                background:{{ $isBersih ? '#dbeafe' : '#fef9c3' }};
                color:{{ $isBersih ? '#1e40af' : '#854d0e' }};">
                {{ $sumberAir->tipe_label }}
            </span>
            <span style="font-size:12px;font-weight:600;padding:4px 10px;border-radius:6px;
                background:{{ $sumberAir->status === 'aktif' ? '#dcfce7' : '#fee2e2' }};
                color:{{ $sumberAir->status === 'aktif' ? '#166534' : '#991b1b' }};">
                {{ ucfirst($sumberAir->status) }}
            </span>
            <a href="{{ route('sumber-air.edit', $sumberAir) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-pen"></i> Edit
            </a>
        </div>
    </div>
    @if($sumberAir->deskripsi)
    <div class="card-body" style="font-size:13.5px;color:#4a5568;line-height:1.7;">
        {{ $sumberAir->deskripsi }}
    </div>
    @endif
</div>

{{-- Peta --}}
@if($sumberAir->latitude && $sumberAir->longitude)
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-map-marker-alt" style="color:#1a7f4b;margin-right:8px;"></i>Lokasi Sumber Air</h3>
    </div>
    <div class="card-body" style="padding:12px;">
        <div id="mapDebit"></div>
        <div style="font-size:12px;color:#6b7a8d;margin-top:6px;">
            <i class="fas fa-crosshairs"></i>
            {{ $sumberAir->latitude }}, {{ $sumberAir->longitude }}
        </div>
    </div>
</div>
@endif

{{-- Distribusi --}}
@if($isBersih)
{{-- Bak Tampung --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-archive" style="color:#1a7f4b;margin-right:8px;"></i>Bak Tampung & Distribusi</h3>
        @if(!$sumberAir->bakTampung)
        <button onclick="document.getElementById('formBak').style.display = document.getElementById('formBak').style.display==='none' ? 'block' : 'none'"
                class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Bak</button>
        @endif
    </div>

    {{-- Form tambah bak --}}
    @if(!$sumberAir->bakTampung)
    <div id="formBak" style="display:none;padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e8ecf0;">
        <form method="POST" action="{{ route('sumber-air.bak.store', $sumberAir) }}">
            @csrf
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;margin-bottom:10px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Nama Bak <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Bak A" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Kapasitas (m³)</label>
                    <input type="number" name="kapasitas" class="form-control" step="0.01" min="0" placeholder="0">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Latitude Bak</label>
                    <input type="number" name="latitude" id="bakLat" class="form-control" step="any" placeholder="Opsional">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Longitude Bak</label>
                    <input type="number" name="longitude" id="bakLng" class="form-control" step="any" placeholder="Opsional">
                </div>
            </div>
            <button type="button" onclick="ambilGps('bakLat','bakLng','bakGpsStatus')" class="btn btn-outline btn-sm" style="margin-bottom:8px;">
                <i class="fas fa-map-marker-alt"></i> Ambil GPS Bak
            </button>
            <span id="bakGpsStatus" style="font-size:12px;color:#6b7a8d;margin-left:8px;"></span>
            <div style="display:flex;gap:8px;margin-top:8px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
                <button type="button" onclick="document.getElementById('formBak').style.display='none'" class="btn btn-outline btn-sm">Batal</button>
            </div>
        </form>
    </div>
    @endif

    <div class="card-body">
        @if($sumberAir->bakTampung)
        @php $bak = $sumberAir->bakTampung; @endphp
        <div style="border:1px solid #e8ecf0;border-radius:8px;padding:14px;background:#f8fafc;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                <div>
                    <strong style="font-size:14px;">{{ $bak->nama }}</strong>
                    @if($bak->kapasitas)
                    <span style="font-size:12px;color:#6b7a8d;margin-left:8px;">Kapasitas: {{ $bak->kapasitas }} m³</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('sumber-air.bak.destroy', [$sumberAir, $bak]) }}"
                      onsubmit="return confirm('Hapus bak tampung ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm btn-icon"><i class="fas fa-trash"></i></button>
                </form>
            </div>

            {{-- Kampung Layanan --}}
            <div style="margin-top:8px;">
                <div style="font-size:12px;font-weight:600;color:#4a5568;margin-bottom:6px;">
                    Kampung yang Dilayani:
                </div>
                @forelse($bak->kampungLayanan as $k)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 8px;background:#fff;border-radius:4px;margin-bottom:4px;border:1px solid #e8ecf0;">
                    <span style="font-size:13px;">
                        <i class="fas fa-home" style="color:#1a56db;margin-right:6px;"></i>
                        {{ $k->nama_kampung }}
                        @if($k->jumlah_kk > 0)
                        <span style="color:#6b7a8d;font-size:11px;"> — {{ number_format($k->jumlah_kk) }} KK</span>
                        @endif
                    </span>
                    <form method="POST" action="{{ route('sumber-air.kampung.destroy', [$sumberAir, $bak, $k]) }}"
                          onsubmit="return confirm('Hapus kampung ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm btn-icon" style="padding:2px 6px;font-size:10px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div style="font-size:12px;color:#adb5bd;font-style:italic;">Belum ada kampung.</div>
                @endforelse

                {{-- Form tambah kampung --}}
                <form method="POST" action="{{ route('sumber-air.kampung.store', [$sumberAir, $bak]) }}"
                      style="display:grid;grid-template-columns:2fr 1fr auto;gap:8px;align-items:end;margin-top:8px;">
                    @csrf
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="text" name="nama_kampung" class="form-control"
                               placeholder="Nama kampung..." style="font-size:13px;" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="number" name="jumlah_kk" class="form-control"
                               placeholder="Jml KK" min="0" style="font-size:13px;">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i>
                    </button>
                </form>
            </div>
        </div>
        @else
        <p style="color:#adb5bd;font-style:italic;text-align:center;padding:20px 0;">
            Belum ada bak tampung. Klik <strong>Tambah Bak</strong> di atas.
        </p>
        @endif
    </div>
</div>

@else
{{-- Lahan Layanan (Air Baku) --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-seedling" style="color:#1a7f4b;margin-right:8px;"></i>Lahan yang Dilayani</h3>
        <button onclick="document.getElementById('formLahan').style.display = document.getElementById('formLahan').style.display==='none' ? 'block' : 'none'"
                class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Lahan</button>
    </div>

    <div id="formLahan" style="display:none;padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e8ecf0;">
        <form method="POST" action="{{ route('sumber-air.lahan.store', $sumberAir) }}">
            @csrf
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;margin-bottom:10px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Nama Lahan <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_lahan" class="form-control" placeholder="Contoh: Sawah Pak Budi" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Tipe <span style="color:#d93025;">*</span></label>
                    <select name="tipe_lahan" class="form-control" required>
                        <option value="sawah">Sawah</option>
                        <option value="kolam">Kolam</option>
                        <option value="kebun">Kebun</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Luas (Ha)</label>
                    <input type="number" name="luas_ha" class="form-control" step="0.01" min="0" placeholder="0">
                </div>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Tambahkan</button>
                <button type="button" onclick="document.getElementById('formLahan').style.display='none'" class="btn btn-outline btn-sm">Batal</button>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Lahan</th>
                    <th style="width:80px;">Tipe</th>
                    <th style="text-align:right;width:80px;">Luas (Ha)</th>
                    <th style="text-align:center;width:60px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sumberAir->lahanLayanan as $l)
                <tr>
                    <td><strong>{{ $l->nama_lahan }}</strong></td>
                    <td>
                        <span style="font-size:11px;padding:2px 8px;border-radius:4px;font-weight:600;
                            background:{{ $l->tipe_lahan==='sawah' ? '#dcfce7' : ($l->tipe_lahan==='kolam' ? '#dbeafe' : '#fef9c3') }};
                            color:{{ $l->tipe_lahan==='sawah' ? '#166534' : ($l->tipe_lahan==='kolam' ? '#1e40af' : '#854d0e') }};">
                            {{ ucfirst($l->tipe_lahan) }}
                        </span>
                    </td>
                    <td style="text-align:right;">{{ $l->luas_ha > 0 ? $l->luas_ha.' Ha' : '-' }}</td>
                    <td style="text-align:center;">
                        <form method="POST" action="{{ route('sumber-air.lahan.destroy', [$sumberAir, $l]) }}"
                              onsubmit="return confirm('Hapus lahan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm btn-icon"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:#adb5bd;padding:24px;">
                        Belum ada lahan. Klik <strong>Tambah Lahan</strong>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Chart Debit --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-chart-line" style="color:#1a7f4b;margin-right:8px;"></i>Grafik Debit 6 Bulan</h3>
    </div>
    <div class="card-body">
        <canvas id="chartDebit" style="max-height:220px;"></canvas>
    </div>
</div>

{{-- Riwayat Pengukuran --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-clipboard-list" style="color:#1a7f4b;margin-right:8px;"></i>
            Riwayat Pengukuran
            <span style="font-weight:400;color:#6b7a8d;font-size:12px;">({{ $sumberAir->pengukuran->count() }})</span>
        </h3>
        <button onclick="document.getElementById('formUkur').style.display = document.getElementById('formUkur').style.display==='none' ? 'block' : 'none'"
                class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Pengukuran</button>
    </div>

    {{-- Form Pengukuran --}}
    <div id="formUkur" style="display:none;padding:16px 20px;background:#f8fafc;border-bottom:1px solid #e8ecf0;">
        <form method="POST" action="{{ route('sumber-air.pengukuran.store', $sumberAir) }}" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:10px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Tanggal <span style="color:#d93025;">*</span></label>
                    <input type="date" name="tanggal" class="form-control"
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Debit <span style="color:#d93025;">*</span></label>
                    <input type="number" name="debit" class="form-control" step="0.001" min="0"
                           placeholder="0.000" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Satuan <span style="color:#d93025;">*</span></label>
                    <select name="satuan" class="form-control" required>
                        @foreach(\App\Models\PengukuranDebit::$satuanOptions as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Nama Petugas <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_petugas" class="form-control"
                           placeholder="Nama petugas pengukur" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Foto Dokumentasi</label>
                    <input type="file" name="foto" class="form-control" accept="image/*" capture="environment">
                </div>
            </div>
            <div style="margin-bottom:10px;">
                <label class="form-label" style="font-size:12px;">Koordinat GPS Titik Ukur</label>
                <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:8px;align-items:end;">
                    <input type="number" name="latitude"  id="ukurLat" class="form-control"
                           placeholder="Latitude" step="any">
                    <input type="number" name="longitude" id="ukurLng" class="form-control"
                           placeholder="Longitude" step="any">
                    <button type="button" onclick="ambilGps('ukurLat','ukurLng','ukurGps')" class="btn btn-outline btn-sm">
                        <i class="fas fa-crosshairs"></i> GPS
                    </button>
                </div>
                <span id="ukurGps" style="font-size:12px;color:#6b7a8d;"></span>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label class="form-label">Catatan</label>
                <input type="text" name="catatan" class="form-control" placeholder="Opsional...">
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
                <button type="button" onclick="document.getElementById('formUkur').style.display='none'" class="btn btn-outline btn-sm">Batal</button>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:90px;">Tanggal</th>
                    <th>Petugas</th>
                    <th style="text-align:right;width:110px;">Debit</th>
                    <th style="text-align:center;width:60px;">Foto</th>
                    <th style="text-align:center;width:60px;">Lokasi</th>
                    <th>Catatan</th>
                    <th style="text-align:center;width:50px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sumberAir->pengukuran as $p)
                <tr>
                    <td style="font-size:12px;">{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $p->nama_petugas }}</td>
                    <td style="text-align:right;font-weight:600;color:#1a7f4b;">{{ $p->debit_format }}</td>
                    <td style="text-align:center;">
                        @if($p->foto)
                        <img src="{{ asset($p->foto) }}" class="foto-thumb"
                             onclick="window.open('{{ asset($p->foto) }}','_blank')" alt="foto">
                        @else
                        <span style="color:#adb5bd;font-size:11px;">-</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($p->latitude && $p->longitude)
                        <a href="https://www.google.com/maps?q={{ $p->latitude }},{{ $p->longitude }}"
                           target="_blank" style="color:#1a56db;font-size:18px;" title="{{ $p->latitude }}, {{ $p->longitude }}">
                            <i class="fas fa-map-marker-alt"></i>
                        </a>
                        @else
                        <span style="color:#adb5bd;font-size:11px;">-</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:#6b7a8d;">{{ $p->catatan ?: '-' }}</td>
                    <td style="text-align:center;">
                        <form method="POST" action="{{ route('sumber-air.pengukuran.destroy', [$sumberAir, $p]) }}"
                              onsubmit="return confirm('Hapus data pengukuran ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm btn-icon"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:#adb5bd;padding:32px;">
                        Belum ada data pengukuran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@if($sumberAir->latitude && $sumberAir->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif
<script>
// Chart debit
const chartData = @json($chart);
new Chart(document.getElementById('chartDebit'), {
    type: 'line',
    data: {
        labels: chartData.labels,
        datasets: [{
            label: 'Rata-rata Debit',
            data: chartData.data,
            borderColor: '#1a7f4b',
            backgroundColor: 'rgba(26,127,75,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#1a7f4b',
            pointRadius: 5,
            tension: 0.3,
            fill: true,
            spanGaps: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Debit' },
            }
        }
    }
});

@if($sumberAir->latitude && $sumberAir->longitude)
// Peta
const map = L.map('mapDebit').setView([{{ $sumberAir->latitude }}, {{ $sumberAir->longitude }}], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);
const greenIcon = L.divIcon({
    html: '<i class="fas fa-tint" style="font-size:22px;color:#1a7f4b;text-shadow:0 1px 3px rgba(0,0,0,.3);"></i>',
    iconSize: [24, 24], iconAnchor: [12, 24], className: ''
});
L.marker([{{ $sumberAir->latitude }}, {{ $sumberAir->longitude }}], {icon: greenIcon})
    .addTo(map)
    .bindPopup('<strong>{{ addslashes($sumberAir->nama) }}</strong><br>{{ $sumberAir->tipe_label }}')
    .openPopup();
@if($sumberAir->bakTampung && $sumberAir->bakTampung->latitude && $sumberAir->bakTampung->longitude)
const bakIcon = L.divIcon({
    html: '<i class="fas fa-archive" style="font-size:20px;color:#1a56db;text-shadow:0 1px 3px rgba(0,0,0,.3);"></i>',
    iconSize: [22, 22], iconAnchor: [11, 22], className: ''
});
L.marker([{{ $sumberAir->bakTampung->latitude }}, {{ $sumberAir->bakTampung->longitude }}], {icon: bakIcon})
    .addTo(map)
    .bindPopup('<strong>{{ addslashes($sumberAir->bakTampung->nama) }}</strong><br>Bak Tampung');
@endif
@endif

// GPS helper
function ambilGps(latId, lngId, statusId) {
    const status = document.getElementById(statusId);
    status.textContent = 'Mengambil lokasi...';
    if (!navigator.geolocation) { status.textContent = 'Tidak didukung.'; return; }
    navigator.geolocation.getCurrentPosition(
        pos => {
            document.getElementById(latId).value  = pos.coords.latitude.toFixed(8);
            document.getElementById(lngId).value  = pos.coords.longitude.toFixed(8);
            status.textContent = '✓ Didapat (±' + Math.round(pos.coords.accuracy) + 'm)';
            status.style.color = '#1a7f4b';
        },
        err => { status.textContent = 'Gagal: ' + err.message; status.style.color = '#d93025'; },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}
</script>
@endpush
