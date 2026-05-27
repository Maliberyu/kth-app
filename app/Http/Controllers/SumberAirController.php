<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\SumberAir;
use App\Models\BakTampung;
use App\Models\KampungLayanan;
use App\Models\LahanLayanan;
use App\Models\PengukuranDebit;

class SumberAirController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $list   = SumberAir::where('created_by', $userId)
            ->with(['bakTampung.kampungLayanan', 'lahanLayanan', 'pengukuran'])
            ->latest()
            ->get();

        return view('sumber-air.index', compact('list'));
    }

    public function create()
    {
        return view('sumber-air.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'tipe'      => 'required|in:air_bersih,air_baku',
            'deskripsi' => 'nullable|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status'    => 'required|in:aktif,tidak_aktif',
        ]);

        $data['created_by'] = Auth::id();
        SumberAir::create($data);

        return redirect()->route('sumber-air.index')->with('success', 'Sumber air berhasil ditambahkan.');
    }

    public function show(SumberAir $sumberAir)
    {
        $this->authorize($sumberAir);

        $sumberAir->load(['bakTampung.kampungLayanan', 'lahanLayanan', 'pengukuran']);

        // Chart data: debit 6 bulan terakhir
        $chart = ['labels' => [], 'data' => []];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $chart['labels'][] = $date->isoFormat('MMM Y');
            $avg = PengukuranDebit::where('sumber_air_id', $sumberAir->id)
                ->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->avg('debit');
            $chart['data'][] = $avg ? round($avg, 3) : null;
        }

        return view('sumber-air.show', compact('sumberAir', 'chart'));
    }

    public function edit(SumberAir $sumberAir)
    {
        $this->authorize($sumberAir);
        return view('sumber-air.edit', compact('sumberAir'));
    }

    public function update(Request $request, SumberAir $sumberAir)
    {
        $this->authorize($sumberAir);

        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'tipe'      => 'required|in:air_bersih,air_baku',
            'deskripsi' => 'nullable|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status'    => 'required|in:aktif,tidak_aktif',
        ]);

        $sumberAir->update($data);

        return redirect()->route('sumber-air.show', $sumberAir)->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(SumberAir $sumberAir)
    {
        $this->authorize($sumberAir);
        $sumberAir->delete();
        return redirect()->route('sumber-air.index')->with('success', 'Sumber air dihapus.');
    }

    // ── Bak Tampung ───────────────────────────────────────────────────────────

    public function storeBak(Request $request, SumberAir $sumberAir)
    {
        $this->authorize($sumberAir);

        $data = $request->validate([
            'nama'      => 'required|string|max:255',
            'kapasitas' => 'nullable|numeric|min:0',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $data['sumber_air_id'] = $sumberAir->id;
        BakTampung::create($data);

        return back()->with('success', 'Bak tampung berhasil ditambahkan.');
    }

    public function destroyBak(SumberAir $sumberAir, BakTampung $bak)
    {
        $this->authorize($sumberAir);
        $bak->delete();
        return back()->with('success', 'Bak tampung dihapus.');
    }

    // ── Kampung Layanan ───────────────────────────────────────────────────────

    public function storeKampung(Request $request, SumberAir $sumberAir, BakTampung $bak)
    {
        $this->authorize($sumberAir);

        $data = $request->validate([
            'nama_kampung' => 'required|string|max:255',
            'jumlah_kk'    => 'nullable|integer|min:0',
        ]);

        $data['bak_tampung_id'] = $bak->id;
        $data['jumlah_kk']      = $data['jumlah_kk'] ?? 0;
        KampungLayanan::create($data);

        return back()->with('success', 'Kampung berhasil ditambahkan.');
    }

    public function destroyKampung(SumberAir $sumberAir, BakTampung $bak, KampungLayanan $kampung)
    {
        $this->authorize($sumberAir);
        $kampung->delete();
        return back()->with('success', 'Kampung dihapus.');
    }

    // ── Lahan Layanan ─────────────────────────────────────────────────────────

    public function storeLahan(Request $request, SumberAir $sumberAir)
    {
        $this->authorize($sumberAir);

        $data = $request->validate([
            'nama_lahan' => 'required|string|max:255',
            'tipe_lahan' => 'required|in:sawah,kolam,kebun',
            'luas_ha'    => 'nullable|numeric|min:0',
        ]);

        $data['sumber_air_id'] = $sumberAir->id;
        $data['luas_ha']       = $data['luas_ha'] ?? 0;
        LahanLayanan::create($data);

        return back()->with('success', 'Lahan berhasil ditambahkan.');
    }

    public function destroyLahan(SumberAir $sumberAir, LahanLayanan $lahan)
    {
        $this->authorize($sumberAir);
        $lahan->delete();
        return back()->with('success', 'Lahan dihapus.');
    }

    // ── Pengukuran Debit ──────────────────────────────────────────────────────

    public function storePengukuran(Request $request, SumberAir $sumberAir)
    {
        $this->authorize($sumberAir);

        $data = $request->validate([
            'tanggal'      => 'required|date',
            'debit'        => 'required|numeric|min:0',
            'satuan'       => 'required|in:liter/detik,m3/hari',
            'nama_petugas' => 'required|string|max:255',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'catatan'      => 'nullable|string',
            'foto'         => 'nullable|image|max:3072',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'debit_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/sumber-air'), $filename);
            $data['foto'] = 'uploads/sumber-air/' . $filename;
        }

        $data['sumber_air_id'] = $sumberAir->id;
        $data['created_by']    = Auth::id();
        PengukuranDebit::create($data);

        return back()->with('success', 'Pengukuran berhasil disimpan.');
    }

    public function destroyPengukuran(SumberAir $sumberAir, PengukuranDebit $pengukuran)
    {
        $this->authorize($sumberAir);
        if ($pengukuran->foto && file_exists(public_path($pengukuran->foto))) {
            unlink(public_path($pengukuran->foto));
        }
        $pengukuran->delete();
        return back()->with('success', 'Data pengukuran dihapus.');
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function authorize(SumberAir $sumberAir): void
    {
        if ($sumberAir->created_by !== Auth::id() && !Auth::user()->hasRole('super_admin')) {
            abort(403);
        }
    }
}
