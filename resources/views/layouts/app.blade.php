<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KTH Management')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        :root {
            --primary: #1a7f4b;
            --primary-dark: #145f38;
            --primary-light: #e8f5ee;
            --accent: #f0a500;
            --sidebar-w: 260px;
        }
        body { background: #f4f6f8; color: #1e2a35; }

        /* SIDEBAR */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: #0f2419;
            display: flex; flex-direction: column;
            z-index: 100; transition: transform .3s;
        }
        .sidebar-logo {
            padding: 24px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-logo h1 {
            color: #fff; font-size: 18px; font-weight: 700; margin: 0;
        }
        .sidebar-logo span { color: var(--accent); }
        .sidebar-logo p { color: rgba(255,255,255,.4); font-size: 11px; margin: 2px 0 0; }
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 12px 0; }
        .nav-label {
            color: rgba(255,255,255,.3); font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 16px 20px 6px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px; color: rgba(255,255,255,.65);
            text-decoration: none; font-size: 13.5px; font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .2s;
        }
        .nav-item:hover, .nav-item.active {
            background: rgba(255,255,255,.06);
            color: #fff; border-left-color: var(--accent);
        }
        .nav-item i { width: 18px; text-align: center; font-size: 14px; }
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; flex-shrink: 0;
        }
        .user-name { color: #fff; font-size: 13px; font-weight: 600; }
        .user-role { color: rgba(255,255,255,.4); font-size: 11px; }
        .logout-btn {
            margin-left: auto; color: rgba(255,255,255,.4);
            background: none; border: none; cursor: pointer; font-size: 14px;
            transition: color .2s;
        }
        .logout-btn:hover { color: #ff6b6b; }

        /* MAIN */
        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e8ecf0;
            padding: 0 28px; height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 16px; font-weight: 600; color: #1e2a35; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .page-content { padding: 28px; }

        /* CARDS */
        .card {
            background: #fff; border-radius: 12px;
            border: 1px solid #e8ecf0; overflow: hidden;
        }
        .card-header {
            padding: 16px 20px; border-bottom: 1px solid #f0f3f6;
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-header h3 { font-size: 14px; font-weight: 600; margin: 0; }
        .card-body { padding: 20px; }

        /* STAT CARDS */
        .stat-card {
            background: #fff; border-radius: 12px;
            border: 1px solid #e8ecf0; padding: 20px;
        }
        .stat-card .stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; margin-bottom: 12px;
        }
        .stat-card .stat-value { font-size: 24px; font-weight: 700; margin: 0; }
        .stat-card .stat-label { font-size: 12px; color: #6b7a8d; margin: 4px 0 0; }
        .icon-green { background: var(--primary-light); color: var(--primary); }
        .icon-amber { background: #fff8e6; color: var(--accent); }
        .icon-blue  { background: #e8f0fe; color: #1a73e8; }
        .icon-red   { background: #fce8e6; color: #d93025; }

        /* TABLE */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead th {
            background: #f8fafc; padding: 10px 14px;
            text-align: left; font-weight: 600; color: #6b7a8d;
            font-size: 12px; text-transform: uppercase; letter-spacing: .5px;
            border-bottom: 1px solid #e8ecf0;
        }
        tbody td { padding: 12px 14px; border-bottom: 1px solid #f0f3f6; vertical-align: middle; }
        tbody tr:hover { background: #fafbfc; }
        tbody tr:last-child td { border-bottom: none; }

        /* BADGES */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .badge-success { background: #e8f5ee; color: var(--primary-dark); }
        .badge-warning { background: #fff8e6; color: #9a6800; }
        .badge-danger  { background: #fce8e6; color: #c5221f; }
        .badge-info    { background: #e8f0fe; color: #1a56db; }
        .badge-gray    { background: #f1f3f4; color: #5f6368; }

        /* BUTTONS */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 8px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            text-decoration: none; border: none; transition: all .2s;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); color: #fff; }
        .btn-outline {
            background: transparent; color: var(--primary);
            border: 1.5px solid var(--primary);
        }
        .btn-outline:hover { background: var(--primary-light); }
        .btn-danger { background: #fce8e6; color: #c5221f; border: 1.5px solid #f5c6c3; }
        .btn-danger:hover { background: #f5c6c3; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-icon { width: 32px; height: 32px; padding: 0; justify-content: center; border-radius: 8px; }

        /* FORMS */
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #374151; }
        .form-control {
            width: 100%; padding: 9px 12px;
            border: 1.5px solid #d1d9e0; border-radius: 8px;
            font-size: 14px; font-family: inherit;
            transition: border-color .2s;
            background: #fff; color: #1e2a35;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,127,75,.1);
        }
        .form-select { appearance: none; }

        /* ALERTS */
        .alert {
            padding: 12px 16px; border-radius: 8px;
            font-size: 13.5px; margin-bottom: 16px;
            display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #e8f5ee; color: #145f38; border: 1px solid #b7dfca; }
        .alert-error   { background: #fce8e6; color: #c5221f; border: 1px solid #f5c6c3; }

        /* PAGINATION */
        .pagination { display: flex; gap: 4px; align-items: center; }
        .page-link {
            padding: 6px 12px; border-radius: 6px; font-size: 13px;
            text-decoration: none; color: #374151;
            border: 1px solid #e8ecf0; background: #fff;
            transition: all .2s;
        }
        .page-link:hover, .page-link.active {
            background: var(--primary); color: #fff; border-color: var(--primary);
        }

        /* GRID */
        .grid { display: grid; gap: 16px; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }

        @media (max-width: 1024px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <h1>KTH <span>App</span></h1>
        <p>Kelompok Tani Hutan</p>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        @role('super_admin')
        <div class="nav-label">Master</div>
        <a href="{{ route('super.kth.index') }}" class="nav-item {{ request()->routeIs('super.kth.*') ? 'active' : '' }}">
            <i class="fas fa-sitemap"></i> Data KTH
        </a>
        @endrole

        @role('admin_kth')
        <div class="nav-label">Data Master</div>
        <a href="{{ route('penyadap.index') }}" class="nav-item {{ request()->routeIs('penyadap.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Penyadap
        </a>
        <a href="{{ route('blok.index') }}" class="nav-item {{ request()->routeIs('blok.*') ? 'active' : '' }}">
            <i class="fas fa-map"></i> Blok
        </a>

        <div class="nav-label">Produksi</div>
        <a href="{{ route('produksi.index') }}" class="nav-item {{ request()->routeIs('produksi.*') ? 'active' : '' }}">
            <i class="fas fa-droplet"></i> Produksi Getah
        </a>

        <div class="nav-label">Pengiriman</div>
        <a href="{{ route('surat-jalan.index') }}" class="nav-item {{ request()->routeIs('surat-jalan.*') ? 'active' : '' }}">
            <i class="fas fa-truck"></i> Surat Jalan
        </a>
        <a href="{{ route('penjualan.index') }}" class="nav-item {{ request()->routeIs('penjualan.*') ? 'active' : '' }}">
            <i class="fas fa-money-bill-wave"></i> Penjualan
        </a>

        <div class="nav-label">Inventaris</div>
        <a href="{{ route('inventaris.index') }}" class="nav-item {{ request()->routeIs('inventaris.*') ? 'active' : '' }}">
            <i class="fas fa-boxes-stacked"></i> Stok Barang
        </a>
        <a href="{{ route('inventaris.masuk') }}" class="nav-item">
            <i class="fas fa-arrow-down"></i> Barang Masuk
        </a>
        <a href="{{ route('inventaris.distribusi') }}" class="nav-item">
            <i class="fas fa-arrow-up"></i> Distribusi
        </a>
        @endrole

        @role('penyadap')
        <div class="nav-label">Saya</div>
        <a href="{{ route('saya.blok') }}" class="nav-item {{ request()->routeIs('saya.blok*') ? 'active' : '' }}">
            <i class="fas fa-map-location-dot"></i> Blok Saya
        </a>
        <a href="{{ route('saya.produksi') }}" class="nav-item {{ request()->routeIs('saya.produksi*') ? 'active' : '' }}">
            <i class="fas fa-droplet"></i> Produksi Saya
        </a>
        @endrole
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->nama }}</div>
                <div class="user-role">{{ ucfirst(str_replace('_',' ', auth()->user()->role)) }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<div class="main-wrap">
    <div class="topbar">
        <div class="topbar-title">@yield('page_title', 'Dashboard')</div>
        <div class="topbar-right">
            <span style="font-size:13px; color:#6b7a8d;">
                {{ now()->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </div>

    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>