<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peserta - {{ $kegiatan->nama_kegiatan }}</title>
    <style>
        * { font-family: 'Arial', sans-serif; font-size: 12px; }
        body { margin: 20px; color: #1e2a35; }
        .header { text-align: center; margin-bottom: 24px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header p  { margin: 2px 0; color: #555; font-size: 12px; }
        .meta { display: flex; gap: 24px; margin-bottom: 20px; flex-wrap: wrap; }
        .meta div { font-size: 12px; }
        .meta strong { color: #1e2a35; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead th {
            background: #0f2419; color: #fff;
            padding: 8px 10px; text-align: left;
            font-size: 11px; text-transform: uppercase; letter-spacing: .5px;
        }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #e8ecf0; font-size: 12px; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .footer { margin-top: 20px; text-align: right; font-size: 11px; color: #999; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom:16px;">
    <button onclick="window.print()" style="padding:8px 20px; background:#1a7f4b; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:13px;">
        🖨️ Cetak / Simpan PDF
    </button>
    <button onclick="window.close()" style="padding:8px 20px; background:#f0f3f6; border:1px solid #d1d9e0; border-radius:6px; cursor:pointer; font-size:13px; margin-left:8px;">
        Tutup
    </button>
</div>

<div class="header">
    <h1>Daftar Hadir Peserta</h1>
    <p><strong>{{ $kegiatan->nama_kegiatan }}</strong></p>
    <p>{{ $kegiatan->lokasi }} &nbsp;|&nbsp; {{ $kegiatan->tanggal_mulai->format('d/m/Y H:i') }}</p>
</div>

<div class="meta">
    <div><span style="color:#555;">Total Peserta: </span><strong>{{ $peserta->count() }}</strong></div>
    <div><span style="color:#555;">Dicetak: </span><strong>{{ now()->format('d/m/Y H:i') }}</strong></div>
    <div><span style="color:#555;">Status: </span><strong>{{ ucfirst($kegiatan->status) }}</strong></div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:28px;">No</th>
            <th>Nama Lengkap</th>
            <th>Jabatan</th>
            <th>Asal Instansi</th>
            <th>No HP</th>
            <th>Email</th>
            <th>Waktu Hadir</th>
        </tr>
    </thead>
    <tbody>
        @forelse($peserta as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $p->nama_lengkap }}</strong></td>
            <td>{{ $p->jabatan ?? '-' }}</td>
            <td>{{ $p->asal_instansi ?? '-' }}</td>
            <td>{{ $p->no_hp }}</td>
            <td>{{ $p->email ?? '-' }}</td>
            <td>{{ $p->waktu_hadir->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center; color:#adb5bd; padding:24px;">Belum ada peserta.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Digenerate oleh KTH App &mdash; {{ now()->format('d/m/Y H:i:s') }}
</div>

</body>
</html>
