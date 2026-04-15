{{-- resources/views/surat_jalan/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Buat Surat Jalan')
@section('page_title', 'Buat Surat Jalan')

@section('content')
<div class="card" style="max-width:640px;">
    <div class="card-header">
        <h3><i class="fas fa-file-alt" style="color:#1a7f4b;margin-right:8px;"></i>Form Surat Jalan</h3>
        <a href="{{ route('surat-jalan.index') }}" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('surat-jalan.store') }}">
            @csrf
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Nomor Surat Jalan <span style="color:red">*</span></label>
                    <input type="text" name="nomor" class="form-control"
                           value="{{ old('nomor', 'SJ-'.date('Ymd').'-'.rand(100,999)) }}" required>
                    @error('nomor')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal <span style="color:red">*</span></label>
                    <input type="date" name="tanggal" class="form-control"
                           value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Penyimpanan Asal <span style="color:red">*</span></label>
                    <select name="penyimpanan_id" class="form-control form-select" required>
                        <option value="">— Pilih —</option>
                        @foreach($penyimpanan as $ps)
                            <option value="{{ $ps->id }}" {{ old('penyimpanan_id')==$ps->id ? 'selected':'' }}>
                                {{ $ps->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Vendor Tujuan <span style="color:red">*</span></label>
                    <select name="vendor_id" class="form-control form-select" required>
                        <option value="">— Pilih —</option>
                        @foreach($vendor as $v)
                            <option value="{{ $v->id }}" {{ old('vendor_id')==$v->id ? 'selected':'' }}>
                                {{ $v->nama_vendor }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Total Berat (kg) <span style="color:red">*</span></label>
                <input type="number" name="total_berat" class="form-control"
                       value="{{ old('total_berat') }}" placeholder="0.00" step="0.01" min="0.01" required>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('surat-jalan.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection