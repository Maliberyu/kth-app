{{-- resources/views/produksi/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Produksi Getah')
@section('page_title', 'Produksi Getah')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-droplet" style="color:#1a7f4b;margin-right:8px;"></i>Data Produksi Getah</h3>
        <a href="{{ route('produksi.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Input Produksi
        </a>
    </div>

    {{-- Filter --}}
    <div style="padding:16px 20px;border-bottom:1px solid #f0f3f6;background:#fafbfc;">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label class="form-label" style="font-size:12px;">Status</label>
                <select name="status" class="form-control form-select" style="width:140px;">
                    <option value="">Semua</option>
                    <option value="pending"  {{ request('status')==='pending'  ? 'selected':'' }}>Pending</option>
                    <option value="valid"    {{ request('status')==='valid'    ? 'selected':'' }}>Valid</option>
                    <option value="ditolak"  {{ request('status')==='ditolak'  ? 'selected':'' }}>Ditolak</option>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size:12px;">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}" style="width:160px;">
            </div>
            <div>
                <label class="form-label" style="font-size:12px;">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}" style="width:160px;">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('produksi.index') }}" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Penyadap</th>
                    <th>Blok</th>
                    <th>Penyimpanan</th>
                    <th>Berat (kg)</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produksi as $i => $p)
                <tr>
                    <td>{{ $produksi->firstItem() + $i }}</td>
                    <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td><strong>{{ $p->penyadap->nama }}</strong></td>
                    <td>{{ $p->blok->nama_blok }}</td>
                    <td>{{ $p->penyimpanan->nama_lokasi }}</td>
                    <td><strong>{{ number_format($p->berat, 2) }}</strong></td>
                    <td>
                        <span class="badge {{ match($p->status_validasi) {
                            'valid'   => 'badge-success',
                            'ditolak' => 'badge-danger',
                            default   => 'badge-warning'
                        } }}">{{ ucfirst($p->status_validasi) }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            @if($p->status_validasi === 'pending')
                            <button type="button"
                                onclick="openValidasi({{ $p->id }})"
                                class="btn btn-primary btn-sm">
                                <i class="fas fa-check"></i> Validasi
                            </button>
                            <a href="{{ route('produksi.edit', $p) }}" class="btn btn-outline btn-sm btn-icon">
                                <i class="fas fa-pen"></i>
                            </a>
                            @endif
                            @if($p->status_validasi === 'valid')
                            <span style="font-size:12px;color:#1a7f4b;"><i class="fas fa-check-circle"></i> Tervalidasi</span>
                            @endif
                            @if($p->status_validasi === 'ditolak')
                            <span style="font-size:12px;color:#dc3545;"><i class="fas fa-times-circle"></i> Ditolak</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#adb5bd;padding:32px;">Belum ada data produksi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($produksi->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f0f3f6;">
        {{ $produksi->links() }}
    </div>
    @endif
</div>

{{-- Modal Validasi --}}
<div id="modal-validasi" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:420px;margin:16px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h4 style="margin:0;font-size:16px;font-weight:700;">Validasi Produksi</h4>
            <button type="button" onclick="closeValidasi()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#6b7a8d;line-height:1;">×</button>
        </div>
        <form id="form-validasi" method="POST">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label class="form-label">Status Validasi <span style="color:red">*</span></label>
                <select name="status_validasi" class="form-control form-select" required>
                    <option value="valid">✅ Valid — Masukkan ke stok getah</option>
                    <option value="ditolak">❌ Tolak</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan (opsional)</label>
                <textarea name="catatan" class="form-control" rows="3" placeholder="Alasan penolakan atau catatan tambahan..."></textarea>
            </div>
            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
                    <i class="fas fa-save"></i> Simpan Validasi
                </button>
                <button type="button" onclick="closeValidasi()" class="btn btn-outline">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openValidasi(id) {
    document.getElementById('form-validasi').action = '{{ url("/produksi") }}/' + id + '/validasi';
    document.getElementById('modal-validasi').style.display = 'flex';
}
function closeValidasi() {
    document.getElementById('modal-validasi').style.display = 'none';
}
// Tutup modal jika klik backdrop
document.getElementById('modal-validasi').addEventListener('click', function(e) {
    if (e.target === this) closeValidasi();
});
</script>
@endpush