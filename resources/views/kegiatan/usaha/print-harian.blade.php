<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Harian – {{ $kegiatanUsaha->nama_usaha }}</title>
    <style>
        * { font-family: Arial, sans-serif; box-sizing: border-box; }
        body { margin: 0; padding: 24px; color: #1e2a35; font-size: 13px; }

        .no-print { background: #e8f5ee; border: 1px solid #b7dfca; border-radius: 6px; padding: 10px 16px; margin-bottom: 20px; display: flex; gap: 8px; align-items: center; }
        .no-print button { padding: 6px 14px; border-radius: 6px; border: none; cursor: pointer; font-size: 13px; font-weight: 600; }
        .btn-print { background: #1a7f4b; color: #fff; }
        .btn-close { background: transparent; color: #1a7f4b; border: 1.5px solid #1a7f4b !important; }

        .header { border-bottom: 2px solid #1a7f4b; padding-bottom: 12px; margin-bottom: 24px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; color: #0f2419; }
        .header .sub { font-size: 12px; color: #6b7a8d; margin: 2px 0; }

        .entry { border: 1px solid #d1d9e0; border-radius: 6px; margin-bottom: 14px; page-break-inside: avoid; overflow: hidden; }
        .entry-head { padding: 8px 12px; background: #f8fafc; border-bottom: 1px solid #e8ecf0; }
        .entry-date { font-size: 11px; color: #6b7a8d; margin: 0 0 2px; }
        .entry-title { font-size: 13px; font-weight: 700; margin: 0; }
        .entry-kondisi { font-size: 11px; color: #9a6800; margin-left: 6px; }
        .entry-body { padding: 10px 12px; }
        .entry-isi { font-size: 12.5px; line-height: 1.65; margin: 0 0 10px; white-space: pre-line; }

        .foto-row { display: flex; gap: 6px; flex-wrap: wrap; }
        .foto-row img { height: 80px; width: auto; max-width: 120px; object-fit: cover; border-radius: 4px; border: 1px solid #e8ecf0; }

        .empty { text-align: center; padding: 40px; color: #adb5bd; }
        .print-footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e8ecf0; font-size: 11px; color: #adb5bd; text-align: center; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 12px; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <span style="font-size:13px; color:#145f38;"><strong>Pratinjau Cetak</strong> — Catatan Harian</span>
    <button class="btn-print" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
    <button class="btn-close" onclick="window.close()">Tutup</button>
</div>

<div class="header">
    <h1>{{ $kegiatanUsaha->nama_usaha }}</h1>
    <p class="sub">{{ $kegiatanUsaha->jenis_usaha ?? 'Kegiatan Usaha' }} &nbsp;·&nbsp; Mulai: {{ $kegiatanUsaha->tanggal_mulai->format('d M Y') }}</p>
    <p class="sub">Laporan Catatan Harian &nbsp;·&nbsp; Dicetak {{ now()->isoFormat('dddd, D MMMM Y HH:mm') }}</p>
</div>

@forelse($kegiatanUsaha->harian as $h)
<div class="entry">
    <div class="entry-head">
        <p class="entry-date">{{ $h->tanggal->isoFormat('dddd, D MMMM Y') }}</p>
        <p class="entry-title">
            {{ $h->judul }}
            @if($h->kondisi)<span class="entry-kondisi">· {{ ucfirst($h->kondisi) }}</span>@endif
        </p>
    </div>
    <div class="entry-body">
        <p class="entry-isi">{{ $h->isi }}</p>
        @if($h->fotos->count() > 0)
        <div class="foto-row">
            @foreach($h->fotos as $foto)
            <img src="{{ asset($foto->file_path) }}" alt="Foto {{ $loop->iteration }}">
            @endforeach
        </div>
        @endif
    </div>
</div>
@empty
<div class="empty">Belum ada catatan harian.</div>
@endforelse

<div class="print-footer">
    Laporan ini dicetak dari sistem KTH App &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
