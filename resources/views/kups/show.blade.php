@extends('layouts.app')
@section('title', $kup->nama_kups)
@section('page_title', $kup->nama_kups)

@push('styles')
<style>
    .edit-row-form { display:none; background:#f8fafc; }
    .edit-row-form.show { display:table-row; }
    .edit-row-form td { padding:10px 12px; border-bottom:1px solid #e8ecf0; }
    .inline-grid { display:grid; grid-template-columns:2fr 1fr 1fr 1.2fr; gap:8px; align-items:end; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- Breadcrumb --}}
<div style="margin-bottom:16px; font-size:13px; color:#6b7a8d;">
    <a href="{{ route('kups.index') }}" style="color:var(--primary); text-decoration:none;">Master KUPS</a>
    <span style="margin:0 6px;">/</span>
    {{ $kup->nama_kups }}
</div>

{{-- Stat cards --}}
<div class="grid grid-3" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-box-open"></i></div>
        <p class="stat-value">{{ $komoditas->count() }}</p>
        <p class="stat-label">Total Komoditas</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-money-bill-wave"></i></div>
        <p class="stat-value" style="font-size:14px;">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
        <p class="stat-label">Estimasi Nilai</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-info-circle"></i></div>
        <p class="stat-value">{{ ucfirst($kup->status) }}</p>
        <p class="stat-label">Status KUPS</p>
    </div>
</div>

{{-- Info + Aksi --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-layer-group" style="color:#1a7f4b;margin-right:8px;"></i>Info KUPS</h3>
        <div style="display:flex; gap:6px;">
            <a href="{{ route('kups.print', $kup) }}" target="_blank" class="btn btn-outline btn-sm">
                <i class="fas fa-print"></i> Cetak PDF
            </a>
            <a href="{{ route('kups.edit', $kup) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-pen"></i> Edit
            </a>
        </div>
    </div>
    <div class="card-body" style="font-size:13.5px;">
        @if($kup->deskripsi)
        <p style="color:#4a5568; line-height:1.7; margin:0;">{{ $kup->deskripsi }}</p>
        @else
        <p style="color:#adb5bd; font-style:italic; margin:0;">Tidak ada deskripsi.</p>
        @endif
    </div>
</div>

{{-- Tabel Komoditas --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list" style="color:#1a7f4b;margin-right:8px;"></i>
            Daftar Komoditas
            <span style="font-weight:400; color:#6b7a8d; font-size:12px;">({{ $komoditas->count() }})</span>
        </h3>
        <button onclick="const el=document.getElementById('formTambah'); el.style.display = el.style.display==='none' ? 'block' : 'none';"
                class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah
        </button>
    </div>

    {{-- Form Tambah --}}
    <div id="formTambah" style="display:none; padding:16px 20px; background:#f8fafc; border-bottom:1px solid #e8ecf0;">
        @error('nama_komoditas') <div class="alert alert-error" style="margin-bottom:10px;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
        @error('volume')        <div class="alert alert-error" style="margin-bottom:10px;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
        @error('harga')         <div class="alert alert-error" style="margin-bottom:10px;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
        <form method="POST" action="{{ route('kups.komoditas.store', $kup) }}">
            @csrf
            <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1.2fr; gap:10px; margin-bottom:10px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Nama Komoditas <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_komoditas" class="form-control"
                           value="{{ old('nama_komoditas') }}" placeholder="Contoh: BAMBU" style="text-transform:uppercase;">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Volume <span style="color:#d93025;">*</span></label>
                    <input type="number" name="volume" class="form-control" step="0.01" min="0"
                           value="{{ old('volume') }}" placeholder="0">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Satuan <span style="color:#d93025;">*</span></label>
                    <select name="satuan" class="form-control">
                        @foreach(\App\Models\Komoditas::$satuanOptions as $s)
                        <option value="{{ $s }}" {{ old('satuan') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Harga (Rp) <span style="color:#d93025;">*</span></label>
                    <input type="number" name="harga" class="form-control" min="0"
                           value="{{ old('harga') }}" placeholder="0">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" class="form-control"
                       value="{{ old('keterangan') }}" placeholder="Opsional">
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambahkan</button>
                <button type="button" onclick="document.getElementById('formTambah').style.display='none'"
                        class="btn btn-outline btn-sm">Batal</button>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:36px;">No</th>
                    <th>Nama Komoditas</th>
                    <th style="text-align:right; width:90px;">Volume</th>
                    <th style="width:70px;">Satuan</th>
                    <th style="text-align:right; width:120px;">Harga</th>
                    <th style="text-align:right; width:130px;">Nilai Estimasi</th>
                    <th style="text-align:center; width:80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($komoditas as $i => $k)
                <tr id="row-{{ $k->id }}">
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $k->nama_komoditas }}</strong>
                        @if($k->keterangan)<br><span style="font-size:11px; color:#6b7a8d;">{{ $k->keterangan }}</span>@endif
                    </td>
                    <td style="text-align:right;">{{ $k->volume_format }}</td>
                    <td>{{ $k->satuan }}</td>
                    <td style="text-align:right; color:#1a7f4b; font-weight:600;">{{ $k->harga_format }}</td>
                    <td style="text-align:right; color:#4a5568;">
                        Rp {{ number_format($k->volume * $k->harga, 0, ',', '.') }}
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:4px; justify-content:center;">
                            <button onclick="toggleEdit({{ $k->id }})"
                                    class="btn btn-outline btn-sm btn-icon" title="Edit">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form method="POST" action="{{ route('kups.komoditas.destroy', [$kup, $k]) }}"
                                  onsubmit="return confirm('Hapus komoditas {{ addslashes($k->nama_komoditas) }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm btn-icon"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                {{-- Baris edit inline --}}
                <tr id="edit-{{ $k->id }}" class="edit-row-form">
                    <td colspan="7">
                        <form method="POST" action="{{ route('kups.komoditas.update', [$kup, $k]) }}">
                            @csrf @method('PUT')
                            <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1.2fr 1fr; gap:8px; margin-bottom:8px;">
                                <div>
                                    <label class="form-label" style="font-size:11px;">Nama</label>
                                    <input type="text" name="nama_komoditas" class="form-control"
                                           value="{{ $k->nama_komoditas }}" style="text-transform:uppercase;">
                                </div>
                                <div>
                                    <label class="form-label" style="font-size:11px;">Volume</label>
                                    <input type="number" name="volume" class="form-control" step="0.01"
                                           value="{{ $k->volume }}">
                                </div>
                                <div>
                                    <label class="form-label" style="font-size:11px;">Satuan</label>
                                    <select name="satuan" class="form-control">
                                        @foreach(\App\Models\Komoditas::$satuanOptions as $s)
                                        <option value="{{ $s }}" {{ $k->satuan === $s ? 'selected' : '' }}>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label" style="font-size:11px;">Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control" value="{{ $k->harga }}">
                                </div>
                                <div>
                                    <label class="form-label" style="font-size:11px;">Keterangan</label>
                                    <input type="text" name="keterangan" class="form-control" value="{{ $k->keterangan }}">
                                </div>
                            </div>
                            <div style="display:flex; gap:6px;">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
                                <button type="button" onclick="toggleEdit({{ $k->id }})" class="btn btn-outline btn-sm">Batal</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#adb5bd; padding:32px;">
                        Belum ada komoditas. Klik <strong>Tambah</strong> untuk menambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($komoditas->count() > 0)
            <tfoot>
                <tr style="background:#f8fafc; font-weight:700;">
                    <td colspan="5" style="text-align:right; padding:10px;">Total Estimasi Nilai</td>
                    <td style="text-align:right; padding:10px; color:#1a7f4b;">
                        Rp {{ number_format($totalNilai, 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function toggleEdit(id) {
        const row = document.getElementById('edit-' + id);
        row.classList.toggle('show');
    }

    @if($errors->any())
    document.getElementById('formTambah').style.display = 'block';
    @endif
</script>
@endpush
