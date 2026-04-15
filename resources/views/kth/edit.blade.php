{{-- resources/views/kth/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit KTH')
@section('page_title', 'Edit KTH')

@section('content')
<div class="card" style="max-width:560px;">
    <div class="card-header">
        <h3><i class="fas fa-pen" style="color:#1a7f4b;margin-right:8px;"></i>Edit: {{ $kth->nama_kth }}</h3>
        <a href="{{ route('super.kth.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('super.kth.update', $kth) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama KTH <span style="color:red">*</span></label>
                <input type="text" name="nama_kth" class="form-control"
                       value="{{ old('nama_kth', $kth->nama_kth) }}" required>
                @error('nama_kth')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $kth->alamat) }}</textarea>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('super.kth.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection