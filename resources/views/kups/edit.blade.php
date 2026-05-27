@extends('layouts.app')
@section('title', 'Edit KUPS')
@section('page_title', 'Edit KUPS')

@section('content')

<div style="max-width:600px;">
    <div style="margin-bottom:16px;">
        <a href="{{ route('kups.show', $kup) }}" style="color:var(--primary); text-decoration:none; font-size:13px;">
            <i class="fas fa-arrow-left"></i> Kembali ke {{ $kup->nama_kups }}
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-pen" style="color:#1a7f4b;margin-right:8px;"></i>Edit KUPS</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('kups.update', $kup) }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama KUPS <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_kups" class="form-control @error('nama_kups') is-invalid @enderror"
                           value="{{ old('nama_kups', $kup->nama_kups) }}">
                    @error('nama_kups')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-control">{{ old('deskripsi', $kup->deskripsi) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Status <span style="color:#d93025;">*</span></label>
                    <select name="status" class="form-control">
                        <option value="aktif"    {{ old('status', $kup->status) === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $kup->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div style="display:flex; gap:8px; margin-top:8px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="{{ route('kups.show', $kup) }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
