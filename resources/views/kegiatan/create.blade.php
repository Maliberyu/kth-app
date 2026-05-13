@extends('layouts.app')
@section('title', 'Buat Kegiatan Baru')
@section('page_title', 'Buat Kegiatan Baru')

@section('content')
<div style="max-width:680px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar-plus" style="color:#1a7f4b;margin-right:8px;"></i>Form Kegiatan</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('kegiatan.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama Kegiatan <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_kegiatan" class="form-control @error('nama_kegiatan') is-error @enderror"
                           value="{{ old('nama_kegiatan') }}" placeholder="Contoh: Rapat Koordinasi KTH 2026">
                    @error('nama_kegiatan')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi <span style="color:#d93025;">*</span></label>
                    <input type="text" name="lokasi" class="form-control @error('lokasi') is-error @enderror"
                           value="{{ old('lokasi') }}" placeholder="Contoh: Aula KTH, Gedung A lantai 2">
                    @error('lokasi')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Tanggal & Jam Mulai <span style="color:#d93025;">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-error @enderror"
                               value="{{ old('tanggal_mulai') }}">
                        @error('tanggal_mulai')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal & Jam Selesai</label>
                        <input type="datetime-local" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-error @enderror"
                               value="{{ old('tanggal_selesai') }}">
                        @error('tanggal_selesai')<div style="color:#d93025;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipe Kegiatan <span style="color:#d93025;">*</span></label>
                    <select name="tipe" class="form-control form-select">
                        <option value="dalam_ruangan" {{ old('tipe','dalam_ruangan') === 'dalam_ruangan' ? 'selected' : '' }}>
                            Dalam Ruangan
                        </option>
                        <option value="luar_ruangan" {{ old('tipe') === 'luar_ruangan' ? 'selected' : '' }}>
                            Luar Ruangan
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"
                              placeholder="Keterangan singkat kegiatan (opsional)">{{ old('deskripsi') }}</textarea>
                </div>

                <div style="display:flex; gap:10px; margin-top:8px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan & Generate QR
                    </button>
                    <a href="{{ route('kegiatan.index') }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
