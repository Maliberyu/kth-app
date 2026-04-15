{{-- resources/views/kth/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Data KTH')
@section('page_title', 'Manajemen KTH')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-sitemap" style="color:#1a7f4b;margin-right:8px;"></i>Daftar KTH</h3>
        <a href="{{ route('super.kth.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah KTH
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama KTH</th>
                    <th>Alamat</th>
                    <th>Penyadap</th>
                    <th>Blok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kth as $i => $k)
                <tr>
                    <td>{{ $kth->firstItem() + $i }}</td>
                    <td><strong>{{ $k->nama_kth }}</strong></td>
                    <td>{{ $k->alamat ?? '-' }}</td>
                    <td><span class="badge badge-info">{{ $k->penyadap_count }} orang</span></td>
                    <td><span class="badge badge-gray">{{ $k->blok_count }} blok</span></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('super.kth.show', $k) }}" class="btn btn-outline btn-sm btn-icon" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('super.kth.edit', $k) }}" class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('super.kth.destroy', $k) }}"
                                  onsubmit="return confirm('Hapus KTH ini? Semua data terkait akan ikut terhapus!')">
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
                    <td colspan="6" style="text-align:center;color:#adb5bd;padding:32px;">Belum ada KTH</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kth->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f6;">
        {{ $kth->links() }}
    </div>
    @endif
</div>
@endsection