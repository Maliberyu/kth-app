{{-- resources/views/produksi/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Produksi')
@section('page_title', 'Edit Produksi Getah')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3><i class="fas fa-pen" style="color:#1a7f4b;margin-right:8px;"></i>Edit Produksi</h3>
        <a href="{{ route('produksi.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('produksi.update', $produksi) }}">
            @csrf @method('PUT')
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Penyadap <span style="color:red">*</span></label>
                    <select name="penyadap_id" class="form-control form-select" required>
                        @foreach($penyadap as $p)
                            <option value="{{ $p->id }}" {{ $produksi->penyadap_id == $p->id ? 'selected':'' }}>
                                {{ $p->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Blok <span style="color:red">*</span></label>
                    <select name="blok_id" class="form-control form-select" required>
                        @foreach($blok as $b)
                            <option value="{{ $b->id }}" {{ $produksi->blok_id == $b->id ? 'selected':'' }}>
                                {{ $b->nama_blok }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Penyimpanan <span style="color:red">*</span></label>
                <select name="penyimpanan_id" class="form-control form-select" required>
                    @foreach($penyimpanan as $ps)
                        <option value="{{ $ps->id }}" {{ $produksi->penyimpanan_id == $ps->id ? 'selected':'' }}>
                            {{ $ps->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Tanggal <span style="color:red">*</span></label>
                    <input type="date" name="tanggal" class="form-control"
                           value="{{ old('tanggal', $produksi->tanggal->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Berat (kg) <span style="color:red">*</span></label>
                    <input type="number" name="berat" class="form-control"
                           value="{{ old('berat', $produksi->berat) }}" step="0.01" min="0.01" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $produksi->catatan) }}</textarea>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('produksi.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection