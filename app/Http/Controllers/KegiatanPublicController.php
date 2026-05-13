<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KegiatanPeserta;
use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;

class KegiatanPublicController extends Controller
{
    public function show(string $token)
    {
        $kegiatan = Kegiatan::where('qr_token', $token)
            ->where('status', 'aktif')
            ->firstOrFail();

        return view('kegiatan.register', compact('kegiatan'));
    }

    public function store(Request $request, string $token)
    {
        $kegiatan = Kegiatan::where('qr_token', $token)
            ->where('status', 'aktif')
            ->firstOrFail();

        $data = $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'jabatan'       => 'nullable|string|max:255',
            'asal_instansi' => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255',
            'no_hp'         => 'required|string|max:20|regex:/^[0-9+\-\s]+$/',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'no_hp.required'        => 'Nomor HP wajib diisi.',
            'no_hp.regex'           => 'Format nomor HP tidak valid.',
            'email.email'           => 'Format email tidak valid.',
        ]);

        // Normalisasi no HP: hapus spasi/strip
        $data['no_hp'] = preg_replace('/[\s\-]/', '', $data['no_hp']);

        try {
            $peserta = KegiatanPeserta::create([
                'kegiatan_id'  => $kegiatan->id,
                'nama_lengkap' => $data['nama_lengkap'],
                'jabatan'      => $data['jabatan'] ?? null,
                'asal_instansi'=> $data['asal_instansi'] ?? null,
                'email'        => $data['email'] ?? null,
                'no_hp'        => $data['no_hp'],
                'waktu_hadir'  => now(),
            ]);
        } catch (UniqueConstraintViolationException $e) {
            // No HP sudah terdaftar di kegiatan ini
            $existing = KegiatanPeserta::where('kegiatan_id', $kegiatan->id)
                ->where('no_hp', $data['no_hp'])
                ->first();

            return redirect()->route('daftar.sukses', ['token' => $token, 'peserta' => $existing->id])
                ->with('already_registered', true);
        }

        return redirect()->route('daftar.sukses', ['token' => $token, 'peserta' => $peserta->id]);
    }

    public function sukses(Request $request, string $token)
    {
        $kegiatan = Kegiatan::where('qr_token', $token)->firstOrFail();
        $peserta  = KegiatanPeserta::findOrFail($request->query('peserta'));

        $alreadyRegistered = session('already_registered', false);

        return view('kegiatan.sukses', compact('kegiatan', 'peserta', 'alreadyRegistered'));
    }
}
