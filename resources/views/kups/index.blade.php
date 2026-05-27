@extends('layouts.app')
@section('title', 'Master KUPS')
@section('page_title', 'Master KUPS')

@section('content')

@if(session('success'))
<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif

{{-- Header --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <div>
        <p style="color:#6b7a8d; font-size:13px; margin:0;">Kelola data KUPS dan komoditas yang dihasilkan.</p>
    </div>
    <a href="{{ route('kups.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah KUPS
    </a>
</div>

@forelse($kupsList as $kup)
<div class="card" style="margin-bottom:14px;">
    <div class="card-body" style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        <div style="width:48px; height:48px; background:var(--primary-light); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <i class="fas fa-layer-group" style="color:var(--primary); font-size:18px;"></i>
        </div>
        <div style="flex:1; min-width:160px;">
            <div style="font-size:15px; font-weight:700; color:#0f2419;">{{ $kup->nama_kups }}</div>
            @if($kup->deskripsi)
            <div style="font-size:12.5px; color:#6b7a8d; margin-top:2px;">{{ Str::limit($kup->deskripsi, 80) }}</div>
            @endif
            <div style="margin-top:6px; display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <span style="font-size:12px; background:#e8f5ee; color:#1a7f4b; padding:2px 10px; border-radius:20px; font-weight:600;">
                    {{ $kup->komoditas_count }} komoditas
                </span>
                @if($kup->status === 'nonaktif')
                <span class="badge badge-danger">Nonaktif</span>
                @else
                <span class="badge badge-success">Aktif</span>
                @endif
                <span style="font-size:11px; color:#adb5bd;">
                    Dibuat {{ $kup->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
        <div style="display:flex; gap:8px; flex-shrink:0;">
            <a href="{{ route('kups.show', $kup) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-eye"></i> Detail
            </a>
            <a href="{{ route('kups.edit', $kup) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-pen"></i>
            </a>
            <form method="POST" action="{{ route('kups.destroy', $kup) }}"
                  onsubmit="return confirm('Hapus KUPS {{ addslashes($kup->nama_kups) }} beserta semua komoditasnya?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm btn-icon"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body" style="text-align:center; padding:48px; color:#adb5bd;">
        <i class="fas fa-layer-group" style="font-size:40px; display:block; margin-bottom:12px; opacity:.3;"></i>
        Belum ada KUPS. Klik <strong>Tambah KUPS</strong> untuk memulai.
    </div>
</div>
@endforelse

@endsection
