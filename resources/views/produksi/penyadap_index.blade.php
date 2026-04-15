{{-- resources/views/produksi/penyadap_index.blade.php --}}
@extends('layouts.app')
@section('title', 'Produksi Saya')
@section('page_title', 'Riwayat Produksi Saya')

@section('content')

{{-- Stat --}}
<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-droplet"></i></div>
        <p class="stat-value">{{ number_format($total_valid, 1) }} kg</p>
        <p class="stat-label">Total Produksi Valid</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-calendar-check"></i></div>
        <p class="stat-value">{{ number_format($bulan_ini, 1) }} kg</p>
        <p class="stat-label">Bulan Ini</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-clock"></i></div>
        <p class="stat-value">{{ $pending }}</p>
        <p class="stat-label">Menunggu Validasi</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-droplet" style="color:#1a7f4b;margin-right:8px;"></i>Riwayat Produksi</h3>
        <a href="{{ route('saya.produksi.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Input Produksi
        </a>
    </div>

    {{-- Filter Bulan --}}
    <div style="padding:12px 20px;border-bottom:1px solid #f0f3f6;background:#fafbfc;">
        <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            <div>
                <label class="form-label" style="font-size:12px;">Bulan</label>
                <input type="month" name="bulan" class="form-control"
                       value="{{ request('bulan', date('Y-m')) }}" style="width:160px;">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('saya.produksi') }}" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Blok</th>
                    <th>Penyimpanan</th>
                    <th>Berat (kg)</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produksi as $p)
                <tr>
                    <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td><strong>{{ $p->blok->nama_blok }}</strong></td>
                    <td>{{ $p->penyimpanan->nama_lokasi }}</td>
                    <td>
                        <strong style="color:{{ $p->status_validasi === 'valid' ? '#1a7f4b' : 'inherit' }}">
                            {{ number_format($p->berat, 2) }}
                        </strong>
                    </td>
                    <td>
                        <span class="badge {{ match($p->status_validasi) {
                            'valid'   => 'badge-success',
                            'ditolak' => 'badge-danger',
                            default   => 'badge-warning'
                        } }}">
                            {{ match($p->status_validasi) {
                                'valid'   => '✓ Valid',
                                'ditolak' => '✗ Ditolak',
                                default   => '⏳ Pending'
                            } }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:#6b7a8d;">{{ $p->catatan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:#adb5bd;padding:32px;">
                        Belum ada produksi pada bulan ini
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($produksi->count() > 0)
            <tfoot>
                <tr style="background:#f8fafc;">
                    <td colspan="3" style="padding:10px 14px;font-weight:600;color:#6b7a8d;text-align:right;">Total Valid:</td>
                    <td style="padding:10px 14px;font-weight:700;color:#1a7f4b;">
                        {{ number_format($produksi->where('status_validasi','valid')->sum('berat'), 2) }} kg
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    @if($produksi->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f6;">
        {{ $produksi->links() }}
    </div>
    @endif
</div>
@endsection