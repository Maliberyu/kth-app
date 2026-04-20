{{-- resources/views/produksi/penyadap_index.blade.php --}}
@extends('layouts.app')

@section('title', 'Produksi Saya')
@section('page_title', ' Produksi Getah Saya')

@section('content')
<div class="container mx-auto px-4 py-6">
    
    {{-- 📊 Stat Ringkas --}}
    <div class="grid grid-3 mb-6">
        <div class="stat-card">
            <div class="stat-icon icon-green"><i class="fas fa-droplet"></i></div>
            <p class="stat-value">{{ number_format($produksi->sum('berat'), 1) }} kg</p>
            <p class="stat-label">Total Produksi</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-blue"><i class="fas fa-check-circle"></i></div>
            <p class="stat-value">{{ $produksi->where('status_validasi', 'valid')->count() }}</p>
            <p class="stat-label">Disetujui</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-amber"><i class="fas fa-clock"></i></div>
            <p class="stat-value">{{ $produksi->where('status_validasi', 'pending')->count() }}</p>
            <p class="stat-label">Pending</p>
        </div>
    </div>

    {{-- 🔘 Tombol Tambah --}}
    <div class="mb-4 text-right">
        <a href="{{ route('saya.produksi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Catat Produksi Baru
        </a>
    </div>

    {{-- 📋 Tabel Produksi --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Blok</th>
                            <th>Berat (kg)</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produksi as $p)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $p->blok->nama_blok ?? '-' }}</td>
                            <td class="font-bold">{{ number_format($p->berat, 1) }}</td>
                            <td>
                                @php
                                    $badge = match($p->status_validasi) {
                                        'valid' => 'badge-success',
                                        'ditolak' => 'badge-danger',
                                        default => 'badge-warning'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">
                                    {{ ucfirst($p->status_validasi) }}
                                </span>
                            </td>
                            <td>{{ Str::limit($p->catatan, 30) }}</td>
                            <td>
                                <a href="{{ route('produksi.show', $p) }}" class="btn btn-outline btn-sm btn-icon" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-muted">
                                <i class="fas fa-inbox" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                                Belum ada data produksi dicatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- Pagination (jika pakai paginate) --}}
    @if($produksi instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $produksi->links() }}
        </div>
    @endif
</div>
@endsection