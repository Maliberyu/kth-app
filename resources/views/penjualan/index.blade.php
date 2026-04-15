{{-- resources/views/penjualan/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Penjualan')
@section('page_title', 'Transaksi Penjualan')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-money-bill-wave" style="color:#1a7f4b;margin-right:8px;"></i>Daftar Penjualan</h3>
        <a href="{{ route('penjualan.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Penjualan
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Surat Jalan</th>
                    <th>Vendor</th>
                    <th>Periode</th>
                    <th>Total Berat</th>
                    <th>Harga/kg</th>
                    <th>Total Penjualan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjualan as $i => $pj)
                <tr>
                    <td>{{ $penjualan->firstItem() + $i }}</td>
                    <td><strong>{{ $pj->suratJalan->nomor }}</strong></td>
                    <td>{{ $pj->vendor->nama_vendor }}</td>
                    <td><span class="badge badge-info">{{ $pj->periode->nama_periode }}</span></td>
                    <td>{{ number_format($pj->total_berat, 2) }} kg</td>
                    <td>Rp {{ number_format($pj->harga_jual, 0, ',', '.') }}</td>
                    <td><strong style="color:#1a7f4b;">Rp {{ number_format($pj->total_penjualan, 0, ',', '.') }}</strong></td>
                    <td>
                        <a href="{{ route('penjualan.show', $pj) }}" class="btn btn-outline btn-sm btn-icon">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#adb5bd;padding:32px;">Belum ada transaksi penjualan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($penjualan->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f6;">
        {{ $penjualan->links() }}
    </div>
    @endif
</div>
@endsection