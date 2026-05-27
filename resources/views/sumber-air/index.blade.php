@extends('layouts.app')
@section('title', 'Sumber Air')
@section('page_title', 'Sumber Air')

@section('content')

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- Breadcrumb --}}
<div style="margin-bottom:16px; font-size:13px; color:#6b7a8d;">
    Sumber Air
</div>

{{-- Stat cards --}}
<div class="grid grid-3" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon icon-blue"><i class="fas fa-tint"></i></div>
        <p class="stat-value">{{ $list->where('tipe','air_bersih')->count() }}</p>
        <p class="stat-label">Air Bersih</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-amber"><i class="fas fa-water"></i></div>
        <p class="stat-value">{{ $list->where('tipe','air_baku')->count() }}</p>
        <p class="stat-label">Air Baku</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
        <p class="stat-value">{{ $list->where('status','aktif')->count() }}</p>
        <p class="stat-label">Aktif</p>
    </div>
</div>

{{-- List --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-tint" style="color:#1a7f4b;margin-right:8px;"></i>
            Daftar Sumber Air
            <span style="font-weight:400;color:#6b7a8d;font-size:12px;">({{ $list->count() }})</span>
        </h3>
        <a href="{{ route('sumber-air.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>

    @if($list->isEmpty())
    <div style="padding:48px; text-align:center; color:#adb5bd;">
        <i class="fas fa-tint" style="font-size:40px;margin-bottom:12px;display:block;"></i>
        Belum ada sumber air. Klik <strong>Tambah</strong> untuk menambahkan.
    </div>
    @else
    <div style="padding:16px 20px; display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:16px;">
        @foreach($list as $s)
        @php
            $debitTerakhir = $s->pengukuran->first();
            $trend = $s->trend;
            $trendIcon = $trend === 'naik' ? '↑' : ($trend === 'turun' ? '↓' : '→');
            $trendColor = $trend === 'naik' ? '#1a7f4b' : ($trend === 'turun' ? '#d93025' : '#6b7a8d');
            $isBersih = $s->tipe === 'air_bersih';
        @endphp
        <div style="border:1px solid #e8ecf0;border-radius:10px;padding:16px;background:#fff;transition:box-shadow .2s;"
             onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
             onmouseout="this.style.boxShadow='none'">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                <div>
                    <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:4px;
                        background:{{ $isBersih ? '#dbeafe' : '#fef9c3' }};
                        color:{{ $isBersih ? '#1e40af' : '#854d0e' }};">
                        {{ $s->tipe_label }}
                    </span>
                    @if($s->status === 'tidak_aktif')
                    <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:4px;background:#fee2e2;color:#991b1b;margin-left:4px;">
                        Tidak Aktif
                    </span>
                    @endif
                </div>
                <div style="display:flex;gap:4px;">
                    <a href="{{ route('sumber-air.edit', $s) }}" class="btn btn-outline btn-sm btn-icon" title="Edit">
                        <i class="fas fa-pen"></i>
                    </a>
                    <form method="POST" action="{{ route('sumber-air.destroy', $s) }}"
                          onsubmit="return confirm('Hapus {{ addslashes($s->nama) }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm btn-icon"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>

            <h4 style="margin:0 0 8px;font-size:15px;color:#1a202c;">{{ $s->nama }}</h4>

            @if($debitTerakhir)
            <div style="margin-bottom:8px;">
                <span style="font-size:22px;font-weight:700;color:{{ $trendColor }};">
                    {{ $debitTerakhir->debit_format }}
                </span>
                <span style="font-size:16px;color:{{ $trendColor }};margin-left:4px;">{{ $trendIcon }}</span>
                <div style="font-size:11px;color:#6b7a8d;margin-top:2px;">
                    Terakhir diukur: {{ $debitTerakhir->tanggal->format('d/m/Y') }}
                    oleh {{ $debitTerakhir->nama_petugas }}
                </div>
            </div>
            @else
            <div style="color:#adb5bd;font-size:13px;margin-bottom:8px;font-style:italic;">
                Belum ada pengukuran
            </div>
            @endif

            <div style="font-size:12px;color:#4a5568;border-top:1px solid #f0f0f0;padding-top:8px;margin-top:8px;">
                @if($isBersih && $s->bakTampung)
                    @php $totalKk = $s->bakTampung->kampungLayanan->sum('jumlah_kk'); @endphp
                    <i class="fas fa-home" style="color:#1a56db;margin-right:4px;"></i>
                    {{ $s->bakTampung->kampungLayanan->count() }} kampung
                    @if($totalKk > 0) • {{ number_format($totalKk) }} KK @endif
                @elseif(!$isBersih)
                    <i class="fas fa-seedling" style="color:#1a7f4b;margin-right:4px;"></i>
                    {{ $s->lahanLayanan->count() }} lahan
                    ({{ $s->lahanLayanan->pluck('tipe_lahan')->unique()->implode(', ') ?: '-' }})
                @else
                    <i class="fas fa-minus-circle" style="color:#adb5bd;margin-right:4px;"></i>
                    Belum ada distribusi
                @endif
            </div>

            <a href="{{ route('sumber-air.show', $s) }}"
               class="btn btn-primary btn-sm" style="width:100%;margin-top:12px;text-align:center;">
                <i class="fas fa-eye"></i> Detail
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection
