<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\SuratJalan;
use App\Models\Vendor;
use App\Models\Periode;
use App\Models\Penyadap;
use App\Models\Blok;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index()
    {
        $kthId = auth()->user()->kth_id;
        $penjualan = Penjualan::with(['vendor','periode','suratJalan'])
            ->whereHas('suratJalan.penyimpanan', fn($q) => $q->where('kth_id', $kthId))
            ->latest()
            ->paginate(15);

        return view('penjualan.index', compact('penjualan'));
    }

    public function create()
    {
        $kthId = auth()->user()->kth_id;

        // FIX: ambil semua surat jalan status selesai milik kth ini
        // tidak filter doesntHave karena satu surat jalan bisa punya banyak penjualan
        $suratJalan = SuratJalan::whereHas('penyimpanan', fn($q) => $q->where('kth_id', $kthId))
                        ->whereIn('status', ['selesai', 'dikirim'])
                        ->with('penyimpanan','vendor')
                        ->latest('tanggal')
                        ->get();

        $vendor   = Vendor::orderBy('nama_vendor')->get();
        $periode  = Periode::orderBy('tanggal_mulai','desc')->get();
        $penyadap = Penyadap::where('kth_id', $kthId)->orderBy('nama')->get();
        $blok     = Blok::where('kth_id', $kthId)->orderBy('nama_blok')->get();

        // Jika belum ada periode, buat otomatis periode bulan ini
        if ($periode->isEmpty()) {
            $periode = collect([
                (object)[
                    'id'            => 0,
                    'nama_periode'  => 'Buat periode baru di master data',
                    'tanggal_mulai' => now(),
                ]
            ]);
        }

        return view('penjualan.create', compact('suratJalan','vendor','periode','penyadap','blok'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'surat_jalan_id'       => 'required|exists:surat_jalan,id',
            'vendor_id'            => 'required|exists:vendor,id',
            'periode_id'           => 'required|exists:periode,id',
            'total_berat'          => 'required|numeric|min:0.01',
            'harga_jual'           => 'required|numeric|min:0',
            'detail'               => 'required|array|min:1',
            'detail.*.penyadap_id' => 'required|exists:penyadap,id',
            'detail.*.blok_id'     => 'required|exists:blok,id',
            'detail.*.berat'       => 'required|numeric|min:0.01',
            'detail.*.harga_beli'  => 'required|numeric|min:0',
        ]);

        $totalPenjualan = $request->total_berat * $request->harga_jual;

        $penjualan = Penjualan::create([
            'surat_jalan_id'  => $request->surat_jalan_id,
            'vendor_id'       => $request->vendor_id,
            'periode_id'      => $request->periode_id,
            'total_berat'     => $request->total_berat,
            'harga_jual'      => $request->harga_jual,
            'total_penjualan' => $totalPenjualan,
        ]);

        foreach ($request->detail as $d) {
            PenjualanDetail::create([
                'penjualan_id' => $penjualan->id,
                'penyadap_id'  => $d['penyadap_id'],
                'blok_id'      => $d['blok_id'],
                'berat'        => $d['berat'],
                'harga_beli'   => $d['harga_beli'],
                'total_beli'   => $d['berat'] * $d['harga_beli'],
            ]);
        }

        return redirect()->route('penjualan.index')
               ->with('success', 'Transaksi penjualan berhasil disimpan.');
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['vendor','periode','suratJalan','detail.penyadap','detail.blok']);
        return view('penjualan.show', compact('penjualan'));
    }
}