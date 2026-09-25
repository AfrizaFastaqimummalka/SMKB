<?php

namespace App\Http\Controllers;

use App\Models\RekomendasiKesehatan;
use App\Services\RekomendasiKesehatanService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekomendasiKesehatanController extends Controller
{
    public function __construct(private RekomendasiKesehatanService $service)
    {
    }

    public function index(Request $request): View
    {
        $riwayat = RekomendasiKesehatan::with('penilaianManual')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('rekomendasi.index', [
            'riwayat' => $riwayat,
            'aiTersedia' => $this->service->isTersedia(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'periode_tipe' => ['required', 'in:harian,mingguan,bulanan,tahunan'],
        ]);

        [$dari, $sampai] = $this->rentang($validated['periode_tipe']);

        $rekomendasi = $this->service->generate(
            $validated['periode_tipe'],
            $dari,
            $sampai,
            auth()->user()?->username,
        );

        if (!$rekomendasi) {
            return back()->withErrors(['ai' => 'Gagal generate rekomendasi — API key Gemini belum diisi atau sedang bermasalah. Coba lagi beberapa saat.']);
        }

        return redirect()->route('rekomendasi.show', $rekomendasi)->with('status', 'Rekomendasi kesehatan keuangan berhasil dibuat.');
    }

    public function show(RekomendasiKesehatan $rekomendasi): View
    {
        $rekomendasi->load('penilaianManual');

        return view('rekomendasi.show', compact('rekomendasi'));
    }

    private function rentang(string $periodeTipe): array
    {
        $now = Carbon::now('Asia/Jakarta');

        return match ($periodeTipe) {
            'mingguan' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'bulanan' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'tahunan' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()], // harian
        };
    }
}
