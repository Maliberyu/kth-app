@extends('layouts.app')
@section('title', $kegiatanLuar->nama_kegiatan)
@section('page_title', $kegiatanLuar->nama_kegiatan)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #mapShow { height:220px; border-radius:10px; border:1px solid #e8ecf0; z-index:1; }

    /* Gallery */
    .foto-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px; }
    .foto-item {
        position:relative; border-radius:10px; overflow:hidden;
        aspect-ratio:4/3; background:#f0f3f6; cursor:pointer;
        border:1px solid #e8ecf0; transition:transform .2s, box-shadow .2s;
    }
    .foto-item:hover { transform:scale(1.02); box-shadow:0 4px 16px rgba(0,0,0,.12); }
    .foto-item img { width:100%; height:100%; object-fit:cover; display:block; }
    .foto-item .foto-overlay {
        position:absolute; inset:0; background:rgba(0,0,0,0);
        display:flex; align-items:center; justify-content:center;
        transition:background .2s;
    }
    .foto-item:hover .foto-overlay { background:rgba(0,0,0,.3); }
    .foto-item .foto-actions {
        display:none; gap:6px;
    }
    .foto-item:hover .foto-actions { display:flex; }
    .foto-item .keterangan-badge {
        position:absolute; bottom:0; left:0; right:0;
        background:linear-gradient(transparent, rgba(0,0,0,.6));
        color:#fff; font-size:11px; padding:16px 8px 6px;
    }
    .foto-add-btn {
        border:2px dashed #d1d9e0; border-radius:10px;
        aspect-ratio:4/3; display:flex; flex-direction:column;
        align-items:center; justify-content:center;
        cursor:pointer; background:#fafbfc; transition:all .2s;
        color:#6b7a8d; font-size:13px; gap:6px;
    }
    .foto-add-btn:hover { border-color:var(--primary); background:var(--primary-light); color:var(--primary); }

    /* Lightbox */
    .lightbox {
        display:none; position:fixed; inset:0; background:rgba(0,0,0,.9);
        z-index:9999; align-items:center; justify-content:center; flex-direction:column;
    }
    .lightbox.show { display:flex; }
    .lightbox img { max-width:90vw; max-height:75vh; border-radius:8px; object-fit:contain; }
    .lightbox-caption { color:#fff; font-size:14px; margin-top:12px; text-align:center; }
    .lightbox-close {
        position:absolute; top:16px; right:20px; color:#fff; font-size:24px;
        cursor:pointer; background:none; border:none; line-height:1;
    }
    .lightbox-nav {
        position:absolute; top:50%; transform:translateY(-50%);
        color:#fff; font-size:28px; cursor:pointer;
        background:rgba(255,255,255,.1); border:none; border-radius:50%;
        width:44px; height:44px; display:flex; align-items:center; justify-content:center;
        transition:background .2s;
    }
    .lightbox-nav:hover { background:rgba(255,255,255,.25); }
    .lightbox-prev { left:16px; }
    .lightbox-next { right:16px; }

    /* Peserta form panel */
    .add-peserta-panel { display:none; background:#f8fafc; border-top:1px solid #e8ecf0; padding:20px; }
    .add-peserta-panel.show { display:block; }
    .hidden-panel { display:none !important; }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div style="margin-bottom:16px; font-size:13px; color:#6b7a8d;">
    <a href="{{ route('kegiatan-luar.index') }}" style="color:var(--primary); text-decoration:none;">Kegiatan Luar Ruangan</a>
    <span style="margin:0 6px;">/</span>
    {{ $kegiatanLuar->nama_kegiatan }}
</div>

{{-- Statistik --}}
<div class="grid grid-3" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-images"></i></div>
        <p class="stat-value">{{ $fotos->count() }}/5</p>
        <p class="stat-label">Foto Dokumentasi</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-users"></i></div>
        <p class="stat-value">{{ $peserta->count() }}</p>
        <p class="stat-label">Peserta Kegiatan</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-calendar-check"></i></div>
        <p class="stat-value">{{ $kegiatanLuar->tanggal_mulai->format('d/m/Y') }}</p>
        <p class="stat-label">Tanggal Kegiatan</p>
    </div>
</div>

{{-- Row 1: Info + Peta --}}
<div class="grid grid-2" style="margin-bottom:20px; align-items:start;">

    {{-- Info Kegiatan --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle" style="color:#1a7f4b;margin-right:8px;"></i>Info Kegiatan</h3>
            <div style="display:flex; gap:6px;">
                @php $badge = match($kegiatanLuar->status) { 'aktif'=>'badge-success','selesai'=>'badge-info','batal'=>'badge-danger',default=>'badge-gray' }; @endphp
                <span class="badge {{ $badge }}">{{ ucfirst($kegiatanLuar->status) }}</span>
                <a href="{{ route('kegiatan-luar.edit', $kegiatanLuar) }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-pen"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body" style="font-size:13.5px; display:flex; flex-direction:column; gap:10px;">
            <div style="display:flex; gap:10px;">
                <span style="color:#6b7a8d; width:100px; flex-shrink:0;">Tempat</span>
                <strong>{{ $kegiatanLuar->lokasi }}</strong>
            </div>
            <div style="display:flex; gap:10px;">
                <span style="color:#6b7a8d; width:100px; flex-shrink:0;">Mulai</span>
                <strong>{{ $kegiatanLuar->tanggal_mulai->isoFormat('dddd, D MMMM Y — HH:mm') }}</strong>
            </div>
            @if($kegiatanLuar->tanggal_selesai)
            <div style="display:flex; gap:10px;">
                <span style="color:#6b7a8d; width:100px; flex-shrink:0;">Selesai</span>
                <strong>{{ $kegiatanLuar->tanggal_selesai->isoFormat('dddd, D MMMM Y — HH:mm') }}</strong>
            </div>
            @endif
            @if($kegiatanLuar->latitude && $kegiatanLuar->longitude)
            <div style="display:flex; gap:10px; align-items:center;">
                <span style="color:#6b7a8d; width:100px; flex-shrink:0;">Koordinat</span>
                <div>
                    <code style="font-size:12px; background:#f0f3f6; padding:2px 6px; border-radius:4px;">
                        {{ $kegiatanLuar->latitude }}, {{ $kegiatanLuar->longitude }}
                    </code>
                    <a href="https://www.google.com/maps?q={{ $kegiatanLuar->latitude }},{{ $kegiatanLuar->longitude }}"
                       target="_blank" style="color:var(--primary); font-size:12px; margin-left:6px;">
                        <i class="fas fa-external-link-alt"></i> Google Maps
                    </a>
                </div>
            </div>
            @endif
            @if($kegiatanLuar->uraian_kegiatan)
            <div style="border-top:1px solid #f0f3f6; padding-top:12px; margin-top:4px;">
                <div style="color:#6b7a8d; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px;">Uraian Kegiatan</div>
                <div style="font-size:13.5px; line-height:1.7; white-space:pre-wrap;">{{ $kegiatanLuar->uraian_kegiatan }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Peta --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-map-location-dot" style="color:#1a7f4b;margin-right:8px;"></i>Titik Lokasi</h3>
            @if($kegiatanLuar->latitude && $kegiatanLuar->longitude)
            <a href="https://www.google.com/maps?q={{ $kegiatanLuar->latitude }},{{ $kegiatanLuar->longitude }}"
               target="_blank" class="btn btn-outline btn-sm">
                <i class="fas fa-external-link-alt"></i> Buka Maps
            </a>
            @endif
        </div>
        <div class="card-body">
            @if($kegiatanLuar->latitude && $kegiatanLuar->longitude)
                <div id="mapShow"></div>
            @else
                <div style="text-align:center; padding:40px; color:#adb5bd;">
                    <i class="fas fa-map" style="font-size:36px; display:block; margin-bottom:10px; opacity:.3;"></i>
                    Titik koordinat belum diset.
                    <br><a href="{{ route('kegiatan-luar.edit', $kegiatanLuar) }}" style="color:var(--primary); font-size:13px;">Set Koordinat</a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Foto Dokumentasi --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3><i class="fas fa-images" style="color:#1a7f4b;margin-right:8px;"></i>
            Foto Dokumentasi
            <span style="font-weight:400; color:#6b7a8d; font-size:12px;">({{ $fotos->count() }}/5)</span>
        </h3>
    </div>
    <div class="card-body">
        @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        <div class="foto-grid">
            {{-- Foto existing --}}
            @foreach($fotos as $foto)
            <div class="foto-item" onclick="bukaLightbox({{ $loop->index }})">
                <img src="{{ asset($foto->file_path) }}" alt="Foto {{ $foto->urutan }}">
                <div class="foto-overlay">
                    <div class="foto-actions">
                        <button onclick="event.stopPropagation(); bukaLightbox({{ $loop->index }})"
                                style="background:rgba(255,255,255,.9); border:none; border-radius:6px; padding:6px 10px; cursor:pointer; font-size:12px;">
                            <i class="fas fa-expand"></i>
                        </button>
                        <form method="POST" action="{{ route('kegiatan-luar.hapus-foto', [$kegiatanLuar, $foto]) }}"
                              onsubmit="event.stopPropagation(); return confirm('Hapus foto ini?')">
                            @csrf @method('DELETE')
                            <button style="background:rgba(220,38,38,.9); color:#fff; border:none; border-radius:6px; padding:6px 10px; cursor:pointer; font-size:12px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @if($foto->keterangan)
                <div class="keterangan-badge">{{ $foto->keterangan }}</div>
                @endif
            </div>
            @endforeach

            {{-- Tombol tambah foto --}}
            @if($fotos->count() < 5)
            <div class="foto-add-btn" onclick="document.getElementById('panelUploadFoto').classList.toggle('hidden-panel')">
                <i class="fas fa-plus-circle" style="font-size:24px;"></i>
                <span>Tambah Foto</span>
                <span style="font-size:11px;">{{ 5 - $fotos->count() }} slot tersisa</span>
            </div>
            @endif
        </div>

        {{-- Panel Upload --}}
        <div id="panelUploadFoto" class="hidden-panel"
             style="margin-top:16px; background:#f8fafc; border:1px solid #e8ecf0; border-radius:10px; padding:16px;">
            @error('foto')
            <div class="alert alert-error" style="margin-bottom:10px;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
            @enderror
            <form method="POST" action="{{ route('kegiatan-luar.upload-foto', $kegiatanLuar) }}" enctype="multipart/form-data">
                @csrf
                <div style="display:grid; grid-template-columns:1fr auto; gap:10px; align-items:end; flex-wrap:wrap;">
                    <div>
                        <div class="form-group" style="margin-bottom:10px;">
                            <label class="form-label">Pilih Foto</label>
                            <div id="dropzoneFoto" onclick="document.getElementById('fotoFileInput').click()"
                                 style="border:2px dashed #d1d9e0; border-radius:8px; padding:12px 16px; cursor:pointer;
                                        display:flex; align-items:center; gap:10px; background:#fff; transition:border-color .2s;">
                                <i class="fas fa-cloud-upload-alt" style="color:#adb5bd; font-size:18px;"></i>
                                <span id="fotoLabel" style="font-size:13px; color:#6b7a8d;">JPG, PNG, WebP — maks 3MB</span>
                            </div>
                            <input type="file" id="fotoFileInput" name="foto" accept="image/*"
                                   style="display:none" onchange="onFotoSelected(this)" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Keterangan Foto</label>
                            <input type="text" name="keterangan" class="form-control"
                                   placeholder="Deskripsi singkat foto (opsional)">
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <button type="submit" class="btn btn-primary" id="btnUploadFoto" disabled>
                            <i class="fas fa-upload"></i> Upload
                        </button>
                        <button type="button" onclick="document.getElementById('panelUploadFoto').classList.add('hidden-panel')"
                                class="btn btn-outline">Batal</button>
                    </div>
                </div>
                {{-- Preview --}}
                <div id="previewWrap" style="display:none; margin-top:10px;">
                    <img id="previewMini" src="" style="max-height:80px; border-radius:6px; border:1px solid #e8ecf0;">
                    <span id="previewName" style="font-size:12px; color:#6b7a8d; margin-left:8px;"></span>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Peserta Kegiatan --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users" style="color:#1a7f4b;margin-right:8px;"></i>
            Peserta Kegiatan <span style="font-weight:400; color:#6b7a8d;">({{ $peserta->count() }})</span>
        </h3>
        <div style="display:flex; gap:6px;">
            <a href="{{ route('kegiatan-luar.export-excel', $kegiatanLuar) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-file-csv"></i> Export
            </a>
            <button onclick="document.getElementById('panelTambahPeserta').classList.toggle('show')"
                    class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Tambah
            </button>
        </div>
    </div>

    {{-- Form Tambah Peserta --}}
    <div id="panelTambahPeserta" class="add-peserta-panel">
        @error('nama_lengkap') <div class="alert alert-error" style="margin-bottom:10px;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
        @error('no_hp')        <div class="alert alert-error" style="margin-bottom:10px;"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
        <form method="POST" action="{{ route('kegiatan-luar.add-peserta', $kegiatanLuar) }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:12px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Nama Lengkap <span style="color:#d93025;">*</span></label>
                    <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" placeholder="Nama peserta">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">No HP <span style="color:#d93025;">*</span></label>
                    <input type="tel" name="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="08xxx">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}" placeholder="Opsional">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Asal Instansi</label>
                    <input type="text" name="asal_instansi" class="form-control" value="{{ old('asal_instansi') }}" placeholder="Opsional">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Opsional">
                </div>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Peserta</button>
                <button type="button" onclick="document.getElementById('panelTambahPeserta').classList.remove('show')"
                        class="btn btn-outline btn-sm">Batal</button>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:32px;">No</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan</th>
                    <th>Asal Instansi</th>
                    <th>No HP</th>
                    <th>Email</th>
                    <th style="text-align:center; width:60px;">Hapus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peserta as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $p->nama_lengkap }}</strong></td>
                    <td>{{ $p->jabatan ?? '-' }}</td>
                    <td>{{ $p->asal_instansi ?? '-' }}</td>
                    <td style="font-size:12px;">{{ $p->no_hp }}</td>
                    <td style="font-size:12px;">{{ $p->email ?? '-' }}</td>
                    <td style="text-align:center;">
                        <form method="POST" action="{{ route('kegiatan-luar.remove-peserta', [$kegiatanLuar, $p]) }}"
                              onsubmit="return confirm('Hapus peserta ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm btn-icon">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#adb5bd; padding:32px;">
                        Belum ada peserta. Klik <strong>Tambah</strong> untuk menambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Lightbox --}}
<div class="lightbox" id="lightbox" onclick="tutupLightbox()">
    <button class="lightbox-close" onclick="tutupLightbox()"><i class="fas fa-times"></i></button>
    <button class="lightbox-nav lightbox-prev" onclick="event.stopPropagation(); navLightbox(-1)"><i class="fas fa-chevron-left"></i></button>
    <img id="lightboxImg" src="" onclick="event.stopPropagation()">
    <div class="lightbox-caption" id="lightboxCaption"></div>
    <button class="lightbox-nav lightbox-next" onclick="event.stopPropagation(); navLightbox(1)"><i class="fas fa-chevron-right"></i></button>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ── Peta ──────────────────────────────────────────
    @if($kegiatanLuar->latitude && $kegiatanLuar->longitude)
    const mapShow = L.map('mapShow').setView([{{ $kegiatanLuar->latitude }}, {{ $kegiatanLuar->longitude }}], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'© OpenStreetMap' }).addTo(mapShow);
    L.marker([{{ $kegiatanLuar->latitude }}, {{ $kegiatanLuar->longitude }}])
        .addTo(mapShow)
        .bindPopup('<strong>{{ addslashes($kegiatanLuar->nama_kegiatan) }}</strong><br>{{ addslashes($kegiatanLuar->lokasi) }}')
        .openPopup();
    @endif

    // ── Upload Foto ──────────────────────────────────
    @if($errors->has('foto'))
    document.getElementById('panelUploadFoto')?.classList.remove('hidden-panel');
    @endif

    @if(session('error') && str_contains(session('error',''), 'foto'))
    document.getElementById('panelUploadFoto')?.classList.remove('hidden-panel');
    @endif

    function onFotoSelected(input) {
        const btn   = document.getElementById('btnUploadFoto');
        const label = document.getElementById('fotoLabel');
        const wrap  = document.getElementById('previewWrap');
        const mini  = document.getElementById('previewMini');
        const name  = document.getElementById('previewName');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            label.textContent = file.name;
            name.textContent  = (file.size / 1024).toFixed(0) + ' KB';
            btn.disabled = false;
            document.getElementById('dropzoneFoto').style.borderColor = 'var(--primary)';
            const reader = new FileReader();
            reader.onload = e => { mini.src = e.target.result; wrap.style.display = 'block'; };
            reader.readAsDataURL(file);
        }
    }

    // ── Peserta panel: buka jika ada error validasi ──
    @if($errors->has('nama_lengkap') || $errors->has('no_hp'))
    document.getElementById('panelTambahPeserta')?.classList.add('show');
    @endif

    // ── Lightbox ─────────────────────────────────────
    const fotosData = @json($fotos->map(fn($f) => ['src' => asset($f->file_path), 'ket' => $f->keterangan]));
    let currentIdx = 0;

    function bukaLightbox(idx) {
        if (!fotosData.length) return;
        currentIdx = idx;
        document.getElementById('lightboxImg').src    = fotosData[idx].src;
        document.getElementById('lightboxCaption').textContent = fotosData[idx].ket ?? '';
        document.getElementById('lightbox').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function tutupLightbox() {
        document.getElementById('lightbox').classList.remove('show');
        document.body.style.overflow = '';
    }

    function navLightbox(dir) {
        currentIdx = (currentIdx + dir + fotosData.length) % fotosData.length;
        document.getElementById('lightboxImg').src    = fotosData[currentIdx].src;
        document.getElementById('lightboxCaption').textContent = fotosData[currentIdx].ket ?? '';
    }

    document.addEventListener('keydown', e => {
        if (!document.getElementById('lightbox').classList.contains('show')) return;
        if (e.key === 'ArrowLeft')  navLightbox(-1);
        if (e.key === 'ArrowRight') navLightbox(1);
        if (e.key === 'Escape')     tutupLightbox();
    });
</script>
@endpush
