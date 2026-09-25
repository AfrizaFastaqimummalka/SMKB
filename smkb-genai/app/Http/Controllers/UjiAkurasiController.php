<?php

namespace App\Http\Controllers;

use App\Models\PenilaianManual;
use App\Models\RekomendasiKesehatan;
use App\Services\UjiAkurasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UjiAkurasiController extends Controller
{
    public function __construct(private UjiAkurasiService $service)
    {
    }

    public function index(): View
    {
        $daftar = RekomendasiKesehatan::with('penilaianManual')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('uji-akurasi.index', [
            'daftar' => $daftar,
            'ringkasanAkurasi' => $this->service->ringkasanAkurasi(),
        ]);
    }

    public function store(Request $request, RekomendasiKesehatan $rekomendasi): RedirectResponse
    {
        if ($rekomendasi->penilaianManual) {
            return back()->withErrors(['penilaian' => 'Rekomendasi ini sudah punya penilaian manual. Hapus dulu kalau mau menilai ulang.']);
        }

        $validated = $request->validate([
            'skor_manual' => ['required', 'integer', 'min:0', 'max:100'],
            'label_manual' => ['required', 'in:sehat,perlu_perhatian,kurang_sehat'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'dinilai_oleh' => ['nullable', 'string', 'max:255'],
        ]);

        PenilaianManual::create([
            'rekomendasi_kesehatan_id' => $rekomendasi->id,
            ...$validated,
        ]);

        return redirect()->route('uji-akurasi.index')->with('status', 'Penilaian manual berhasil disimpan.');
    }

    public function destroy(PenilaianManual $penilaian): RedirectResponse
    {
        $penilaian->delete();

        return redirect()->route('uji-akurasi.index')->with('status', 'Penilaian manual dihapus.');
    }
}
