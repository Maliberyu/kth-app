<?php

namespace App\Http\Controllers;

use App\Models\Blok;
use App\Models\BlokPeta;
use App\Models\PenugasanBlok;
use App\Models\Penyadap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlokController extends Controller
{
    public function index()
    {
        $kthId = auth()->user()->kth_id;
        $blok  = Blok::where('kth_id', $kthId)
                     ->withCount('penyadap')
                     ->paginate(15);
        return view('blok.index', compact('blok'));
    }

    public function create()
    {
        return view('blok.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_blok'               => 'required|string|max:100',
            'jenis_blok'              => 'nullable|string|max:50',
            'luas'                    => 'nullable|numeric|min:0',
            'jarak_antar_pohon'       => 'nullable|numeric|min:0',
            'total_pohon'             => 'nullable|integer|min:0',
            'pohon_produktif'         => 'nullable|integer|min:0',
            'pohon_tidak_produktif'   => 'nullable|integer|min:0',
            'geojson'                 => 'nullable|json',
        ]);

        // 🔥 Simpan data Blok dulu
        $blok = Blok::create([
            'kth_id'                  => auth()->user()->kth_id,
            'nama_blok'               => $validated['nama_blok'],
            'jenis_blok'              => $validated['jenis_blok'] ?? null,
            'luas'                    => $validated['luas'] ?? 0,
            'jarak_antar_pohon'       => $validated['jarak_antar_pohon'] ?? null,
            'total_pohon'             => $validated['total_pohon'] ?? 0,
            'pohon_produktif'         => $validated['pohon_produktif'] ?? 0,
            'pohon_tidak_produktif'   => $validated['pohon_tidak_produktif'] ?? 0,
        ]);

        // 🔥 Jika ada geojson, simpan ke tabel blok_peta
        if (!empty($validated['geojson'])) {
            BlokPeta::create([
                'blok_id'        => $blok->id,
                'dibuat_oleh'    => auth()->id(),
                'geojson'        => $validated['geojson'],
                'status_mapping' => 'disetujui', // Auto approve untuk input admin
                'catatan'        => 'Import dari form create blok',
            ]);
        }

        return redirect()->route('blok.index')->with('success', 'Blok berhasil ditambahkan.');
    }

    public function show(Blok $blok)
    {
        $blok->load(['penyadap', 'blokPeta' => fn($q) => $q->latest()]);
        $penyadapTersedia = Penyadap::where('kth_id', auth()->user()->kth_id)
                            ->whereNotIn('id', $blok->penyadap->pluck('id'))
                            ->get();
        return view('blok.show', compact('blok', 'penyadapTersedia'));
    }

    public function edit(Blok $blok)
    {
        return view('blok.edit', compact('blok'));
    }

    public function update(Request $request, Blok $blok)
    {
        $validated = $request->validate([
            'nama_blok'               => 'required|string|max:100',
            'jenis_blok'              => 'nullable|string|max:50',
            'luas'                    => 'nullable|numeric|min:0',
            'jarak_antar_pohon'       => 'nullable|numeric|min:0',
            'total_pohon'             => 'nullable|integer|min:0',
            'pohon_produktif'         => 'nullable|integer|min:0',
            'pohon_tidak_produktif'   => 'nullable|integer|min:0',
            'geojson'                 => 'nullable|json',
        ]);

        // 🔥 Update data Blok
        $blok->update([
            'nama_blok'               => $validated['nama_blok'],
            'jenis_blok'              => $validated['jenis_blok'] ?? null,
            'luas'                    => $validated['luas'] ?? 0,
            'jarak_antar_pohon'       => $validated['jarak_antar_pohon'] ?? null,
            'total_pohon'             => $validated['total_pohon'] ?? 0,
            'pohon_produktif'         => $validated['pohon_produktif'] ?? 0,
            'pohon_tidak_produktif'   => $validated['pohon_tidak_produktif'] ?? 0,
        ]);

        // 🔥 Jika ada geojson baru, simpan/update ke blok_peta
        if (!empty($validated['geojson'])) {
            // Cek apakah sudah ada record peta untuk blok ini
            $blokPeta = BlokPeta::where('blok_id', $blok->id)->first();
            
            if ($blokPeta) {
                // Update existing
                $blokPeta->update([
                    'geojson'        => $validated['geojson'],
                    'status_mapping' => 'disetujui',
                    'catatan'        => 'Update dari form edit blok - ' . now()->format('d/m/Y H:i'),
                ]);
            } else {
                // Create new
                BlokPeta::create([
                    'blok_id'        => $blok->id,
                    'dibuat_oleh'    => auth()->id(),
                    'geojson'        => $validated['geojson'],
                    'status_mapping' => 'disetujui',
                    'catatan'        => 'Import dari form edit blok',
                ]);
            }
        }

        return redirect()->route('blok.index')->with('success', 'Blok berhasil diupdate.');
    }

    public function destroy(Blok $blok)
    {
        // 🔥 Hapus juga record di blok_peta jika ada (opsional, biar rapi)
        $blok->blokPeta()->delete();
        $blok->delete();
        
        return redirect()->route('blok.index')->with('success', 'Blok berhasil dihapus.');
    }

    // Penugasan blok ke penyadap
    public function tugaskan(Request $request, Blok $blok)
    {
        $request->validate([
            'penyadap_id' => 'required|exists:penyadap,id',
        ]);

        PenugasanBlok::firstOrCreate([
            'blok_id'     => $blok->id,
            'penyadap_id' => $request->penyadap_id,
        ]);

        return back()->with('success', 'Penyadap berhasil ditugaskan ke blok ini.');
    }

    public function hapusTugas(Blok $blok, Penyadap $penyadap)
    {
        PenugasanBlok::where('blok_id', $blok->id)
                     ->where('penyadap_id', $penyadap->id)
                     ->delete();

        return back()->with('success', 'Penugasan berhasil dihapus.');
    }

    // Simpan peta GeoJSON dari penyadap (tetap dipertahankan untuk flow penyadap)
    public function simpanPeta(Request $request, Blok $blok)
    {
        $request->validate([
            'geojson' => 'required|string',
        ]);

        BlokPeta::create([
            'blok_id'        => $blok->id,
            'dibuat_oleh'    => auth()->id(),
            'geojson'        => $request->geojson,
            'status_mapping' => 'pending', // Penyadap tetap pending, admin yang approve
        ]);

        return back()->with('success', 'Peta berhasil disimpan, menunggu validasi.');
    }
    

    public function validasiPeta(Request $request, BlokPeta $blokPeta)
    {
        $request->validate([
            'status_mapping' => 'required|in:disetujui,ditolak',
            'catatan'        => 'nullable|string',
        ]);

        $blokPeta->update([
            'status_mapping' => $request->status_mapping,
            'catatan'        => $request->catatan,
        ]);

        return back()->with('success', 'Status mapping berhasil diupdate.');
    }

    // View untuk penyadap
    // public function indexPenyadap()
    // {
    //     $penyadapId = auth()->user()->penyadap_id;
    //     $blok = Blok::whereHas('penyadap', fn($q) => $q->where('penyadap.id', $penyadapId))
    //                 ->with(['blokPeta' => fn($q) => $q->latest()])
    //                 ->get();
    //     return view('blok.penyadap_index', compact('blok'));
    // }

    // public function showPenyadap(Blok $blok)
    // {
    //     $blok->load(['blokPeta' => fn($q) => $q->latest()]);
    //     return view('blok.penyadap_show', compact('blok'));
    // }
    // ... method simpanPeta dan validasiPeta ...

    // ==========================================
    // 🔥 VIEW UNTUK PENYADAP (PASTIKAN AKTIF)
    // ==========================================
    
    public function indexPenyadap()
    {
        $penyadapId = auth()->user()->penyadap_id;
        
        $blok = Blok::whereHas('penyadap', fn($q) => $q->where('penyadap.id', $penyadapId))
                    ->with(['blokPeta' => fn($q) => $q->latest()])
                    ->get();
        
        // 🔥 Siapkan data untuk mini map (JS-friendly)
        $blokData = $blok->map(function($b) {
            $peta = $b->blokPeta->first();
            return [
                'id' => $b->id,
                'geojson' => $peta?->geojson,
                'center' => [-7.324426, 108.0145812],
            ];
        });

        return view('blok.penyadap_index', compact('blok', 'blokData'));
    }

    // ✅ METHOD INI HARUS AKTIF (jangan dicomment!)
    public function showPenyadap(Blok $blok)
    {
        $blok->load(['blokPeta' => fn($q) => $q->latest()]);
        return view('blok.penyadap_show', compact('blok'));
    }
}