<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600">Selamat datang, {{ Auth::user()->nama ?? Auth::user()->username }}!</p>
            </div>
            @can('create produksi')
            <a href="{{ route('produksi.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-sage-500 hover:bg-sage-600 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Input Produksi
            </a>
            @endcan
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $stats = [
                    ['label'=>'Total Penyadap','value'=>\App\Models\Penyadap::count(),'icon'=>'M12 4.354a4 4 0 110 5.292','color'=>'bg-blue-100 text-blue-600'],
                    ['label'=>'Blok Aktif','value'=>\App\Models\Blok::count(),'icon'=>'M9 20l-5.447-2.724','color'=>'bg-green-100 text-green-600'],
                    ['label'=>'Stok Getah','value'=>number_format(\App\Models\StokGetah::sum('total_stok')?:0,2).' kg','icon'=>'M19 11H5','color'=>'bg-amber-100 text-amber-600'],
                    ['label'=>'Penjualan Bulan Ini','value'=>'Rp '.number_format(\App\Models\Penjualan::whereMonth('created_at', now()->month)->sum('total_penjualan')?:0,0,'.','.'),'icon'=>'M12 8c-1.657 0-3 .895-3 2','color'=>'bg-purple-100 text-purple-600'],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="bg-white p-6 rounded-xl border border-sage-200 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg {{ $stat['color'] }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ $stat['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stat['value'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Recent Activity & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Produksi -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-sage-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-sage-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Produksi Terbaru</h3>
                    <a href="{{ route('produksi.index') }}" class="text-sm text-sage-600 hover:text-sage-700">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-sage-50 text-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">Tanggal</th>
                                <th class="px-6 py-3 text-left font-medium">Penyadap</th>
                                <th class="px-6 py-3 text-left font-medium">Blok</th>
                                <th class="px-6 py-3 text-right font-medium">Berat (kg)</th>
                                <th class="px-6 py-3 text-center font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sage-100">
                            @forelse(\App\Models\ProduksiGetah::with(['penyadap','blok'])->latest()->limit(5)->get() as $p)
                            <tr class="hover:bg-sage-50">
                                <td class="px-6 py-4">{{ $p->tanggal->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">{{ $p->penyadap->nama ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $p->blok->nama_blok ?? '-' }}</td>
                                <td class="px-6 py-4 text-right font-medium">{{ number_format($p->berat, 2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $p->status_validasi === 'disetujui' ? 'bg-green-100 text-green-700' : ($p->status_validasi === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ ucfirst($p->status_validasi ?? 'pending') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data produksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-sage-200 shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    @can('manage penyadap')
                    <a href="{{ route('penyadap.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-sage-50 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-sage-100 flex items-center justify-center group-hover:bg-sage-200 transition-colors">
                            <svg class="w-5 h-5 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Tambah Penyadap</span>
                    </a>
                    @endcan
                    @can('manage blok')
                    <a href="{{ route('blok.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-sage-50 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition-colors">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Tambah Blok</span>
                    </a>
                    @endcan
                    @can('create surat_jalan')
                    <a href="{{ route('surat-jalan.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-sage-50 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Buat Surat Jalan</span>
                    </a>
                    @endcan
                    <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-sage-50 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Lihat Laporan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>