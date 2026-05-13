@extends('layouts.app')
@section('title', $kegiatan->nama_kegiatan)
@section('page_title', $kegiatan->nama_kegiatan)

@push('styles')
<style>
    .qr-box {
        background:#fff; border:1px solid #e8ecf0; border-radius:12px;
        padding:24px; text-align:center;
    }
    #qrcode { display:inline-block; margin-bottom:12px; }
    #qrcode canvas, #qrcode img { border-radius:8px; }
    .url-copy {
        display:flex; gap:8px; align-items:center;
        background:#f8fafc; border:1px solid #e8ecf0;
        border-radius:8px; padding:8px 12px; margin-top:10px;
    }
    .url-copy input {
        border:none; background:transparent; flex:1;
        font-size:12px; color:#374151; outline:none;
    }
    .search-box {
        display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap;
    }
    .search-box input {
        flex:1; min-width:200px; padding:8px 12px;
        border:1.5px solid #d1d9e0; border-radius:8px;
        font-size:13px; font-family:inherit;
    }
    .search-box input:focus { outline:none; border-color:var(--primary); }
    .no-result { display:none; text-align:center; color:#adb5bd; padding:32px; }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div style="margin-bottom:16px; font-size:13px; color:#6b7a8d;">
    <a href="{{ route('kegiatan.index', ['tipe'=>$kegiatan->tipe]) }}" style="color:var(--primary); text-decoration:none;">
        Kegiatan {{ $kegiatan->tipe === 'dalam_ruangan' ? 'Dalam Ruangan' : 'Luar Ruangan' }}
    </a>
    <span style="margin:0 6px;">/</span>
    {{ $kegiatan->nama_kegiatan }}
</div>

{{-- Statistik --}}
<div class="grid grid-3" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-users"></i></div>
        <p class="stat-value">{{ $stats['total'] }}</p>
        <p class="stat-label">Total Peserta Hadir</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-calendar-day"></i></div>
        <p class="stat-value">{{ $stats['hari_ini'] }}</p>
        <p class="stat-label">Hadir Hari Ini</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-building"></i></div>
        <p class="stat-value">{{ $stats['instansi'] }}</p>
        <p class="stat-label">Asal Instansi</p>
    </div>
</div>

<div class="grid grid-2" style="margin-bottom:20px; align-items:start;">

    {{-- Info Kegiatan & QR Code --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Info --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-info-circle" style="color:#1a7f4b;margin-right:8px;"></i>Info Kegiatan</h3>
                <div style="display:flex; gap:6px;">
                    <a href="{{ route('kegiatan.edit', $kegiatan) }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body" style="font-size:13.5px; display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; gap:10px;">
                    <span style="color:#6b7a8d; width:110px; flex-shrink:0;">Lokasi</span>
                    <strong>{{ $kegiatan->lokasi }}</strong>
                </div>
                <div style="display:flex; gap:10px;">
                    <span style="color:#6b7a8d; width:110px; flex-shrink:0;">Mulai</span>
                    <strong>{{ $kegiatan->tanggal_mulai->isoFormat('dddd, D MMMM Y — HH:mm') }}</strong>
                </div>
                @if($kegiatan->tanggal_selesai)
                <div style="display:flex; gap:10px;">
                    <span style="color:#6b7a8d; width:110px; flex-shrink:0;">Selesai</span>
                    <strong>{{ $kegiatan->tanggal_selesai->isoFormat('dddd, D MMMM Y — HH:mm') }}</strong>
                </div>
                @endif
                <div style="display:flex; gap:10px;">
                    <span style="color:#6b7a8d; width:110px; flex-shrink:0;">Tipe</span>
                    <span>{{ $kegiatan->tipe === 'dalam_ruangan' ? 'Dalam Ruangan' : 'Luar Ruangan' }}</span>
                </div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <span style="color:#6b7a8d; width:110px; flex-shrink:0;">Status</span>
                    @php $badge = match($kegiatan->status) { 'aktif'=>'badge-success','selesai'=>'badge-info','batal'=>'badge-danger',default=>'badge-gray' }; @endphp
                    <span class="badge {{ $badge }}">{{ ucfirst($kegiatan->status) }}</span>
                </div>
                @if($kegiatan->deskripsi)
                <div style="display:flex; gap:10px;">
                    <span style="color:#6b7a8d; width:110px; flex-shrink:0;">Deskripsi</span>
                    <span>{{ $kegiatan->deskripsi }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- QR Code --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-qrcode" style="color:#1a7f4b;margin-right:8px;"></i>QR Code Registrasi</h3>
                <button onclick="printQr()" class="btn btn-outline btn-sm">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
            <div class="card-body">
                @if($kegiatan->status === 'aktif')
                <div class="qr-box">
                    <div id="qrcode"></div>
                    <p style="font-size:12px; color:#6b7a8d; margin:0 0 4px;">Scan untuk daftar hadir</p>
                    <p style="font-weight:700; font-size:14px; margin:0 0 10px;">{{ $kegiatan->nama_kegiatan }}</p>
                    <div class="url-copy">
                        <input type="text" id="regUrl" value="{{ $kegiatan->registrasi_url }}" readonly>
                        <button onclick="copyUrl()" class="btn btn-outline btn-sm" id="btnCopy">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                @else
                <div style="text-align:center; padding:32px; color:#adb5bd;">
                    <i class="fas fa-qrcode" style="font-size:40px; display:block; margin-bottom:12px; opacity:.3;"></i>
                    QR Code tidak aktif karena status kegiatan <strong>{{ $kegiatan->status }}</strong>.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Daftar Peserta --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list-check" style="color:#1a7f4b;margin-right:8px;"></i>
                Daftar Peserta <span style="font-weight:400; color:#6b7a8d;">({{ $peserta->count() }})</span>
            </h3>
            <div style="display:flex; gap:6px;">
                <a href="{{ route('kegiatan.export-excel', $kegiatan) }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-file-csv"></i> Excel
                </a>
                <a href="{{ route('kegiatan.export-pdf', $kegiatan) }}" target="_blank" class="btn btn-outline btn-sm">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>
        <div class="card-body" style="padding-bottom:0;">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Cari nama, instansi, atau no HP..." oninput="filterTable()">
            </div>
        </div>
        <div class="table-wrap" style="max-height:460px; overflow-y:auto;">
            <table id="pesertaTable">
                <thead>
                    <tr>
                        <th style="width:28px;">No</th>
                        <th>Nama</th>
                        <th>Instansi / Jabatan</th>
                        <th>No HP</th>
                        <th>Hadir</th>
                    </tr>
                </thead>
                <tbody id="pesertaTbody">
                    @forelse($peserta as $i => $p)
                    <tr class="peserta-row">
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $p->nama_lengkap }}</strong>
                            @if($p->email)
                            <div style="font-size:11px; color:#6b7a8d;">{{ $p->email }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $p->asal_instansi ?? '-' }}</div>
                            @if($p->jabatan)
                            <div style="font-size:11px; color:#6b7a8d;">{{ $p->jabatan }}</div>
                            @endif
                        </td>
                        <td style="font-size:12px;">{{ $p->no_hp }}</td>
                        <td style="font-size:12px; white-space:nowrap;">{{ $p->waktu_hadir->format('d/m H:i') }}</td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="5" style="text-align:center; color:#adb5bd; padding:32px;">
                            Belum ada peserta yang check-in.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="no-result" id="noResult">
                <i class="fas fa-search" style="font-size:28px; margin-bottom:8px; display:block;"></i>
                Peserta tidak ditemukan.
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const regUrl = "{{ $kegiatan->registrasi_url }}";

    @if($kegiatan->status === 'aktif')
    new QRCode(document.getElementById("qrcode"), {
        text: regUrl,
        width: 200,
        height: 200,
        colorDark: "#0f2419",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    @endif

    function copyUrl() {
        const input = document.getElementById('regUrl');
        input.select();
        document.execCommand('copy');
        const btn = document.getElementById('btnCopy');
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => btn.innerHTML = '<i class="fas fa-copy"></i>', 2000);
    }

    function printQr() {
        const canvas = document.querySelector('#qrcode canvas');
        if (!canvas) return alert('QR Code tidak tersedia.');
        const img = canvas.toDataURL('image/png');
        const win = window.open('', '_blank');
        win.document.write(`
            <html><head><title>QR Code - {{ addslashes($kegiatan->nama_kegiatan) }}</title>
            <style>
                body { margin:0; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:100vh; font-family:sans-serif; }
                img { width:280px; height:280px; }
                h2 { font-size:18px; margin:16px 0 4px; text-align:center; }
                p  { font-size:13px; color:#666; text-align:center; margin:0; }
            </style></head>
            <body>
                <img src="${img}">
                <h2>{{ addslashes($kegiatan->nama_kegiatan) }}</h2>
                <p>{{ addslashes($kegiatan->lokasi) }}</p>
                <p>{{ $kegiatan->tanggal_mulai->format('d/m/Y H:i') }}</p>
            </body></html>
        `);
        win.document.close();
        win.onload = () => { win.print(); };
    }

    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.peserta-row');
        let visible = 0;
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const match = text.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('noResult').style.display = visible === 0 && rows.length > 0 ? 'block' : 'none';
    }
</script>
@endpush
