{{-- resources/views/blok/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Blok')
@section('page_title', 'Edit Blok')

@section('content')
<div class="card" style="max-width:680px;">
    <div class="card-header">
        <h3><i class="fas fa-pen" style="color:#1a7f4b;margin-right:8px;"></i>Edit Blok: {{ $blok->nama_blok }}</h3>
        <a href="{{ route('blok.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('blok.update', $blok) }}">
            @csrf @method('PUT')
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Blok <span style="color:red">*</span></label>
                    <input type="text" name="nama_blok" class="form-control"
                           value="{{ old('nama_blok', $blok->nama_blok) }}" required>
                    @error('nama_blok')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Jenis Blok</label>
                    <select name="jenis_blok" class="form-control form-select">
                        <option value="">— Pilih Jenis —</option>
                        @foreach(['Produksi','Konservasi','Rehabilitasi'] as $jenis)
                        <option value="{{ $jenis }}" {{ old('jenis_blok', $blok->jenis_blok) === $jenis ? 'selected':'' }}>
                            {{ $jenis }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Luas (Hektar)</label>
                <input type="number" name="luas" class="form-control"
                       value="{{ old('luas', $blok->luas) }}" step="0.01" min="0">
            </div>
            <div class="grid grid-3">
                <div class="form-group">
                    <label class="form-label">Total Pohon</label>
                    <input type="number" name="total_pohon" class="form-control"
                           value="{{ old('total_pohon', $blok->total_pohon) }}" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Pohon Produktif</label>
                    <input type="number" name="pohon_produktif" class="form-control"
                           value="{{ old('pohon_produktif', $blok->pohon_produktif) }}" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Tidak Produktif</label>
                    <input type="number" name="pohon_tidak_produktif" class="form-control"
                           value="{{ old('pohon_tidak_produktif', $blok->pohon_tidak_produktif) }}" min="0">
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('blok.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection