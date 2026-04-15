{{-- resources/views/inventaris/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Inventaris')
@section('page_title', 'Stok Inventaris')

@section('content')
<div class="grid grid-2">
    {{-- Daftar Barang + Stok --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-boxes-stacked" style="color:#1a7f4b;margin-right:8px;"></i>Daftar Barang</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Nama Barang</th><th>Satuan</th><th>Stok</th></tr>
                </thead>
                <tbody>
                    @forelse($inventaris as $inv)
                    <tr>
                        <td><strong>{{ $inv->nama_barang }}</strong></td>
                        <td>{{ $inv->satuan ?? '-' }}</td>
                        <td>
                            @php $stok = $inv->stok?->total_stok ?? 0; @endphp
                            <span class="badge {{ $stok > 0 ? 'badge-success' : 'badge-danger' }}">
                                {{ number_format($stok) }} {{ $inv->satuan }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:#adb5bd;padding:24px;">Belum ada barang</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Form Tambah Barang --}}
        <div style="padding:16px 20px;border-top:1px solid #f0f3f6;background:#fafbfc;">
            <p style="font-size:12px;font-weight:600;margin-bottom:10px;color:#6b7a8d;">TAMBAH BARANG BARU</p>
            <form method="POST" action="{{ route('inventaris.store') }}" style="display:flex;gap:10px;flex-wrap:wrap;">
                @csrf
                <input type="text" name="nama_barang" class="form-control" placeholder="Nama barang" required style="flex:1;min-width:140px;">
                <input type="text" name="satuan" class="form-control" placeholder="Satuan (pcs/kg/ltr)" style="width:140px;">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </form>
        </div>
    </div>

    {{-- Aksi Cepat --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-arrow-down" style="color:#1a73e8;margin-right:8px;"></i>Inventaris Masuk</h3>
                <a href="{{ route('inventaris.masuk.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Catat Masuk
                </a>
            </div>
            <div style="padding:12px 20px;">
                <a href="{{ route('inventaris.masuk') }}" class="btn btn-outline" style="width:100%;justify-content:center;">
                    <i class="fas fa-list"></i> Lihat Riwayat Masuk
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-arrow-up" style="color:#f0a500;margin-right:8px;"></i>Distribusi</h3>
                <a href="{{ route('inventaris.distribusi.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Catat Distribusi
                </a>
            </div>
            <div style="padding:12px 20px;">
                <a href="{{ route('inventaris.distribusi') }}" class="btn btn-outline" style="width:100%;justify-content:center;">
                    <i class="fas fa-list"></i> Lihat Riwayat Distribusi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection