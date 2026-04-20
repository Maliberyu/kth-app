{{-- resources/views/produksi/penyadap_create.blade.php --}}
@extends('layouts.app')

@section('title', 'Catat Produksi')
@section('page_title', '✏️ Catat Produksi Baru')

@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header">
        <h3><i class="fas fa-plus-circle"></i> Form Produksi</h3>
        <a href="{{ route('saya.produksi') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('saya.produksi.store') }}">
            @csrf
            
            <div class="form-group">
                <label>Blok *</label>
                <select name="blok_id" class="form-control" required>
                    <option value="">-- Pilih Blok --</option>
                    @foreach($blokTersedia as $b)
                        <option value="{{ $b->id }}" {{ old('blok_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_blok }} ({{ number_format($b->luas, 2) }} Ha)
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>Tanggal *</label>
                <input type="date" name="tanggal" class="form-control" 
                       value="{{ old('tanggal', date('Y-m-d')) }}" required>
            </div>
            
            <div class="form-group">
                <label>Berat Getah (kg) *</label>
                <input type="number" name="berat" class="form-control" 
                       step="0.1" min="0.1" value="{{ old('berat') }}" required>
                <small class="text-muted">Contoh: 12.5</small>
            </div>
            
            <div class="form-group">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
            </div>
            
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Produksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection