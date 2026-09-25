# Pivot Skripsi — Generative AI pada Aplikasi Sistem Keuangan (Metode Agile)

Studi kasus: **Panca Mitra Cendikia**. Sistem dibangun ulang mengikuti arah baru:
Telegram dihapus total, AI bukan lagi cuma parsing input tapi **menghasilkan
rekomendasi kesehatan keuangan** (skor 0-100 + label 3 tingkat + ringkasan & saran
bahasa awam) — plus modul **Uji Akurasi** untuk membandingkan hasil AI dengan
penilaian manual pihak keuangan (instrumen BAB III Testing).

## Modul yang ada sekarang

1. **Pemasukan** & **Pengeluaran** — pencatatan transaksi, sama seperti sebelumnya
   (kolom `sumber` sudah dihapus karena tidak relevan lagi tanpa Telegram)
2. **Dashboard** — ringkasan keuangan + grafik 7 hari + widget rekomendasi kesehatan terbaru
3. **Rekomendasi Kesehatan Keuangan** *(fitur inti skripsi)* — generate penilaian AI per
   periode (harian/mingguan/bulanan/tahunan), tersimpan sebagai riwayat
4. **Uji Akurasi** *(instrumen BAB III Testing)* — input penilaian manual untuk tiap
   rekomendasi AI, sistem hitung otomatis: persentase kecocokan label + rata-rata
   selisih skor
5. **Laporan** — filter periode + export Excel (fitur lama, dipertahankan karena masih berguna)

## Cara pakai (project baru dari nol)

Kalau ini scaffold Laravel baru (belum ada project sebelumnya):

```bash
composer create-project laravel/laravel:^13.0 nama-project
cd nama-project
composer require phpoffice/phpspreadsheet
```

Lalu overlay semua folder di zip ini (`app/`, `database/`, `routes/web.php`,
`resources/views/`, `config/gemini.php`) ke project itu — sama seperti overlay
sebelumnya.

## Cara pakai (lanjut dari project SMKB yang sudah ada)

Karena ini perubahan besar (skema database berubah total: tabel `pelanggan`,
`transaksi_penjualan`, `telegram_subscriber`, `ai_log` semua hilang; tabel `pemasukan`
& `pengeluaran` kehilangan kolom `sumber`; ada 2 tabel baru `rekomendasi_kesehatan`
& `penilaian_manual`) — cara paling bersih:

1. **Backup dulu** kalau ada data yang mau disimpan (`.env`, atau export manual)
2. **Hapus folder `app/`, `database/migrations/`, `routes/web.php`,
   `resources/views/` yang lama**, ganti total dengan isi zip ini (bukan overlay
   sebagian — supaya tidak ada file sisa dari arsitektur Telegram yang lama)
3. Konfigurasi `.env` tetap sama (Neon DB non-pooler, `GEMINI_API_KEY`) — **hapus**
   baris `TELEGRAM_BOT_TOKEN` dan `TELEGRAM_*` lainnya, sudah tidak dipakai
4. `php artisan migrate:fresh --seed` (destruktif — pastikan sudah tidak butuh data lama)
5. Hapus juga `config/telegram.php` dan `bootstrap/app.php` bagian
   `validateCsrfTokens(except: ['webhook/telegram'])` yang sudah tidak relevan (boleh
   dibiarkan juga, tidak error, cuma jadi dead code)

## Yang perlu disiapkan untuk BAB III Testing

Setelah sistem jalan dan ada beberapa transaksi pemasukan/pengeluaran tercatat:
1. Generate rekomendasi AI di halaman **Rekomendasi Kesehatan** untuk beberapa periode
2. Minta pihak keuangan Panca Mitra Cendikia menilai manual periode yang sama di
   halaman **Uji Akurasi** (skor 0-100 + label + catatan alasan)
3. Statistik "Kecocokan Label" dan "Rata-rata Selisih Skor" di halaman itu langsung
   jadi angka yang bisa dipakai di BAB III/BAB IV skripsi

## Catatan desain

- Skor & label AI dipaksa konsisten lewat system instruction ke Gemini (skor 70-100 =
  sehat, 40-69 = perlu perhatian, 0-39 = kurang sehat) — kalau AI tetap tidak konsisten,
  ada fallback di kode yang menurunkan label dari skor
- Setiap generate rekomendasi disimpan permanen (bukan cuma tampil sekali) — supaya
  ada jejak riwayat untuk screenshot BAB IV dan bahan Uji Akurasi
- API key Gemini masih pola dummy-dulu seperti sebelumnya — isi `GEMINI_API_KEY` di
  `.env` kapan saja, tidak ada langkah tambahan setelah itu
