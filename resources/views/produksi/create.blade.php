{{-- resources/views/produksi/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Input Produksi Getah')
@section('page_title', 'Input Produksi Getah')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3><i class="fas fa-droplet" style="color:#1a7f4b;margin-right:8px;"></i>Form Input Produksi</h3>
        <a href="{{ route('produksi.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('produksi.store') }}">
            @csrf
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Penyadap <span style="color:red">*</span></label>
                    <select name="penyadap_id" class="form-control form-select" required>
                        <option value="">— Pilih Penyadap —</option>
                        @foreach($penyadap as $p)
                            <option value="{{ $p->id }}" {{ old('penyadap_id')==$p->id ? 'selected':'' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('penyadap_id')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Blok <span style="color:red">*</span></label>
                    <select name="blok_id" class="form-control form-select" required>
                        <option value="">— Pilih Blok —</option>
                        @foreach($blok as $b)
                            <option value="{{ $b->id }}" {{ old('blok_id')==$b->id ? 'selected':'' }}>
                                {{ $b->nama_blok }}
                            </option>
                        @endforeach
                    </select>
                    @error('blok_id')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Penyimpanan <span style="color:red">*</span></label>
                <select name="penyimpanan_id" class="form-control form-select" required>
                    <option value="">— Pilih Lokasi Penyimpanan —</option>
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
                           value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Berat (kg) <span style="color:red">*</span></label>
                    <input type="number" name="berat" class="form-control"
                           value="{{ old('berat') }}" placeholder="0.00" step="0.01" min="0.01" required>
                    @error('berat')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Produksi
                </button>
                <a href="{{ route('produksi.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection