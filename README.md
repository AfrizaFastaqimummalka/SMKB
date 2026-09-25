# Panduan Penggunaan Sistem Manajemen Keuangan CV Panca Mitra Cendekia

Sistem ini adalah **Sistem Manajemen Keuangan Berbasis Generative AI** yang dirancang khusus untuk mempermudah pembukuan, pelaporan, dan analisa kesehatan keuangan secara cerdas. 

Berikut adalah panduan lengkap dari awal instalasi hingga cara menggunakan fitur-fiturnya.

---

## 🛠️ 1. Persiapan Awal (Instalasi & Menjalankan Aplikasi)

Jika proyek ini baru saja disiapkan di komputer baru, ikuti langkah-langkah berikut:

1. **Buka Terminal / Command Prompt** di dalam folder proyek (`c:\Afriza\PROJECT\SMKB\SMKB`).
2. **Install Dependensi PHP & Node.js:**
   ```bash
   composer install
   npm install
   ```
3. **Konfigurasi Environment:**
   - Salin file `.env.example` menjadi `.env` (atau langsung buka file `.env` yang sudah ada).
   - Pastikan konfigurasi database (`DB_DATABASE`, `DB_USERNAME`, dll) sudah benar.
   - Pastikan juga **API Key Gemini** (`GEMINI_API_KEY`) sudah terisi di dalam file `.env` agar fitur AI dapat berjalan!
4. **Generate App Key & Migrasi Database:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
   *(Perintah `--seed` digunakan jika Anda memiliki data awal/dummy untuk admin login).*
5. **Jalankan Server Lokal:**
   Buka 2 terminal terpisah dan jalankan:
   - Terminal 1: `php artisan serve` (Menjalankan server PHP)
   - Terminal 2: `npm run dev` (Menjalankan asset Vite / CSS)
6. **Buka Aplikasi di Browser:** Akses tautan `http://127.0.0.1:8000`

---

## 🚀 2. Panduan Penggunaan Aplikasi (Langkah demi Langkah)

Untuk mendapatkan hasil yang optimal, ikuti alur penggunaan (workflow) berikut:

### Langkah 1: Login
- Buka halaman utama, Anda akan diarahkan ke form Login.
- Masukkan *Username* / *Email* dan *Password* Admin yang telah disiapkan.

### Langkah 2: Catat Arus Kas (Pemasukan & Pengeluaran)
Pembukuan yang rapi adalah kunci agar AI bisa memberikan rekomendasi yang akurat.
- Masuk ke menu **Pemasukan**. Klik **"Tambah Pemasukan"** setiap kali ada dana masuk. Lengkapi Tanggal, Jumlah, Kategori, dan Keterangan.
- Masuk ke menu **Pengeluaran**. Klik **"Tambah Pengeluaran"** untuk mencatat seluruh biaya operasional, gaji, dsb.
- **Tips:** Anda bisa melihat grafik dan ringkasan kondisi keuangan terkini pada menu **Dashboard**.

### Langkah 3: Generate Laporan (Export Excel)
Di akhir bulan atau saat dibutuhkan, Anda dapat mencetak laporan yang terformat rapi.
- Pergi ke menu **Laporan**.
- Pilih rentang periode (misalnya Bulan Ini, atau pilih tanggal spesifik).
- Klik tombol **"Unduh Excel"**. File akan otomatis berisikan lembar kerja: *Ringkasan*, *Pemasukan*, dan *Pengeluaran* dengan logo dan warna perusahaan.

### Langkah 4: Meminta Analisis & Rekomendasi AI
Inilah fitur unggulan aplikasi ini. Setelah memiliki data keuangan yang cukup dalam satu periode:
- Pergi ke menu **Rekomendasi AI**.
- Minta AI untuk menganalisa periode tertentu (misal bulan lalu atau bulan ini).
- AI akan mengkalkulasi selisih Pemasukan dan Pengeluaran, lalu Gemini AI akan memberikan:
  - **Skor Kesehatan Keuangan (0-100)**
  - **Label:** (Sehat / Perlu Perhatian / Kurang Sehat)
  - **Ringkasan Analisa & Saran Tindakan Strategis.**

### Langkah 5: Uji Akurasi (Validasi AI vs Manual)
Untuk memastikan saran AI sejalan dengan pandangan manajemen keuangan perusahaan:
- Masuk ke menu **Uji Akurasi**.
- Di sini akan tampil daftar Rekomendasi AI yang sudah dibuat sebelumnya.
- Klik **"Nilai Manual"**. Pihak keuangan atau manajemen bisa memasukkan *Skor Manual* dan *Label Manual* menurut opini ahli mereka.
- Sistem akan menampilkan persentase kecocokan antara **AI vs Manusia**, mempermudah tim untuk menyesuaikan parameter strategi ke depannya.

---

## 💡 Tips & Trik

1. **Logo Perusahaan:** Jika Anda ingin mengubah logo yang muncul di Sidebar Web maupun di dalam Laporan Excel, cukup timpa file `logo.png` yang berada di dalam folder `public/`.
2. **Keamanan Data:** Lakukan *Backup* Database (MySQL) secara berkala pada akhir bulan untuk mencegah hilangnya data pembukuan.
3. **Keterangan Transaksi:** Biasakan mengisi "Keterangan" transaksi dengan jelas, agar jika terjadi audit atau review dari AI, konteks keuangannya dapat terbaca dengan sangat spesifik.

---

Selamat menggunakan Sistem Manajemen Keuangan Berbasis AI! ✨
