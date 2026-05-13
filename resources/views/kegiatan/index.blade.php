@extends('layouts.app')
@section('title', 'Kegiatan ' . ($tipe === 'dalam_ruangan' ? 'Dalam Ruangan' : 'Luar Ruangan'))
@section('page_title', 'Kegiatan ' . ($tipe === 'dalam_ruangan' ? 'Dalam Ruangan' : 'Luar Ruangan'))

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div style="display:flex; gap:8px;">
        <a href="{{ route('kegiatan.index', ['tipe'=>'dalam_ruangan']) }}"
           class="btn {{ $tipe === 'dalam_ruangan' ? 'btn-primary' : 'btn-outline' }}">
            <i class="fas fa-door-open"></i> Dalam Ruangan
        </a>
        <a href="{{ route('kegiatan.index', ['tipe'=>'luar_ruangan']) }}"
           class="btn {{ $tipe === 'luar_ruangan' ? 'btn-primary' : 'btn-outline' }}"
           style="{{ $tipe === 'luar_ruangan' ? '' : 'opacity:.6; pointer-events:none;' }}"
           title="Coming soon">
            <i class="fas fa-tree"></i> Luar Ruangan
            <span style="font-size:10px; background:#f0a500; color:#fff; padding:1px 6px; border-radius:10px; margin-left:2px;">Soon</span>
        </a>
    </div>
    @if($tipe === 'dalam_ruangan')
    <a href="{{ route('kegiatan.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Buat Kegiatan
    </a>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-calendar-alt" style="color:#1a7f4b;margin-right:8px;"></i>
            Daftar Kegiatan {{ $tipe === 'dalam_ruangan' ? 'Dalam Ruangan' : 'Luar Ruangan' }}
        </h3>
        <span style="font-size:12px; color:#6b7a8d;">{{ $kegiatans->total() }} kegiatan</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Kegiatan</th>
                    <th>Lokasi</th>
                    <th>Tanggal Mulai</th>
                    <th>Peserta</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatans as $k)
                <tr>
                    <td>
                        <strong>{{ $k->nama_kegiatan }}</strong>
                        @if($k->deskripsi)
                        <div style="font-size:12px; color:#6b7a8d; margin-top:2px;">{{ Str::limit($k->deskripsi, 60) }}</div>
                        @endif
                    </td>
                    <td><i class="fas fa-map-marker-alt" style="color:#6b7a8d;margin-right:4px;"></i>{{ $k->lokasi }}</td>
                    <td>{{ $k->tanggal_mulai->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge badge-info">
                            <i class="fas fa-users" style="margin-right:4px;"></i>{{ $k->peserta_count }}
                        </span>
                    </td>
                    <td>
                        @php
                            $badge = match($k->status) {
                                'aktif'  => 'badge-success',
                                'selesai'=> 'badge-info',
                                'batal'  => 'badge-danger',
                                default  => 'badge-gray',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($k->status) }}</span>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('kegiatan.show', $k) }}" class="btn btn-outline btn-sm btn-icon" title="Detail & QR Code">
                                <i class="fas fa-qrcode"></i>
                            </a>
                            <a href="{{ route('kegiatan.edit', $k) }}" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('kegiatan.destroy', $k) }}"
                                  onsubmit="return confirm('Hapus kegiatan ini? Semua data peserta akan ikut terhapus.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-icon" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#adb5bd; padding:40px;">
                        <i class="fas fa-calendar-xmark" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                        Belum ada kegiatan. <a href="{{ route('kegiatan.create') }}" style="color:var(--primary);">Buat sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kegiatans->hasPages())
    <div style="padding:16px 20px; border-top:1px solid #f0f3f6;">
        {{ $kegiatans->appends(['tipe' => $tipe])->links() }}
    </div>
    @endif
</div>
@endsection
