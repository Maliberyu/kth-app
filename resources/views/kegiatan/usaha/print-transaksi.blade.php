<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan – {{ $kegiatanUsaha->nama_usaha }}</title>
    <style>
        * { font-family: Arial, sans-serif; box-sizing: border-box; }
        body { margin: 0; padding: 24px; color: #1e2a35; font-size: 13px; }

        .no-print { background: #e8f5ee; border: 1px solid #b7dfca; border-radius: 6px; padding: 10px 16px; margin-bottom: 20px; display: flex; gap: 8px; align-items: center; }
        .no-print button { padding: 6px 14px; border-radius: 6px; border: none; cursor: pointer; font-size: 13px; font-weight: 600; }
        .btn-print { background: #1a7f4b; color: #fff; }
        .btn-close { background: transparent; color: #1a7f4b; border: 1.5px solid #1a7f4b !important; }

        .header { border-bottom: 2px solid #1a7f4b; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; color: #0f2419; }
        .header .sub { font-size: 12px; color: #6b7a8d; margin: 2px 0; }

        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 24px; }
        .stat-box { border: 1px solid #d1d9e0; border-radius: 6px; padding: 10px 12px; text-align: center; }
        .stat-box .val { font-size: 14px; font-weight: 700; margin: 0 0 2px; }
        .stat-box .lbl { font-size: 10px; color: #6b7a8d; margin: 0; }

        h2 { font-size: 14px; margin: 20px 0 8px; color: #0f2419; border-left: 3px solid #1a7f4b; padding-left: 8px; }
        table { width: 100%; border-collapse: collapse; font-size: 12.5px; margin-bottom: 16px; }
        thead th { background: #f8fafc; padding: 7px 10px; text-align: left; font-weight: 700; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid #d1d9e0; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #f0f3f6; }
        tfoot td { padding: 7px 10px; font-weight: 700; background: #f8fafc; border-top: 1px solid #d1d9e0; }
        .text-right { text-align: right; }
        .text-green { color: #1a7f4b; }
        .text-red { color: #d93025; }
        .text-blue { color: #1a56db; }
        .empty { color: #adb5bd; font-style: italic; padding: 8px 10px; }
        .print-footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e8ecf0; font-size: 11px; color: #adb5bd; text-align: center; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 12px; }
            h2 { page-break-before: auto; }
            table { page-break-inside: auto; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <span style="font-size:13px; color:#145f38;"><strong>Pratinjau Cetak</strong> — Laporan Keuangan</span>
    <button class="btn-print" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
    <button class="btn-close" onclick="window.close()">Tutup</button>
</div>

<div class="header">
    <h1>{{ $kegiatanUsaha->nama_usaha }}</h1>
    <p class="sub">{{ $kegiatanUsaha->jenis_usaha ?? 'Kegiatan Usaha' }} &nbsp;·&nbsp; Mulai: {{ $kegiatanUsaha->tanggal_mulai->format('d M Y') }}</p>
    <p class="sub">Laporan Keuangan &nbsp;·&nbsp; Dicetak {{ now()->isoFormat('dddd, D MMMM Y HH:mm') }}</p>
</div>

{{-- Ringkasan --}}
<div class="stats-grid">
    <div class="stat-box">
        <p class="val text-blue">Rp {{ number_format($totalModal, 0, ',', '.') }}</p>
        <p class="lbl">Modal Diterima</p>
    </div>
    <div class="stat-box">
        <p class="val text-green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
        <p class="lbl">Total Pemasukan</p>
    </div>
    <div class="stat-box">
        <p class="val text-red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
        <p class="lbl">Total Pengeluaran</p>
    </div>
    <div class="stat-box">
        <p class="val">Rp {{ number_format($kasTersedia, 0, ',', '.') }}</p>
        <p class="lbl">Kas Tersedia</p>
    </div>
    <div class="stat-box">
        <p class="val {{ $labaRugi >= 0 ? 'text-green' : 'text-red' }}">
            {{ $labaRugi >= 0 ? '' : '-' }}Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
        </p>
        <p class="lbl">Laba / Rugi</p>
    </div>
</div>

{{-- Sumber Modal --}}
<h2>Sumber Modal</h2>
@php $sumberMap = ['hibah_pemerintah'=>'Hibah Pemerintah','pinjaman_bank'=>'Pinjaman Bank/KSP','modal_sendiri'=>'Modal Sendiri','donasi_csr'=>'Donasi/CSR','lain_lain'=>'Lain-lain']; @endphp
@if($kegiatanUsaha->modal->count() > 0)
<table>
    <thead><tr><th>Tanggal Diterima</th><th>Sumber Modal</th><th>Keterangan</th><th class="text-right">Jumlah</th></tr></thead>
    <tbody>
        @foreach($kegiatanUsaha->modal as $m)
        <tr>
            <td>{{ $m->tanggal_terima->format('d/m/Y') }}</td>
            <td>{{ $sumberMap[$m->sumber] ?? ucwords(str_replace('_',' ',$m->sumber)) }}</td>
            <td>{{ $m->keterangan ?? '-' }}</td>
            <td class="text-right text-blue">Rp {{ number_format($m->jumlah, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot><tr><td colspan="3" class="text-right">Total Modal</td><td class="text-right text-blue">Rp {{ number_format($totalModal, 0, ',', '.') }}</td></tr></tfoot>
</table>
@else<p class="empty">Belum ada data modal.</p>@endif

{{-- Pemasukan --}}
<h2>Pemasukan</h2>
@php $pemasukan = $kegiatanUsaha->transaksi->where('tipe','pemasukan'); @endphp
@if($pemasukan->count() > 0)
<table>
    <thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th class="text-right">Jumlah</th></tr></thead>
    <tbody>
        @foreach($pemasukan as $t)
        <tr>
            <td>{{ $t->tanggal->format('d/m/Y') }}</td>
            <td>{{ ucwords(str_replace('_',' ',$t->kategori)) }}</td>
            <td>{{ $t->keterangan ?? '-' }}</td>
            <td class="text-right text-green">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot><tr><td colspan="3" class="text-right">Total Pemasukan</td><td class="text-right text-green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td></tr></tfoot>
</table>
@else<p class="empty">Belum ada pemasukan.</p>@endif

{{-- Pengeluaran --}}
<h2>Pengeluaran</h2>
@php $pengeluaran = $kegiatanUsaha->transaksi->where('tipe','pengeluaran'); @endphp
@if($pengeluaran->count() > 0)
<table>
    <thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th class="text-right">Jumlah</th></tr></thead>
    <tbody>
        @foreach($pengeluaran as $t)
        <tr>
            <td>{{ $t->tanggal->format('d/m/Y') }}</td>
            <td>{{ ucwords(str_replace('_',' ',$t->kategori)) }}</td>
            <td>{{ $t->keterangan ?? '-' }}</td>
            <td class="text-right text-red">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot><tr><td colspan="3" class="text-right">Total Pengeluaran</td><td class="text-right text-red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td></tr></tfoot>
</table>
@else<p class="empty">Belum ada pengeluaran.</p>@endif

<div class="print-footer">
    Laporan ini dicetak dari sistem KTH App &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
