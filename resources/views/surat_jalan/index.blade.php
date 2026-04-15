{{-- resources/views/surat_jalan/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Surat Jalan')
@section('page_title', 'Surat Jalan')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-truck" style="color:#1a7f4b;margin-right:8px;"></i>Daftar Surat Jalan</h3>
        <a href="{{ route('surat-jalan.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Buat Surat Jalan
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Vendor</th>
                    <th>Penyimpanan</th>
                    <th>Total Berat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suratJalan as $sj)
                <tr>
                    <td><strong>{{ $sj->nomor }}</strong></td>
                    <td>{{ $sj->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $sj->vendor->nama_vendor }}</td>
                    <td>{{ $sj->penyimpanan->nama_lokasi }}</td>
                    <td><strong>{{ number_format($sj->total_berat, 2) }} kg</strong></td>
                    <td>
                        @php
                            $badge = match($sj->status) {
                                'selesai'    => 'badge-success',
                                'dikirim'    => 'badge-info',
                                'draft'      => 'badge-gray',
                                'dibatalkan' => 'badge-danger',
                                default      => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($sj->status) }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('surat-jalan.show', $sj) }}" class="btn btn-outline btn-sm btn-icon" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($sj->status === 'draft')
                            <form method="POST" action="{{ route('surat-jalan.kirim', $sj) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-primary btn-sm" title="Kirim">
                                    <i class="fas fa-paper-plane"></i> Kirim
                                </button>
                            </form>
                            @endif
                            @if($sj->status === 'dikirim')
                            <form method="POST" action="{{ route('surat-jalan.selesai', $sj) }}"
                                  onsubmit="return confirm('Tandai selesai? Stok akan dikurangi.')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check"></i> Selesai
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:#adb5bd;padding:32px;">Belum ada surat jalan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suratJalan->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f6;">
        {{ $suratJalan->links() }}
    </div>
    @endif
</div>
@endsection