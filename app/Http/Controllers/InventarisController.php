<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\InventarisMasuk;
use App\Models\InventarisMasukDetail;
use App\Models\DistribusiInventaris;
use App\Models\DistribusiInventarisDetail;
use App\Models\StokInventaris;
use App\Models\Vendor;
use App\Models\Penyadap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarisController extends Controller
{
    // =====================================================
    // STOK / MASTER BARANG
    // =====================================================
    public function index()
    {
        $kthId = auth()->user()->kth_id;
        $inventaris = Inventaris::where('kth_id', $kthId)
                        ->with('stok')
                        ->orderBy('nama_barang')
                        ->get();
        return view('inventaris.index', compact('inventaris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:150',
            'satuan'      => 'nullable|string|max:30',
        ]);

        $kthId = auth()->user()->kth_id;

        // Cek duplikat
        $exists = Inventaris::where('kth_id', $kthId)
                    ->where('nama_barang', $request->nama_barang)->exists();
        if ($exists) {
            return back()->with('error', 'Barang dengan nama tersebut sudah ada.');
        }

        $inv = Inventaris::create([
            'kth_id'      => $kthId,
            'nama_barang' => $request->nama_barang,
            'satuan'      => $request->satuan,
        ]);

        // Buat stok awal = 0
        StokInventaris::create([
            'inventaris_id' => $inv->id,
            'total_stok'    => 0,
        ]);

        return redirect()->route('inventaris.index')
               ->with('success', 'Barang "' . $inv->nama_barang . '" berhasil ditambahkan.');
    }

    // =====================================================
    // INVENTARIS MASUK
    // =====================================================
    public function masukIndex()
    {
        $kthId = auth()->user()->kth_id;
        $masuk = InventarisMasuk::where('kth_id', $kthId)
                    ->with(['vendor', 'detail.inventaris'])
                    ->latest('tanggal')
                    ->paginate(15);
        return view('inventaris.masuk.index', compact('masuk'));
    }

    public function masukCreate()
    {
        $kthId  = auth()->user()->kth_id;
        $vendor = Vendor::orderBy('nama_vendor')->get();
        $barang = Inventaris::where('kth_id', $kthId)
                    ->with('stok')
                    ->orderBy('nama_barang')
                    ->get();

        if ($vendor->isEmpty()) {
            return back()->with('error', 'Belum ada vendor. Tambah vendor terlebih dahulu.');
        }
        if ($barang->isEmpty()) {
            return back()->with('error', 'Belum ada data barang inventaris. Tambah barang terlebih dahulu.');
        }

        return view('inventaris.masuk.create', compact('vendor', 'barang'));
    }

    public function masukStore(Request $request)
    {
        $request->validate([
            'vendor_id'              => 'required|exists:vendor,id',
            'tanggal'                => 'required|date',
            'detail'                 => 'required|array|min:1',
            'detail.*.inventaris_id' => 'required|exists:inventaris,id',
            'detail.*.jumlah'        => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $masuk = InventarisMasuk::create([
                'vendor_id'  => $request->vendor_id,
                'kth_id'     => auth()->user()->kth_id,
                'tanggal'    => $request->tanggal,
                'keterangan' => $request->keterangan,
            ]);

            foreach ($request->detail as $d) {
                InventarisMasukDetail::create([
                    'inventaris_masuk_id' => $masuk->id,
                    'inventaris_id'       => $d['inventaris_id'],
                    'jumlah'              => $d['jumlah'],
                ]);
                // Update stok manual (backup jika trigger tidak jalan)
                StokInventaris::where('inventaris_id', $d['inventaris_id'])
                    ->increment('total_stok', $d['jumlah']);
            }

            DB::commit();
            return redirect()->route('inventaris.masuk')
                   ->with('success', 'Inventaris masuk berhasil dicatat, stok diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // =====================================================
    // DISTRIBUSI INVENTARIS
    // =====================================================
    public function distribusiIndex()
    {
        $kthId = auth()->user()->kth_id;
        $distribusi = DistribusiInventaris::where('kth_id', $kthId)
                        ->with(['penyadap', 'detail.inventaris'])
                        ->latest('tanggal')
                        ->paginate(15);
        return view('inventaris.distribusi.index', compact('distribusi'));
    }

    // public function distribusiCreate()
    // {
    //     $kthId    = auth()->user()->kth_id;
    //     $penyadap = Penyadap::where('kth_id', $kthId)->orderBy('nama')->get();
    //     $barang   = Inventaris::where('kth_id', $kthId)
    //                     ->with('stok')
    //                     ->orderBy('nama_barang')
    //                     ->get();

    //     if ($penyadap->isEmpty()) {
    //         return back()->with('error', 'Belum ada penyadap terdaftar.');
    //     }
    //     if ($barang->isEmpty()) {
    //         return back()->with('error', 'Belum ada data barang inventaris.');
    //     }

    //     return view('inventaris.distribusi.create', compact('penyadap', 'barang'));
    // }
    // public function distribusiCreate()
    //     {
    //         $kthId    = auth()->user()->kth_id;
    //         $penyadap = Penyadap::where('kth_id', $kthId)->orderBy('nama')->get();
    //         $barang   = Inventaris::where('kth_id', $kthId)
    //                         ->with('stok')
    //                         ->orderBy('nama_barang')
    //                         ->get();

    //         if ($penyadap->isEmpty()) {
    //             return back()->with('error', 'Belum ada penyadap terdaftar.');
    //         }
    //         if ($barang->isEmpty()) {
    //             return back()->with('error', 'Belum ada data barang inventaris.');
    //         }

    //         // ✅ Mapping di controller, hindari fn() di Blade
    //         $barangJson = $barang->map(function ($b) {
    //             return [
    //                 'id'     => $b->id,
    //                 'nama'   => $b->nama_barang,
    //                 'stok'   => optional($b->stok)->total_stok ?? 0,
    //                 'satuan' => $b->satuan ?? '',
    //             ];
    //         });

    //         return view('inventaris.distribusi.create', compact('penyadap', 'barang', 'barangJson'));
    //     }
    public function distribusiCreate()
        {
            $kthId    = auth()->user()->kth_id;
            $penyadap = Penyadap::where('kth_id', $kthId)->orderBy('nama')->get();
            $barang   = Inventaris::where('kth_id', $kthId)
                            ->with('stok')
                            ->orderBy('nama_barang')
                            ->get();

            if ($penyadap->isEmpty()) {
                return back()->with('error', 'Belum ada penyadap terdaftar.');
            }
            if ($barang->isEmpty()) {
                return back()->with('error', 'Belum ada data barang inventaris.');
            }

            $barangJson = $barang->map(function ($b) {
                return [
                    'id'     => $b->id,
                    'nama'   => $b->nama_barang,
                    'stok'   => optional($b->stok)->total_stok ?? 0,
                    'satuan' => $b->satuan ?? '',
                ];
            });

            return view('inventaris.distribusi.create', compact('penyadap', 'barang', 'barangJson'));
        }

    public function distribusiStore(Request $request)
    {
        $request->validate([
            'penyadap_id'            => 'required|exists:penyadap,id',
            'tanggal'                => 'required|date',
            'detail'                 => 'required|array|min:1',
            'detail.*.inventaris_id' => 'required|exists:inventaris,id',
            'detail.*.jumlah'        => 'required|integer|min:1',
        ]);

        // Validasi stok cukup
        foreach ($request->detail as $d) {
            $stok = StokInventaris::where('inventaris_id', $d['inventaris_id'])->first();
            $inv  = Inventaris::find($d['inventaris_id']);
            if (!$stok || $stok->total_stok < $d['jumlah']) {
                return back()
                    ->withInput()
                    ->with('error', 'Stok "' . ($inv->nama_barang ?? '') . '" tidak cukup. Stok tersedia: ' . ($stok->total_stok ?? 0));
            }
        }

        DB::beginTransaction();
        try {
            $distribusi = DistribusiInventaris::create([
                'penyadap_id' => $request->penyadap_id,
                'kth_id'      => auth()->user()->kth_id,
                'tanggal'     => $request->tanggal,
                'keterangan'  => $request->keterangan,
            ]);

            foreach ($request->detail as $d) {
                DistribusiInventarisDetail::create([
                    'distribusi_id' => $distribusi->id,
                    'inventaris_id' => $d['inventaris_id'],
                    'jumlah'        => $d['jumlah'],
                ]);
                // Update stok manual (backup jika trigger tidak jalan)
                StokInventaris::where('inventaris_id', $d['inventaris_id'])
                    ->decrement('total_stok', $d['jumlah']);
            }

            DB::commit();
            return redirect()->route('inventaris.distribusi')
                   ->with('success', 'Distribusi inventaris berhasil dicatat, stok dikurangi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
