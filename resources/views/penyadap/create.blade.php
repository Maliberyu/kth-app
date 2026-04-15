{{-- resources/views/penyadap/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah Penyadap')
@section('page_title', 'Tambah Penyadap')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3><i class="fas fa-user-plus" style="color:#1a7f4b;margin-right:8px;"></i>Form Tambah Penyadap</h3>
        <a href="{{ route('penyadap.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('penyadap.store') }}">
            @csrf
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                    <input type="text" name="nama" class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}"
                           value="{{ old('nama') }}" placeholder="Nama penyadap" required>
                    @error('nama')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">NIK</label>
                    <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" placeholder="16 digit NIK" maxlength="20">
                    @error('nik')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx">
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('penyadap.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection