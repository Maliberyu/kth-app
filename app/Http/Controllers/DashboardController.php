<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kth;
use App\Models\Penyadap;
use App\Models\ProduksiGetah;
use App\Models\StokGetah;
use App\Models\SuratJalan;
use App\Models\Penjualan;
use App\Models\Kegiatan;
use App\Models\KegiatanUsaha;
use App\Models\UsahaTransaksi;
use App\Models\Kups;
use App\Models\Komoditas;
use App\Models\Inventaris;
use App\Models\InventarisMasuk;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) return $this->superAdminDashboard();
        if ($user->hasRole('admin_kth'))   return $this->adminKthDashboard($user);

        return $this->penyadapDashboard($user->penyadap_id);
    }

    private function superAdminDashboard()
    {
        $total_kth       = Kth::count();
        $total_penyadap  = Penyadap::count();
        $total_produksi  = ProduksiGetah::where('status_validasi','valid')->sum('berat');
        $total_penjualan = Penjualan::sum('total_penjualan');

        $kth_list = Kth::withCount('penyadap')
            ->withSum(['produksiGetah' => fn($q) => $q->where('status_validasi','valid')], 'berat')
            ->get();

        $chart_bulan = ['labels' => [], 'data' => []];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chart_bulan['labels'][] = $date->isoFormat('MMM Y');
            $chart_bulan['data'][]   = (float) ProduksiGetah::where('status_validasi','valid')
                ->whereYear('tanggal',  $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('berat');
        }

        $chart_kth = ['labels' => [], 'data' => []];
        foreach ($kth_list as $k) {
            $chart_kth['labels'][] = $k->nama_kth;
            $chart_kth['data'][]   = (float) ($k->produksi_getah_sum_berat ?? 0);
        }

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

    private function adminKthDashboard($user)
    {
        $kthId  = $user->kth_id;
        $userId = $user->id;

        // ── Getah ──────────────────────────────────────────
        $total_penyadap   = Penyadap::where('kth_id', $kthId)->count();
        $total_produksi   = ProduksiGetah::whereHas('penyadap', fn($q) => $q->where('kth_id',$kthId))
                                ->where('status_validasi','valid')->sum('berat');
        $produksi_pending = ProduksiGetah::whereHas('penyadap', fn($q) => $q->where('kth_id',$kthId))
                                ->where('status_validasi','pending')->count();
        $stok_getah       = StokGetah::whereHas('penyimpanan', fn($q) => $q->where('kth_id',$kthId))
                                ->sum('total_stok');

        // ── Surat Jalan ────────────────────────────────────
        $surat_jalan = SuratJalan::whereHas('penyimpanan', fn($q) => $q->where('kth_id',$kthId))
                            ->latest()->take(5)->get();

        // ── Kegiatan ───────────────────────────────────────
        $total_kegiatan_dalam = Kegiatan::where('created_by', $userId)->where('tipe','dalam_ruangan')->count();
        $total_kegiatan_luar  = Kegiatan::where('created_by', $userId)->where('tipe','luar_ruangan')->count();
        $total_peserta        = \App\Models\KegiatanPeserta::whereHas('kegiatan', fn($q) => $q->where('created_by',$userId))->count();
        $kegiatan_terbaru     = Kegiatan::where('created_by', $userId)->latest('tanggal_mulai')->take(5)->get();

        // ── Kegiatan Usaha ─────────────────────────────────
        $usaha_aktif     = KegiatanUsaha::where('created_by', $userId)->where('status','aktif')->count();
        $usaha_list      = KegiatanUsaha::where('created_by', $userId)->with('transaksi','modal')->latest()->take(6)->get();
        $total_modal_all = \App\Models\UsahaModal::whereHas('usaha', fn($q) => $q->where('created_by',$userId))->sum('jumlah');
        $total_pemasukan_all    = UsahaTransaksi::whereHas('usaha', fn($q) => $q->where('created_by',$userId))
                                    ->where('tipe','pemasukan')->sum('jumlah');
        $total_pengeluaran_all  = UsahaTransaksi::whereHas('usaha', fn($q) => $q->where('created_by',$userId))
                                    ->where('tipe','pengeluaran')->sum('jumlah');

        // ── KUPS & Komoditas ───────────────────────────────
        $total_kups      = Kups::where('created_by', $userId)->count();
        $total_komoditas = Komoditas::whereIn('kups_id', Kups::where('created_by',$userId)->pluck('id'))->count();

        // ── Inventaris ─────────────────────────────────────
        $total_inventaris = Inventaris::where('kth_id', $kthId)->count();
        $inventaris_terbaru = InventarisMasuk::where('kth_id', $kthId)
                                ->latest()->take(4)->get();

        // ── Chart 1: Produksi Getah 6 bulan ───────────────
        $chart_produksi = ['labels' => [], 'data' => []];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chart_produksi['labels'][] = $date->isoFormat('MMM');
            $chart_produksi['data'][]   = (float) ProduksiGetah
                ::whereHas('penyadap', fn($q) => $q->where('kth_id',$kthId))
                ->where('status_validasi','valid')
                ->whereYear('tanggal', $date->year)
                ->whereMonth('tanggal', $date->month)
                ->sum('berat');
        }

        // ── Chart 2: Kegiatan per bulan 6 bulan ───────────
        $chart_kegiatan = ['labels' => [], 'dalam' => [], 'luar' => []];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chart_kegiatan['labels'][] = $date->isoFormat('MMM');
            $chart_kegiatan['dalam'][]  = Kegiatan::where('created_by',$userId)
                ->where('tipe','dalam_ruangan')
                ->whereYear('tanggal_mulai', $date->year)
                ->whereMonth('tanggal_mulai', $date->month)
                ->count();
            $chart_kegiatan['luar'][]   = Kegiatan::where('created_by',$userId)
                ->where('tipe','luar_ruangan')
                ->whereYear('tanggal_mulai', $date->year)
                ->whereMonth('tanggal_mulai', $date->month)
                ->count();
        }

        // ── Chart 3: Kegiatan Usaha Keuangan ──────────────
        $chart_usaha = ['labels' => [], 'pemasukan' => [], 'pengeluaran' => [], 'modal' => []];
        foreach ($usaha_list as $u) {
            $chart_usaha['labels'][]     = \Illuminate\Support\Str::limit($u->nama_usaha, 18);
            $chart_usaha['pemasukan'][]  = (float) $u->transaksi->where('tipe','pemasukan')->sum('jumlah');
            $chart_usaha['pengeluaran'][]= (float) $u->transaksi->where('tipe','pengeluaran')->sum('jumlah');
            $chart_usaha['modal'][]      = (float) $u->modal->sum('jumlah');
        }

        // ── Chart 4: Komoditas per KUPS ───────────────────
        $kups_list  = Kups::where('created_by', $userId)->withCount('komoditas')->get();
        $chart_kups = ['labels' => [], 'data' => []];
        foreach ($kups_list as $k) {
            $chart_kups['labels'][] = $k->nama_kups;
            $chart_kups['data'][]   = $k->komoditas_count;
        }

        return view('dashboard.admin_kth', compact(
            'total_penyadap','total_produksi','produksi_pending','stok_getah',
            'surat_jalan',
            'total_kegiatan_dalam','total_kegiatan_luar','total_peserta','kegiatan_terbaru',
            'usaha_aktif','usaha_list','total_modal_all','total_pemasukan_all','total_pengeluaran_all',
            'total_kups','total_komoditas','kups_list',
            'total_inventaris','inventaris_terbaru',
            'chart_produksi','chart_kegiatan','chart_usaha','chart_kups'
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
        $riwayat = ProduksiGetah::where('penyadap_id', $penyadapId)
                     ->with(['blok'])->latest('tanggal')->take(10)->get();

        return view('dashboard.penyadap', compact('total_produksi','produksi_bulan','riwayat'));
    }
}
