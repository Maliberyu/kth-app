<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $usaha->nama_usaha }} – Laporan Transparansi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f8; color: #1e2a35; }
        :root { --primary: #1a7f4b; --primary-dark: #0f5132; --accent: #f0a500; }

        .pub-header {
            background: linear-gradient(135deg, #0f2419 0%, #1a7f4b 100%);
            padding: 20px 20px 24px; color: #fff;
        }
        .pub-brand { font-size: 12px; color: rgba(255,255,255,.6); margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
        .pub-brand i { color: var(--accent); }
        .pub-title { font-size: 20px; font-weight: 700; line-height: 1.3; }
        .pub-meta { font-size: 12px; color: rgba(255,255,255,.65); margin-top: 6px; }
        .pub-status { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; margin-top: 8px; }
        .status-aktif   { background: rgba(255,255,255,.15); color: #7fffc4; }
        .status-selesai { background: rgba(255,255,255,.15); color: #93c5fd; }
        .status-tutup   { background: rgba(255,255,255,.15); color: #fca5a5; }

        .stats-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; padding: 16px; background: #fff; border-bottom: 1px solid #e8ecf0; }
        .stat-pub { background: #f8fafc; border-radius: 10px; padding: 12px 14px; }
        .stat-pub .val { font-size: 15px; font-weight: 700; }
        .stat-pub .lbl { font-size: 11px; color: #6b7a8d; margin-top: 3px; }

        .tab-nav-pub { display: flex; overflow-x: auto; background: #fff; border-bottom: 2px solid #e8ecf0; }
        .tab-nav-pub::-webkit-scrollbar { display: none; }
        .tab-btn-pub { flex-shrink: 0; padding: 12px 16px; border: none; background: none; font-size: 13px; font-weight: 600; color: #6b7a8d; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; white-space: nowrap; }
        .tab-btn-pub.active { color: var(--primary); border-bottom-color: var(--primary); }

        .tab-body { padding: 16px; }

        .harian-entry { background: #fff; border-radius: 10px; border: 1px solid #e8ecf0; margin-bottom: 12px; overflow: hidden; }
        .harian-entry-head { padding: 10px 14px; background: #f8fafc; border-bottom: 1px solid #f0f3f6; }
        .harian-entry-date { font-size: 11px; color: #6b7a8d; }
        .harian-entry-title { font-size: 14px; font-weight: 700; margin-top: 2px; }
        .harian-entry-body { padding: 12px 14px; }
        .harian-entry-isi { font-size: 13px; line-height: 1.65; white-space: pre-line; color: #374151; }
        .foto-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-top: 10px; }
        .foto-grid img { width: 100%; aspect-ratio: 4/3; object-fit: cover; border-radius: 6px; cursor: pointer; }
        .kondisi-tag { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 10px; margin-left: 6px; }
        .kondisi-cerah   { background: #fff8e6; color: #9a6800; }
        .kondisi-berawan { background: #f1f3f4; color: #5f6368; }
        .kondisi-mendung { background: #e8f0fe; color: #1a56db; }
        .kondisi-hujan   { background: #e8f5ee; color: #1a7f4b; }

        table.pub-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .pub-table thead th { background: #f8fafc; padding: 9px 12px; text-align: left; font-weight: 700; font-size: 11px; text-transform: uppercase; color: #6b7a8d; border-bottom: 1px solid #e8ecf0; }
        .pub-table tbody td { padding: 10px 12px; border-bottom: 1px solid #f0f3f6; }
        .pub-table tfoot td { padding: 10px 12px; font-weight: 700; background: #f8fafc; border-top: 1px solid #e8ecf0; }
        .text-right { text-align: right; }
        .text-green { color: #1a7f4b; }
        .text-red { color: #d93025; }
        .text-blue { color: #1a56db; }

        .empty-state { text-align: center; padding: 32px 16px; color: #adb5bd; font-size: 13px; }
        .empty-state i { font-size: 28px; display: block; margin-bottom: 8px; }

        .pub-footer { padding: 20px 16px; text-align: center; font-size: 11px; color: #adb5bd; border-top: 1px solid #e8ecf0; background: #fff; margin-top: 16px; }

        /* Lightbox */
        #pub-lb { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.92); z-index: 9999; align-items: center; justify-content: center; }
        #pub-lb.show { display: flex; }
        #pub-lb img { max-width: 90vw; max-height: 85vh; border-radius: 6px; object-fit: contain; }
        #pub-lb-close { position: fixed; top: 16px; right: 20px; color: #fff; font-size: 28px; cursor: pointer; }

        /* Non-aktif */
        .nonaktif-wrap { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 32px 20px; text-align: center; }
        .nonaktif-icon { width: 72px; height: 72px; background: #fce8e6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px; color: #d93025; }
        .nonaktif-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .nonaktif-name { font-size: 15px; color: var(--primary); font-weight: 600; margin-bottom: 8px; }
        .nonaktif-msg { font-size: 13px; color: #6b7a8d; line-height: 1.6; max-width: 320px; margin: 0 auto; }
    </style>
</head>
<body>

@if($nonaktif ?? false)

{{-- ═══ NON-AKTIF ═══ --}}
<div style="background: linear-gradient(135deg, #0f2419 0%, #1a7f4b 100%); padding: 16px 20px;">
    <div style="font-size: 12px; color: rgba(255,255,255,.6);"><i class="fas fa-leaf" style="color:#f0a500;margin-right:4px;"></i> KTH App</div>
</div>
<div class="nonaktif-wrap">
    <div class="nonaktif-icon"><i class="fas fa-lock"></i></div>
    <p class="nonaktif-title">Laporan Tidak Dapat Diakses</p>
    <p class="nonaktif-name">{{ $usaha->nama_usaha }}</p>
    <p class="nonaktif-msg">
        Laporan transparansi kegiatan usaha ini saat ini tidak aktif.<br>
        Hubungi admin untuk informasi lebih lanjut.
    </p>
</div>

@else

{{-- ═══ AKTIF — HEADER ═══ --}}
<div class="pub-header">
    <div class="pub-brand"><i class="fas fa-leaf"></i> KTH App &nbsp;·&nbsp; Laporan Transparansi</div>
    <div class="pub-title">{{ $usaha->nama_usaha }}</div>
    <div class="pub-meta">
        {{ $usaha->jenis_usaha ?? 'Kegiatan Usaha' }}
        &nbsp;·&nbsp; Mulai {{ $usaha->tanggal_mulai->format('d M Y') }}
    </div>
    @php $sc = match($usaha->status) { 'aktif'=>'status-aktif','selesai'=>'status-selesai','tutup'=>'status-tutup',default=>'' }; @endphp
    <div class="pub-status {{ $sc }}">
        <i class="fas fa-circle" style="font-size:8px;"></i> {{ ucfirst($usaha->status) }}
    </div>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-pub">
        <div class="val text-blue">Rp {{ number_format($totalModal, 0, ',', '.') }}</div>
        <div class="lbl"><i class="fas fa-coins" style="margin-right:3px;"></i>Modal Diterima</div>
    </div>
    <div class="stat-pub">
        <div class="val text-green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
        <div class="lbl"><i class="fas fa-arrow-down" style="margin-right:3px;"></i>Pemasukan</div>
    </div>
    <div class="stat-pub">
        <div class="val text-red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
        <div class="lbl"><i class="fas fa-arrow-up" style="margin-right:3px;"></i>Pengeluaran</div>
    </div>
    <div class="stat-pub">
        <div class="val {{ $labaRugi >= 0 ? 'text-green' : 'text-red' }}">
            {{ $labaRugi >= 0 ? '' : '-' }}Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
        </div>
        <div class="lbl"><i class="fas fa-chart-line" style="margin-right:3px;"></i>Laba / Rugi</div>
    </div>
</div>

{{-- Tabs --}}
<nav class="tab-nav-pub">
    <button class="tab-btn-pub active" data-pub-tab="harian" onclick="pubTab('harian')">
        <i class="fas fa-book-open" style="margin-right:5px;"></i>Kegiatan Harian
        <span style="background:#e8ecf0;font-size:11px;padding:1px 5px;border-radius:8px;margin-left:4px;">{{ $usaha->harian->count() }}</span>
    </button>
    <button class="tab-btn-pub" data-pub-tab="pemasukan" onclick="pubTab('pemasukan')">
        <i class="fas fa-arrow-down" style="margin-right:5px;"></i>Pemasukan
    </button>
    <button class="tab-btn-pub" data-pub-tab="pengeluaran" onclick="pubTab('pengeluaran')">
        <i class="fas fa-arrow-up" style="margin-right:5px;"></i>Pengeluaran
    </button>
    <button class="tab-btn-pub" data-pub-tab="modal" onclick="pubTab('modal')">
        <i class="fas fa-coins" style="margin-right:5px;"></i>Sumber Dana
    </button>
</nav>

{{-- Lightbox --}}
<div id="pub-lb" onclick="if(event.target===this)pubLbClose()">
    <span id="pub-lb-close" onclick="pubLbClose()"><i class="fas fa-times"></i></span>
    <img id="pub-lb-img" src="" alt="">
</div>

<div class="tab-body">

    {{-- Tab: Harian --}}
    <div id="pubtab-harian">
        @forelse($usaha->harian as $h)
        <div class="harian-entry">
            <div class="harian-entry-head">
                <div class="harian-entry-date">
                    {{ $h->tanggal->isoFormat('dddd, D MMMM Y') }}
                    @if($h->kondisi)
                    <span class="kondisi-tag kondisi-{{ $h->kondisi }}">{{ ucfirst($h->kondisi) }}</span>
                    @endif
                </div>
                <div class="harian-entry-title">{{ $h->judul }}</div>
            </div>
            <div class="harian-entry-body">
                <p class="harian-entry-isi">{{ $h->isi }}</p>
                @if($h->fotos->count() > 0)
                <div class="foto-grid">
                    @foreach($h->fotos as $foto)
                    <img src="{{ asset($foto->file_path) }}" alt="" onclick="pubLbOpen('{{ asset($foto->file_path) }}')">
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state"><i class="fas fa-book-open"></i>Belum ada catatan harian.</div>
        @endforelse
    </div>

    {{-- Tab: Pemasukan --}}
    <div id="pubtab-pemasukan" style="display:none;">
        @php $pemasukan = $usaha->transaksi->where('tipe','pemasukan'); @endphp
        @if($pemasukan->count() > 0)
        <div style="overflow-x:auto;">
        <table class="pub-table">
            <thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
                @foreach($pemasukan as $t)
                <tr>
                    <td style="white-space:nowrap;">{{ $t->tanggal->format('d/m/Y') }}</td>
                    <td>{{ ucwords(str_replace('_',' ',$t->kategori)) }}</td>
                    <td style="color:#6b7a8d;">{{ $t->keterangan ?? '-' }}</td>
                    <td class="text-right text-green">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot><tr><td colspan="3" class="text-right">Total</td><td class="text-right text-green">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td></tr></tfoot>
        </table>
        </div>
        @else
        <div class="empty-state"><i class="fas fa-receipt"></i>Belum ada data pemasukan.</div>
        @endif
    </div>

    {{-- Tab: Pengeluaran --}}
    <div id="pubtab-pengeluaran" style="display:none;">
        @php $pengeluaran = $usaha->transaksi->where('tipe','pengeluaran'); @endphp
        @if($pengeluaran->count() > 0)
        <div style="overflow-x:auto;">
        <table class="pub-table">
            <thead><tr><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
                @foreach($pengeluaran as $t)
                <tr>
                    <td style="white-space:nowrap;">{{ $t->tanggal->format('d/m/Y') }}</td>
                    <td>{{ ucwords(str_replace('_',' ',$t->kategori)) }}</td>
                    <td style="color:#6b7a8d;">{{ $t->keterangan ?? '-' }}</td>
                    <td class="text-right text-red">Rp {{ number_format($t->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot><tr><td colspan="3" class="text-right">Total</td><td class="text-right text-red">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td></tr></tfoot>
        </table>
        </div>
        @else
        <div class="empty-state"><i class="fas fa-receipt"></i>Belum ada data pengeluaran.</div>
        @endif
    </div>

    {{-- Tab: Sumber Dana --}}
    <div id="pubtab-modal" style="display:none;">
        @php $sumberMap = ['hibah_pemerintah'=>'Hibah Pemerintah','pinjaman_bank'=>'Pinjaman Bank/KSP','modal_sendiri'=>'Modal Sendiri','donasi_csr'=>'Donasi/CSR','lain_lain'=>'Lain-lain']; @endphp
        @if($usaha->modal->count() > 0)
        <div style="overflow-x:auto;">
        <table class="pub-table">
            <thead><tr><th>Tanggal Diterima</th><th>Sumber</th><th>Keterangan</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
                @foreach($usaha->modal as $m)
                <tr>
                    <td style="white-space:nowrap;">{{ $m->tanggal_terima->format('d/m/Y') }}</td>
                    <td>{{ $sumberMap[$m->sumber] ?? ucwords(str_replace('_',' ',$m->sumber)) }}</td>
                    <td style="color:#6b7a8d;">{{ $m->keterangan ?? '-' }}</td>
                    <td class="text-right text-blue">Rp {{ number_format($m->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot><tr><td colspan="3" class="text-right">Total</td><td class="text-right text-blue">Rp {{ number_format($totalModal, 0, ',', '.') }}</td></tr></tfoot>
        </table>
        </div>
        @else
        <div class="empty-state"><i class="fas fa-coins"></i>Belum ada data sumber dana.</div>
        @endif
    </div>

</div>

<div class="pub-footer">
    <i class="fas fa-leaf" style="color:#1a7f4b;margin-right:4px;"></i>
    Laporan ini diterbitkan secara otomatis oleh sistem KTH App untuk keperluan transparansi.<br>
    Diakses pada {{ now()->isoFormat('D MMMM Y, HH:mm') }}
</div>

<script>
    function pubTab(name) {
        ['harian','pemasukan','pengeluaran','modal'].forEach(t => {
            document.getElementById('pubtab-' + t).style.display = t === name ? 'block' : 'none';
        });
        document.querySelectorAll('.tab-btn-pub').forEach(b => {
            b.classList.toggle('active', b.dataset.pubTab === name);
        });
    }
    function pubLbOpen(src) {
        document.getElementById('pub-lb-img').src = src;
        document.getElementById('pub-lb').classList.add('show');
    }
    function pubLbClose() {
        document.getElementById('pub-lb').classList.remove('show');
    }
</script>

@endif
</body>
</html>
