{{-- resources/views/blok/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Data Blok')
@section('page_title', 'Data Blok')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-map" style="color:#1a7f4b;margin-right:8px;"></i>Daftar Blok</h3>
        <a href="{{ route('blok.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Blok
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Blok</th>
                    <th>Jenis</th>
                    <th>Luas (Ha)</th>
                    <th>Total Pohon</th>
                    <th>Produktif</th>
                    <th>Penyadap</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blok as $i => $b)
                <tr>
                    <td>{{ $blok->firstItem() + $i }}</td>
                    <td><strong>{{ $b->nama_blok }}</strong></td>
                    <td>
                        @if($b->jenis_blok)
                            <span class="badge badge-info">{{ $b->jenis_blok }}</span>
                        @else
                            <span style="color:#adb5bd">-</span>
                        @endif
                    </td>
                    <td>{{ number_format($b->luas, 2) }}</td>
                    <td>{{ number_format($b->total_pohon) }}</td>
                    <td>
                        <span class="badge badge-success">{{ number_format($b->pohon_produktif) }}</span>
                        <span style="font-size:11px;color:#adb5bd;">/ {{ number_format($b->pohon_tidak_produktif) }} tidak</span>
                    </td>
                    <td><span class="badge badge-gray">{{ $b->penyadap_count }} orang</span></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('blok.show', $b) }}" class="btn btn-outline btn-sm btn-icon" title="Detail & Tugaskan">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('blok.edit', $b) }}" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('blok.destroy', $b) }}"
                                  onsubmit="return confirm('Hapus blok ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#adb5bd;padding:32px;">
                        <i class="fas fa-map" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        Belum ada data blok
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($blok->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f6;">
        {{ $blok->links() }}
    </div>
    @endif
</div>
@endsection