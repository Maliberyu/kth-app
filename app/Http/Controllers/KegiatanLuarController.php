<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KegiatanFoto;
use App\Models\KegiatanPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class KegiatanLuarController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::where('tipe', 'luar_ruangan')
            ->where('created_by', Auth::id())
            ->withCount(['peserta', 'fotos'])
            ->latest()
            ->paginate(10);

        return view('kegiatan.luar.index', compact('kegiatans'));
    }

    public function create()
    {
        return view('kegiatan.luar.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kegiatan'   => 'required|string|max:255',
            'lokasi'          => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'uraian_kegiatan' => 'nullable|string',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
        ], [
            'nama_kegiatan.required' => 'Judul kegiatan wajib diisi.',
            'lokasi.required'        => 'Tempat kegiatan wajib diisi.',
            'tanggal_mulai.required' => 'Waktu kegiatan wajib diisi.',
        ]);

        $data['tipe']       = 'luar_ruangan';
        $data['status']     = 'aktif';
        $data['created_by'] = Auth::id();
        $data['qr_token']   = \Illuminate\Support\Str::random(32);

        $kegiatan = Kegiatan::create($data);

        return redirect()->route('kegiatan-luar.show', $kegiatan)
            ->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Kegiatan $kegiatanLuar)
    {
        $this->authorize($kegiatanLuar);
        $fotos   = $kegiatanLuar->fotos;
        $peserta = $kegiatanLuar->peserta()->orderBy('created_at')->get();

        return view('kegiatan.luar.show', compact('kegiatanLuar', 'fotos', 'peserta'));
    }

    public function edit(Kegiatan $kegiatanLuar)
    {
        $this->authorize($kegiatanLuar);
        return view('kegiatan.luar.edit', compact('kegiatanLuar'));
    }

    public function update(Request $request, Kegiatan $kegiatanLuar)
    {
        $this->authorize($kegiatanLuar);

        $data = $request->validate([
            'nama_kegiatan'   => 'required|string|max:255',
            'lokasi'          => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'uraian_kegiatan' => 'nullable|string',
            'status'          => 'required|in:aktif,selesai,batal',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
        ], [
            'nama_kegiatan.required' => 'Judul kegiatan wajib diisi.',
            'lokasi.required'        => 'Tempat kegiatan wajib diisi.',
            'tanggal_mulai.required' => 'Waktu kegiatan wajib diisi.',
        ]);

        $kegiatanLuar->update($data);

        return redirect()->route('kegiatan-luar.show', $kegiatanLuar)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatanLuar)
    {
        $this->authorize($kegiatanLuar);

        // Hapus semua foto fisik
        foreach ($kegiatanLuar->fotos as $foto) {
            if (File::exists(public_path($foto->file_path))) {
                File::delete(public_path($foto->file_path));
            }
        }

        $kegiatanLuar->delete();

        return redirect()->route('kegiatan-luar.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    // ── Foto Dokumentasi ──────────────────────────────────────

    public function uploadFoto(Request $request, Kegiatan $kegiatanLuar)
    {
        $this->authorize($kegiatanLuar);

        if ($kegiatanLuar->fotos()->count() >= 5) {
            return back()->with('error', 'Maksimal 5 foto dokumentasi per kegiatan.');
        }

        $request->validate([
            'foto'        => 'required|image|mimes:jpg,jpeg,png,webp|max:3072',
            'keterangan'  => 'nullable|string|max:255',
        ], [
            'foto.required' => 'Pilih foto terlebih dahulu.',
            'foto.image'    => 'File harus berupa gambar.',
            'foto.max'      => 'Ukuran foto maksimal 3MB.',
        ]);

        $file     = $request->file('foto');
        $filename = time() . '_' . str($file->getClientOriginalName())->slug() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/kegiatan-luar'), $filename);

        $urutan = $kegiatanLuar->fotos()->count() + 1;

        $kegiatanLuar->fotos()->create([
            'file_path'  => 'uploads/kegiatan-luar/' . $filename,
            'keterangan' => $request->input('keterangan'),
            'urutan'     => $urutan,
        ]);

        return back()->with('success', 'Foto berhasil ditambahkan.');
    }

    public function hapusFoto(Kegiatan $kegiatanLuar, KegiatanFoto $foto)
    {
        $this->authorize($kegiatanLuar);

        if (File::exists(public_path($foto->file_path))) {
            File::delete(public_path($foto->file_path));
        }
        $foto->delete();

        // Urutkan ulang
        $kegiatanLuar->fotos()->orderBy('urutan')->get()->each(function ($f, $i) {
            $f->update(['urutan' => $i + 1]);
        });

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    // ── Peserta Manual ────────────────────────────────────────

    public function addPeserta(Request $request, Kegiatan $kegiatanLuar)
    {
        $this->authorize($kegiatanLuar);

        $data = $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'jabatan'       => 'nullable|string|max:255',
            'asal_instansi' => 'nullable|string|max:255',
            'no_hp'         => 'required|string|max:20',
            'email'         => 'nullable|email|max:255',
        ], [
            'nama_lengkap.required' => 'Nama wajib diisi.',
            'no_hp.required'        => 'No HP wajib diisi.',
        ]);

        $data['no_hp'] = preg_replace('/[\s\-]/', '', $data['no_hp']);

        // Cek duplikat
        $exists = $kegiatanLuar->peserta()
            ->where('no_hp', $data['no_hp'])->exists();

        if ($exists) {
            return back()->with('error', 'Peserta dengan nomor HP ini sudah terdaftar.');
        }

        $kegiatanLuar->peserta()->create([
            ...$data,
            'waktu_hadir' => now(),
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function removePeserta(Kegiatan $kegiatanLuar, KegiatanPeserta $peserta)
    {
        $this->authorize($kegiatanLuar);
        $peserta->delete();
        return back()->with('success', 'Peserta berhasil dihapus.');
    }

    public function exportExcel(Kegiatan $kegiatanLuar)
    {
        $this->authorize($kegiatanLuar);

        $peserta  = $kegiatanLuar->peserta()->orderBy('created_at')->get();
        $filename = 'peserta-' . str($kegiatanLuar->nama_kegiatan)->slug() . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($peserta) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['No', 'Nama Lengkap', 'Jabatan', 'Asal Instansi', 'No HP', 'Email']);
            foreach ($peserta as $i => $p) {
                fputcsv($handle, [
                    $i + 1, $p->nama_lengkap, $p->jabatan ?? '-',
                    $p->asal_instansi ?? '-', $p->no_hp, $p->email ?? '-',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function authorize(Kegiatan $kegiatan): void
    {
        if ($kegiatan->tipe !== 'luar_ruangan') abort(404);
        if ($kegiatan->created_by !== Auth::id() && !auth()->user()->hasRole('super_admin')) {
            abort(403);
        }
    }
}
