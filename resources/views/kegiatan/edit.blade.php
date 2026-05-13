@extends('layouts.app')
@section('title', 'Edit Kegiatan')
@section('page_title', 'Edit Kegiatan')

@section('content')
<div style="max-width:680px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-pen" style="color:#1a7f4b;margin-right:8px;"></i>Edit: {{ $kegiatan->nama_kegiatan }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('kegiatan.update', $kegiatan) }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama Kegiatan <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_kegiatan" class="form-control"
                           value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}">
                    @error('nama_kegiatan')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi <span style="color:#d93025;">*</span></label>
                    <input type="text" name="lokasi" class="form-control"
                           value="{{ old('lokasi', $kegiatan->lokasi) }}">
                    @error('lokasi')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Tanggal & Jam Mulai <span style="color:#d93025;">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai" class="form-control"
                               value="{{ old('tanggal_mulai', $kegiatan->tanggal_mulai->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal & Jam Selesai</label>
                        <input type="datetime-local" name="tanggal_selesai" class="form-control"
                               value="{{ old('tanggal_selesai', $kegiatan->tanggal_selesai?->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Tipe Kegiatan</label>
                        <select name="tipe" class="form-control form-select">
                            <option value="dalam_ruangan" {{ old('tipe', $kegiatan->tipe) === 'dalam_ruangan' ? 'selected' : '' }}>Dalam Ruangan</option>
                            <option value="luar_ruangan"  {{ old('tipe', $kegiatan->tipe) === 'luar_ruangan'  ? 'selected' : '' }}>Luar Ruangan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control form-select">
                            <option value="aktif"   {{ old('status', $kegiatan->status) === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                            <option value="selesai" {{ old('status', $kegiatan->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="batal"   {{ old('status', $kegiatan->status) === 'batal'   ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="{{ route('kegiatan.show', $kegiatan) }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
