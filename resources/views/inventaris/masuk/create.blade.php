{{-- resources/views/inventaris/masuk/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Inventaris Masuk')
@section('page_title', 'Catat Inventaris Masuk')

@section('content')
<div class="card" style="max-width:720px;">
    <div class="card-header">
        <h3><i class="fas fa-arrow-down" style="color:#1a73e8;margin-right:8px;"></i>Form Inventaris Masuk</h3>
        <a href="{{ route('inventaris.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('inventaris.masuk.store') }}">
            @csrf
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Vendor <span style="color:red">*</span></label>
                    <select name="vendor_id" class="form-control form-select" required>
                        <option value="">— Pilih Vendor —</option>
                        @foreach($vendor as $v)
                            <option value="{{ $v->id }}">{{ $v->nama_vendor }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal <span style="color:red">*</span></label>
                    <input type="date" name="tanggal" class="form-control"
                           value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan..."></textarea>
            </div>

            <div style="margin-bottom:12px;">
                <label class="form-label">Detail Barang <span style="color:red">*</span></label>
                <div id="detail-list" style="display:flex;flex-direction:column;gap:8px;">
                    <div class="detail-row" style="display:flex;gap:8px;align-items:center;">
                        <select name="detail[0][inventaris_id]" class="form-control form-select" style="flex:2;" required>
                            <option value="">— Pilih Barang —</option>
                            @foreach($barang as $b)
                                <option value="{{ $b->id }}">{{ $b->nama_barang }} ({{ $b->satuan }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="detail[0][jumlah]" class="form-control" placeholder="Jumlah" min="1" required style="width:100px;">
                        <button type="button" onclick="hapusRow(this)" class="btn btn-danger btn-sm btn-icon">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" onclick="tambahRow()" class="btn btn-outline btn-sm" style="margin-top:8px;">
                    <i class="fas fa-plus"></i> Tambah Barang
                </button>
            </div>

            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('inventaris.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let idx = 1;
const barang = @json($barang->map(fn($b) => ['id' => $b->id, 'nama' => $b->nama_barang . ' (' . $b->satuan . ')']));

function tambahRow() {
    const opts = barang.map(b => `<option value="${b.id}">${b.nama}</option>`).join('');
    const row = document.createElement('div');
    row.className = 'detail-row';
    row.style.cssText = 'display:flex;gap:8px;align-items:center;';
    row.innerHTML = `
        <select name="detail[${idx}][inventaris_id]" class="form-control" style="flex:2;" required>
            <option value="">— Pilih Barang —</option>${opts}
        </select>
        <input type="number" name="detail[${idx}][jumlah]" class="form-control" placeholder="Jumlah" min="1" required style="width:100px;">
        <button type="button" onclick="hapusRow(this)" class="btn btn-danger btn-sm btn-icon">
            <i class="fas fa-times"></i>
        </button>`;
    document.getElementById('detail-list').appendChild(row);
    idx++;
}

function hapusRow(btn) {
    const rows = document.querySelectorAll('.detail-row');
    if (rows.length > 1) btn.closest('.detail-row').remove();
}
</script>
@endpush