{{-- resources/views/penjualan/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Penjualan')
@section('page_title', 'Detail Penjualan')

@section('content')
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-money-bill-wave" style="color:#1a7f4b;margin-right:8px;"></i>Ringkasan Penjualan</h3>
        <a href="{{ route('penjualan.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="grid grid-4">
            <div class="stat-card">
                <div class="stat-icon icon-blue"><i class="fas fa-truck"></i></div>
                <p class="stat-value" style="font-size:16px;">{{ $penjualan->suratJalan->nomor }}</p>
                <p class="stat-label">Surat Jalan</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-amber"><i class="fas fa-droplet"></i></div>
                <p class="stat-value">{{ number_format($penjualan->total_berat, 1) }} kg</p>
                <p class="stat-label">Total Berat</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="fas fa-tag"></i></div>
                <p class="stat-value" style="font-size:18px;">Rp {{ number_format($penjualan->harga_jual, 0, ',', '.') }}</p>
                <p class="stat-label">Harga Jual/kg</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="fas fa-money-bill-wave"></i></div>
                <p class="stat-value" style="font-size:18px;color:#1a7f4b;">Rp {{ number_format($penjualan->total_penjualan, 0, ',', '.') }}</p>
                <p class="stat-label">Total Penjualan</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list" style="color:#1a7f4b;margin-right:8px;"></i>Detail per Penyadap</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Penyadap</th>
                    <th>Blok</th>
                    <th>Berat (kg)</th>
                    <th>Harga Beli/kg</th>
                    <th>Total Beli</th>
                </tr>
            </thead>
            <tbody>
                @php $totalBeli = 0; @endphp
                @foreach($penjualan->detail as $i => $d)
                @php $totalBeli += $d->total_beli; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $d->penyadap->nama }}</strong></td>
                    <td>{{ $d->blok->nama_blok }}</td>
                    <td>{{ number_format($d->berat, 2) }}</td>
                    <td>Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                    <td><strong>Rp {{ number_format($d->total_beli, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8fafc;">
                    <td colspan="5" style="text-align:right;padding:12px 14px;font-weight:600;color:#6b7a8d;">Total Pembelian:</td>
                    <td style="padding:12px 14px;font-weight:700;color:#1a7f4b;">Rp {{ number_format($totalBeli, 0, ',', '.') }}</td>
                </tr>
                <tr style="background:#e8f5ee;">
                    <td colspan="5" style="text-align:right;padding:12px 14px;font-weight:600;color:#145f38;">Keuntungan:</td>
                    <td style="padding:12px 14px;font-weight:700;color:#1a7f4b;font-size:16px;">
                        Rp {{ number_format($penjualan->total_penjualan - $totalBeli, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection