<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Satu-satunya pintu masuk untuk mencatat pemasukan & pengeluaran, dipakai oleh
 * form web dan RekomendasiKesehatanService (untuk mengambil ringkasan) — supaya
 * angka yang dipakai untuk hitung kesehatan keuangan selalu konsisten dengan
 * yang tampil di Dashboard/Laporan.
 */
class KeuanganService
{
    public function catatPemasukan(
        float $jumlah,
        ?string $keterangan,
        string $kategori = 'lainnya',
        ?string $inputOleh = null,
        ?Carbon $tanggal = null,
    ): Pemasukan {
        if (!in_array($kategori, Pemasukan::KATEGORI, true)) {
            $kategori = 'lainnya';
        }

        return DB::transaction(function () use ($jumlah, $keterangan, $kategori, $inputOleh, $tanggal) {
            return Pemasukan::create([
                'tanggal' => ($tanggal ?? Carbon::now('Asia/Jakarta'))->toDateString(),
                'jumlah' => $jumlah,
                'keterangan' => $keterangan,
                'kategori' => $kategori,
                'input_oleh' => $inputOleh,
            ]);
        });
    }

    public function catatPengeluaran(
        float $jumlah,
        ?string $keterangan,
        string $kategori = 'lainnya',
        ?string $inputOleh = null,
        ?Carbon $tanggal = null,
    ): Pengeluaran {
        if (!in_array($kategori, Pengeluaran::KATEGORI, true)) {
            $kategori = 'lainnya';
        }

        return DB::transaction(function () use ($jumlah, $keterangan, $kategori, $inputOleh, $tanggal) {
            return Pengeluaran::create([
                'tanggal' => ($tanggal ?? Carbon::now('Asia/Jakarta'))->toDateString(),
                'jumlah' => $jumlah,
                'keterangan' => $keterangan,
                'kategori' => $kategori,
                'input_oleh' => $inputOleh,
            ]);
        });
    }

    /**
     * Ringkasan saldo untuk rentang tanggal tertentu. Dipakai Dashboard, Laporan,
     * dan RekomendasiKesehatanService — supaya angka selalu konsisten.
     */
    public function ringkasan(Carbon $dari, Carbon $sampai): array
    {
        $totalPemasukan = (float) Pemasukan::tanggal($dari->toDateString(), $sampai->toDateString())->sum('jumlah');
        $totalPengeluaran = (float) Pengeluaran::tanggal($dari->toDateString(), $sampai->toDateString())->sum('jumlah');

        return [
            'pemasukan' => $totalPemasukan,
            'pengeluaran' => $totalPengeluaran,
            'saldo' => $totalPemasukan - $totalPengeluaran,
        ];
    }
}
