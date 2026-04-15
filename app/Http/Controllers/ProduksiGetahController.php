<?php

namespace App\Http\Controllers;

use App\Models\ProduksiGetah;
use App\Models\Penyadap;
use App\Models\Blok;
use App\Models\Penyimpanan;
use Illuminate\Http\Request;

class ProduksiGetahController extends Controller
{
    public function index()
    {
        $kthId = auth()->user()->kth_id;
        $produksi = ProduksiGetah::with(['penyadap', 'blok', 'penyimpanan'])
            ->whereHas('penyadap', fn($q) => $q->where('kth_id', $kthId))
            ->latest('tanggal')
            ->paginate(20);

        return view('produksi.index', compact('produksi'));
    }

    public function create()
    {
        $kthId      = auth()->user()->kth_id;
        $penyadap   = Penyadap::where('kth_id', $kthId)->get();
        $blok       = Blok::where('kth_id', $kthId)->get();
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
        ]);

        ProduksiGetah::create([
            'penyadap_id'    => $request->penyadap_id,
            'blok_id'        => $request->blok_id,
            'penyimpanan_id' => $request->penyimpanan_id,
            'tanggal'        => $request->tanggal,
            'berat'          => $request->berat,
            'diinput_oleh'   => auth()->id(),
            'status_validasi' => 'pending',
            'catatan'        => $request->catatan,
        ]);

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil dicatat.');
    }

    public function edit(ProduksiGetah $produksi)
    {
        $kthId       = auth()->user()->kth_id;
        $penyadap    = Penyadap::where('kth_id', $kthId)->get();
        $blok        = Blok::where('kth_id', $kthId)->get();
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
            'catatan'         => 'nullable|string',
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
}