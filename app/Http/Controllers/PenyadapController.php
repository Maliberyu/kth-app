<?php

namespace App\Http\Controllers;

use App\Models\Penyadap;
use App\Models\Kth;
use Illuminate\Http\Request;

class PenyadapController extends Controller
{
    public function index()
    {
        $kthId = auth()->user()->kth_id;
        $penyadap = Penyadap::where('kth_id', $kthId)
                        ->with('bpjs')
                        ->paginate(15);
        return view('penyadap.index', compact('penyadap'));
    }

    public function create()
    {
        return view('penyadap.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'nik'    => 'nullable|string|max:20|unique:penyadap,nik',
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $kthId = auth()->user()->kth_id;

        Penyadap::create([
            'kth_id' => $kthId,
            'nama'   => $request->nama,
            'nik'    => $request->nik,
            'no_hp'  => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('penyadap.index')->with('success', 'Penyadap berhasil ditambahkan.');
    }

    public function show(Penyadap $penyadap)
    {
        $penyadap->load(['bpjs', 'blok', 'produksiGetah' => fn($q) => $q->latest('tanggal')->take(10)]);
        return view('penyadap.show', compact('penyadap'));
    }

    public function edit(Penyadap $penyadap)
    {
        return view('penyadap.edit', compact('penyadap'));
    }

    public function update(Request $request, Penyadap $penyadap)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'nik'    => 'nullable|string|max:20|unique:penyadap,nik,' . $penyadap->id,
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $penyadap->update($request->only('nama', 'nik', 'no_hp', 'alamat'));

        return redirect()->route('penyadap.index')->with('success', 'Data penyadap berhasil diupdate.');
    }

    public function destroy(Penyadap $penyadap)
    {
        $penyadap->delete();
        return redirect()->route('penyadap.index')->with('success', 'Penyadap berhasil dihapus.');
    }
}