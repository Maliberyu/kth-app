<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    public function index()
    {
        $periode = Periode::orderBy('tanggal_mulai', 'desc')->paginate(15);
        return view('periode.index', compact('periode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode'    => 'required|string|max:100',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        Periode::create($request->only('nama_periode', 'tanggal_mulai', 'tanggal_selesai'));

        return redirect()->route('periode.index')
               ->with('success', 'Periode berhasil ditambahkan.');
    }

    public function destroy(Periode $periode)
    {
        if ($periode->penjualan()->count() > 0) {
            return back()->with('error', 'Periode tidak bisa dihapus karena sudah digunakan di transaksi penjualan.');
        }
        $periode->delete();
        return redirect()->route('periode.index')->with('success', 'Periode dihapus.');
    }
}