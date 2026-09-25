<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService
{
    public function generateLaporanKeuangan(Carbon $dari, Carbon $sampai): string
    {
        $pemasukan = Pemasukan::tanggal($dari->toDateString(), $sampai->toDateString())
            ->orderBy('tanggal')
            ->get();

        $pengeluaran = Pengeluaran::tanggal($dari->toDateString(), $sampai->toDateString())
            ->orderBy('tanggal')
            ->get();

        $spreadsheet = new Spreadsheet();

        $this->isiSheetPemasukan($spreadsheet->getActiveSheet(), $pemasukan);

        $sheetPengeluaran = $spreadsheet->createSheet();
        $sheetPengeluaran->setTitle('Pengeluaran');
        $this->isiSheetPengeluaran($sheetPengeluaran, $pengeluaran);

        $sheetRingkasan = $spreadsheet->createSheet();
        $sheetRingkasan->setTitle('Ringkasan');
        $totalPemasukan = (float) $pemasukan->sum('jumlah');
        $totalPengeluaran = (float) $pengeluaran->sum('jumlah');
        $sheetRingkasan->fromArray([
            ['Periode', $dari->format('d/m/Y') . ' s/d ' . $sampai->format('d/m/Y')],
            ['Total Pemasukan', $totalPemasukan],
            ['Total Pengeluaran', $totalPengeluaran],
            ['Saldo', $totalPemasukan - $totalPengeluaran],
        ], null, 'A1');

        $spreadsheet->setActiveSheetIndex(0);

        $namaFile = 'laporan-keuangan-' . $dari->format('Ymd') . '-' . $sampai->format('Ymd') . '.xlsx';
        $direktori = storage_path('app/laporan');
        if (!is_dir($direktori)) {
            mkdir($direktori, 0755, true);
        }
        $path = $direktori . DIRECTORY_SEPARATOR . $namaFile;

        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }

    private function isiSheetPemasukan($sheet, $pemasukan): void
    {
        $sheet->setTitle('Pemasukan');
        $sheet->fromArray(['Tanggal', 'Jumlah', 'Kategori', 'Keterangan', 'Input Oleh'], null, 'A1');

        $baris = 2;
        foreach ($pemasukan as $item) {
            $sheet->fromArray([
                $item->tanggal->format('d/m/Y'),
                (float) $item->jumlah,
                $item->kategori,
                $item->keterangan,
                $item->input_oleh,
            ], null, 'A' . $baris);
            $baris++;
        }
    }

    private function isiSheetPengeluaran($sheet, $pengeluaran): void
    {
        $sheet->fromArray(['Tanggal', 'Jumlah', 'Kategori', 'Keterangan', 'Input Oleh'], null, 'A1');

        $baris = 2;
        foreach ($pengeluaran as $item) {
            $sheet->fromArray([
                $item->tanggal->format('d/m/Y'),
                (float) $item->jumlah,
                $item->kategori,
                $item->keterangan,
                $item->input_oleh,
            ], null, 'A' . $baris);
            $baris++;
        }
    }
}
