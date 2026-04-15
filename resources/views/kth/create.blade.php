{{-- resources/views/kth/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Tambah KTH')
@section('page_title', 'Tambah KTH')

@section('content')
<div class="card" style="max-width:560px;">
    <div class="card-header">
        <h3><i class="fas fa-sitemap" style="color:#1a7f4b;margin-right:8px;"></i>Form Tambah KTH</h3>
        <a href="{{ route('super.kth.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('super.kth.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama KTH <span style="color:red">*</span></label>
                <input type="text" name="nama_kth" class="form-control"
                       value="{{ old('nama_kth') }}" placeholder="Nama Kelompok Tani Hutan" required>
                @error('nama_kth')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"
                          placeholder="Alamat lengkap KTH">{{ old('alamat') }}</textarea>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('super.kth.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection