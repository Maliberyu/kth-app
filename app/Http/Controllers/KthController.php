<?php

namespace App\Http\Controllers;

use App\Models\Kth;
use Illuminate\Http\Request;

class KthController extends Controller
{
    public function index()
    {
        $kth = Kth::withCount(['penyadap','blok'])->paginate(10);
         return view('kth.index', compact('kth'));
    }

    public function create()
    {
        return view('kth.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kth' => 'required|string|max:150',
            'alamat'   => 'nullable|string',
        ]);

        Kth::create($request->only('nama_kth', 'alamat'));

        return redirect()->route('kth.index')->with('success', 'KTH berhasil ditambahkan.');
    }

    public function show(Kth $kth)
    {
        $kth->load(['penyadap', 'blok', 'penyimpanan']);
        return view('kth.show', compact('kth'));
    }

    public function edit(Kth $kth)
    {
        return view('kth.edit', compact('kth'));
    }

    public function update(Request $request, Kth $kth)
    {
        $request->validate([
            'nama_kth' => 'required|string|max:150',
            'alamat'   => 'nullable|string',
        ]);

        $kth->update($request->only('nama_kth', 'alamat'));

        return redirect()->route('kth.index')->with('success', 'KTH berhasil diupdate.');
    }

    public function destroy(Kth $kth)
    {
        $kth->delete();
        return redirect()->route('kth.index')->with('success', 'KTH berhasil dihapus.');
    }
}