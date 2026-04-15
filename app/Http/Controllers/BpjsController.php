<?php

namespace App\Http\Controllers;

use App\Models\Bpjs;
use App\Models\Penyadap;
use Illuminate\Http\Request;

class BpjsController extends Controller
{
    public function store(Request $request, Penyadap $penyadap)
    {
        $request->validate([
            'jenis_bpjs'  => 'required|in:Kesehatan,Ketenagakerjaan',
            'nomor'       => 'required|string|max:30',
            'status_aktif'=> 'required|in:Aktif,Tidak Aktif',
            'penanggung'  => 'nullable|string|max:100',
        ]);

        Bpjs::create([
            'penyadap_id'  => $penyadap->id,
            'jenis_bpjs'   => $request->jenis_bpjs,
            'nomor'        => $request->nomor,
            'status_aktif' => $request->status_aktif,
            'penanggung'   => $request->penanggung,
        ]);

        return back()->with('success', 'Data BPJS berhasil ditambahkan.');
    }

    public function destroy(Bpjs $bpjs)
    {
        $bpjs->delete();
        return back()->with('success', 'Data BPJS dihapus.');
    }
}