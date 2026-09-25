<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Services\KeuanganService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PemasukanController extends Controller
{
    public function __construct(private KeuanganService $keuanganService)
    {
    }

    public function index(Request $request): View
    {
        $pemasukan = Pemasukan::query()
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('dari')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')))
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->string('kategori')))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('pemasukan.index', ['pemasukan' => $pemasukan, 'kategoriList' => Pemasukan::KATEGORI]);
    }

    public function create(): View
    {
        return view('pemasukan.create', ['kategoriList' => Pemasukan::KATEGORI]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'kategori' => ['required', 'in:' . implode(',', Pemasukan::KATEGORI)],
            'tanggal' => ['nullable', 'date'],
        ]);

        $this->keuanganService->catatPemasukan(
            jumlah: (float) $validated['jumlah'],
            keterangan: $validated['keterangan'] ?? null,
            kategori: $validated['kategori'],
            inputOleh: auth()->user()?->username,
            tanggal: isset($validated['tanggal']) ? \Carbon\Carbon::parse($validated['tanggal']) : null,
        );

        return redirect()->route('pemasukan.index')->with('status', 'Pemasukan berhasil dicatat.');
    }

    public function destroy(Pemasukan $pemasukan): RedirectResponse
    {
        $pemasukan->delete();

        return redirect()->route('pemasukan.index')->with('status', 'Pemasukan berhasil dihapus.');
    }
}
