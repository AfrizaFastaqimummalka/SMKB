<?php

namespace App\Services;

use App\Models\RekomendasiKesehatan;

/**
 * Instrumen Uji Akurasi (BAB III Testing): membandingkan skor & label yang dihasilkan
 * AI dengan penilaian manual pihak keuangan, untuk rekomendasi yang sudah punya
 * pasangan penilaian manual.
 */
class UjiAkurasiService
{
    /**
     * @return array{
     *   total_dinilai: int,
     *   label_cocok: int,
     *   persentase_kecocokan_label: float,
     *   rata_rata_selisih_skor: float,
     * }
     */
    public function ringkasanAkurasi(): array
    {
        $data = RekomendasiKesehatan::query()
            ->whereHas('penilaianManual')
            ->with('penilaianManual')
            ->get();

        $totalDinilai = $data->count();

        if ($totalDinilai === 0) {
            return [
                'total_dinilai' => 0,
                'label_cocok' => 0,
                'persentase_kecocokan_label' => 0.0,
                'rata_rata_selisih_skor' => 0.0,
            ];
        }

        $labelCocok = $data->filter(fn ($r) => $r->label === $r->penilaianManual->label_manual)->count();
        $totalSelisihSkor = $data->sum(fn ($r) => abs($r->skor - $r->penilaianManual->skor_manual));

        return [
            'total_dinilai' => $totalDinilai,
            'label_cocok' => $labelCocok,
            'persentase_kecocokan_label' => round(($labelCocok / $totalDinilai) * 100, 1),
            'rata_rata_selisih_skor' => round($totalSelisihSkor / $totalDinilai, 1),
        ];
    }
}
