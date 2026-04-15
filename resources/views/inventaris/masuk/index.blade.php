{{-- resources/views/inventaris/masuk/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Inventaris Masuk')
@section('page_title', 'Riwayat Inventaris Masuk')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-arrow-down" style="color:#1a73e8;margin-right:8px;"></i>Daftar Inventaris Masuk</h3>
        <a href="{{ route('inventaris.masuk.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Catat Masuk
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Tanggal</th><th>Vendor</th><th>Jumlah Item</th><th>Keterangan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($masuk as $i => $m)
                <tr>
                    <td>{{ $masuk->firstItem() + $i }}</td>
                    <td>{{ $m->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $m->vendor->nama_vendor }}</td>
                    <td><span class="badge badge-info">{{ $m->detail->count() }} item</span></td>
                    <td>{{ $m->keterangan ?? '-' }}</td>
                    <td>
                        <button onclick="toggleDetail('detail-{{ $m->id }}')" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                    </td>
                </tr>
                <tr id="detail-{{ $m->id }}" style="display:none;background:#f8fafc;">
                    <td colspan="6" style="padding:12px 20px;">
                        <table style="width:100%;font-size:13px;">
                            <thead><tr><th>Barang</th><th>Jumlah</th></tr></thead>
                            <tbody>
                                @foreach($m->detail as $d)
                                <tr>
                                    <td>{{ $d->inventaris->nama_barang }}</td>
                                    <td>{{ number_format($d->jumlah) }} {{ $d->inventaris->satuan }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#adb5bd;padding:32px;">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($masuk->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f6;">{{ $masuk->links() }}</div>
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