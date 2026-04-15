{{-- resources/views/penjualan/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Penjualan')
@section('page_title', 'Transaksi Penjualan Baru')

@section('content')

{{-- Cek jika tidak ada surat jalan --}}
@if($suratJalan->isEmpty())
<div class="alert alert-error" style="margin-bottom:20px;">
    <i class="fas fa-exclamation-triangle"></i>
    Belum ada surat jalan dengan status <strong>dikirim</strong> atau <strong>selesai</strong>.
    Buat surat jalan terlebih dahulu.
    <a href="{{ route('surat-jalan.create') }}" class="btn btn-danger btn-sm" style="margin-left:10px;">Buat Surat Jalan</a>
</div>
@endif

<div class="card" style="max-width:860px;">
    <div class="card-header">
        <h3><i class="fas fa-money-bill-wave" style="color:#1a7f4b;margin-right:8px;"></i>Form Penjualan</h3>
        <a href="{{ route('penjualan.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('penjualan.store') }}">
            @csrf

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Surat Jalan <span style="color:red">*</span></label>
                    <select name="surat_jalan_id" class="form-control form-select" required
                            onchange="isiVendor(this)">
                        <option value="">— Pilih Surat Jalan —</option>
                        @foreach($suratJalan as $sj)
                        <option value="{{ $sj->id }}"
                                data-vendor="{{ $sj->vendor_id }}"
                                data-berat="{{ $sj->total_berat }}"
                                {{ old('surat_jalan_id') == $sj->id ? 'selected' : '' }}>
                            {{ $sj->nomor }} —
                            {{ number_format($sj->total_berat,2) }} kg —
                            {{ ucfirst($sj->status) }}
                        </option>
                        @endforeach
                    </select>
                    @error('surat_jalan_id')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Vendor <span style="color:red">*</span></label>
                    <select name="vendor_id" id="select-vendor" class="form-control form-select" required>
                        <option value="">— Pilih Vendor —</option>
                        @foreach($vendor as $v)
                        <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->nama_vendor }}
                        </option>
                        @endforeach
                    </select>
                    @error('vendor_id')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Periode <span style="color:red">*</span></label>
                <select name="periode_id" class="form-control form-select" required>
                    <option value="">— Pilih Periode —</option>
                    @foreach($periode as $p)
                    @if($p->id > 0)
                    <option value="{{ $p->id }}" {{ old('periode_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_periode }}
                    </option>
                    @endif
                    @endforeach
                </select>
                @error('periode_id')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                @if($periode->where('id',0)->count() > 0)
                <div style="font-size:12px;color:#f0a500;margin-top:4px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Belum ada periode. <a href="#" style="color:#f0a500;">Tambah periode</a> terlebih dahulu.
                </div>
                @endif
            </div>

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Total Berat (kg) <span style="color:red">*</span></label>
                    <input type="number" name="total_berat" id="total_berat" class="form-control"
                           value="{{ old('total_berat') }}"
                           step="0.01" min="0.01" required
                           oninput="hitungTotal()">
                    @error('total_berat')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Harga Jual/kg (Rp) <span style="color:red">*</span></label>
                    <input type="number" name="harga_jual" id="harga_jual" class="form-control"
                           value="{{ old('harga_jual') }}"
                           step="100" min="0" required
                           oninput="hitungTotal()">
                    @error('harga_jual')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Preview total --}}
            <div style="background:#e8f5ee;border-radius:10px;padding:14px 18px;margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;color:#145f38;font-weight:600;">
                    <i class="fas fa-calculator"></i> Total Penjualan
                </span>
                <span id="preview-total" style="font-size:22px;font-weight:700;color:#1a7f4b;">Rp 0</span>
            </div>

            {{-- Detail per penyadap --}}
            <div style="margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <label class="form-label" style="margin:0;">Detail per Penyadap <span style="color:red">*</span></label>
                    <button type="button" onclick="tambahRow()" class="btn btn-outline btn-sm">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                </div>

                {{-- Header kolom --}}
                <div style="display:grid;grid-template-columns:2fr 1.5fr 1fr 1fr 36px;gap:8px;margin-bottom:6px;padding:0 4px;">
                    <span style="font-size:11px;font-weight:600;color:#6b7a8d;text-transform:uppercase;">Penyadap</span>
                    <span style="font-size:11px;font-weight:600;color:#6b7a8d;text-transform:uppercase;">Blok</span>
                    <span style="font-size:11px;font-weight:600;color:#6b7a8d;text-transform:uppercase;">Berat (kg)</span>
                    <span style="font-size:11px;font-weight:600;color:#6b7a8d;text-transform:uppercase;">Harga Beli/kg</span>
                    <span></span>
                </div>

                <div id="detail-list" style="display:flex;flex-direction:column;gap:8px;">
                    <div class="detail-row" style="display:grid;grid-template-columns:2fr 1.5fr 1fr 1fr 36px;gap:8px;align-items:center;">
                        <select name="detail[0][penyadap_id]" class="form-control form-select" required>
                            <option value="">— Penyadap —</option>
                            @foreach($penyadap as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                        <select name="detail[0][blok_id]" class="form-control form-select" required>
                            <option value="">— Blok —</option>
                            @foreach($blok as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_blok }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="detail[0][berat]" class="form-control"
                               placeholder="0.00" step="0.01" min="0.01" required>
                        <input type="number" name="detail[0][harga_beli]" class="form-control"
                               placeholder="0" step="100" min="0" required>
                        <button type="button" onclick="hapusRow(this)" class="btn btn-danger btn-sm btn-icon" title="Hapus">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:16px;">
                <ul style="margin:0;padding-left:16px;">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
                    <i class="fas fa-save"></i> Simpan Penjualan
                </button>
                <a href="{{ route('penjualan.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let idx = 1;
const penyadapList = @json($penyadap->map(fn($p) => ['id'=>$p->id,'nama'=>$p->nama]));
const blokList     = @json($blok->map(fn($b) => ['id'=>$b->id,'nama'=>$b->nama_blok]));

function hitungTotal() {
    const berat = parseFloat(document.getElementById('total_berat').value) || 0;
    const harga = parseFloat(document.getElementById('harga_jual').value) || 0;
    document.getElementById('preview-total').textContent =
        'Rp ' + (berat * harga).toLocaleString('id-ID');
}

function isiVendor(sel) {
    const opt = sel.options[sel.selectedIndex];
    const vendorId = opt.getAttribute('data-vendor');
    const berat    = opt.getAttribute('data-berat');
    if (vendorId) {
        document.getElementById('select-vendor').value = vendorId;
    }
    if (berat) {
        document.getElementById('total_berat').value = berat;
        hitungTotal();
    }
}

function tambahRow() {
    const pOpts = penyadapList.map(p => `<option value="${p.id}">${p.nama}</option>`).join('');
    const bOpts = blokList.map(b => `<option value="${b.id}">${b.nama}</option>`).join('');
    const row = document.createElement('div');
    row.className = 'detail-row';
    row.style.cssText = 'display:grid;grid-template-columns:2fr 1.5fr 1fr 1fr 36px;gap:8px;align-items:center;';
    row.innerHTML = `
        <select name="detail[${idx}][penyadap_id]" class="form-control" required>
            <option value="">— Penyadap —</option>${pOpts}
        </select>
        <select name="detail[${idx}][blok_id]" class="form-control" required>
            <option value="">— Blok —</option>${bOpts}
        </select>
        <input type="number" name="detail[${idx}][berat]" class="form-control" placeholder="0.00" step="0.01" min="0.01" required>
        <input type="number" name="detail[${idx}][harga_beli]" class="form-control" placeholder="0" step="100" min="0" required>
        <button type="button" onclick="hapusRow(this)" class="btn btn-danger btn-sm btn-icon">
            <i class="fas fa-times"></i>
        </button>`;
    document.getElementById('detail-list').appendChild(row);
    idx++;
}

function hapusRow(btn) {
    if (document.querySelectorAll('.detail-row').length > 1) {
        btn.closest('.detail-row').remove();
    }
}
</script>
@endpush