<?php

namespace App\Http\Controllers;

use App\Models\KegiatanUsaha;

class UsahaPublicController extends Controller
{
    public function show(string $token)
    {
        $usaha = KegiatanUsaha::where('qr_token', $token)->firstOrFail();

        if (!$usaha->qr_aktif) {
            return view('kegiatan.usaha.public', ['usaha' => $usaha, 'nonaktif' => true]);
        }

        $usaha->load(['modal', 'transaksi', 'harian.fotos']);

        $totalModal       = $usaha->modal->sum('jumlah');
        $totalPemasukan   = $usaha->transaksi->where('tipe', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = $usaha->transaksi->where('tipe', 'pengeluaran')->sum('jumlah');
        $kasTersedia      = $totalModal + $totalPemasukan - $totalPengeluaran;
        $labaRugi         = $totalPemasukan - $totalPengeluaran;

        return view('kegiatan.usaha.public', compact(
            'usaha', 'totalModal', 'totalPemasukan',
            'totalPengeluaran', 'kasTersedia', 'labaRugi'
        ));
    }
}
