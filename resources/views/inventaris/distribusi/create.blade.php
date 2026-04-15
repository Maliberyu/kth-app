{{-- resources/views/inventaris/distribusi/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Distribusi Inventaris')
@section('page_title', 'Catat Distribusi Inventaris')

@section('content')
<div class="card" style="max-width:720px;">
    <div class="card-header">
        <h3><i class="fas fa-arrow-up" style="color:#f0a500;margin-right:8px;"></i>Form Distribusi Inventaris</h3>
        <a href="{{ route('inventaris.distribusi') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">

        @if(session('error'))
        <div class="alert alert-error" style="margin-bottom:16px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:16px;">
            <ul style="margin:0;padding-left:16px;">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('inventaris.distribusi.store') }}">
            @csrf
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Penyadap Penerima <span style="color:red">*</span></label>
                    <select name="penyadap_id" class="form-control form-select" required>
                        <option value="">— Pilih Penyadap —</option>
                        @foreach($penyadap as $p)
                        <option value="{{ $p->id }}" {{ old('penyadap_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('penyadap_id')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal <span style="color:red">*</span></label>
                    <input type="date" name="tanggal" class="form-control"
                           value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2"
                          placeholder="Keterangan distribusi...">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Detail Barang --}}
            <div style="margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <label class="form-label" style="margin:0;">Detail Barang <span style="color:red">*</span></label>
                    <button type="button" onclick="tambahRow()" class="btn btn-outline btn-sm">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                </div>

                {{-- Header --}}
                <div style="display:grid;grid-template-columns:2fr 1fr 36px;gap:8px;margin-bottom:6px;padding:0 4px;">
                    <span style="font-size:11px;font-weight:600;color:#6b7a8d;text-transform:uppercase;">Nama Barang</span>
                    <span style="font-size:11px;font-weight:600;color:#6b7a8d;text-transform:uppercase;">Jumlah</span>
                    <span></span>
                </div>

                <div id="detail-list" style="display:flex;flex-direction:column;gap:8px;">
                    <div class="detail-row" style="display:grid;grid-template-columns:2fr 1fr 36px;gap:8px;align-items:center;">
                        <select name="detail[0][inventaris_id]" class="form-control form-select" required>
                            <option value="">— Pilih Barang —</option>
                            @foreach($barang as $b)
                            <option value="{{ $b->id }}"
                                    data-stok="{{ $b->stok?->total_stok ?? 0 }}"
                                    {{ old('detail.0.inventaris_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->nama_barang }}
                                (Stok: {{ number_format($b->stok?->total_stok ?? 0) }} {{ $b->satuan }})
                            </option>
                            @endforeach
                        </select>
                        <input type="number" name="detail[0][jumlah]" class="form-control"
                               placeholder="Jumlah" min="1" required
                               value="{{ old('detail.0.jumlah') }}">
                        <button type="button" onclick="hapusRow(this)" class="btn btn-danger btn-sm btn-icon">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
                    <i class="fas fa-save"></i> Simpan Distribusi
                </button>
                <a href="{{ route('inventaris.distribusi') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let idx = 1;
const barangList = @json($barang->map(fn($b) => [
    'id'   => $b->id,
    'nama' => $b->nama_barang,
    'stok' => $b->stok?->total_stok ?? 0,
    'satuan' => $b->satuan ?? '',
]));

function tambahRow() {
    const opts = barangList.map(b =>
        `<option value="${b.id}" data-stok="${b.stok}">${b.nama} (Stok: ${b.stok} ${b.satuan})</option>`
    ).join('');

    const row = document.createElement('div');
    row.className = 'detail-row';
    row.style.cssText = 'display:grid;grid-template-columns:2fr 1fr 36px;gap:8px;align-items:center;';
    row.innerHTML = `
        <select name="detail[${idx}][inventaris_id]" class="form-control" required>
            <option value="">— Pilih Barang —</option>${opts}
        </select>
        <input type="number" name="detail[${idx}][jumlah]" class="form-control"
               placeholder="Jumlah" min="1" required>
        <button type="button" onclick="hapusRow(this)" class="btn btn-danger btn-sm btn-icon">
            <i class="fas fa-times"></i>
        </button>`;
    document.getElementById('detail-list').appendChild(row);
    idx++;
}

function hapusRow(btn) {
    const rows = document.querySelectorAll('.detail-row');
    if (rows.length > 1) btn.closest('.detail-row').remove();
    else alert('Minimal harus ada 1 barang.');
}
</script>
@endpush