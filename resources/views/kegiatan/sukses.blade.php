<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Check-in Berhasil — {{ $kegiatan->nama_kegiatan }}</title>
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
            width: 100%; max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            overflow: hidden; text-align: center;
        }
        .success-top {
            background: var(--primary-light);
            padding: 36px 24px 24px;
            border-bottom: 1px solid #c8e6d8;
        }
        .success-icon {
            width: 72px; height: 72px;
            background: var(--primary); color: #fff;
            border-radius: 50%; font-size: 30px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            animation: pop .4s ease;
        }
        @keyframes pop {
            0%   { transform: scale(.5); opacity: 0; }
            80%  { transform: scale(1.1); }
            100% { transform: scale(1);  opacity: 1; }
        }
        .success-top h1 { font-size: 22px; color: var(--primary-dark); margin-bottom: 4px; }
        .success-top p  { font-size: 13px; color: #4b7a62; }

        .info-box { padding: 24px; }
        .info-row {
            display: flex; gap: 12px; align-items: flex-start;
            text-align: left; margin-bottom: 14px;
        }
        .info-row .icon {
            width: 36px; height: 36px; border-radius: 8px;
            background: var(--primary-light); color: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
        }
        .info-row .label { font-size: 11px; color: #6b7a8d; margin-bottom: 2px; }
        .info-row .value { font-size: 14px; font-weight: 600; color: #1e2a35; }

        .already-badge {
            background: #fff8e6; border: 1px solid #f5d78a;
            border-radius: 8px; padding: 10px 14px;
            font-size: 13px; color: #9a6800;
            margin-bottom: 16px;
            display: flex; align-items: center; gap: 8px;
        }

        .divider { height: 1px; background: #f0f3f6; margin: 4px 0 16px; }

        .kegiatan-info {
            background: #f8fafc; border-radius: 10px;
            padding: 14px; margin-bottom: 20px; text-align: left;
            font-size: 13px;
        }
        .kegiatan-info strong { display: block; font-size: 14px; margin-bottom: 6px; }
        .kegiatan-info span { color: #6b7a8d; display: flex; align-items: center; gap: 6px; margin-top: 4px; }

        .btn-close {
            display: block; width: 100%;
            padding: 13px; background: var(--primary); color: #fff;
            border: none; border-radius: 10px;
            font-size: 15px; font-weight: 700; cursor: pointer;
            text-align: center; text-decoration: none;
            transition: background .2s;
        }
        .btn-close:hover { background: var(--primary-dark); }
    </style>
</head>
<body>

<div class="card">
    <div class="success-top">
        <div class="success-icon">
            @if($alreadyRegistered)
            <i class="fas fa-info"></i>
            @else
            <i class="fas fa-check"></i>
            @endif
        </div>

        @if($alreadyRegistered)
        <h1>Sudah Terdaftar</h1>
        <p>Nomor HP Anda sudah tercatat pada kegiatan ini.</p>
        @else
        <h1>Check-in Berhasil!</h1>
        <p>Kehadiran Anda telah berhasil dicatat.</p>
        @endif
    </div>

    <div class="info-box">

        @if($alreadyRegistered)
        <div class="already-badge">
            <i class="fas fa-exclamation-triangle"></i>
            Anda sudah melakukan check-in sebelumnya.
        </div>
        @endif

        <div class="info-row">
            <div class="icon"><i class="fas fa-user"></i></div>
            <div>
                <div class="label">Nama</div>
                <div class="value">{{ $peserta->nama_lengkap }}</div>
            </div>
        </div>

        @if($peserta->jabatan || $peserta->asal_instansi)
        <div class="info-row">
            <div class="icon"><i class="fas fa-building"></i></div>
            <div>
                @if($peserta->jabatan)
                <div class="label">Jabatan</div>
                <div class="value">{{ $peserta->jabatan }}</div>
                @endif
                @if($peserta->asal_instansi)
                <div class="label" style="margin-top:{{ $peserta->jabatan ? '6px' : '0' }};">Instansi</div>
                <div class="value">{{ $peserta->asal_instansi }}</div>
                @endif
            </div>
        </div>
        @endif

        <div class="info-row">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <div>
                <div class="label">Waktu Hadir</div>
                <div class="value">{{ $peserta->waktu_hadir->isoFormat('dddd, D MMMM Y — HH:mm') }}</div>
            </div>
        </div>

        <div class="divider"></div>

        <div class="kegiatan-info">
            <strong>{{ $kegiatan->nama_kegiatan }}</strong>
            <span><i class="fas fa-map-marker-alt"></i> {{ $kegiatan->lokasi }}</span>
            <span><i class="fas fa-calendar"></i> {{ $kegiatan->tanggal_mulai->format('d/m/Y H:i') }}</span>
        </div>

        <button class="btn-close" onclick="window.close(); history.back();">
            <i class="fas fa-times-circle" style="margin-right:6px;"></i> Tutup
        </button>
    </div>
</div>

</body>
</html>
