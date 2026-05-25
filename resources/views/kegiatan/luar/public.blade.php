<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $kegiatan->nama_kegiatan }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background: #f4f7f5; color: #1e2a35; font-size: 14px; }

        /* Header */
        .hero {
            background: linear-gradient(135deg, #0f5c2e 0%, #1a7f4b 60%, #2da05f 100%);
            color: #fff; padding: 28px 20px 24px;
        }
        .hero-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,.18); border-radius: 20px; padding: 4px 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 12px; }
        .hero h1 { font-size: 22px; font-weight: 700; line-height: 1.35; margin-bottom: 10px; }
        .hero-meta { display: flex; flex-wrap: wrap; gap: 10px 20px; font-size: 12.5px; opacity: .88; margin-top: 6px; }
        .hero-meta span { display: flex; align-items: center; gap: 6px; }

        /* Status badges */
        .badge-aktif    { background: rgba(255,255,255,.25); color: #fff; }
        .badge-selesai  { background: rgba(99,179,237,.3); color: #fff; }
        .badge-batal    { background: rgba(220,38,38,.3); color: #fff; }

        /* Stats row */
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 16px; background: #fff; border-bottom: 1px solid #e8ecf0; }
        .stat-item { text-align: center; padding: 10px 6px; }
        .stat-item .val { font-size: 20px; font-weight: 700; color: #1a7f4b; }
        .stat-item .lbl { font-size: 11px; color: #6b7a8d; margin-top: 2px; }

        /* Section */
        .section { background: #fff; margin: 12px 0; }
        .section-head { padding: 14px 16px 12px; border-bottom: 1px solid #f0f3f6; display: flex; align-items: center; gap: 8px; }
        .section-head h2 { font-size: 14px; font-weight: 700; color: #0f2419; }
        .section-head .icon { width: 28px; height: 28px; background: #e8f5ee; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #1a7f4b; font-size: 13px; flex-shrink: 0; }
        .section-body { padding: 14px 16px; }

        /* Info rows */
        .info-row { display: flex; gap: 10px; padding: 7px 0; border-bottom: 1px solid #f5f7f9; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .key { color: #6b7a8d; width: 90px; flex-shrink: 0; }
        .info-row .val { font-weight: 500; flex: 1; }
        .uraian { font-size: 13px; line-height: 1.75; color: #2d3a45; white-space: pre-line; background: #f8fafc; border-left: 3px solid #1a7f4b; border-radius: 0 6px 6px 0; padding: 10px 12px; margin-top: 8px; }

        /* Map */
        #mapPublic { height: 220px; border-radius: 8px; overflow: hidden; border: 1px solid #e8ecf0; }

        /* Foto grid */
        .foto-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .foto-item { position: relative; aspect-ratio: 4/3; border-radius: 8px; overflow: hidden; background: #f0f3f6; cursor: pointer; }
        .foto-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .foto-ket { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,.65)); color: #fff; font-size: 11px; padding: 16px 8px 6px; }
        .empty-state { text-align: center; padding: 32px; color: #adb5bd; font-size: 13px; }
        .empty-state svg { display: block; margin: 0 auto 10px; opacity: .3; }

        /* Peserta table */
        .tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { background: #f8fafc; padding: 9px 10px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6b7a8d; letter-spacing: .4px; border-bottom: 1px solid #e8ecf0; white-space: nowrap; }
        tbody td { padding: 9px 10px; border-bottom: 1px solid #f5f7f9; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        .no-circle { width: 26px; height: 26px; background: #e8f5ee; color: #1a7f4b; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; }

        /* Lightbox */
        .lb { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.92); z-index: 9999; align-items: center; justify-content: center; flex-direction: column; }
        .lb.open { display: flex; }
        .lb img { max-width: 92vw; max-height: 72vh; object-fit: contain; border-radius: 6px; }
        .lb-cap { color: #fff; font-size: 13px; margin-top: 10px; text-align: center; padding: 0 20px; }
        .lb-close { position: absolute; top: 14px; right: 16px; color: #fff; font-size: 22px; background: rgba(255,255,255,.12); border: none; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .lb-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,.15); border: none; color: #fff; font-size: 20px; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .lb-prev { left: 12px; }
        .lb-next { right: 12px; }

        /* Footer */
        .footer { padding: 20px 16px; text-align: center; font-size: 11px; color: #adb5bd; border-top: 1px solid #e8ecf0; background: #fff; }

        @media (min-width: 480px) {
            .foto-grid { grid-template-columns: repeat(3, 1fr); }
            .hero h1 { font-size: 24px; }
        }
        @media (min-width: 640px) {
            .hero { padding: 36px 28px 30px; }
            .section-body { padding: 16px 20px; }
            .section-head { padding: 16px 20px 14px; }
        }
    </style>
</head>
<body>

{{-- Hero Header --}}
<div class="hero">
    @php
        $badgeClass = match($kegiatan->status) {
            'aktif'   => 'badge-aktif',
            'selesai' => 'badge-selesai',
            'batal'   => 'badge-batal',
            default   => 'badge-aktif',
        };
        $statusLabel = match($kegiatan->status) {
            'aktif'   => '&#9679; Sedang Berlangsung',
            'selesai' => '&#10003; Selesai',
            'batal'   => '&#215; Dibatalkan',
            default   => ucfirst($kegiatan->status),
        };
    @endphp
    <div class="hero-badge {{ $badgeClass }}">{!! $statusLabel !!}</div>
    <h1>{{ $kegiatan->nama_kegiatan }}</h1>
    <div class="hero-meta">
        <span>&#128205; {{ $kegiatan->lokasi }}</span>
        <span>&#128197; {{ $kegiatan->tanggal_mulai->isoFormat('D MMMM Y') }}</span>
        @if($kegiatan->tanggal_selesai && $kegiatan->tanggal_selesai->ne($kegiatan->tanggal_mulai))
        <span>&#8594; {{ $kegiatan->tanggal_selesai->isoFormat('D MMMM Y') }}</span>
        @endif
    </div>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-item">
        <div class="val">{{ $peserta->count() }}</div>
        <div class="lbl">Peserta</div>
    </div>
    <div class="stat-item">
        <div class="val">{{ $fotos->count() }}</div>
        <div class="lbl">Foto</div>
    </div>
    <div class="stat-item">
        <div class="val">{{ $kegiatan->tanggal_mulai->diffInDays($kegiatan->tanggal_selesai ?? $kegiatan->tanggal_mulai) + 1 }}</div>
        <div class="lbl">Hari</div>
    </div>
</div>

{{-- Info Kegiatan --}}
<div class="section" style="margin-top:12px;">
    <div class="section-head">
        <div class="icon">&#8505;</div>
        <h2>Info Kegiatan</h2>
    </div>
    <div class="section-body">
        <div class="info-row">
            <span class="key">Tempat</span>
            <span class="val">{{ $kegiatan->lokasi }}</span>
        </div>
        <div class="info-row">
            <span class="key">Tanggal</span>
            <span class="val">
                {{ $kegiatan->tanggal_mulai->isoFormat('dddd, D MMMM Y') }}
                @if($kegiatan->tanggal_selesai && $kegiatan->tanggal_selesai->ne($kegiatan->tanggal_mulai))
                    &ndash; {{ $kegiatan->tanggal_selesai->isoFormat('D MMMM Y') }}
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="key">Status</span>
            <span class="val">{{ ucfirst($kegiatan->status) }}</span>
        </div>
        @if($kegiatan->latitude && $kegiatan->longitude)
        <div class="info-row">
            <span class="key">Koordinat</span>
            <span class="val">
                {{ $kegiatan->latitude }}, {{ $kegiatan->longitude }}
                &nbsp;<a href="https://www.google.com/maps?q={{ $kegiatan->latitude }},{{ $kegiatan->longitude }}"
                    target="_blank" style="color:#1a7f4b; font-size:12px;">&#8599; Google Maps</a>
            </span>
        </div>
        @endif
        @if($kegiatan->uraian_kegiatan)
        <div style="margin-top:10px;">
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; color:#6b7a8d; letter-spacing:.5px; margin-bottom:6px;">Uraian Kegiatan</div>
            <div class="uraian">{{ $kegiatan->uraian_kegiatan }}</div>
        </div>
        @endif
    </div>
</div>

{{-- Peta Lokasi --}}
@if($kegiatan->latitude && $kegiatan->longitude)
<div class="section">
    <div class="section-head">
        <div class="icon">&#128205;</div>
        <h2>Titik Lokasi</h2>
    </div>
    <div class="section-body" style="padding-bottom:16px;">
        <div id="mapPublic"></div>
        <a href="https://www.google.com/maps?q={{ $kegiatan->latitude }},{{ $kegiatan->longitude }}"
           target="_blank"
           style="display:block; margin-top:10px; text-align:center; font-size:13px; color:#1a7f4b; font-weight:600; text-decoration:none;">
            &#128279; Buka di Google Maps
        </a>
    </div>
</div>
@endif

{{-- Foto Dokumentasi --}}
<div class="section">
    <div class="section-head">
        <div class="icon">&#128247;</div>
        <h2>Foto Dokumentasi <span style="font-weight:400; color:#6b7a8d; font-size:12px;">({{ $fotos->count() }})</span></h2>
    </div>
    <div class="section-body">
        @if($fotos->count() > 0)
        <div class="foto-grid">
            @foreach($fotos as $i => $foto)
            <div class="foto-item" onclick="bukaLb({{ $i }})">
                <img src="{{ asset($foto->file_path) }}" alt="Foto {{ $foto->urutan }}" loading="lazy">
                @if($foto->keterangan)
                <div class="foto-ket">{{ $foto->keterangan }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
            Belum ada foto dokumentasi.
        </div>
        @endif
    </div>
</div>

{{-- Daftar Peserta --}}
<div class="section">
    <div class="section-head">
        <div class="icon">&#128101;</div>
        <h2>Daftar Peserta <span style="font-weight:400; color:#6b7a8d; font-size:12px;">({{ $peserta->count() }})</span></h2>
    </div>
    <div class="tbl-wrap">
        @if($peserta->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width:36px;">No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Instansi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peserta as $i => $p)
                <tr>
                    <td><div class="no-circle">{{ $i + 1 }}</div></td>
                    <td><strong>{{ $p->nama_lengkap }}</strong></td>
                    <td style="color:#6b7a8d;">{{ $p->jabatan ?? '-' }}</td>
                    <td style="color:#6b7a8d;">{{ $p->asal_instansi ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state" style="padding:28px 16px;">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#adb5bd" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Belum ada peserta terdaftar.
        </div>
        @endif
    </div>
</div>

<div class="footer">
    Informasi kegiatan ini dipublikasikan oleh KTH App &nbsp;&middot;&nbsp; {{ now()->format('d/m/Y') }}
</div>

{{-- Lightbox --}}
<div class="lb" id="lb" onclick="tutupLb()">
    <button class="lb-close" onclick="tutupLb()">&#215;</button>
    <button class="lb-nav lb-prev" onclick="event.stopPropagation(); navLb(-1)">&#8249;</button>
    <img id="lbImg" src="" onclick="event.stopPropagation()">
    <div class="lb-cap" id="lbCap"></div>
    <button class="lb-nav lb-next" onclick="event.stopPropagation(); navLb(1)">&#8250;</button>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ── Peta ────────────────────────────────────────────────
    @if($kegiatan->latitude && $kegiatan->longitude)
    const map = L.map('mapPublic', { zoomControl: true, scrollWheelZoom: false })
                 .setView([{{ $kegiatan->latitude }}, {{ $kegiatan->longitude }}], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);
    L.marker([{{ $kegiatan->latitude }}, {{ $kegiatan->longitude }}])
        .addTo(map)
        .bindPopup('<strong>{{ addslashes($kegiatan->nama_kegiatan) }}</strong><br>{{ addslashes($kegiatan->lokasi) }}')
        .openPopup();
    @endif

    // ── Lightbox ─────────────────────────────────────────────
    const fotosLb = @json($fotos->map(fn($f) => ['src' => asset($f->file_path), 'ket' => $f->keterangan]));
    let lbIdx = 0;

    function bukaLb(idx) {
        if (!fotosLb.length) return;
        lbIdx = idx;
        document.getElementById('lbImg').src = fotosLb[idx].src;
        document.getElementById('lbCap').textContent = fotosLb[idx].ket ?? '';
        document.getElementById('lb').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function tutupLb() {
        document.getElementById('lb').classList.remove('open');
        document.body.style.overflow = '';
    }
    function navLb(dir) {
        lbIdx = (lbIdx + dir + fotosLb.length) % fotosLb.length;
        document.getElementById('lbImg').src = fotosLb[lbIdx].src;
        document.getElementById('lbCap').textContent = fotosLb[lbIdx].ket ?? '';
    }
    document.addEventListener('keydown', e => {
        if (!document.getElementById('lb').classList.contains('open')) return;
        if (e.key === 'ArrowLeft')  navLb(-1);
        if (e.key === 'ArrowRight') navLb(1);
        if (e.key === 'Escape')     tutupLb();
    });
</script>
</body>
</html>
