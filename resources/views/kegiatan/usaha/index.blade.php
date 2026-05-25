@extends('layouts.app')
@section('title', 'Kegiatan Usaha')
@section('page_title', 'Kegiatan Usaha')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div></div>
    <a href="{{ route('kegiatan-usaha.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Buat Kegiatan Usaha
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-briefcase" style="color:#1a7f4b;margin-right:8px;"></i>Daftar Kegiatan Usaha</h3>
        <span style="font-size:12px; color:#6b7a8d;">{{ $usahas->total() }} kegiatan</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Usaha</th>
                    <th>Jenis</th>
                    <th>Tanggal Mulai</th>
                    <th>Catatan Harian</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usahas as $u)
                <tr>
                    <td>
                        <strong>{{ $u->nama_usaha }}</strong>
                        @if($u->deskripsi)
                        <div style="font-size:12px; color:#6b7a8d; margin-top:2px;">{{ Str::limit(strip_tags($u->deskripsi), 60) }}</div>
                        @endif
                    </td>
                    <td>{{ $u->jenis_usaha ?? '-' }}</td>
                    <td>{{ $u->tanggal_mulai->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-info">
                            <i class="fas fa-book" style="margin-right:4px;"></i>{{ $u->harian_count }} entri
                        </span>
                    </td>
                    <td>
                        @php $badge = match($u->status) { 'aktif'=>'badge-success','selesai'=>'badge-info','tutup'=>'badge-danger',default=>'badge-gray' }; @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($u->status) }}</span>
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:6px; justify-content:center;">
                            <a href="{{ route('kegiatan-usaha.show', $u) }}" class="btn btn-outline btn-sm btn-icon" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('kegiatan-usaha.edit', $u) }}" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('kegiatan-usaha.destroy', $u) }}"
                                  onsubmit="return confirm('Hapus kegiatan usaha ini beserta semua data di dalamnya?')">
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
                        <i class="fas fa-briefcase" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                        Belum ada kegiatan usaha. <a href="{{ route('kegiatan-usaha.create') }}" style="color:var(--primary);">Buat sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($usahas->hasPages())
    <div style="padding:16px 20px; border-top:1px solid #f0f3f6;">{{ $usahas->links() }}</div>
    @endif
</div>
@endsection
