{{-- resources/views/dashboard/penyadap.blade.php --}}
@extends('layouts.app')
@section('title', 'Dashboard Saya')
@section('page_title', 'Dashboard Saya')

@section('content')
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-droplet"></i></div>
        <p class="stat-value">{{ number_format($total_produksi, 1) }} kg</p>
        <p class="stat-label">Total Produksi (Valid)</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-calendar"></i></div>
        <p class="stat-value">{{ number_format($produksi_bulan, 1) }} kg</p>
        <p class="stat-label">Produksi Bulan Ini</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-history"></i></div>
        <p class="stat-value">{{ count($riwayat) }}</p>
        <p class="stat-label">Riwayat Terakhir</p>
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-history" style="color:#1a7f4b;margin-right:8px;"></i>Riwayat Produksi</h3>
            <a href="{{ route('saya.produksi') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Tanggal</th><th>Blok</th><th>Berat</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                    <tr>
                        <td>{{ $r->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $r->blok->nama_blok }}</td>
                        <td>{{ number_format($r->berat, 2) }} kg</td>
                        <td>
                            <span class="badge {{ $r->status_validasi === 'valid' ? 'badge-success' : ($r->status_validasi === 'ditolak' ? 'badge-danger' : 'badge-warning') }}">
                                {{ ucfirst($r->status_validasi) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:#adb5bd;padding:20px;">Belum ada riwayat</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-bolt" style="color:#f0a500;margin-right:8px;"></i>Aksi Cepat</h3>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ route('saya.produksi.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Input Produksi
            </a>
            <a href="{{ route('saya.blok') }}" class="btn btn-outline">
                <i class="fas fa-map"></i> Lihat Blok Saya
            </a>
        </div>
    </div>
</div>
@endsection