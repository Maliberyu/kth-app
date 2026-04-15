{{-- resources/views/surat_jalan/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Surat Jalan')
@section('page_title', 'Detail Surat Jalan')

@section('content')
<div class="grid grid-2" style="margin-bottom:20px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-file-alt" style="color:#1a7f4b;margin-right:8px;"></i>Info Surat Jalan</h3>
            <span class="badge {{ match($suratJalan->status) {
                'selesai' => 'badge-success',
                'dikirim' => 'badge-info',
                'draft'   => 'badge-gray',
                default   => 'badge-danger'
            } }}">{{ ucfirst($suratJalan->status) }}</span>
        </div>
        <div class="card-body">
            <table style="width:100%;font-size:13.5px;">
                <tr><td style="color:#6b7a8d;padding:6px 0;width:40%;">Nomor</td><td><strong>{{ $suratJalan->nomor }}</strong></td></tr>
                <tr><td style="color:#6b7a8d;padding:6px 0;">Tanggal</td><td>{{ $suratJalan->tanggal->format('d/m/Y') }}</td></tr>
                <tr><td style="color:#6b7a8d;padding:6px 0;">Penyimpanan</td><td>{{ $suratJalan->penyimpanan->nama_lokasi }}</td></tr>
                <tr><td style="color:#6b7a8d;padding:6px 0;">Vendor</td><td>{{ $suratJalan->vendor->nama_vendor }}</td></tr>
                <tr><td style="color:#6b7a8d;padding:6px 0;">Total Berat</td><td><strong>{{ number_format($suratJalan->total_berat, 2) }} kg</strong></td></tr>
                <tr><td style="color:#6b7a8d;padding:6px 0;">Keterangan</td><td>{{ $suratJalan->keterangan ?? '-' }}</td></tr>
            </table>
        </div>
        <div style="padding:12px 20px;border-top:1px solid #f0f3f6;display:flex;gap:8px;">
            @if($suratJalan->status === 'draft')
            <form method="POST" action="{{ route('surat-jalan.kirim', $suratJalan) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Kirim</button>
            </form>
            @endif
            @if($suratJalan->status === 'dikirim')
            <form method="POST" action="{{ route('surat-jalan.selesai', $suratJalan) }}"
                  onsubmit="return confirm('Tandai selesai?')">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Selesai</button>
            </form>
            @endif
            <a href="{{ route('surat-jalan.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-truck" style="color:#1a7f4b;margin-right:8px;"></i>Detail Pengiriman</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Jumlah Dikirim</th><th>Catatan</th></tr></thead>
                <tbody>
                    @foreach($suratJalan->pengirimanGetah as $pg)
                    <tr>
                        <td><strong>{{ number_format($pg->jumlah_dikirim, 2) }} kg</strong></td>
                        <td>{{ $pg->catatan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($suratJalan->penjualan->count() > 0)
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-money-bill-wave" style="color:#1a7f4b;margin-right:8px;"></i>Transaksi Penjualan</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Periode</th><th>Total Berat</th><th>Harga/kg</th><th>Total</th></tr></thead>
            <tbody>
                @foreach($suratJalan->penjualan as $pj)
                <tr>
                    <td>{{ $pj->periode->nama_periode }}</td>
                    <td>{{ number_format($pj->total_berat, 2) }} kg</td>
                    <td>Rp {{ number_format($pj->harga_jual, 0, ',', '.') }}</td>
                    <td><strong style="color:#1a7f4b;">Rp {{ number_format($pj->total_penjualan, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection