{{-- resources/views/dashboard/super_admin.blade.php --}}
@extends('layouts.app')
@section('title', 'Dashboard Super Admin')
@section('page_title', 'Dashboard Global')

@section('content')
<div class="grid grid-4" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-sitemap"></i></div>
        <p class="stat-value">{{ number_format($total_kth) }}</p>
        <p class="stat-label">Total KTH</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        <p class="stat-value">{{ number_format($total_penyadap) }}</p>
        <p class="stat-label">Total Penyadap</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-droplet"></i></div>
        <p class="stat-value">{{ number_format($total_produksi, 1) }} kg</p>
        <p class="stat-label">Total Produksi</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-money-bill-wave"></i></div>
        <p class="stat-value">Rp {{ number_format($total_penjualan, 0, ',', '.') }}</p>
        <p class="stat-label">Total Penjualan</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-sitemap" style="color:#1a7f4b;margin-right:8px;"></i>Daftar KTH</h3>
        <a href="{{ route('super.kth.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah KTH
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Nama KTH</th><th>Alamat</th><th>Penyadap</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($kth_list as $k)
                <tr>
                    <td><strong>{{ $k->nama_kth }}</strong></td>
                    <td>{{ $k->alamat ?? '-' }}</td>
                    <td><span class="badge badge-info">{{ $k->penyadap_count }} orang</span></td>
                    <td>
                        <a href="{{ route('super.kth.edit', $k) }}" class="btn btn-outline btn-sm btn-icon">
                            <i class="fas fa-pen"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#adb5bd;padding:24px;">Belum ada KTH</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection