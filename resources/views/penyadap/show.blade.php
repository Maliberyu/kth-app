{{-- resources/views/penyadap/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Penyadap')
@section('page_title', 'Detail Penyadap')

@section('content')
<div class="grid grid-2" style="margin-bottom:20px;">
    {{-- Info Penyadap --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user" style="color:#1a7f4b;margin-right:8px;"></i>Data Diri</h3>
            <a href="{{ route('penyadap.edit', $penyadap) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-pen"></i> Edit
            </a>
        </div>
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                <div style="width:56px;height:56px;border-radius:50%;background:#e8f5ee;color:#1a7f4b;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;flex-shrink:0;">
                    {{ strtoupper(substr($penyadap->nama, 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:16px;font-weight:700;">{{ $penyadap->nama }}</div>
                    <div style="font-size:12px;color:#6b7a8d;">{{ $penyadap->kth->nama_kth }}</div>
                </div>
            </div>
            <table style="width:100%;font-size:13.5px;">
                <tr>
                    <td style="color:#6b7a8d;padding:6px 0;width:35%;">NIK</td>
                    <td>{{ $penyadap->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:6px 0;">No. HP</td>
                    <td>{{ $penyadap->no_hp ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="color:#6b7a8d;padding:6px 0;">Alamat</td>
                    <td>{{ $penyadap->alamat ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- BPJS --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-id-card" style="color:#1a73e8;margin-right:8px;"></i>Data BPJS</h3>
            <button onclick="document.getElementById('modal-bpjs').style.display='flex'" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah
            </button>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
            @forelse($penyadap->bpjs as $b)
            <div style="background:#f8fafc;border:1px solid #e8ecf0;border-radius:10px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-weight:600;font-size:13.5px;">{{ $b->jenis_bpjs }}</div>
                    <div style="font-size:12px;color:#6b7a8d;">No: {{ $b->nomor }}</div>
                    @if($b->penanggung)
                    <div style="font-size:12px;color:#6b7a8d;">Penanggung: {{ $b->penanggung }}</div>
                    @endif
                </div>
                <span class="badge {{ $b->status_aktif === 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                    {{ $b->status_aktif }}
                </span>
            </div>
            @empty
            <p style="color:#adb5bd;text-align:center;padding:20px 0;font-size:13px;">Belum ada data BPJS</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Blok Ditugaskan --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-map" style="color:#1a7f4b;margin-right:8px;"></i>Blok Ditugaskan</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Nama Blok</th><th>Jenis</th><th>Luas</th><th>Pohon Produktif</th></tr>
            </thead>
            <tbody>
                @forelse($penyadap->blok as $b)
                <tr>
                    <td><strong>{{ $b->nama_blok }}</strong></td>
                    <td>{{ $b->jenis_blok ?? '-' }}</td>
                    <td>{{ number_format($b->luas, 2) }} Ha</td>
                    <td><span class="badge badge-success">{{ number_format($b->pohon_produktif) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#adb5bd;padding:20px;">Belum ada blok ditugaskan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Riwayat Produksi --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-droplet" style="color:#1a7f4b;margin-right:8px;"></i>Riwayat Produksi (10 terakhir)</h3>
        <a href="{{ route('produksi.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Tanggal</th><th>Blok</th><th>Berat</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($penyadap->produksiGetah as $pg)
                <tr>
                    <td>{{ $pg->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $pg->blok->nama_blok }}</td>
                    <td>{{ number_format($pg->berat, 2) }} kg</td>
                    <td>
                        <span class="badge {{ $pg->status_validasi === 'valid' ? 'badge-success' : ($pg->status_validasi === 'ditolak' ? 'badge-danger' : 'badge-warning') }}">
                            {{ ucfirst($pg->status_validasi) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#adb5bd;padding:20px;">Belum ada riwayat produksi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal BPJS --}}
<div id="modal-bpjs" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:440px;margin:16px;">
        <h4 style="margin:0 0 16px;font-size:16px;">Tambah Data BPJS</h4>
        <form method="POST" action="{{ route('penyadap.bpjs.store', $penyadap) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Jenis BPJS</label>
                <select name="jenis_bpjs" class="form-control form-select" required>
                    <option value="Kesehatan">Kesehatan</option>
                    <option value="Ketenagakerjaan">Ketenagakerjaan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nomor</label>
                <input type="text" name="nomor" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status_aktif" class="form-control form-select">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Penanggung</label>
                <input type="text" name="penanggung" class="form-control" placeholder="Opsional">
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" onclick="document.getElementById('modal-bpjs').style.display='none'" class="btn btn-outline">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection