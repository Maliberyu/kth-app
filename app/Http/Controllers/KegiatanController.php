<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\KegiatanPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $tipe = $request->get('tipe', 'dalam_ruangan');

        $kegiatans = Kegiatan::where('tipe', $tipe)
            ->where('created_by', Auth::id())
            ->withCount('peserta')
            ->latest()
            ->paginate(10);

        return view('kegiatan.index', compact('kegiatans', 'tipe'));
    }

    public function create()
    {
        return view('kegiatan.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kegiatan'   => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'lokasi'          => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => [
                'nullable', 'date',
                function ($attr, $value, $fail) use ($request) {
                    $mulai   = $request->input('tanggal_mulai');
                    if ($mulai && $value && strtotime($value) < strtotime($mulai)) {
                        $fail('Tanggal selesai tidak boleh sebelum tanggal mulai.');
                    }
                },
            ],
            'tipe'            => 'required|in:dalam_ruangan,luar_ruangan',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'lokasi.required'        => 'Lokasi wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'foto.image'             => 'File harus berupa gambar.',
            'foto.max'               => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . str($file->getClientOriginalName())->slug() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kegiatan'), $filename);
            $data['foto'] = 'uploads/kegiatan/' . $filename;
        }

        $data['created_by'] = Auth::id();
        $data['status']     = 'aktif';

        $kegiatan = Kegiatan::create($data);

        return redirect()->route('kegiatan.show', $kegiatan)
            ->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);

        $peserta = $kegiatan->peserta()->latest('waktu_hadir')->get();

        $stats = [
            'total'      => $peserta->count(),
            'hari_ini'   => $peserta->filter(fn($p) => $p->waktu_hadir->isToday())->count(),
            'instansi'   => $peserta->groupBy('asal_instansi')->count(),
        ];

        return view('kegiatan.show', compact('kegiatan', 'peserta', 'stats'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);
        return view('kegiatan.edit', compact('kegiatan'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);

        $data = $request->validate([
            'nama_kegiatan'   => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'lokasi'          => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => [
                'nullable', 'date',
                function ($attr, $value, $fail) use ($request) {
                    $mulai = $request->input('tanggal_mulai');
                    if ($mulai && $value && strtotime($value) < strtotime($mulai)) {
                        $fail('Tanggal selesai tidak boleh sebelum tanggal mulai.');
                    }
                },
            ],
            'tipe'            => 'required|in:dalam_ruangan,luar_ruangan',
            'status'          => 'required|in:aktif,selesai,batal',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'lokasi.required'        => 'Lokasi wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'foto.image'             => 'File harus berupa gambar.',
            'foto.max'               => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($kegiatan->foto && File::exists(public_path($kegiatan->foto))) {
                File::delete(public_path($kegiatan->foto));
            }
            $file = $request->file('foto');
            $filename = time() . '_' . str($file->getClientOriginalName())->slug() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/kegiatan'), $filename);
            $data['foto'] = 'uploads/kegiatan/' . $filename;
        }

        if ($request->boolean('hapus_foto') && $kegiatan->foto) {
            if (File::exists(public_path($kegiatan->foto))) {
                File::delete(public_path($kegiatan->foto));
            }
            $data['foto'] = null;
        }

        $kegiatan->update($data);

        return redirect()->route('kegiatan.show', $kegiatan)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);
        $kegiatan->delete();

        return redirect()->route('kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function exportExcel(Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);

        $peserta = $kegiatan->peserta()->latest('waktu_hadir')->get();
        $filename = 'peserta-' . str($kegiatan->nama_kegiatan)->slug() . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($peserta) {
            $handle = fopen('php://output', 'w');
            // BOM untuk Excel agar UTF-8 terbaca benar
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['No', 'Nama Lengkap', 'Jabatan', 'Asal Instansi', 'Email', 'No HP', 'Waktu Hadir']);

            foreach ($peserta as $i => $p) {
                fputcsv($handle, [
                    $i + 1,
                    $p->nama_lengkap,
                    $p->jabatan ?? '-',
                    $p->asal_instansi ?? '-',
                    $p->email ?? '-',
                    $p->no_hp,
                    $p->waktu_hadir->format('d/m/Y H:i'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);

        $peserta = $kegiatan->peserta()->latest('waktu_hadir')->get();
        return view('kegiatan.export_pdf', compact('kegiatan', 'peserta'));
    }

    public function uploadFoto(Request $request, Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);

        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto.required' => 'Pilih foto terlebih dahulu.',
            'foto.image'    => 'File harus berupa gambar.',
            'foto.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        // Hapus foto lama jika ada
        if ($kegiatan->foto && File::exists(public_path($kegiatan->foto))) {
            File::delete(public_path($kegiatan->foto));
        }

        $file     = $request->file('foto');
        $filename = time() . '_' . str($file->getClientOriginalName())->slug() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/kegiatan'), $filename);

        $kegiatan->update(['foto' => 'uploads/kegiatan/' . $filename]);

        return back()->with('success', 'Foto berhasil diupload.');
    }

    public function hapusFoto(Kegiatan $kegiatan)
    {
        $this->authorizeKegiatan($kegiatan);

        if ($kegiatan->foto && File::exists(public_path($kegiatan->foto))) {
            File::delete(public_path($kegiatan->foto));
        }

        $kegiatan->update(['foto' => null]);

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    private function authorizeKegiatan(Kegiatan $kegiatan): void
    {
        if ($kegiatan->created_by !== Auth::id() && !auth()->user()->hasRole('super_admin')) {
            abort(403);
        }
    }
}
