<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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

        // Sheet 1: Ringkasan
        $this->isiSheetRingkasan($spreadsheet->getActiveSheet(), $dari, $sampai, $pemasukan, $pengeluaran);

        // Sheet 2: Pemasukan
        $sheetPemasukan = $spreadsheet->createSheet();
        $this->isiSheetPemasukan($sheetPemasukan, $pemasukan);

        // Sheet 3: Pengeluaran
        $sheetPengeluaran = $spreadsheet->createSheet();
        $this->isiSheetPengeluaran($sheetPengeluaran, $pengeluaran);

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

    private function isiSheetRingkasan($sheet, Carbon $dari, Carbon $sampai, $pemasukan, $pengeluaran): void
    {
        $sheet->setTitle('Ringkasan');
        $totalPemasukan = (float) $pemasukan->sum('jumlah');
        $totalPengeluaran = (float) $pengeluaran->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Logo
        $logoPath = public_path('logo.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('A1');
            $drawing->setHeight(60);
            $drawing->setWorksheet($sheet);
            
            // Adjust row heights to fit logo
            $sheet->getRowDimension(1)->setRowHeight(15);
            $sheet->getRowDimension(2)->setRowHeight(20);
            $sheet->getRowDimension(3)->setRowHeight(25);
        }

        // Title
        $sheet->setCellValue('B2', 'LAPORAN KEUANGAN CV PANCA MITRA CENDEKIA');
        $sheet->mergeCells('B2:C2');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('FF0F766E');
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Subtitle (Period)
        $periode = '';
        if ($dari->format('Y-m') === $sampai->format('Y-m')) {
            $periode = $dari->translatedFormat('F Y');
        } else {
            $periode = $dari->format('d/m/Y') . ' - ' . $sampai->format('d/m/Y');
        }
        $sheet->setCellValue('B3', $periode);
        $sheet->mergeCells('B3:C3');
        $sheet->getStyle('B3')->getFont()->getColor()->setARGB('FF6B7280');
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Table
        $sheet->setCellValue('B5', 'Total Pemasukan');
        $sheet->setCellValue('C5', $totalPemasukan);
        $sheet->setCellValue('B6', 'Total Pengeluaran');
        $sheet->setCellValue('C6', $totalPengeluaran);
        $sheet->setCellValue('B7', 'Saldo Bersih');
        $sheet->setCellValue('C7', $saldo);

        // Styling Table
        $sheet->getStyle('B5:B7')->getFont()->setBold(true);
        $sheet->getStyle('C5')->getFont()->setBold(true)->getColor()->setARGB('FF0F766E'); // Green
        $sheet->getStyle('C6')->getFont()->setBold(true)->getColor()->setARGB('FFDC2626'); // Red
        $sheet->getStyle('C7')->getFont()->setBold(true)->getColor()->setARGB('FF0F766E'); // Green

        // Format Currency
        $sheet->getStyle('C5:C7')->getNumberFormat()->setFormatCode('"Rp "#,##0');

        // Background & Borders for Table
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF115E59'], // Dark green border
                ],
            ],
        ];
        $sheet->getStyle('B5:C7')->applyFromArray($styleArray);
        
        $sheet->getStyle('B5:C5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0FDF4'); // Light green
        $sheet->getStyle('B6:C6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF1F2'); // Light red
        $sheet->getStyle('B7:C7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0FDF4'); // Light green

        $sheet->getColumnDimension('A')->setWidth(12); // Give space for logo
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
    }

    private function isiSheetPemasukan($sheet, $pemasukan): void
    {
        $sheet->setTitle('Pemasukan');
        $this->buildTransactionSheet($sheet, $pemasukan, 'FF0F766E'); // Dark Green
    }

    private function isiSheetPengeluaran($sheet, $pengeluaran): void
    {
        $sheet->setTitle('Pengeluaran');
        $this->buildTransactionSheet($sheet, $pengeluaran, 'FF7F1D1D'); // Dark Red
    }

    private function buildTransactionSheet($sheet, $data, $headerColor): void
    {
        // Headers
        $sheet->fromArray(['No', 'Tanggal', 'Kategori', 'Keterangan', 'Jumlah (Rp)'], null, 'A1');

        $baris = 2;
        $total = 0;
        foreach ($data as $index => $item) {
            $sheet->fromArray([
                $index + 1,
                $item->tanggal->format('Y-m-d'),
                $item->kategori,
                $item->keterangan ?: '-',
                (float) $item->jumlah,
            ], null, 'A' . $baris);
            $total += (float) $item->jumlah;
            $baris++;
        }

        // Total Row
        $sheet->setCellValue('A' . $baris, 'TOTAL');
        $sheet->mergeCells('A' . $baris . ':D' . $baris);
        $sheet->setCellValue('E' . $baris, $total);
        
        // Alignment
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:A' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $baris)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        // Header and Total Row Styling (Background & Font Color)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => $headerColor],
            ],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getStyle('A' . $baris . ':E' . $baris)->applyFromArray($headerStyle);

        // Borders
        $sheet->getStyle('A1:E' . $baris)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF9CA3AF'],
                ],
            ],
        ]);

        // Auto-size
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format Number for Column E
        $sheet->getStyle('E2:E' . $baris)->getNumberFormat()->setFormatCode('#,##0');
    }
}
