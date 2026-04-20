{{-- resources/views/produksi/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Produksi #' . $produksi->id)
@section('page_title', ' Detail Produksi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="card" style="max-width:700px;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-droplet" style="color:#1a7f4b;margin-right:8px;"></i>Detail Produksi</h3>
            <a href="{{ url()->previous() }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        
        <div class="card-body">
            {{-- Badge Status --}}
            <div class="mb-4">
                @php
                    $badgeClass = match($produksi->status_validasi) {
                        'valid' => 'badge-success',
                        'ditolak' => 'badge-danger',
                        default => 'badge-warning'
                    };
                @endphp
                <span class="badge {{ $badgeClass }}" style="font-size:14px;padding:6px 12px;">
                    {{ ucfirst($produksi->status_validasi) }}
                </span>
            </div>
            
            {{-- Info Table --}}
            <table class="table table-borderless" style="width:100%;">
                <tr>
                    <td style="width:40%;color:#6b7a8d;padding:8px 0;">ID Produksi</td>
                    <td style="padding:8px 0;"><strong>#{{ str_pad($produksi->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:8px 0;">Tanggal</td>
                    <td style="padding:8px 0;">{{ \Carbon\Carbon::parse($produksi->tanggal)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:8px 0;">Blok</td>
                    <td style="padding:8px 0;">
                        <strong>{{ $produksi->blok->nama_blok ?? '-' }}</strong>
                        <br><small class="text-muted">{{ number_format($produksi->blok->luas ?? 0, 2) }} Ha</small>
                    </td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:8px 0;">Penyadap</td>
                    <td style="padding:8px 0;">{{ $produksi->penyadap->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:8px 0;">Penyimpanan</td>
                    <td style="padding:8px 0;">{{ $produksi->penyimpanan->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:8px 0;">Berat Getah</td>
                    <td style="padding:8px 0;" class="font-bold text-success" style="font-size:18px;">
                        {{ number_format($produksi->berat, 2) }} kg
                    </td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:8px 0;">Catatan</td>
                    <td style="padding:8px 0;">{{ $produksi->catatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:8px 0;">Diinput Oleh</td>
                    <td style="padding:8px 0;">{{ $produksi->diinputOleh->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:8px 0;">Dibuat</td>
                    <td style="padding:8px 0;">{{ $produksi->created_at?->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:8px 0;">Terakhir Update</td>
                    <td style="padding:8px 0;">{{ $produksi->updated_at?->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
            
            {{-- Tombol Validasi (Hanya untuk Admin KTH) --}}
            @if(auth()->check() && auth()->user()->role === 'admin_kth' && $produksi->status_validasi === 'pending')
            <hr style="margin:24px 0;">
            <h5 style="margin-bottom:12px;">⚙️ Validasi Produksi</h5>
            <form method="POST" action="{{ route('produksi.validasi', $produksi) }}" class="d-flex gap-2 flex-wrap">
                @csrf @method('PATCH')
                <select name="status_validasi" class="form-control form-select" style="width:auto;min-width:150px;">
                    <option value="valid">✅ Setujui</option>
                    <option value="ditolak">❌ Tolak</option>
                </select>
                <input type="text" name="catatan" class="form-control" placeholder="Catatan validasi..." style="flex:1;min-width:200px;">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection