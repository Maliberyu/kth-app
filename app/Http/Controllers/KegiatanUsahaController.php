<?php

namespace App\Http\Controllers;

use App\Models\KegiatanUsaha;
use App\Models\UsahaModal;
use App\Models\UsahaTransaksi;
use App\Models\UsahaHarian;
use App\Models\UsahaHarianFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class KegiatanUsahaController extends Controller
{
    public function index()
    {
        $usahas = KegiatanUsaha::where('created_by', Auth::id())
            ->withCount('harian')
            ->latest()
            ->paginate(10);

        return view('kegiatan.usaha.index', compact('usahas'));
    }

    public function create()
    {
        return view('kegiatan.usaha.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_usaha'      => 'required|string|max:255',
            'jenis_usaha'     => 'nullable|string|max:100',
            'deskripsi'       => 'nullable|string',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => ['nullable', 'date', function ($attr, $val, $fail) use ($request) {
                if ($val && $request->tanggal_mulai && strtotime($val) < strtotime($request->tanggal_mulai)) {
                    $fail('Tanggal selesai tidak boleh sebelum tanggal mulai.');
                }
            }],
        ], [
            'nama_usaha.required'    => 'Nama usaha wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
        ]);

        $data['status']     = 'aktif';
        $data['created_by'] = Auth::id();

        $usaha = KegiatanUsaha::create($data);

        return redirect()->route('kegiatan-usaha.show', $usaha)
            ->with('success', 'Kegiatan usaha berhasil dibuat.');
    }

    public function show(KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);

        $kegiatanUsaha->load(['modal', 'transaksi', 'harian.fotos']);

        $totalModal       = $kegiatanUsaha->modal->sum('jumlah');
        $totalPemasukan   = $kegiatanUsaha->transaksi->where('tipe', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = $kegiatanUsaha->transaksi->where('tipe', 'pengeluaran')->sum('jumlah');
        $kasTersedia      = $totalModal + $totalPemasukan - $totalPengeluaran;
        $labaRugi         = $totalPemasukan - $totalPengeluaran;

        return view('kegiatan.usaha.show', compact(
            'kegiatanUsaha', 'totalModal', 'totalPemasukan',
            'totalPengeluaran', 'kasTersedia', 'labaRugi'
        ));
    }

    public function edit(KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);
        return view('kegiatan.usaha.edit', compact('kegiatanUsaha'));
    }

    public function update(Request $request, KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);

        $data = $request->validate([
            'nama_usaha'      => 'required|string|max:255',
            'jenis_usaha'     => 'nullable|string|max:100',
            'deskripsi'       => 'nullable|string',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => ['nullable', 'date', function ($attr, $val, $fail) use ($request) {
                if ($val && $request->tanggal_mulai && strtotime($val) < strtotime($request->tanggal_mulai)) {
                    $fail('Tanggal selesai tidak boleh sebelum tanggal mulai.');
                }
            }],
            'status'          => 'required|in:aktif,selesai,tutup',
        ], [
            'nama_usaha.required'    => 'Nama usaha wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
        ]);

        $kegiatanUsaha->update($data);

        return redirect()->route('kegiatan-usaha.show', $kegiatanUsaha)
            ->with('success', 'Kegiatan usaha berhasil diperbarui.');
    }

    public function destroy(KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);

        $kegiatanUsaha->load('harian.fotos');

        foreach ($kegiatanUsaha->harian as $harian) {
            foreach ($harian->fotos as $foto) {
                if (File::exists(public_path($foto->file_path))) {
                    File::delete(public_path($foto->file_path));
                }
                $foto->delete();
            }
            $harian->delete();
        }

        $kegiatanUsaha->modal()->delete();
        $kegiatanUsaha->transaksi()->delete();
        $kegiatanUsaha->delete();

        return redirect()->route('kegiatan-usaha.index')
            ->with('success', 'Kegiatan usaha berhasil dihapus.');
    }

    // ── Modal ─────────────────────────────────────────────────

    public function addModal(Request $request, KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);

        $data = $request->validate([
            'sumber'         => 'required|string|max:100',
            'jumlah'         => 'required|integer|min:1',
            'tanggal_terima' => 'required|date',
            'keterangan'     => 'nullable|string|max:255',
        ], [
            'sumber.required'         => 'Sumber modal wajib dipilih.',
            'jumlah.required'         => 'Jumlah modal wajib diisi.',
            'tanggal_terima.required' => 'Tanggal terima wajib diisi.',
        ]);

        $data['kegiatan_usaha_id'] = $kegiatanUsaha->id;
        UsahaModal::create($data);

        return redirect()->route('kegiatan-usaha.show', $kegiatanUsaha)
            ->with('success', 'Modal berhasil ditambahkan.')
            ->with('active_tab', 'modal');
    }

    public function removeModal(KegiatanUsaha $kegiatanUsaha, UsahaModal $modal)
    {
        $this->authorize($kegiatanUsaha);
        $modal->delete();

        return redirect()->route('kegiatan-usaha.show', $kegiatanUsaha)
            ->with('success', 'Modal berhasil dihapus.')
            ->with('active_tab', 'modal');
    }

    // ── Transaksi ─────────────────────────────────────────────

    public function addTransaksi(Request $request, KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);

        $data = $request->validate([
            'tipe'       => 'required|in:pemasukan,pengeluaran',
            'kategori'   => 'required|string|max:100',
            'jumlah'     => 'required|integer|min:1',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'tipe.required'     => 'Tipe transaksi wajib dipilih.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'jumlah.required'   => 'Jumlah wajib diisi.',
            'tanggal.required'  => 'Tanggal wajib diisi.',
        ]);

        $data['kegiatan_usaha_id'] = $kegiatanUsaha->id;
        UsahaTransaksi::create($data);

        return redirect()->route('kegiatan-usaha.show', $kegiatanUsaha)
            ->with('success', 'Transaksi berhasil ditambahkan.')
            ->with('active_tab', 'transaksi');
    }

    public function removeTransaksi(KegiatanUsaha $kegiatanUsaha, UsahaTransaksi $transaksi)
    {
        $this->authorize($kegiatanUsaha);
        $transaksi->delete();

        return redirect()->route('kegiatan-usaha.show', $kegiatanUsaha)
            ->with('success', 'Transaksi berhasil dihapus.')
            ->with('active_tab', 'transaksi');
    }

    // ── Harian ────────────────────────────────────────────────

    public function addHarian(Request $request, KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);

        $data = $request->validate([
            'tanggal' => 'required|date',
            'judul'   => 'required|string|max:255',
            'isi'     => 'required|string',
            'kondisi' => 'nullable|in:cerah,berawan,mendung,hujan',
            'fotos'   => 'nullable|array|max:3',
            'fotos.*' => 'image|mimes:jpg,jpeg,png,webp|max:3072',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'judul.required'   => 'Judul wajib diisi.',
            'isi.required'     => 'Isi catatan wajib diisi.',
            'fotos.max'        => 'Maksimal 3 foto per catatan.',
            'fotos.*.image'    => 'File harus berupa gambar.',
            'fotos.*.max'      => 'Ukuran foto maksimal 3MB.',
        ]);

        $harian = $kegiatanUsaha->harian()->create([
            'tanggal' => $data['tanggal'],
            'judul'   => $data['judul'],
            'isi'     => $data['isi'],
            'kondisi' => $data['kondisi'] ?? null,
        ]);

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $i => $file) {
                $filename = time() . '_' . $i . '_' . str($file->getClientOriginalName())->slug() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/usaha-harian'), $filename);
                $harian->fotos()->create([
                    'file_path' => 'uploads/usaha-harian/' . $filename,
                    'urutan'    => $i + 1,
                ]);
            }
        }

        return redirect()->route('kegiatan-usaha.show', $kegiatanUsaha)
            ->with('success', 'Catatan harian berhasil ditambahkan.')
            ->with('active_tab', 'harian');
    }

    public function removeHarian(KegiatanUsaha $kegiatanUsaha, UsahaHarian $harian)
    {
        $this->authorize($kegiatanUsaha);

        $harian->load('fotos');
        foreach ($harian->fotos as $foto) {
            if (File::exists(public_path($foto->file_path))) {
                File::delete(public_path($foto->file_path));
            }
            $foto->delete();
        }
        $harian->delete();

        return redirect()->route('kegiatan-usaha.show', $kegiatanUsaha)
            ->with('success', 'Catatan harian berhasil dihapus.')
            ->with('active_tab', 'harian');
    }

    // ── QR Transparansi ───────────────────────────────────

    public function generateQr(KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);
        if (!$kegiatanUsaha->qr_token) {
            $kegiatanUsaha->update(['qr_token' => \Illuminate\Support\Str::random(32), 'qr_aktif' => true]);
        }
        return back()->with('success', 'QR berhasil digenerate.');
    }

    public function toggleQr(KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);
        $kegiatanUsaha->update(['qr_aktif' => !$kegiatanUsaha->qr_aktif]);
        $status = $kegiatanUsaha->qr_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "QR transparansi berhasil {$status}.");
    }

    // ── Print / PDF ────────────────────────────────────────

    public function printHarian(KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);
        $kegiatanUsaha->load('harian.fotos');
        return view('kegiatan.usaha.print-harian', compact('kegiatanUsaha'));
    }

    public function printTransaksi(KegiatanUsaha $kegiatanUsaha)
    {
        $this->authorize($kegiatanUsaha);
        $kegiatanUsaha->load(['modal', 'transaksi']);

        $totalModal       = $kegiatanUsaha->modal->sum('jumlah');
        $totalPemasukan   = $kegiatanUsaha->transaksi->where('tipe', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = $kegiatanUsaha->transaksi->where('tipe', 'pengeluaran')->sum('jumlah');
        $kasTersedia      = $totalModal + $totalPemasukan - $totalPengeluaran;
        $labaRugi         = $totalPemasukan - $totalPengeluaran;

        return view('kegiatan.usaha.print-transaksi', compact(
            'kegiatanUsaha', 'totalModal', 'totalPemasukan',
            'totalPengeluaran', 'kasTersedia', 'labaRugi'
        ));
    }

    private function authorize(KegiatanUsaha $usaha): void
    {
        if ($usaha->created_by !== Auth::id() && !auth()->user()->hasRole('super_admin')) {
            abort(403);
        }
    }
}
