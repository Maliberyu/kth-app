{{-- resources/views/produksi/penyadap_create.blade.php --}}
@extends('layouts.app')
@section('title', 'Input Produksi')
@section('page_title', 'Input Produksi Getah')

@section('content')
<div class="card" style="max-width:560px;">
    <div class="card-header">
        <h3><i class="fas fa-droplet" style="color:#1a7f4b;margin-right:8px;"></i>Form Input Produksi</h3>
        <a href="{{ route('saya.produksi') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <div style="background:#e8f5ee;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-user" style="color:#1a7f4b;"></i>
            <div>
                <div style="font-size:13px;font-weight:600;color:#145f38;">{{ auth()->user()->nama }}</div>
                <div style="font-size:12px;color:#6b7a8d;">Produksi akan dicatat atas nama Anda</div>
            </div>
        </div>

        <form method="POST" action="{{ route('saya.produksi.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Blok <span style="color:red">*</span></label>
                <select name="blok_id" class="form-control form-select" required>
                    <option value="">— Pilih Blok —</option>
                    @foreach($blok as $b)
                        <option value="{{ $b->id }}" {{ old('blok_id')==$b->id ? 'selected':'' }}>
                            {{ $b->nama_blok }} ({{ number_format($b->pohon_produktif) }} pohon produktif)
                        </option>
                    @endforeach
                </select>
                @error('blok_id')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Penyimpanan <span style="color:red">*</span></label>
                <select name="penyimpanan_id" class="form-control form-select" required>
                    <option value="">— Pilih Penyimpanan —</option>
                    @foreach($penyimpanan as $ps)
                        <option value="{{ $ps->id }}" {{ old('penyimpanan_id')==$ps->id ? 'selected':'' }}>
                            {{ $ps->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
                @error('penyimpanan_id')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Tanggal <span style="color:red">*</span></label>
                    <input type="date" name="tanggal" class="form-control"
                           value="{{ old('tanggal', date('Y-m-d')) }}" required
                           max="{{ date('Y-m-d') }}">
                    @error('tanggal')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Berat (kg) <span style="color:red">*</span></label>
                    <input type="number" name="berat" class="form-control"
                           value="{{ old('berat') }}" placeholder="0.00"
                           step="0.01" min="0.01" required>
                    @error('berat')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2"
                          placeholder="Kondisi getah, cuaca, dll...">{{ old('catatan') }}</textarea>
            </div>

            <div style="background:#fff8e6;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:12px;color:#9a6800;">
                <i class="fas fa-info-circle"></i>
                Produksi Anda akan masuk status <strong>Pending</strong> dan perlu divalidasi oleh Admin KTH sebelum dihitung ke stok.
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">
                    <i class="fas fa-save"></i> Kirim Produksi
                </button>
                <a href="{{ route('saya.produksi') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection