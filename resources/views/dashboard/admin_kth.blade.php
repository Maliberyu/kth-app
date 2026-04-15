@extends('layouts.app')
@section('title', 'Dashboard — KTH App')
@section('page_title', 'Dashboard')

@section('content')
<div class="grid grid-4" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-users"></i></div>
        <p class="stat-value">{{ number_format($total_penyadap) }}</p>
        <p class="stat-label">Total Penyadap</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-droplet"></i></div>
        <p class="stat-value">{{ number_format($total_produksi, 1) }} kg</p>
        <p class="stat-label">Total Produksi Valid</p>
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

<div class="grid grid-2">
    {{-- Surat Jalan Terbaru --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-truck" style="color:#1a7f4b;margin-right:8px;"></i>Surat Jalan Terbaru</h3>
            <a href="{{ route('surat-jalan.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Berat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surat_jalan as $sj)
                    <tr>
                        <td><strong>{{ $sj->nomor }}</strong></td>
                        <td>{{ $sj->tanggal->format('d/m/Y') }}</td>
                        <td>{{ number_format($sj->total_berat, 1) }} kg</td>
                        <td>
                            @php
                                $badge = match($sj->status) {
                                    'selesai' => 'badge-success',
                                    'dikirim' => 'badge-info',
                                    'draft'   => 'badge-gray',
                                    default   => 'badge-danger',
                                };
                            @endphp
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

    {{-- Aksi Cepat --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-bolt" style="color:#f0a500;margin-right:8px;"></i>Aksi Cepat</h3>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ route('produksi.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Input Produksi Getah
            </a>
            <a href="{{ route('penyadap.create') }}" class="btn btn-outline">
                <i class="fas fa-user-plus"></i> Tambah Penyadap
            </a>
            <a href="{{ route('surat-jalan.create') }}" class="btn btn-outline">
                <i class="fas fa-file-alt"></i> Buat Surat Jalan
            </a>
            <a href="{{ route('inventaris.masuk.create') }}" class="btn btn-outline">
                <i class="fas fa-boxes-stacked"></i> Inventaris Masuk
            </a>
            @if($produksi_pending > 0)
            <a href="{{ route('produksi.index') }}?status=pending" class="btn btn-danger">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $produksi_pending }} Produksi Perlu Validasi
            </a>
            @endif
        </div>
    </div>
</div>
@endsection