@extends('layouts.app')
@section('title', $kegiatanUsaha->nama_usaha)
@section('page_title', 'Detail Kegiatan Usaha')

@push('styles')
<style>
    .stat-usaha { background:#fff; border:1px solid #e8ecf0; border-radius:10px; padding:16px 20px; text-align:center; }
    .stat-usaha .val { font-size:18px; font-weight:700; margin:0; }
    .stat-usaha .lbl { font-size:11px; color:#6b7a8d; margin:4px 0 0; }
    .tab-nav { display:flex; gap:0; border-bottom:2px solid #e8ecf0; margin-bottom:20px; }
    .tab-btn {
        padding:10px 20px; border:none; background:none; cursor:pointer;
        font-size:13px; font-weight:600; color:#6b7a8d;
        border-bottom:2px solid transparent; margin-bottom:-2px;
        transition:all .2s;
    }
    .tab-btn:hover { color:#1a7f4b; }
    .tab-btn.active { color:#1a7f4b; border-bottom-color:#1a7f4b; }
    .harian-card { border:1px solid #e8ecf0; border-radius:10px; margin-bottom:12px; overflow:hidden; }
    .harian-head { padding:12px 16px; display:flex; align-items:center; justify-content:space-between; background:#f8fafc; }
    .harian-body { padding:16px; border-top:1px solid #f0f3f6; }
    .harian-foto-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-top:12px; }
    .harian-foto-grid img { width:100%; aspect-ratio:4/3; object-fit:cover; border-radius:8px; cursor:pointer; transition:opacity .2s; }
    .harian-foto-grid img:hover { opacity:.85; }
    .kondisi-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:600; }
    .kondisi-cerah   { background:#fff8e6; color:#9a6800; }
    .kondisi-berawan { background:#f1f3f4; color:#5f6368; }
    .kondisi-mendung { background:#e8f0fe; color:#1a56db; }
    .kondisi-hujan   { background:#e8f5ee; color:#1a7f4b; }
    .add-panel { background:#f8fafc; border:1px solid #e8ecf0; border-radius:10px; padding:20px; margin-bottom:20px; display:none; }
    .add-panel.open { display:block; }
    .info-row { display:flex; gap:8px; margin-bottom:8px; font-size:13.5px; }
    .info-row .info-key { color:#6b7a8d; min-width:120px; font-weight:500; }
    /* Lightbox */
    #lb-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.92); z-index:9999; align-items:center; justify-content:center; }
    #lb-overlay.show { display:flex; }
    #lb-img { max-width:90vw; max-height:85vh; border-radius:8px; object-fit:contain; }
    #lb-close { position:fixed; top:20px; right:24px; color:#fff; font-size:28px; cursor:pointer; opacity:.7; }
    #lb-close:hover { opacity:1; }
    #lb-prev, #lb-next { position:fixed; top:50%; transform:translateY(-50%); color:#fff; font-size:32px; cursor:pointer; opacity:.6; padding:12px; }
    #lb-prev:hover, #lb-next:hover { opacity:1; }
    #lb-prev { left:16px; } #lb-next { right:16px; }
</style>
@endpush

@section('content')

{{-- Lightbox --}}
<div id="lb-overlay">
    <span id="lb-close" onclick="lbClose()"><i class="fas fa-times"></i></span>
    <span id="lb-prev" onclick="lbNav(-1)"><i class="fas fa-chevron-left"></i></span>
    <img id="lb-img" src="" alt="">
    <span id="lb-next" onclick="lbNav(1)"><i class="fas fa-chevron-right"></i></span>
</div>

{{-- Header actions --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <a href="{{ route('kegiatan-usaha.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <div style="display:flex; gap:8px;">
        <a href="{{ route('kegiatan-usaha.edit', $kegiatanUsaha) }}" class="btn btn-outline">
            <i class="fas fa-pen"></i> Edit
        </a>
        <form method="POST" action="{{ route('kegiatan-usaha.destroy', $kegiatanUsaha) }}"
              onsubmit="return confirm('Hapus kegiatan usaha ini beserta semua data di dalamnya?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</button>
        </form>
    </div>
</div>

{{-- Info card --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <div>
            <h3 style="margin:0;">{{ $kegiatanUsaha->nama_usaha }}</h3>
            @if($kegiatanUsaha->jenis_usaha)
            <span style="font-size:12px; color:#6b7a8d;">{{ $kegiatanUsaha->jenis_usaha }}</span>
            @endif
        </div>
        @php $badge = match($kegiatanUsaha->status) { 'aktif'=>'badge-success','selesai'=>'badge-info','tutup'=>'badge-danger',default=>'badge-gray' }; @endphp
        <span class="badge {{ $badge }}" style="font-size:12px;">{{ ucfirst($kegiatanUsaha->status) }}</span>
    </div>
    <div class="card-body">
        {{-- Stats row --}}
        <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:20px;">
            <div class="stat-usaha">
                <p class="val" style="color:#1a56db;">Rp {{ number_format($totalModal, 0, ',', '.') }}</p>
                <p class="lbl"><i class="fas fa-coins" style="margin-right:3px;"></i>Modal Diterima</p>
            </div>
            <div class="stat-usaha">
                <p class="val" style="color:#1a7f4b;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                <p class="lbl"><i class="fas fa-arrow-down" style="margin-right:3px;"></i>Total Pemasukan</p>
            </div>
            <div class="stat-usaha">
                <p class="val" style="color:#d93025;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                <p class="lbl"><i class="fas fa-arrow-up" style="margin-right:3px;"></i>Total Pengeluaran</p>
            </div>
            <div class="stat-usaha">
                <p class="val" style="color:#0f5132;">Rp {{ number_format($kasTersedia, 0, ',', '.') }}</p>
                <p class="lbl"><i class="fas fa-wallet" style="margin-right:3px;"></i>Kas Tersedia</p>
            </div>
            <div class="stat-usaha">
                <p class="val" style="color:{{ $labaRugi >= 0 ? '#1a7f4b' : '#d93025' }};">
                    {{ $labaRugi >= 0 ? '' : '-' }}Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
                </p>
                <p class="lbl"><i class="fas fa-chart-line" style="margin-right:3px;"></i>Laba / Rugi</p>
            </div>
        </div>

        {{-- Info bawah --}}
        <div style="display:grid; grid-template-columns:220px 1fr; gap:24px; align-items:start;">
            <div>
                <div class="info-row">
                    <span class="info-key"><i class="fas fa-tag" style="width:14px;"></i> Jenis</span>
                    <span>{{ $kegiatanUsaha->jenis_usaha ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key"><i class="fas fa-calendar" style="width:14px;"></i> Mulai</span>
                    <span>{{ $kegiatanUsaha->tanggal_mulai->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key"><i class="fas fa-calendar-check" style="width:14px;"></i> Selesai</span>
                    <span>{{ $kegiatanUsaha->tanggal_selesai ? $kegiatanUsaha->tanggal_selesai->format('d M Y') : '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key"><i class="fas fa-book" style="width:14px;"></i> Harian</span>
                    <span>{{ $kegiatanUsaha->harian->count() }} catatan</span>
                </div>
            </div>
            <div>
                @if($kegiatanUsaha->deskripsi)
                <p style="font-size:13.5px; color:#374151; line-height:1.6; margin:0; white-space:pre-line;">{{ $kegiatanUsaha->deskripsi }}</p>
                @else
                <p style="color:#adb5bd; font-size:13px; margin:0;">Tidak ada deskripsi.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- QR Transparansi --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-qrcode" style="color:#1a7f4b;margin-right:8px;"></i>QR Transparansi Publik</h3>
        @if($kegiatanUsaha->qr_token)
        <form method="POST" action="{{ route('kegiatan-usaha.toggle-qr', $kegiatanUsaha) }}">
            @csrf @method('PATCH')
            <button class="btn {{ $kegiatanUsaha->qr_aktif ? 'btn-outline' : 'btn-primary' }} btn-sm">
                @if($kegiatanUsaha->qr_aktif)
                <i class="fas fa-toggle-on" style="color:#1a7f4b;"></i> Aktif — Nonaktifkan
                @else
                <i class="fas fa-toggle-off"></i> Nonaktif — Aktifkan
                @endif
            </button>
        </form>
        @endif
    </div>
    <div class="card-body">
        @if($kegiatanUsaha->qr_token)
        <div style="display:flex; gap:24px; align-items:flex-start; flex-wrap:wrap;">
            <div style="flex-shrink:0;">
                <div id="qrUsahaCode" style="padding:8px; background:#fff; border:1px solid #e8ecf0; border-radius:8px; display:inline-block;"></div>
                <div style="margin-top:8px; text-align:center;">
                    @if($kegiatanUsaha->qr_aktif)
                    <span class="badge badge-success"><i class="fas fa-circle" style="font-size:7px;margin-right:3px;"></i>Aktif</span>
                    @else
                    <span class="badge badge-danger"><i class="fas fa-circle" style="font-size:7px;margin-right:3px;"></i>Nonaktif</span>
                    @endif
                </div>
            </div>
            <div style="flex:1; min-width:200px;">
                <p style="font-size:13px; color:#374151; line-height:1.6; margin-bottom:12px;">
                    Bagikan QR ini kepada pemberi modal atau masyarakat agar dapat memantau kegiatan usaha, keuangan, dan sumber dana secara transparan.
                </p>
                <div style="background:#f8fafc; border:1px solid #e8ecf0; border-radius:8px; padding:10px 12px; margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                    <code style="font-size:11px; color:#374151; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $kegiatanUsaha->laporan_url }}</code>
                    <button onclick="copyLink()" class="btn btn-outline btn-sm" id="copyBtn" title="Salin link">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <a href="{{ $kegiatanUsaha->laporan_url }}" target="_blank" class="btn btn-outline btn-sm">
                        <i class="fas fa-external-link-alt"></i> Buka Halaman
                    </a>
                </div>
            </div>
        </div>
        @else
        <div style="text-align:center; padding:20px;">
            <p style="color:#6b7a8d; font-size:13px; margin-bottom:12px;">QR belum digenerate untuk kegiatan usaha ini.</p>
            <form method="POST" action="{{ route('kegiatan-usaha.generate-qr', $kegiatanUsaha) }}">
                @csrf
                <button class="btn btn-primary"><i class="fas fa-qrcode"></i> Generate QR</button>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- Tabs card --}}
<div class="card">
    <div class="card-header" style="padding-bottom:0; border-bottom:none;">
        <nav class="tab-nav">
            <button class="tab-btn" data-tab="harian" onclick="switchTab('harian')">
                <i class="fas fa-book-open" style="margin-right:6px;"></i>Catatan Harian
                <span style="background:#e8ecf0; color:#374151; font-size:11px; padding:1px 6px; border-radius:10px; margin-left:4px;">{{ $kegiatanUsaha->harian->count() }}</span>
            </button>
            <button class="tab-btn" data-tab="transaksi" onclick="switchTab('transaksi')">
                <i class="fas fa-receipt" style="margin-right:6px;"></i>Transaksi
                <span style="background:#e8ecf0; color:#374151; font-size:11px; padding:1px 6px; border-radius:10px; margin-left:4px;">{{ $kegiatanUsaha->transaksi->count() }}</span>
            </button>
            <button class="tab-btn" data-tab="modal" onclick="switchTab('modal')">
                <i class="fas fa-coins" style="margin-right:6px;"></i>Modal
                <span style="background:#e8ecf0; color:#374151; font-size:11px; padding:1px 6px; border-radius:10px; margin-left:4px;">{{ $kegiatanUsaha->modal->count() }}</span>
            </button>
        </nav>
    </div>
    <div class="card-body">

        {{-- ═══════════════════ TAB HARIAN ═══════════════════ --}}
        <div id="tab-harian" class="tab-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <span style="font-size:13px; color:#6b7a8d;">{{ $kegiatanUsaha->harian->count() }} catatan tercatat</span>
                <div style="display:flex; gap:8px;">
                    <a href="{{ route('kegiatan-usaha.print-harian', $kegiatanUsaha) }}" target="_blank" class="btn btn-outline btn-sm">
                        <i class="fas fa-print"></i> Cetak PDF
                    </a>
                    <button onclick="togglePanel('addHarianPanel')" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Catatan
                    </button>
                </div>
            </div>

            {{-- Form tambah harian --}}
            <div id="addHarianPanel" class="add-panel">
                <h4 style="margin:0 0 16px; font-size:14px; color:#1e2a35;"><i class="fas fa-plus-circle" style="color:#1a7f4b;margin-right:6px;"></i>Catatan Harian Baru</h4>
                <form method="POST" action="{{ route('kegiatan-usaha.add-harian', $kegiatanUsaha) }}" enctype="multipart/form-data">
                    @csrf
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Tanggal <span style="color:#d93025;">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kondisi</label>
                            <select name="kondisi" class="form-control form-select">
                                <option value="">-- Kondisi --</option>
                                <option value="cerah">☀️ Cerah</option>
                                <option value="berawan">⛅ Berawan</option>
                                <option value="mendung">🌥️ Mendung</option>
                                <option value="hujan">🌧️ Hujan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Judul Catatan <span style="color:#d93025;">*</span></label>
                        <input type="text" name="judul" class="form-control" placeholder="Ringkasan singkat kegiatan hari ini">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Isi Catatan <span style="color:#d93025;">*</span></label>
                        <textarea name="isi" class="form-control" rows="5"
                                  placeholder="Ceritakan aktivitas, progres, kendala, dan hasil yang dicapai hari ini..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Foto Dokumentasi <span style="font-size:11px; color:#6b7a8d;">(maks. 3 foto, jpg/png, maks. 3MB)</span></label>
                        <input type="file" name="fotos[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp">
                    </div>
                    <div style="display:flex; gap:10px;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan Catatan</button>
                        <button type="button" onclick="togglePanel('addHarianPanel')" class="btn btn-outline btn-sm">Batal</button>
                    </div>
                </form>
            </div>

            {{-- Daftar harian --}}
            @forelse($kegiatanUsaha->harian as $h)
            <div class="harian-card">
                <div class="harian-head">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div>
                            <div style="font-size:12px; color:#6b7a8d; font-weight:600;">{{ $h->tanggal->isoFormat('dddd, D MMMM Y') }}</div>
                            <div style="font-size:14px; font-weight:700; color:#1e2a35; margin-top:2px;">{{ $h->judul }}</div>
                        </div>
                        @if($h->kondisi)
                        @php $kmap = ['cerah'=>['cerah','☀️'],'berawan'=>['berawan','⛅'],'mendung'=>['mendung','🌥️'],'hujan'=>['hujan','🌧️']]; @endphp
                        <span class="kondisi-badge kondisi-{{ $h->kondisi }}">
                            {{ $kmap[$h->kondisi][1] ?? '' }} {{ ucfirst($h->kondisi) }}
                        </span>
                        @endif
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        @if($h->fotos->count() > 0)
                        <span style="font-size:11px; color:#6b7a8d;"><i class="fas fa-image" style="margin-right:3px;"></i>{{ $h->fotos->count() }}</span>
                        @endif
                        <form method="POST" action="{{ route('kegiatan-usaha.remove-harian', [$kegiatanUsaha, $h]) }}"
                              onsubmit="return confirm('Hapus catatan harian ini beserta fotonya?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm btn-icon" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="harian-body">
                    <p style="font-size:13.5px; color:#374151; line-height:1.7; margin:0; white-space:pre-line;">{{ $h->isi }}</p>
                    @if($h->fotos->count() > 0)
                    <div class="harian-foto-grid" style="grid-template-columns:repeat({{ min($h->fotos->count(), 3) }},1fr);">
                        @foreach($h->fotos as $foto)
                        <img src="{{ asset($foto->file_path) }}" alt="" onclick="lbOpen({{ $loop->index }}, 'harian_{{ $h->id }}')">
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:40px; color:#adb5bd;">
                <i class="fas fa-book-open" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                Belum ada catatan harian. Klik <strong>Tambah Catatan</strong> untuk mulai mencatat.
            </div>
            @endforelse
        </div>

        {{-- ═══════════════════ TAB TRANSAKSI ═══════════════════ --}}
        <div id="tab-transaksi" class="tab-content" style="display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <span style="font-size:13px; color:#6b7a8d;">{{ $kegiatanUsaha->transaksi->count() }} transaksi tercatat</span>
                <div style="display:flex; gap:8px;">
                    <a href="{{ route('kegiatan-usaha.print-transaksi', $kegiatanUsaha) }}" target="_blank" class="btn btn-outline btn-sm">
                        <i class="fas fa-print"></i> Cetak PDF
                    </a>
                    <button onclick="togglePanel('addTransaksiPanel')" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Transaksi
                    </button>
                </div>
            </div>

            {{-- Form tambah transaksi --}}
            <div id="addTransaksiPanel" class="add-panel">
                <h4 style="margin:0 0 16px; font-size:14px;"><i class="fas fa-plus-circle" style="color:#1a7f4b;margin-right:6px;"></i>Transaksi Baru</h4>
                <form method="POST" action="{{ route('kegiatan-usaha.add-transaksi', $kegiatanUsaha) }}">
                    @csrf
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Tipe <span style="color:#d93025;">*</span></label>
                            <select name="tipe" id="tipeSelect" class="form-control form-select" onchange="updateKategori(this.value)">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="pemasukan">Pemasukan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kategori <span style="color:#d93025;">*</span></label>
                            <select name="kategori" id="kategoriSelect" class="form-control form-select">
                                <option value="">-- Pilih tipe dulu --</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Jumlah (Rp) <span style="color:#d93025;">*</span></label>
                            <input type="number" name="jumlah" class="form-control" min="1" placeholder="500000">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal <span style="color:#d93025;">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Keterangan singkat (opsional)">
                    </div>
                    <div style="display:flex; gap:10px;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
                        <button type="button" onclick="togglePanel('addTransaksiPanel')" class="btn btn-outline btn-sm">Batal</button>
                    </div>
                </form>
            </div>

            {{-- Tabel transaksi --}}
            @if($kegiatanUsaha->transaksi->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th style="text-align:right;">Jumlah</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kegiatanUsaha->transaksi as $t)
                        <tr>
                            <td style="white-space:nowrap;">{{ $t->tanggal->format('d/m/Y') }}</td>
                            <td>
                                @if($t->tipe === 'pemasukan')
                                <span class="badge badge-success"><i class="fas fa-arrow-down" style="margin-right:3px;"></i>Pemasukan</span>
                                @else
                                <span class="badge badge-danger"><i class="fas fa-arrow-up" style="margin-right:3px;"></i>Pengeluaran</span>
                                @endif
                            </td>
                            <td>{{ ucwords(str_replace('_',' ', $t->kategori)) }}</td>
                            <td style="color:#6b7a8d;">{{ $t->keterangan ?? '-' }}</td>
                            <td style="text-align:right; font-weight:600; color:{{ $t->tipe === 'pemasukan' ? '#1a7f4b' : '#d93025' }};">
                                {{ $t->tipe === 'pengeluaran' ? '-' : '' }}Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                            </td>
                            <td style="text-align:center;">
                                <form method="POST" action="{{ route('kegiatan-usaha.remove-transaksi', [$kegiatanUsaha, $t]) }}"
                                      onsubmit="return confirm('Hapus transaksi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-icon"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8fafc;">
                            <td colspan="4" style="text-align:right; font-weight:700; padding:10px 14px; font-size:13px;">Total Pemasukan</td>
                            <td style="text-align:right; font-weight:700; color:#1a7f4b; padding:10px 14px;">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                        <tr style="background:#f8fafc;">
                            <td colspan="4" style="text-align:right; font-weight:700; padding:6px 14px 10px; font-size:13px;">Total Pengeluaran</td>
                            <td style="text-align:right; font-weight:700; color:#d93025; padding:6px 14px 10px;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div style="text-align:center; padding:40px; color:#adb5bd;">
                <i class="fas fa-receipt" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                Belum ada transaksi. Klik <strong>Tambah Transaksi</strong> untuk mulai mencatat.
            </div>
            @endif
        </div>

        {{-- ═══════════════════ TAB MODAL ═══════════════════ --}}
        <div id="tab-modal" class="tab-content" style="display:none;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <span style="font-size:13px; color:#6b7a8d;">{{ $kegiatanUsaha->modal->count() }} sumber modal</span>
                <button onclick="togglePanel('addModalPanel')" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Modal
                </button>
            </div>

            {{-- Form tambah modal --}}
            <div id="addModalPanel" class="add-panel">
                <h4 style="margin:0 0 16px; font-size:14px;"><i class="fas fa-plus-circle" style="color:#1a7f4b;margin-right:6px;"></i>Permodalan Baru</h4>
                <form method="POST" action="{{ route('kegiatan-usaha.add-modal', $kegiatanUsaha) }}">
                    @csrf
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Sumber Modal <span style="color:#d93025;">*</span></label>
                            <select name="sumber" class="form-control form-select">
                                <option value="">-- Pilih Sumber --</option>
                                <option value="hibah_pemerintah">Hibah Pemerintah</option>
                                <option value="pinjaman_bank">Pinjaman Bank / KSP</option>
                                <option value="modal_sendiri">Modal Sendiri</option>
                                <option value="donasi_csr">Donasi / CSR</option>
                                <option value="lain_lain">Lain-lain</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Diterima <span style="color:#d93025;">*</span></label>
                            <input type="date" name="tanggal_terima" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Jumlah (Rp) <span style="color:#d93025;">*</span></label>
                            <input type="number" name="jumlah" class="form-control" min="1" placeholder="5000000">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Nomor SP / SK / kontrak (opsional)">
                        </div>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Simpan</button>
                        <button type="button" onclick="togglePanel('addModalPanel')" class="btn btn-outline btn-sm">Batal</button>
                    </div>
                </form>
            </div>

            {{-- Tabel modal --}}
            @if($kegiatanUsaha->modal->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal Diterima</th>
                            <th>Sumber Modal</th>
                            <th>Keterangan</th>
                            <th style="text-align:right;">Jumlah</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kegiatanUsaha->modal as $m)
                        <tr>
                            <td>{{ $m->tanggal_terima->format('d/m/Y') }}</td>
                            <td>
                                @php $sumberMap = ['hibah_pemerintah'=>'Hibah Pemerintah','pinjaman_bank'=>'Pinjaman Bank/KSP','modal_sendiri'=>'Modal Sendiri','donasi_csr'=>'Donasi/CSR','lain_lain'=>'Lain-lain']; @endphp
                                <span class="badge badge-info">{{ $sumberMap[$m->sumber] ?? ucwords(str_replace('_',' ',$m->sumber)) }}</span>
                            </td>
                            <td style="color:#6b7a8d;">{{ $m->keterangan ?? '-' }}</td>
                            <td style="text-align:right; font-weight:700; color:#1a56db;">Rp {{ number_format($m->jumlah, 0, ',', '.') }}</td>
                            <td style="text-align:center;">
                                <form method="POST" action="{{ route('kegiatan-usaha.remove-modal', [$kegiatanUsaha, $m]) }}"
                                      onsubmit="return confirm('Hapus data modal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-icon"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8fafc;">
                            <td colspan="3" style="text-align:right; font-weight:700; padding:10px 14px; font-size:13px;">Total Modal</td>
                            <td style="text-align:right; font-weight:700; color:#1a56db; padding:10px 14px;">Rp {{ number_format($totalModal, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div style="text-align:center; padding:40px; color:#adb5bd;">
                <i class="fas fa-coins" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                Belum ada data modal. Klik <strong>Tambah Modal</strong> untuk mencatat permodalan.
            </div>
            @endif
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // ── Lightbox ────────────────────────────────────────────
    const lbData = {};
    @foreach($kegiatanUsaha->harian as $h)
    @if($h->fotos->count() > 0)
    lbData['harian_{{ $h->id }}'] = [
        @foreach($h->fotos as $foto)
        '{{ asset($foto->file_path) }}',
        @endforeach
    ];
    @endif
    @endforeach

    let lbCurrent = 0, lbGroup = null;

    function lbOpen(idx, group) {
        lbGroup = group;
        lbCurrent = idx;
        document.getElementById('lb-img').src = lbData[group][idx];
        document.getElementById('lb-overlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function lbClose() {
        document.getElementById('lb-overlay').classList.remove('show');
        document.body.style.overflow = '';
    }

    function lbNav(dir) {
        const imgs = lbData[lbGroup];
        lbCurrent = (lbCurrent + dir + imgs.length) % imgs.length;
        document.getElementById('lb-img').src = imgs[lbCurrent];
    }

    document.getElementById('lb-overlay').addEventListener('click', function(e) {
        if (e.target === this) lbClose();
    });

    document.addEventListener('keydown', e => {
        if (!document.getElementById('lb-overlay').classList.contains('show')) return;
        if (e.key === 'Escape') lbClose();
        if (e.key === 'ArrowLeft') lbNav(-1);
        if (e.key === 'ArrowRight') lbNav(1);
    });

    // ── Tabs ───────────────────────────────────────────────
    function switchTab(name) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + name).style.display = 'block';
        document.querySelector('[data-tab="' + name + '"]').classList.add('active');
        localStorage.setItem('usaha_tab_{{ $kegiatanUsaha->id }}', name);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const active = '{{ session("active_tab", "harian") }}';
        const saved  = localStorage.getItem('usaha_tab_{{ $kegiatanUsaha->id }}') || active;
        switchTab(saved);
    });

    // ── Panel toggle ───────────────────────────────────────
    function togglePanel(id) {
        const panel = document.getElementById(id);
        panel.classList.toggle('open');
    }

    // ── Kategori dinamis per tipe transaksi ────────────────
    const kategoriMap = {
        pemasukan: {
            penjualan:       'Penjualan Produk',
            pendapatan_jasa: 'Pendapatan Jasa',
            lain_lain:       'Lain-lain',
        },
        pengeluaran: {
            bahan_baku:   'Bahan Baku',
            operasional:  'Biaya Operasional',
            tenaga_kerja: 'Tenaga Kerja',
            transportasi: 'Transportasi',
            lain_lain:    'Lain-lain',
        },
    };

    function updateKategori(tipe) {
        const sel = document.getElementById('kategoriSelect');
        sel.innerHTML = '<option value="">-- Pilih Kategori --</option>';
        if (!tipe || !kategoriMap[tipe]) return;
        for (const [val, label] of Object.entries(kategoriMap[tipe])) {
            const opt = document.createElement('option');
            opt.value = val;
            opt.textContent = label;
            sel.appendChild(opt);
        }
    }

    // ── QR Code ───────────────────────────────────────────
    @if($kegiatanUsaha->qr_token)
    new QRCode(document.getElementById('qrUsahaCode'), {
        text: '{{ $kegiatanUsaha->laporan_url }}',
        width: 156, height: 156,
        colorDark: '#0f2419', colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M,
    });
    @endif

    function copyLink() {
        navigator.clipboard.writeText('{{ $kegiatanUsaha->laporan_url }}').then(() => {
            const btn = document.getElementById('copyBtn');
            btn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy"></i>'; }, 2000);
        });
    }
</script>
@endpush
