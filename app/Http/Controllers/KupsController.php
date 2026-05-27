<?php

namespace App\Http\Controllers;

use App\Models\Kups;
use App\Models\Komoditas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KupsController extends Controller
{
    public function index()
    {
        $kupsList = Kups::withCount('komoditas')
            ->where('created_by', Auth::id())
            ->latest()
            ->get();

        return view('kups.index', compact('kupsList'));
    }

    public function create()
    {
        return view('kups.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kups'  => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
            'status'     => 'required|in:aktif,nonaktif',
        ], [
            'nama_kups.required' => 'Nama KUPS wajib diisi.',
        ]);

        $data['created_by'] = Auth::id();
        $kups = Kups::create($data);

        return redirect()->route('kups.show', $kups)
            ->with('success', 'KUPS berhasil dibuat.');
    }

    public function show(Kups $kup)
    {
        $this->authorize($kup);
        $komoditas = $kup->komoditas;
        $totalNilai = $komoditas->sum(fn($k) => $k->volume * $k->harga);

        return view('kups.show', compact('kup', 'komoditas', 'totalNilai'));
    }

    public function edit(Kups $kup)
    {
        $this->authorize($kup);
        return view('kups.edit', compact('kup'));
    }

    public function update(Request $request, Kups $kup)
    {
        $this->authorize($kup);

        $data = $request->validate([
            'nama_kups'  => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
            'status'     => 'required|in:aktif,nonaktif',
        ], [
            'nama_kups.required' => 'Nama KUPS wajib diisi.',
        ]);

        $kup->update($data);

        return redirect()->route('kups.show', $kup)
            ->with('success', 'KUPS berhasil diperbarui.');
    }

    public function destroy(Kups $kup)
    {
        $this->authorize($kup);
        $kup->komoditas()->delete();
        $kup->delete();

        return redirect()->route('kups.index')
            ->with('success', 'KUPS berhasil dihapus.');
    }

    public function printPdf(Kups $kup)
    {
        $this->authorize($kup);
        $komoditas  = $kup->komoditas;
        $totalNilai = $komoditas->sum(fn($k) => $k->volume * $k->harga);

        return view('kups.print', compact('kup', 'komoditas', 'totalNilai'));
    }

    private function authorize(Kups $kup): void
    {
        if ($kup->created_by !== Auth::id() && !auth()->user()->hasRole('super_admin')) {
            abort(403);
        }
    }
}
