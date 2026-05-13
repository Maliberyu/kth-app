<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Check-in — {{ $kegiatan->nama_kegiatan }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --primary: #1a7f4b; --primary-dark: #145f38; --primary-light: #e8f5ee; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2419 0%, #1a7f4b 100%);
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .card {
            background: #fff; border-radius: 20px;
            width: 100%; max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            overflow: hidden;
        }
        .card-top {
            background: var(--primary); padding: 24px;
            color: #fff; text-align: center;
        }
        .card-top .icon {
            width: 56px; height: 56px; background: rgba(255,255,255,.15);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin: 0 auto 14px;
        }
        .card-top h1 { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
        .card-top p  { font-size: 13px; opacity: .8; }
        .card-meta {
            background: var(--primary-light); padding: 12px 20px;
            display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;
        }
        .card-meta span { font-size: 12px; color: var(--primary-dark); display: flex; align-items: center; gap: 5px; }
        .form-body { padding: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 6px;
        }
        .form-label .req { color: #d93025; margin-left: 2px; }
        .form-control {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid #d1d9e0; border-radius: 10px;
            font-size: 15px; font-family: inherit; color: #1e2a35;
            transition: border-color .2s;
            -webkit-appearance: none;
        }
        .form-control:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,127,75,.1);
        }
        .form-control.error { border-color: #d93025; }
        .error-msg { color: #d93025; font-size: 12px; margin-top: 4px; }
        .btn-submit {
            width: 100%; padding: 14px;
            background: var(--primary); color: #fff;
            border: none; border-radius: 10px;
            font-size: 16px; font-weight: 700;
            cursor: pointer; transition: background .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-submit:hover { background: var(--primary-dark); }
        .btn-submit:disabled { opacity: .7; cursor: not-allowed; }
        .form-note { font-size: 11.5px; color: #6b7a8d; text-align: center; margin-top: 12px; }
    </style>
</head>
<body>

<div class="card">
    <div class="card-top">
        <div class="icon"><i class="fas fa-qrcode"></i></div>
        <h1>{{ $kegiatan->nama_kegiatan }}</h1>
        <p>Silakan isi form untuk mencatat kehadiran Anda</p>
    </div>
    <div class="card-meta">
        <span><i class="fas fa-map-marker-alt"></i> {{ $kegiatan->lokasi }}</span>
        <span><i class="fas fa-calendar"></i> {{ $kegiatan->tanggal_mulai->format('d/m/Y H:i') }}</span>
    </div>

    <div class="form-body">

        @if($errors->any())
        <div style="background:#fce8e6; border:1px solid #f5c6c3; border-radius:8px; padding:12px 14px; margin-bottom:16px; font-size:13px; color:#c5221f;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('daftar.store', $kegiatan->qr_token) }}" id="regForm" novalidate>
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') error @enderror"
                       value="{{ old('nama_lengkap') }}"
                       placeholder="Masukkan nama lengkap Anda"
                       autocomplete="name" inputmode="text">
                @error('nama_lengkap')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nomor HP <span class="req">*</span></label>
                <input type="tel" name="no_hp" class="form-control @error('no_hp') error @enderror"
                       value="{{ old('no_hp') }}"
                       placeholder="Contoh: 08123456789"
                       autocomplete="tel" inputmode="tel">
                @error('no_hp')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Jabatan</label>
                <input type="text" name="jabatan" class="form-control @error('jabatan') error @enderror"
                       value="{{ old('jabatan') }}"
                       placeholder="Contoh: Ketua Kelompok (opsional)">
                @error('jabatan')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Asal Instansi</label>
                <input type="text" name="asal_instansi" class="form-control @error('asal_instansi') error @enderror"
                       value="{{ old('asal_instansi') }}"
                       placeholder="Contoh: Dinas Kehutanan Kab. X (opsional)">
                @error('asal_instansi')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control @error('email') error @enderror"
                       value="{{ old('email') }}"
                       placeholder="contoh@email.com (opsional)"
                       autocomplete="email" inputmode="email">
                @error('email')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-submit" id="btnSubmit">
                <i class="fas fa-check-circle"></i> Check-in Sekarang
            </button>
        </form>

        <p class="form-note">
            <i class="fas fa-lock" style="margin-right:3px;"></i>
            Data Anda hanya digunakan untuk keperluan absensi kegiatan ini.
        </p>
    </div>
</div>

<script>
    document.getElementById('regForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    });
</script>
</body>
</html>
