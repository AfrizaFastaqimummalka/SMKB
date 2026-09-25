<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\RekomendasiKesehatan;
use App\Services\KeuanganService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private KeuanganService $keuanganService)
    {
    }

    public function index(Request $request): View
    {
        $filter = $request->string('filter', 'hari')->toString(); // hari|bulan|tahun
        [$dari, $sampai] = $this->rentangTanggal($filter);

        $ringkasan = $this->keuanganService->ringkasan($dari, $sampai);

        $jumlahCatatan = Pemasukan::whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])->count()
            + Pengeluaran::whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])->count();

        // Grafik 7 hari terakhir: pemasukan vs pengeluaran per hari.
        $tujuhHari = collect(range(6, 0))->map(function (int $i) {
            $tanggal = Carbon::now('Asia/Jakarta')->subDays($i);

            return [
                'tanggal' => $tanggal->format('d M'),
                'pemasukan' => (float) Pemasukan::whereDate('tanggal', $tanggal->toDateString())->sum('jumlah'),
                'pengeluaran' => (float) Pengeluaran::whereDate('tanggal', $tanggal->toDateString())->sum('jumlah'),
            ];
        });

        $aktivitasTerbaru = Pemasukan::orderByDesc('created_at')->limit(8)->get()
            ->map(fn ($p) => ['jenis' => 'pemasukan', 'jumlah' => $p->jumlah, 'kategori' => $p->kategori, 'keterangan' => $p->keterangan, 'tanggal' => $p->tanggal, 'created_at' => $p->created_at])
            ->concat(
                Pengeluaran::orderByDesc('created_at')->limit(8)->get()
                    ->map(fn ($p) => ['jenis' => 'pengeluaran', 'jumlah' => $p->jumlah, 'kategori' => $p->kategori, 'keterangan' => $p->keterangan, 'tanggal' => $p->tanggal, 'created_at' => $p->created_at])
            )
            ->sortByDesc('created_at')
            ->take(8)
            ->values();

        $rekomendasiTerbaru = RekomendasiKesehatan::orderByDesc('created_at')->first();

        return view('dashboard.index', [
            'filter' => $filter,
            'ringkasan' => $ringkasan,
            'jumlahCatatan' => $jumlahCatatan,
            'grafik7Hari' => $tujuhHari,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'rekomendasiTerbaru' => $rekomendasiTerbaru,
        ]);
    }

    private function rentangTanggal(string $filter): array
    {
        $now = Carbon::now('Asia/Jakarta');

        return match ($filter) {
            'bulan' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'tahun' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }
}
