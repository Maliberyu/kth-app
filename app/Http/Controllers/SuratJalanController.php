<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\PengirimanGetah;
use App\Models\Penyimpanan;
use App\Models\Vendor;
use Illuminate\Http\Request;

class SuratJalanController extends Controller
{
    public function index()
    {
        $kthId = auth()->user()->kth_id;
        $suratJalan = SuratJalan::with(['vendor', 'penyimpanan'])
            ->whereHas('penyimpanan', fn($q) => $q->where('kth_id', $kthId))
            ->latest('tanggal')
            ->paginate(15);

        return view('surat_jalan.index', compact('suratJalan'));
    }

    public function create()
    {
        $kthId       = auth()->user()->kth_id;
        $penyimpanan = Penyimpanan::where('kth_id', $kthId)->get();
        $vendor      = Vendor::all();

        return view('surat_jalan.create', compact('penyimpanan', 'vendor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor'          => 'required|string|unique:surat_jalan,nomor',
            'tanggal'        => 'required|date',
            'penyimpanan_id' => 'required|exists:penyimpanan,id',
            'vendor_id'      => 'required|exists:vendor,id',
            'total_berat'    => 'required|numeric|min:0.1',
            'keterangan'     => 'nullable|string',
        ]);

        $suratJalan = SuratJalan::create([
            'nomor'          => $request->nomor,
            'tanggal'        => $request->tanggal,
            'penyimpanan_id' => $request->penyimpanan_id,
            'vendor_id'      => $request->vendor_id,
            'total_berat'    => $request->total_berat,
            'status'         => 'draft',
            'keterangan'     => $request->keterangan,
        ]);

        PengirimanGetah::create([
            'surat_jalan_id' => $suratJalan->id,
            'jumlah_dikirim' => $request->total_berat,
        ]);

        return redirect()->route('surat-jalan.index')->with('success', 'Surat jalan berhasil dibuat.');
    }

    public function show(SuratJalan $suratJalan)
    {
        $suratJalan->load(['vendor', 'penyimpanan', 'pengirimanGetah', 'penjualan']);
        return view('surat_jalan.show', compact('suratJalan'));
    }

    public function kirim(SuratJalan $suratJalan)
    {
        $suratJalan->update(['status' => 'dikirim']);
        return redirect()->route('surat-jalan.index')->with('success', 'Status diubah ke dikirim.');
    }

    public function selesai(SuratJalan $suratJalan)
    {
        $suratJalan->update(['status' => 'selesai']);
        return redirect()->route('surat-jalan.index')->with('success', 'Pengiriman selesai, stok diperbarui.');
    }

    public function destroy(SuratJalan $suratJalan)
    {
        if ($suratJalan->status !== 'draft') {
            return back()->with('error', 'Hanya surat jalan berstatus draft yang bisa dihapus.');
        }
        $suratJalan->delete();
        return redirect()->route('surat-jalan.index')->with('success', 'Surat jalan dihapus.');
    }

    // 🔥 Method untuk cetak PDF Surat Jalan
        public function cetakPdf(SuratJalan $suratJalan)
        {
            // Security: Pastikan hanya admin KTH yang bisa cetak
            if (auth()->user()->role !== 'admin_kth') {
                abort(403, 'Anda tidak berhak mencetak surat jalan ini.');
            }
            
            // Load relasi yang dibutuhkan untuk PDF
            $suratJalan->load(['vendor', 'penyimpanan', 'details.produksiGetah.penyadap', 'details.produksiGetah.blok']);
            
            // Opsi 1: Pakai view Blade → HTML → PDF (pakai dompdf/snappy)
            // Opsi 2: Langsung return view untuk print browser (lebih simpel)
            
            // ✅ Opsi Simpel: Return view print-friendly, user print via browser (Ctrl+P → Save as PDF)
            return view('surat_jalan.cetak', compact('suratJalan'))
                ->with('printMode', true);
            
            // 🔄 Kalau mau generate PDF file beneran (pakai dompdf):
            /*
            use Barryvdh\DomPDF\Facade\Pdf;
            
            $pdf = Pdf::loadView('surat_jalan.cetak', compact('suratJalan'));
            return $pdf->stream('Surat-Jalan-'.$suratJalan->nomor.'.pdf');
            */
        }
}