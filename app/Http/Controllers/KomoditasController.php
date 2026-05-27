<?php

namespace App\Http\Controllers;

use App\Models\Kups;
use App\Models\Komoditas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomoditasController extends Controller
{
    public function store(Request $request, Kups $kup)
    {
        $this->authorizeKups($kup);

        $data = $request->validate([
            'nama_komoditas' => 'required|string|max:255',
            'volume'         => 'required|numeric|min:0',
            'satuan'         => 'required|in:Kg,Ikat,Batang,Liter,Buah',
            'harga'          => 'required|integer|min:0',
            'keterangan'     => 'nullable|string|max:500',
        ], [
            'nama_komoditas.required' => 'Nama komoditas wajib diisi.',
            'volume.required'         => 'Volume wajib diisi.',
            'satuan.required'         => 'Satuan wajib dipilih.',
            'harga.required'          => 'Harga wajib diisi.',
        ]);

        $data['kups_id'] = $kup->id;
        Komoditas::create($data);

        return back()->with('success', 'Komoditas berhasil ditambahkan.');
    }

    public function update(Request $request, Kups $kup, Komoditas $komoditas)
    {
        $this->authorizeKups($kup);

        $data = $request->validate([
            'nama_komoditas' => 'required|string|max:255',
            'volume'         => 'required|numeric|min:0',
            'satuan'         => 'required|in:Kg,Ikat,Batang,Liter,Buah',
            'harga'          => 'required|integer|min:0',
            'keterangan'     => 'nullable|string|max:500',
        ]);

        $komoditas->update($data);

        return back()->with('success', 'Komoditas berhasil diperbarui.');
    }

    public function destroy(Kups $kup, Komoditas $komoditas)
    {
        $this->authorizeKups($kup);
        $komoditas->delete();

        return back()->with('success', 'Komoditas berhasil dihapus.');
    }

    private function authorizeKups(Kups $kup): void
    {
        if ($kup->created_by !== Auth::id() && !auth()->user()->hasRole('super_admin')) {
            abort(403);
        }
    }
}
