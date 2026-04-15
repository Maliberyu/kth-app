{{-- resources/views/blok/penyadap_show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-purple-600">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            📍 {{ $blok->nama_blok ?? 'Detail Blok' }}
        </h1>

        <div class="grid md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-600">Jenis Blok</p>
                <p class="font-semibold">{{ $blok->jenis_blok ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-600">Luas</p>
                <p class="font-semibold">{{ $blok->luas }} Ha</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-600">Total Pohon</p>
                <p class="font-semibold">{{ $blok->total_pohon }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-600">Pohon Produktif</p>
                <p class="font-semibold">{{ $blok->pohon_produktif }}</p>
            </div>
        </div>

        {{-- Peta / GeoJSON --}}
        <div class="mb-6">
            <h3 class="font-semibold text-gray-800 mb-2">🗺️ Peta Blok</h3>
            <div id="map" class="h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                <!-- Integrasi OpenStreetMap bisa ditaruh di sini -->
                <span>Map will load here</span>
            </div>
        </div>

        {{-- Form Upload Peta --}}
        <form action="{{ route('saya.blok.peta.store', $blok->id) }}" method="POST" class="mt-4">
            @csrf
            <input type="hidden" name="geojson" id="geojson-input">
            <button type="button" id="start-mapping" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                🗺️ Mulai Mapping
            </button>
            <button type="submit" id="save-mapping" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded ml-2 hidden">
                💾 Simpan Peta
            </button>
        </form>

        <a href="{{ route('saya.blok') }}" class="mt-6 inline-block text-purple-600 hover:underline">
            ← Kembali ke daftar blok
        </a>
    </div>
</div>

@push('scripts')
<script>
// Script untuk OpenStreetMap/Leaflet bisa ditaruh di sini
</script>
@endpush
@endsection