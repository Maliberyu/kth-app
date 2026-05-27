@extends('layouts.app')
@section('title', 'Tambah KUPS')
@section('page_title', 'Tambah KUPS')

@section('content')

<div style="max-width:600px;">
    <div style="margin-bottom:16px;">
        <a href="{{ route('kups.index') }}" style="color:var(--primary); text-decoration:none; font-size:13px;">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar KUPS
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-plus-circle" style="color:#1a7f4b;margin-right:8px;"></i>Tambah KUPS Baru</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('kups.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama KUPS <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_kups" class="form-control @error('nama_kups') is-invalid @enderror"
                           value="{{ old('nama_kups') }}" placeholder="Contoh: KUPS AGROPOREST">
                    @error('nama_kups')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control"
                              placeholder="Deskripsi singkat tentang KUPS ini (opsional)">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Status <span style="color:#d93025;">*</span></label>
                    <select name="status" class="form-control">
                        <option value="aktif"    {{ old('status','aktif') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div style="display:flex; gap:8px; margin-top:8px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    <a href="{{ route('kups.index') }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
