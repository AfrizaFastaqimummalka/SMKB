<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Services\ExcelService;
use App\Services\KeuanganService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanController extends Controller
{
    public function __construct(
        private KeuanganService $keuanganService,
        private ExcelService $excelService,
    ) {
    }

    public function index(Request $request): View
    {
        [$dari, $sampai] = $this->rentang($request);

        $ringkasan = $this->keuanganService->ringkasan($dari, $sampai);

        $pemasukan = Pemasukan::tanggal($dari->toDateString(), $sampai->toDateString())
            ->orderByDesc('tanggal')
            ->get();

        $pengeluaran = Pengeluaran::tanggal($dari->toDateString(), $sampai->toDateString())
            ->orderByDesc('tanggal')
            ->get();

        return view('laporan.index', [
            'dari' => $dari,
            'sampai' => $sampai,
            'periode' => $request->string('periode', 'bulan')->toString(),
            'ringkasan' => $ringkasan,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
        ]);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        [$dari, $sampai] = $this->rentang($request);

        $path = $this->excelService->generateLaporanKeuangan($dari, $sampai);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    private function rentang(Request $request): array
    {
        $periode = $request->string('periode', 'bulan')->toString();
        $now = Carbon::now('Asia/Jakarta');

        if ($request->filled('dari') && $request->filled('sampai')) {
            return [Carbon::parse($request->string('dari'))->startOfDay(), Carbon::parse($request->string('sampai'))->endOfDay()];
        }

        return match ($periode) {
            'harian' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'tahunan' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }
}
