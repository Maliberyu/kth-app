{{-- resources/views/periode/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Master Periode')
@section('page_title', 'Master Periode Penjualan')

@section('content')
<div class="grid grid-2">

    {{-- Form Tambah --}}
    <div class="card" style="align-self:start;">
        <div class="card-header">
            <h3><i class="fas fa-calendar-plus" style="color:#1a7f4b;margin-right:8px;"></i>Tambah Periode Baru</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('periode.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Periode <span style="color:red">*</span></label>
                    <input type="text" name="nama_periode" class="form-control"
                           value="{{ old('nama_periode') }}"
                           placeholder="Contoh: April 2026" required>
                    @error('nama_periode')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Tanggal Mulai <span style="color:red">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control"
                               value="{{ old('tanggal_mulai') }}" required>
                        @error('tanggal_mulai')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Selesai <span style="color:red">*</span></label>
                        <input type="date" name="tanggal_selesai" class="form-control"
                               value="{{ old('tanggal_selesai') }}" required>
                        @error('tanggal_selesai')<div style="color:red;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Auto generate bulan --}}
                <div style="margin-bottom:16px;">
                    <p style="font-size:12px;color:#6b7a8d;margin-bottom:8px;">Atau generate otomatis:</p>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        @php
                            $months = [];
                            for ($i = 0; $i < 6; $i++) {
                                $months[] = now()->subMonths($i);
                            }
                        @endphp
                        @foreach($months as $m)
                        <button type="button" onclick="fillPeriode('{{ $m->isoFormat('MMMM Y') }}', '{{ $m->startOfMonth()->format('Y-m-d') }}', '{{ $m->copy()->endOfMonth()->format('Y-m-d') }}')"
                                class="btn btn-outline btn-sm" style="font-size:11px;">
                            {{ $m->isoFormat('MMM Y') }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <i class="fas fa-save"></i> Simpan Periode
                </button>
            </form>
        </div>
    </div>

    {{-- Daftar Periode --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-calendar" style="color:#1a7f4b;margin-right:8px;"></i>Daftar Periode</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama Periode</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periode as $p)
                    <tr>
                        <td><strong>{{ $p->nama_periode }}</strong></td>
                        <td>{{ $p->tanggal_mulai->format('d/m/Y') }}</td>
                        <td>{{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('periode.destroy', $p) }}"
                                  onsubmit="return confirm('Hapus periode ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:#adb5bd;padding:32px;">
                            <i class="fas fa-calendar" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            Belum ada periode. Tambah di form kiri.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($periode->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f0f3f6;">
            {{ $periode->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function fillPeriode(nama, mulai, selesai) {
    document.querySelector('[name=nama_periode]').value    = nama;
    document.querySelector('[name=tanggal_mulai]').value   = mulai;
    document.querySelector('[name=tanggal_selesai]').value = selesai;
}
</script>
@endpush