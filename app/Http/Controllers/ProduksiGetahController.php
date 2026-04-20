<?php

namespace App\Http\Controllers;

use App\Models\ProduksiGetah;
use App\Models\Penyadap;
use App\Models\Blok;
use App\Models\Penyimpanan;
use App\Models\User;
use Illuminate\Http\Request;

class ProduksiGetahController extends Controller
{
    // ===============================
    // 🔥 ADMIN KTH METHODS
    // ===============================
    
    public function index()
    {
        $kthId = auth()->user()->kth_id;
        $produksi = ProduksiGetah::with(['penyadap', 'blok', 'penyimpanan', 'diinputOleh'])
            ->whereHas('penyadap', fn($q) => $q->where('kth_id', $kthId))
            ->latest('tanggal')
            ->paginate(20);

        return view('produksi.index', compact('produksi'));
    }

    public function create()
    {
        $kthId = auth()->user()->kth_id;
        $penyadap = Penyadap::where('kth_id', $kthId)->get();
        $blok = Blok::where('kth_id', $kthId)->get();
        $penyimpanan = Penyimpanan::where('kth_id', $kthId)->get();

        return view('produksi.create', compact('penyadap', 'blok', 'penyimpanan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penyadap_id'    => 'required|exists:penyadap,id',
            'blok_id'        => 'required|exists:blok,id',
            'penyimpanan_id' => 'required|exists:penyimpanan,id',
            'tanggal'        => 'required|date',
            'berat'          => 'required|numeric|min:0.1',
            'catatan'        => 'nullable|string|max:255',
        ]);

        ProduksiGetah::create([
            'penyadap_id'     => $request->penyadap_id,
            'blok_id'         => $request->blok_id,
            'penyimpanan_id'  => $request->penyimpanan_id,
            'tanggal'         => $request->tanggal,
            'berat'           => $request->berat,
            'diinput_oleh'    => auth()->id(),
            'status_validasi' => 'pending',
            'catatan'         => $request->catatan,
        ]);

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil dicatat.');
    }

    public function show(ProduksiGetah $produksi)
    {
        // Security: Cek akses user
        if (auth()->user()->role === 'penyadap') {
            $penyadapId = auth()->user()->penyadap_id;
            $isAllowed = $produksi->blok->penugasanBlok()
                ->where('penyadap_id', $penyadapId)
                ->exists();
            if (!$isAllowed) {
                abort(403, 'Anda tidak berhak mengakses data ini.');
            }
        }
        
        $produksi->load(['penyadap', 'blok', 'penyimpanan', 'diinputOleh']);
        return view('produksi.show', compact('produksi'));
    }

    public function edit(ProduksiGetah $produksi)
    {
        $kthId = auth()->user()->kth_id;
        $penyadap = Penyadap::where('kth_id', $kthId)->get();
        $blok = Blok::where('kth_id', $kthId)->get();
        $penyimpanan = Penyimpanan::where('kth_id', $kthId)->get();

        return view('produksi.edit', compact('produksi', 'penyadap', 'blok', 'penyimpanan'));
    }

    public function update(Request $request, ProduksiGetah $produksi)
    {
        $request->validate([
            'penyadap_id'    => 'required|exists:penyadap,id',
            'blok_id'        => 'required|exists:blok,id',
            'penyimpanan_id' => 'required|exists:penyimpanan,id',
            'tanggal'        => 'required|date',
            'berat'          => 'required|numeric|min:0.1',
            'catatan'        => 'nullable|string|max:255',
        ]);

        $produksi->update($request->only(
            'penyadap_id', 'blok_id', 'penyimpanan_id', 'tanggal', 'berat', 'catatan'
        ));

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil diupdate.');
    }

    public function validasi(Request $request, ProduksiGetah $produksi)
    {
        $request->validate([
            'status_validasi' => 'required|in:valid,ditolak',
            'catatan'         => 'nullable|string|max:255',
        ]);

        $produksi->update([
            'status_validasi' => $request->status_validasi,
            'catatan'         => $request->catatan,
        ]);

        $pesan = $request->status_validasi === 'valid'
            ? 'Produksi berhasil divalidasi, stok getah diperbarui.'
            : 'Produksi ditolak.';

        return redirect()->route('produksi.index')->with('success', $pesan);
    }

    public function destroy(ProduksiGetah $produksi)
    {
        $produksi->delete();
        return redirect()->route('produksi.index')->with('success', 'Data produksi dihapus.');
    }

    // ===============================
    // 🔥 PENYADAP METHODS
    // ===============================
    
    public function indexPenyadap()
    {
        $penyadapId = auth()->user()->penyadap_id;
        
        $produksi = ProduksiGetah::whereHas('blok.penugasanBlok', function($q) use ($penyadapId) {
                $q->where('penyadap_id', $penyadapId);
            })
            ->with(['blok', 'penyimpanan'])
            ->latest('tanggal')
            ->paginate(20);
        
        return view('produksi.penyadap_index', compact('produksi'));
    }

    public function createPenyadap()
    {
        $penyadapId = auth()->user()->penyadap_id;
        
        $blokTersedia = Blok::whereHas('penyadap', function($q) use ($penyadapId) {
                $q->where('penyadap.id', $penyadapId);
            })
            ->get();
        
        return view('produksi.penyadap_create', compact('blokTersedia'));
    }

    public function storePenyadap(Request $request)
    {
        $validated = $request->validate([
            'blok_id' => 'required|exists:blok,id',
            'berat' => 'required|numeric|min:0.1',  // ✅ Pakai 'berat', bukan 'volume'
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string|max:255',
        ]);
        
        ProduksiGetah::create([
            'blok_id' => $validated['blok_id'],
            'penyadap_id' => auth()->user()->penyadap_id,
            'berat' => $validated['berat'],  // ✅ Field yang benar
            'tanggal' => $validated['tanggal'],
            'diinput_oleh' => auth()->id(),
            'status_validasi' => 'pending',  // ✅ Sesuai enum di migration
            'catatan' => $validated['catatan'] ?? null,
            // Tambahkan penyimpanan_id jika diperlukan:
            // 'penyimpanan_id' => 1, // atau ambil dari request
        ]);
        
        return redirect()->route('saya.produksi')->with('success', 'Produksi berhasil dicatat!');
    }
}