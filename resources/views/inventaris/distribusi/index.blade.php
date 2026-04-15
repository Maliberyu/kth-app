{{-- resources/views/inventaris/distribusi/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Distribusi Inventaris')
@section('page_title', 'Riwayat Distribusi Inventaris')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-arrow-up" style="color:#f0a500;margin-right:8px;"></i>Daftar Distribusi</h3>
        <a href="{{ route('inventaris.distribusi.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Catat Distribusi
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Tanggal</th><th>Penyadap</th><th>Jumlah Item</th><th>Keterangan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($distribusi as $i => $d)
                <tr>
                    <td>{{ $distribusi->firstItem() + $i }}</td>
                    <td>{{ $d->tanggal->format('d/m/Y') }}</td>
                    <td><strong>{{ $d->penyadap->nama }}</strong></td>
                    <td><span class="badge badge-warning">{{ $d->detail->count() }} item</span></td>
                    <td>{{ $d->keterangan ?? '-' }}</td>
                    <td>
                        <button onclick="toggleDetail('dist-{{ $d->id }}')" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>
                <tr id="dist-{{ $d->id }}" style="display:none;background:#f8fafc;">
                    <td colspan="6" style="padding:12px 20px;">
                        <table style="width:100%;font-size:13px;">
                            <thead><tr><th>Barang</th><th>Jumlah</th></tr></thead>
                            <tbody>
                                @foreach($d->detail as $item)
                                <tr>
                                    <td>{{ $item->inventaris->nama_barang }}</td>
                                    <td>{{ number_format($item->jumlah) }} {{ $item->inventaris->satuan }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#adb5bd;padding:32px;">Belum ada data distribusi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($distribusi->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f6;">{{ $distribusi->links() }}</div>
    @endif
</div>
@endsection
@push('scripts')
<script>
function toggleDetail(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? '' : 'none';
}
</script>
@endpush