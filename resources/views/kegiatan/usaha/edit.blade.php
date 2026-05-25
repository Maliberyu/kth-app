@extends('layouts.app')
@section('title', 'Edit Kegiatan Usaha')
@section('page_title', 'Edit Kegiatan Usaha')

@section('content')
<div style="max-width:680px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-pen" style="color:#1a7f4b;margin-right:8px;"></i>Edit: {{ $kegiatanUsaha->nama_usaha }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('kegiatan-usaha.update', $kegiatanUsaha) }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama Usaha <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_usaha" class="form-control"
                           value="{{ old('nama_usaha', $kegiatanUsaha->nama_usaha) }}">
                    @error('nama_usaha')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Jenis Usaha</label>
                        <select name="jenis_usaha" class="form-control form-select">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach(['Pertanian / Perkebunan','Perdagangan','Pengolahan Hasil Hutan','Peternakan','Jasa','Lain-lain'] as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_usaha', $kegiatanUsaha->jenis_usaha) === $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control form-select">
                            <option value="aktif"   {{ old('status', $kegiatanUsaha->status) === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ old('status', $kegiatanUsaha->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="tutup"   {{ old('status', $kegiatanUsaha->status) === 'tutup'   ? 'selected' : '' }}>Tutup</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai <span style="color:#d93025;">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control"
                               value="{{ old('tanggal_mulai', $kegiatanUsaha->tanggal_mulai->format('Y-m-d')) }}">
                        @error('tanggal_mulai')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control"
                               value="{{ old('tanggal_selesai', $kegiatanUsaha->tanggal_selesai?->format('Y-m-d')) }}">
                        @error('tanggal_selesai')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Kegiatan Usaha</label>
                    <textarea name="deskripsi" class="form-control" rows="5">{{ old('deskripsi', $kegiatanUsaha->deskripsi) }}</textarea>
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="{{ route('kegiatan-usaha.show', $kegiatanUsaha) }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
