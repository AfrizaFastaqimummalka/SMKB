<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Services\KeuanganService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengeluaranController extends Controller
{
    public function __construct(private KeuanganService $keuanganService)
    {
    }

    public function index(Request $request): View
    {
        $pengeluaran = Pengeluaran::query()
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('tanggal', '>=', $request->date('dari')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal', '<=', $request->date('sampai')))
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->string('kategori')))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('pengeluaran.index', ['pengeluaran' => $pengeluaran, 'kategoriList' => Pengeluaran::KATEGORI]);
    }

    public function create(): View
    {
        return view('pengeluaran.create', ['kategoriList' => Pengeluaran::KATEGORI]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'kategori' => ['required', 'in:' . implode(',', Pengeluaran::KATEGORI)],
            'tanggal' => ['nullable', 'date'],
        ]);

        $this->keuanganService->catatPengeluaran(
            jumlah: (float) $validated['jumlah'],
            keterangan: $validated['keterangan'] ?? null,
            kategori: $validated['kategori'],
            inputOleh: auth()->user()?->username,
            tanggal: isset($validated['tanggal']) ? \Carbon\Carbon::parse($validated['tanggal']) : null,
        );

        return redirect()->route('pengeluaran.index')->with('status', 'Pengeluaran berhasil dicatat.');
    }

    public function destroy(Pengeluaran $pengeluaran): RedirectResponse
    {
        $pengeluaran->delete();

        return redirect()->route('pengeluaran.index')->with('status', 'Pengeluaran berhasil dihapus.');
    }
}
