{{-- resources/views/penyadap/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Data Penyadap')
@section('page_title', 'Data Penyadap')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users" style="color:#1a7f4b;margin-right:8px;"></i>Daftar Penyadap</h3>
        <a href="{{ route('penyadap.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Penyadap
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>No. HP</th>
                    <th>BPJS</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penyadap as $i => $p)
                <tr>
                    <td>{{ $penyadap->firstItem() + $i }}</td>
                    <td>
                        <strong>{{ $p->nama }}</strong>
                        <div style="font-size:11px;color:#adb5bd;">{{ $p->alamat }}</div>
                    </td>
                    <td>{{ $p->nik ?? '-' }}</td>
                    <td>{{ $p->no_hp ?? '-' }}</td>
                    <td>
                        @foreach($p->bpjs as $b)
                            <span class="badge {{ $b->status_aktif === 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                                {{ $b->jenis_bpjs }}
                            </span>
                        @endforeach
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('penyadap.show', $p) }}" class="btn btn-outline btn-sm btn-icon" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('penyadap.edit', $p) }}" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('penyadap.destroy', $p) }}"
                                  onsubmit="return confirm('Hapus penyadap ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:#adb5bd;padding:32px;">
                        <i class="fas fa-users" style="font-size:32px;margin-bottom:8px;display:block;"></i>
                        Belum ada data penyadap
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($penyadap->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f6;">
        {{ $penyadap->links() }}
    </div>
    @endif
</div>
@endsection