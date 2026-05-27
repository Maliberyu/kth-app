<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komoditas – {{ $kup->nama_kups }}</title>
    <style>
        * { font-family: Arial, sans-serif; box-sizing: border-box; }
        body { margin: 0; padding: 24px; color: #1e2a35; font-size: 13px; }

        .no-print { background: #e8f5ee; border: 1px solid #b7dfca; border-radius: 6px;
                    padding: 10px 16px; margin-bottom: 20px; display: flex; gap: 8px; align-items: center; }
        .no-print button { padding: 6px 14px; border-radius: 6px; border: none; cursor: pointer;
                           font-size: 13px; font-weight: 600; }
        .btn-print { background: #1a7f4b; color: #fff; }
        .btn-close  { background: transparent; color: #1a7f4b; border: 1.5px solid #1a7f4b !important; }

        .doc-header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #1a7f4b; padding-bottom: 14px; }
        .doc-header .judul-utama { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #0f2419; margin: 0 0 6px; }
        .doc-header h1 { font-size: 17px; font-weight: 700; color: #0f2419; margin: 0 0 4px; }
        .doc-header .sub { font-size: 11.5px; color: #6b7a8d; margin: 0; }

        table { width: 100%; border-collapse: collapse; font-size: 12.5px; margin-bottom: 16px; }
        thead th {
            background: #1a7f4b; color: #fff;
            padding: 8px 10px; text-align: left; font-weight: 700; font-size: 11.5px;
            border: 1px solid #15693e;
        }
        thead th.right { text-align: right; }
        tbody td { padding: 7px 10px; border: 1px solid #d1d9e0; vertical-align: middle; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tfoot td { padding: 8px 10px; font-weight: 700; background: #e8f5ee;
                   border: 1px solid #c3dfd0; }
        .text-right { text-align: right; }
        .print-footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e8ecf0;
                        font-size: 11px; color: #adb5bd; text-align: center; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 12px; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <span style="font-size:13px; color:#145f38;"><strong>Pratinjau Cetak</strong> — Daftar Komoditas</span>
    <button class="btn-print" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
    <button class="btn-close" onclick="window.close()">Tutup</button>
</div>

<div class="doc-header">
    <p class="judul-utama">Jenis Komoditas yang Dihasilkan</p>
    <h1>{{ strtoupper($kup->nama_kups) }}</h1>
    <p class="sub">Dicetak {{ now()->isoFormat('dddd, D MMMM Y HH:mm') }}</p>
</div>

@if($komoditas->count() > 0)
<table>
    <thead>
        <tr>
            <th style="width:36px;">No</th>
            <th>Komoditas</th>
            <th class="right" style="width:80px;">Volume</th>
            <th style="width:70px;">Satuan</th>
            <th class="right" style="width:110px;">Harga</th>
            <th class="right" style="width:120px;">Nilai Estimasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($komoditas as $i => $k)
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td>
                <strong>{{ $k->nama_komoditas }}</strong>
                @if($k->keterangan)<br><span style="font-size:11px; color:#6b7a8d;">{{ $k->keterangan }}</span>@endif
            </td>
            <td class="text-right">{{ $k->volume_format }}</td>
            <td>{{ $k->satuan }}</td>
            <td class="text-right">Rp {{ number_format($k->harga, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($k->volume * $k->harga, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right">Total Estimasi Nilai</td>
            <td class="text-right" style="color:#0f5c2e;">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
@else
<p style="text-align:center; color:#adb5bd; padding:32px;">Belum ada data komoditas.</p>
@endif

<div class="print-footer">
    Laporan ini dicetak dari sistem KTH App &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
