@extends('layouts.app')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@push('styles')
<style>
    .section-title {
        font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px;
        color: #6b7a8d; margin: 24px 0 12px; display: flex; align-items: center; gap: 8px;
    }
    .section-title::after { content:''; flex:1; height:1px; background:#e8ecf0; }

    .chart-card { background:#fff; border-radius:12px; border:1px solid #e8ecf0; padding:16px 20px; }
    .chart-card .chart-title { font-size:13px; font-weight:700; color:#0f2419; margin:0 0 14px; }
    .chart-card canvas { max-height:220px; }

    .keg-item { display:flex; gap:10px; align-items:flex-start; padding:10px 0; border-bottom:1px solid #f5f7f9; }
    .keg-item:last-child { border-bottom:none; }
    .keg-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; margin-top:5px; }
    .keg-dot.dalam { background:#1a7f4b; }
    .keg-dot.luar  { background:#1a56db; }

    .usaha-row { display:flex; gap:8px; align-items:center; padding:8px 0; border-bottom:1px solid #f5f7f9; font-size:12.5px; }
    .usaha-row:last-child { border-bottom:none; }

    .mini-stat { background:#fff; border:1px solid #e8ecf0; border-radius:10px; padding:12px 14px; }
    .mini-stat .val { font-size:18px; font-weight:700; }
    .mini-stat .lbl { font-size:11px; color:#6b7a8d; margin-top:2px; }

    .grid-5 { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; }
    .grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
    .grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    .grid-2 { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
    @media(max-width:900px){
        .grid-5,.grid-4 { grid-template-columns:repeat(2,1fr); }
        .grid-3 { grid-template-columns:repeat(2,1fr); }
        .grid-2 { grid-template-columns:1fr; }
    }
    @media(max-width:520px){
        .grid-5,.grid-4,.grid-3 { grid-template-columns:1fr 1fr; }
    }
</style>
@endpush

@section('content')

@if($produksi_pending > 0)
<div class="alert alert-error" style="margin-bottom:16px; display:flex; align-items:center; gap:10px;">
    <i class="fas fa-exclamation-triangle"></i>
    <span>Ada <strong>{{ $produksi_pending }}</strong> data produksi menunggu validasi.</span>
    <a href="{{ route('produksi.index') }}?status=pending" class="btn btn-danger btn-sm" style="margin-left:auto;">Validasi Sekarang</a>
</div>
@endif

{{-- ══════════════════════════════════════════
     PRODUKSI & GETAH
════════════════════════════════════════════ --}}
<div class="section-title"><i class="fas fa-droplet" style="color:#1a7f4b;"></i> Produksi & Getah</div>
<div class="grid-4" style="margin-bottom:4px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-users"></i></div>
        <p class="stat-value">{{ number_format($total_penyadap) }}</p>
        <p class="stat-label">Total Penyadap</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-droplet"></i></div>
        <p class="stat-value">{{ number_format($total_produksi, 1) }} kg</p>
        <p class="stat-label">Produksi Valid</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-clock"></i></div>
        <p class="stat-value">{{ number_format($produksi_pending) }}</p>
        <p class="stat-label">Produksi Pending</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-warehouse"></i></div>
        <p class="stat-value">{{ number_format($stok_getah, 1) }} kg</p>
        <p class="stat-label">Stok Getah</p>
    </div>
</div>

{{-- ══════════════════════════════════════════
     KEGIATAN
════════════════════════════════════════════ --}}
<div class="section-title"><i class="fas fa-calendar-alt" style="color:#1a56db;"></i> Kegiatan</div>
<div class="grid-4" style="margin-bottom:4px;">
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-door-open"></i></div>
        <p class="stat-value">{{ $total_kegiatan_dalam }}</p>
        <p class="stat-label">Kegiatan Dalam Ruangan</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-tree"></i></div>
        <p class="stat-value">{{ $total_kegiatan_luar }}</p>
        <p class="stat-label">Kegiatan Luar Ruangan</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-briefcase"></i></div>
        <p class="stat-value">{{ $usaha_aktif }}</p>
        <p class="stat-label">Usaha Aktif</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        <p class="stat-value">{{ $total_peserta }}</p>
        <p class="stat-label">Total Peserta</p>
    </div>
</div>

{{-- ══════════════════════════════════════════
     KEUANGAN USAHA
════════════════════════════════════════════ --}}
<div class="section-title"><i class="fas fa-money-bill-wave" style="color:#f0a500;"></i> Keuangan Kegiatan Usaha</div>
<div class="grid-3" style="margin-bottom:4px;">
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-hand-holding-dollar"></i></div>
        <p class="stat-value" style="font-size:15px;">Rp {{ number_format($total_modal_all, 0, ',', '.') }}</p>
        <p class="stat-label">Total Modal Diterima</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-arrow-trend-up"></i></div>
        <p class="stat-value" style="font-size:15px;">Rp {{ number_format($total_pemasukan_all, 0, ',', '.') }}</p>
        <p class="stat-label">Total Pemasukan</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-arrow-trend-down"></i></div>
        <p class="stat-value" style="font-size:15px;">Rp {{ number_format($total_pengeluaran_all, 0, ',', '.') }}</p>
        <p class="stat-label">Total Pengeluaran</p>
    </div>
</div>

{{-- ══════════════════════════════════════════
     KUPS & INVENTARIS
════════════════════════════════════════════ --}}
<div class="section-title"><i class="fas fa-layer-group" style="color:#6b7a8d;"></i> KUPS & Inventaris</div>
<div class="grid-4" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-layer-group"></i></div>
        <p class="stat-value">{{ $total_kups }}</p>
        <p class="stat-label">Total KUPS</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-box-open"></i></div>
        <p class="stat-value">{{ $total_komoditas }}</p>
        <p class="stat-label">Total Komoditas</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-boxes-stacked"></i></div>
        <p class="stat-value">{{ $total_inventaris }}</p>
        <p class="stat-label">Jenis Barang Inventaris</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-truck"></i></div>
        <p class="stat-value">{{ $surat_jalan->count() }}</p>
        <p class="stat-label">Surat Jalan Terbaru</p>
    </div>
</div>

{{-- ══════════════════════════════════════════
     GRAFIK — ROW 1: Produksi + Kegiatan
════════════════════════════════════════════ --}}
<div class="grid-2" style="margin-bottom:16px;">

    {{-- Chart: Produksi Getah 6 bulan --}}
    <div class="chart-card">
        <p class="chart-title"><i class="fas fa-chart-bar" style="color:#1a7f4b;margin-right:6px;"></i>Produksi Getah 6 Bulan Terakhir</p>
        <canvas id="chartProduksi"></canvas>
    </div>

    {{-- Chart: Kegiatan per bulan --}}
    <div class="chart-card">
        <p class="chart-title"><i class="fas fa-chart-bar" style="color:#1a56db;margin-right:6px;"></i>Kegiatan 6 Bulan Terakhir</p>
        <canvas id="chartKegiatan"></canvas>
    </div>

</div>

{{-- ══════════════════════════════════════════
     GRAFIK — ROW 2: Usaha + KUPS
════════════════════════════════════════════ --}}
<div class="grid-2" style="margin-bottom:20px;">

    {{-- Chart: Kegiatan Usaha keuangan --}}
    <div class="chart-card">
        <p class="chart-title"><i class="fas fa-chart-column" style="color:#f0a500;margin-right:6px;"></i>Keuangan per Kegiatan Usaha</p>
        @if(count($chart_usaha['labels']) > 0)
        <canvas id="chartUsaha"></canvas>
        @else
        <div style="text-align:center; padding:40px; color:#adb5bd;">
            <i class="fas fa-chart-column" style="font-size:32px; display:block; margin-bottom:8px; opacity:.25;"></i>
            Belum ada data kegiatan usaha.
        </div>
        @endif
    </div>

    {{-- Chart: Komoditas per KUPS --}}
    <div class="chart-card">
        <p class="chart-title"><i class="fas fa-chart-pie" style="color:#6b7a8d;margin-right:6px;"></i>Komoditas per KUPS</p>
        @if(count($chart_kups['labels']) > 0)
        <canvas id="chartKups"></canvas>
        @else
        <div style="text-align:center; padding:40px; color:#adb5bd;">
            <i class="fas fa-layer-group" style="font-size:32px; display:block; margin-bottom:8px; opacity:.25;"></i>
            Belum ada data KUPS.
        </div>
        @endif
    </div>

</div>

{{-- ══════════════════════════════════════════
     TABEL — ROW 1: Surat Jalan + Kegiatan
════════════════════════════════════════════ --}}
<div class="grid-2" style="margin-bottom:16px;">

    {{-- Surat Jalan Terbaru --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-truck" style="color:#1a7f4b;margin-right:8px;"></i>Surat Jalan Terbaru</h3>
            <a href="{{ route('surat-jalan.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Nomor</th><th>Tanggal</th><th>Berat</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($surat_jalan as $sj)
                    <tr>
                        <td><strong>{{ $sj->nomor }}</strong></td>
                        <td>{{ $sj->tanggal->format('d/m/Y') }}</td>
                        <td>{{ number_format($sj->total_berat, 1) }} kg</td>
                        <td>
                            @php $badge = match($sj->status) { 'selesai'=>'badge-success','dikirim'=>'badge-info','draft'=>'badge-gray',default=>'badge-danger' }; @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($sj->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#adb5bd;padding:24px;">Belum ada surat jalan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Kegiatan Terbaru --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar-check" style="color:#1a56db;margin-right:8px;"></i>Kegiatan Terbaru</h3>
            <div style="display:flex; gap:6px;">
                <a href="{{ route('kegiatan.index', ['tipe'=>'dalam_ruangan']) }}" class="btn btn-outline btn-sm">Dalam</a>
                <a href="{{ route('kegiatan-luar.index') }}" class="btn btn-outline btn-sm">Luar</a>
            </div>
        </div>
        <div class="card-body" style="padding:0 16px;">
            @forelse($kegiatan_terbaru as $kg)
            <div class="keg-item">
                <div class="keg-dot {{ $kg->tipe === 'dalam_ruangan' ? 'dalam' : 'luar' }}"></div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $kg->nama_kegiatan }}
                    </div>
                    <div style="font-size:11px; color:#6b7a8d;">
                        {{ $kg->tanggal_mulai->format('d/m/Y') }} &nbsp;·&nbsp;
                        {{ $kg->tipe === 'dalam_ruangan' ? 'Dalam Ruangan' : 'Luar Ruangan' }}
                    </div>
                </div>
                @php $sb = match($kg->status) { 'aktif'=>'badge-success','selesai'=>'badge-info','batal'=>'badge-danger',default=>'badge-gray' }; @endphp
                <span class="badge {{ $sb }}" style="flex-shrink:0;">{{ ucfirst($kg->status) }}</span>
            </div>
            @empty
            <div style="text-align:center; padding:28px; color:#adb5bd;">Belum ada kegiatan.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════
     TABEL — ROW 2: Usaha + KUPS list + Aksi
════════════════════════════════════════════ --}}
<div class="grid-2" style="margin-bottom:16px;">

    {{-- Kegiatan Usaha ringkasan --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-briefcase" style="color:#f0a500;margin-right:8px;"></i>Kegiatan Usaha</h3>
            <a href="{{ route('kegiatan-usaha.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($usaha_list as $u)
            @php
                $pms = $u->transaksi->where('tipe','pemasukan')->sum('jumlah');
                $pgl = $u->transaksi->where('tipe','pengeluaran')->sum('jumlah');
                $kas = $u->modal->sum('jumlah') + $pms - $pgl;
            @endphp
            <div class="usaha-row" style="padding:10px 16px;">
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $u->nama_usaha }}
                    </div>
                    <div style="font-size:11px; color:#6b7a8d;">
                        {{ $u->jenis_usaha ?? '-' }} &nbsp;·&nbsp; {{ $u->tanggal_mulai->format('d/m/Y') }}
                    </div>
                </div>
                <div style="text-align:right; flex-shrink:0;">
                    <div style="font-size:12px; font-weight:700; color:{{ $kas >= 0 ? '#1a7f4b' : '#d93025' }};">
                        Rp {{ number_format(abs($kas), 0, ',', '.') }}
                    </div>
                    <div style="font-size:10px; color:#6b7a8d;">Kas</div>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:28px; color:#adb5bd;">Belum ada kegiatan usaha.</div>
            @endforelse
        </div>
    </div>

    {{-- KUPS + Aksi Cepat --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- KUPS ringkasan --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-layer-group" style="color:#6b7a8d;margin-right:8px;"></i>KUPS & Komoditas</h3>
                <a href="{{ route('kups.index') }}" class="btn btn-outline btn-sm">Kelola</a>
            </div>
            <div class="card-body" style="padding:10px 16px; display:flex; flex-direction:column; gap:8px;">
                @forelse($kups_list as $k)
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px;">
                    <span style="font-weight:600;">{{ $k->nama_kups }}</span>
                    <span style="background:#e8f5ee; color:#1a7f4b; font-size:11px; font-weight:600; padding:2px 8px; border-radius:12px;">
                        {{ $k->komoditas_count }} komoditas
                    </span>
                </div>
                @empty
                <div style="text-align:center; padding:16px; color:#adb5bd; font-size:13px;">Belum ada KUPS.</div>
                @endforelse
            </div>
        </div>

        {{-- Aksi Cepat --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-bolt" style="color:#f0a500;margin-right:8px;"></i>Aksi Cepat</h3>
            </div>
            <div class="card-body" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                <a href="{{ route('produksi.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-droplet"></i> Input Produksi
                </a>
                <a href="{{ route('penyadap.create') }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-user-plus"></i> Tambah Penyadap
                </a>
                <a href="{{ route('surat-jalan.create') }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-file-alt"></i> Surat Jalan
                </a>
                <a href="{{ route('kegiatan.create') }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-calendar-plus"></i> Buat Kegiatan
                </a>
                <a href="{{ route('kegiatan-usaha.create') }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-briefcase"></i> Buat Usaha
                </a>
                <a href="{{ route('kups.create') }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-layer-group"></i> Tambah KUPS
                </a>
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const GREEN   = '#1a7f4b';
const GREEN_L = 'rgba(26,127,75,.15)';
const BLUE    = '#1a56db';
const BLUE_L  = 'rgba(26,86,219,.15)';
const AMBER   = '#f0a500';
const AMBER_L = 'rgba(240,165,0,.15)';
const RED     = '#d93025';
const RED_L   = 'rgba(217,48,37,.15)';

Chart.defaults.font.family = "'Segoe UI', Arial, sans-serif";
Chart.defaults.font.size   = 11;

// ── Chart 1: Produksi Getah ────────────────────
new Chart(document.getElementById('chartProduksi'), {
    type: 'bar',
    data: {
        labels: @json($chart_produksi['labels']),
        datasets: [{
            label: 'Produksi (kg)',
            data:  @json($chart_produksi['data']),
            backgroundColor: GREEN_L,
            borderColor: GREEN,
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f0f3f6' }, ticks: { color: '#6b7a8d' } },
            x: { grid: { display: false }, ticks: { color: '#6b7a8d' } }
        }
    }
});

// ── Chart 2: Kegiatan per Bulan ────────────────
new Chart(document.getElementById('chartKegiatan'), {
    type: 'bar',
    data: {
        labels: @json($chart_kegiatan['labels']),
        datasets: [
            {
                label: 'Dalam Ruangan',
                data: @json($chart_kegiatan['dalam']),
                backgroundColor: GREEN_L,
                borderColor: GREEN,
                borderWidth: 2,
                borderRadius: 4,
            },
            {
                label: 'Luar Ruangan',
                data: @json($chart_kegiatan['luar']),
                backgroundColor: BLUE_L,
                borderColor: BLUE,
                borderWidth: 2,
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { boxWidth: 10, color: '#4a5568' } } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, color: '#6b7a8d' }, grid: { color: '#f0f3f6' } },
            x: { grid: { display: false }, ticks: { color: '#6b7a8d' } }
        }
    }
});

// ── Chart 3: Kegiatan Usaha Keuangan ──────────
@if(count($chart_usaha['labels']) > 0)
new Chart(document.getElementById('chartUsaha'), {
    type: 'bar',
    data: {
        labels: @json($chart_usaha['labels']),
        datasets: [
            {
                label: 'Modal',
                data: @json($chart_usaha['modal']),
                backgroundColor: BLUE_L,
                borderColor: BLUE,
                borderWidth: 2,
                borderRadius: 4,
            },
            {
                label: 'Pemasukan',
                data: @json($chart_usaha['pemasukan']),
                backgroundColor: GREEN_L,
                borderColor: GREEN,
                borderWidth: 2,
                borderRadius: 4,
            },
            {
                label: 'Pengeluaran',
                data: @json($chart_usaha['pengeluaran']),
                backgroundColor: RED_L,
                borderColor: RED,
                borderWidth: 2,
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { boxWidth: 10, color: '#4a5568' } } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f0f3f6' },
                ticks: {
                    color: '#6b7a8d',
                    callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : (v/1000).toFixed(0)+'rb')
                }
            },
            x: { grid: { display: false }, ticks: { color: '#6b7a8d' } }
        }
    }
});
@endif

// ── Chart 4: Komoditas per KUPS ───────────────
@if(count($chart_kups['labels']) > 0)
new Chart(document.getElementById('chartKups'), {
    type: 'doughnut',
    data: {
        labels: @json($chart_kups['labels']),
        datasets: [{
            data: @json($chart_kups['data']),
            backgroundColor: ['#1a7f4b','#1a56db','#f0a500','#d93025','#7c3aed','#059669','#0284c7'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        cutout: '62%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { boxWidth: 10, color: '#4a5568', padding: 12 }
            }
        }
    }
});
@endif
</script>
@endpush
