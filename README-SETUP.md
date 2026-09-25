# Setup — Sistem Keuangan Perusahaan Buku

Semua kode di folder ini (`app/`, `database/migrations/`, `routes/`, `resources/views/`,
`config/`) dirancang untuk di-**overlay** ke instalasi Laravel 13 kosong. Bagian
scaffolding project & composer install dilakukan sendiri sesuai kesepakatan.

## 1. Scaffold project & overlay

```bash
composer create-project laravel/laravel:^13.0 nama-project
cd nama-project

# Salin/timpa folder-folder ini dari hasil generate ke project barumu:
#   app/            -> app/
#   database/migrations/  -> database/migrations/
#   database/seeders/     -> database/seeders/
#   routes/web.php  -> routes/web.php (TIMPA, bukan digabung manual)
#   resources/views/ -> resources/views/
#   config/gemini.php   -> config/gemini.php
#   config/telegram.php -> config/telegram.php
```

## 2. Install dependency tambahan

```bash
composer require phpoffice/phpspreadsheet
```

Dependency lain (Carbon, Http client, dsb) sudah bawaan Laravel — tidak perlu instal tambahan.

## 3. Konfigurasi `.env`

```env
DB_CONNECTION=pgsql
DB_HOST=<host-neon-anda>
DB_PORT=5432
DB_DATABASE=<nama-database>
DB_USERNAME=<username>
DB_PASSWORD=<password>

TELEGRAM_BOT_TOKEN=dummy-telegram-bot-token
GEMINI_API_KEY=dummy-gemini-api-key
GEMINI_MODEL=gemini-3.5-flash
```

### Patch `sslmode=require` untuk Neon

Neon mewajibkan koneksi SSL. Di `config/database.php`, connection `pgsql`, tambahkan opsi berikut
(sesuaikan dengan cara Neon memberi connection string — kalau Neon memberi `DATABASE_URL` lengkap,
pastikan ada `?sslmode=require` di URL-nya; kalau lewat variabel terpisah seperti di atas, tambahkan
key `sslmode` di array connection):

```php
'pgsql' => [
    // ...opsi bawaan Laravel lainnya tetap...
    'sslmode' => 'require',
],
```

## 4. Kecualikan webhook Telegram dari verifikasi CSRF

Endpoint `/webhook/telegram` dipanggil oleh Telegram (bukan browser), jadi tidak membawa CSRF
token. Di `bootstrap/app.php` (Laravel 13), tambahkan pengecualian:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        'webhook/telegram',
    ]);
})
```

## 5. Migrate & seed

```bash
php artisan migrate
php artisan db:seed
```

Login pertama: **username `admin`**, **password `ubah-password-ini`** — segera ganti setelah login.

## 6. Aktivasi Telegram (paling akhir)

1. Buka Telegram, chat ke **@BotFather**, buat bot baru (`/newbot`), simpan token yang diberikan.
2. Isi `.env`: `TELEGRAM_BOT_TOKEN=<token asli>`.
3. Daftarkan chat_id Anda sendiri sebagai admin (kirim pesan apa saja ke bot dulu — chat_id
   biasanya terlihat lewat log, atau pakai https://api.telegram.org/bot<token>/getUpdates
   setelah kirim 1 pesan ke bot untuk melihat chat_id Anda):
   ```bash
   php artisan telegram:admin 123456789 --nama="Nama Anda"
   ```
4. Deploy aplikasi ke URL publik HTTPS, lalu daftarkan webhook:
   ```bash
   curl "https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://domain-anda.com/webhook/telegram"
   ```
5. Test kirim `/help` ke bot dari akun yang sudah didaftarkan sebagai admin.

(Opsional tapi disarankan untuk produksi) isi `TELEGRAM_WEBHOOK_SECRET` di `.env` dengan string
acak, lalu sertakan `&secret_token=<nilai-yang-sama>` saat memanggil `setWebhook` — supaya endpoint
webhook menolak request yang bukan dari Telegram.

## 7. Aktivasi Gemini AI (paling akhir, gratis)

1. Buka https://aistudio.google.com, login dengan akun Google.
2. Klik "Get API key" → "Create API key" (tanpa kartu kredit, tier gratis).
3. Isi `.env`: `GEMINI_API_KEY=<key asli>`.
4. Tidak perlu langkah lain — begitu key diisi, tombol "Buat Analisis" di halaman Laporan
   dan command `/saran` serta parsing pesan bebas di bot Telegram otomatis aktif.

Catatan: tier gratis dibatasi rate limit harian (cukup untuk skala UMKM). Kalau limit tercapai,
sistem tetap jalan normal — fitur AI cuma menampilkan pesan "coba lagi nanti", tidak mengganggu
modul lain.

## Ringkasan command bot Telegram

| Command | Fungsi |
|---|---|
| `/pemasukan <jumlah> <keterangan>` | Catat pemasukan manual |
| `/pengeluaran <jumlah> <keterangan>` | Catat pengeluaran manual |
| `/saldo` | Ringkasan pemasukan/pengeluaran/saldo hari ini |
| `/laporan hari\|bulan [excel]` | Ringkasan teks, atau kirim file Excel |
| `/saran` | Analisis & saran keuangan AI (bulan berjalan) |
| `/help` | Bantuan |
| *(pesan bebas)* | Ditafsirkan otomatis oleh AI jadi pemasukan/pengeluaran |
