<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kth;
use App\Models\Penyadap;
use App\Models\ProduksiGetah;
use App\Models\StokGetah;
use App\Models\SuratJalan;
use App\Models\Penjualan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) return $this->superAdminDashboard();
        if ($user->hasRole('admin_kth'))   return $this->adminKthDashboard($user->kth_id);

        return $this->penyadapDashboard($user->penyadap_id);
    }

    private function superAdminDashboard()
    {
        // Stat cards
        $total_kth       = Kth::count();
        $total_penyadap  = Penyadap::count();
        $total_produksi  = ProduksiGetah::where('status_validasi','valid')->sum('berat');
        $total_penjualan = Penjualan::sum('total_penjualan');

        // KTH list dengan produksi sum
        $kth_list = Kth::withCount('penyadap')
            ->withSum(['produksiGetah' => fn($q) => $q->where('status_validasi','valid')], 'berat')
            ->get();

        // Chart produksi 6 bulan
        $chart_bulan = ['labels' => [], 'data' => []];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $chart_bulan['labels'][] = $date->isoFormat('MMM Y');
            $chart_bulan['data'][]   = (float) ProduksiGetah::where('status_validasi','valid')
                ->whereYear('tanggal',  $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('berat');
        }

        // Chart per KTH
        $chart_kth = ['labels' => [], 'data' => []];
        foreach ($kth_list as $k) {
            $chart_kth['labels'][] = $k->nama_kth;
            $chart_kth['data'][]   = (float) ($k->produksi_getah_sum_berat ?? 0);
        }

        // Chart status produksi
        $chart_status = [
            ProduksiGetah::where('status_validasi','valid')->count(),
            ProduksiGetah::where('status_validasi','pending')->count(),
            ProduksiGetah::where('status_validasi','ditolak')->count(),
        ];

        return view('dashboard.super_admin', compact(
            'total_kth','total_penyadap','total_produksi','total_penjualan',
            'kth_list','chart_bulan','chart_kth','chart_status'
        ));
    }

    private function adminKthDashboard($kthId)
    {
        $total_penyadap   = Penyadap::where('kth_id', $kthId)->count();
        $total_produksi   = ProduksiGetah::whereHas('penyadap', fn($q) => $q->where('kth_id',$kthId))
                              ->where('status_validasi','valid')->sum('berat');
        $produksi_pending = ProduksiGetah::whereHas('penyadap', fn($q) => $q->where('kth_id',$kthId))
                              ->where('status_validasi','pending')->count();
        $stok_getah       = StokGetah::whereHas('penyimpanan', fn($q) => $q->where('kth_id',$kthId))
                              ->sum('total_stok');
        $surat_jalan      = SuratJalan::whereHas('penyimpanan', fn($q) => $q->where('kth_id',$kthId))
                              ->with(['vendor','penyimpanan'])->latest()->take(5)->get();

        return view('dashboard.admin_kth', compact(
            'total_penyadap','total_produksi','produksi_pending','stok_getah','surat_jalan'
        ));
    }

    private function penyadapDashboard($penyadapId)
    {
        $total_produksi = ProduksiGetah::where('penyadap_id', $penyadapId)
                            ->where('status_validasi','valid')->sum('berat');
        $produksi_bulan = ProduksiGetah::where('penyadap_id', $penyadapId)
                            ->whereMonth('tanggal', now()->month)
                            ->whereYear('tanggal',  now()->year)
                            ->where('status_validasi','valid')->sum('berat');
        $riwayat        = ProduksiGetah::where('penyadap_id', $penyadapId)
                            ->with(['blok'])->latest('tanggal')->take(10)->get();

        return view('dashboard.penyadap', compact('total_produksi','produksi_bulan','riwayat'));
    }
}