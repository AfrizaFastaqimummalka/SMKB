<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\RekomendasiKesehatan;
use Carbon\Carbon;

/**
 * Fitur inti skripsi: mengubah data pemasukan/pengeluaran periode tertentu menjadi
 * penilaian kesehatan keuangan yang GENERATIF (bukan cuma template if-else), dengan
 * skor 0-100, label 3 tingkat, ringkasan & saran berbahasa awam untuk pengguna
 * non-akuntan. Hasilnya disimpan (bukan cuma ditampilkan sekali) supaya bisa jadi
 * bahan uji akurasi di modul Uji Akurasi.
 */
class RekomendasiKesehatanService
{
    public function __construct(
        private GeminiService $gemini,
        private KeuanganService $keuanganService,
    ) {
    }

    public function isTersedia(): bool
    {
        return $this->gemini->isConfigured();
    }

    /**
     * @param  string  $periodeTipe  'harian' | 'mingguan' | 'bulanan' | 'tahunan'
     */
    public function generate(string $periodeTipe, Carbon $dari, Carbon $sampai, ?string $dibuatOleh = null): ?RekomendasiKesehatan
    {
        if (!$this->isTersedia()) {
            return null;
        }

        $ringkasanKeuangan = $this->keuanganService->ringkasan($dari, $sampai);

        $rincianPemasukan = Pemasukan::tanggal($dari->toDateString(), $sampai->toDateString())
            ->selectRaw('kategori, SUM(jumlah) as total')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => "{$row->kategori}: Rp " . number_format((float) $row->total, 0, ',', '.'))
            ->implode("\n") ?: '(tidak ada data pemasukan)';

        $rincianPengeluaran = Pengeluaran::tanggal($dari->toDateString(), $sampai->toDateString())
            ->selectRaw('kategori, SUM(jumlah) as total')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => "{$row->kategori}: Rp " . number_format((float) $row->total, 0, ',', '.'))
            ->implode("\n") ?: '(tidak ada data pengeluaran)';

        $prompt = <<<TEXT
            Tipe periode: {$periodeTipe}
            Rentang: {$dari->format('d/m/Y')} s/d {$sampai->format('d/m/Y')}
            Total pemasukan: Rp {$this->rp($ringkasanKeuangan['pemasukan'])}
            Total pengeluaran: Rp {$this->rp($ringkasanKeuangan['pengeluaran'])}
            Saldo: Rp {$this->rp($ringkasanKeuangan['saldo'])}

            Rincian pemasukan per kategori:
            {$rincianPemasukan}

            Rincian pengeluaran per kategori:
            {$rincianPengeluaran}
            TEXT;

        $hasil = $this->gemini->generateJson(
            systemInstruction: $this->systemInstruction(),
            userPrompt: $prompt,
            schema: $this->schema(),
        );

        if (!$hasil) {
            return null;
        }

        $skor = max(0, min(100, (int) round((float) ($hasil['skor'] ?? 0))));
        $label = in_array($hasil['label'] ?? null, array_keys(RekomendasiKesehatan::LABEL_TEKS), true)
            ? $hasil['label']
            : $this->labelDariSkor($skor); // fallback: turunkan label dari skor kalau AI tidak konsisten

        return RekomendasiKesehatan::create([
            'periode_tipe' => $periodeTipe,
            'tanggal_mulai' => $dari->toDateString(),
            'tanggal_selesai' => $sampai->toDateString(),
            'skor' => $skor,
            'label' => $label,
            'ringkasan' => $hasil['ringkasan'] ?? '(AI tidak memberikan ringkasan)',
            'saran' => $hasil['saran'] ?? '(AI tidak memberikan saran)',
            'raw_response' => json_encode($hasil),
            'dibuat_oleh' => $dibuatOleh,
        ]);
    }

    private function systemInstruction(): string
    {
        return <<<TEXT
            Kamu adalah asisten yang menilai KESEHATAN KEUANGAN sebuah usaha kecil-menengah di Indonesia
            (bidang penjualan buku), berdasarkan data pemasukan & pengeluaran satu periode. Penggunamu
            BUKAN akuntan — pakai bahasa sehari-hari, hindari istilah akuntansi teknis (jangan pakai kata
            seperti "likuiditas", "solvabilitas", "rasio", dsb tanpa penjelasan awam).

            Tugasmu, kembalikan HANYA JSON sesuai skema:
            - "skor": angka 0-100 menggambarkan kesehatan keuangan periode ini. Pertimbangkan: apakah
              saldo positif, seberapa besar pengeluaran dibanding pemasukan, apakah ada kategori
              pengeluaran yang mencurigakan besar.
            - "label": WAJIB konsisten dengan skor, pakai rentang ini:
              skor 70-100 -> "sehat", skor 40-69 -> "perlu_perhatian", skor 0-39 -> "kurang_sehat".
            - "ringkasan": 2-4 kalimat kondisi keuangan periode ini, bahasa sangat mudah dipahami orang
              awam yang tidak paham akuntansi.
            - "saran": 2-3 saran KONKRET dan bisa langsung dilakukan (bukan saran generik seperti
              "kurangi pengeluaran"), relevan untuk usaha penjualan buku.

            Jangan mengarang angka yang tidak ada di data. Jangan jawab di luar format JSON.
            TEXT;
    }

    private function schema(): array
    {
        return [
            'type' => 'OBJECT',
            'properties' => [
                'skor' => ['type' => 'NUMBER'],
                'label' => ['type' => 'STRING', 'enum' => ['sehat', 'perlu_perhatian', 'kurang_sehat']],
                'ringkasan' => ['type' => 'STRING'],
                'saran' => ['type' => 'STRING'],
            ],
            'required' => ['skor', 'label', 'ringkasan', 'saran'],
        ];
    }

    private function labelDariSkor(int $skor): string
    {
        return match (true) {
            $skor >= 70 => 'sehat',
            $skor >= 40 => 'perlu_perhatian',
            default => 'kurang_sehat',
        };
    }

    private function rp(float $angka): string
    {
        return number_format($angka, 0, ',', '.');
    }
}
