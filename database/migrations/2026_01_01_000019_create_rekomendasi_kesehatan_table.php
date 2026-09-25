<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Setiap kali AI menghasilkan penilaian kesehatan keuangan untuk suatu periode
     * (harian/mingguan/bulanan/tahunan), hasilnya disimpan di sini — bukan cuma
     * ditampilkan sekali lalu hilang. Ini penting untuk dua hal:
     * 1. Riwayat rekomendasi bisa dilihat ulang (bukti untuk BAB IV skripsi)
     * 2. Jadi bahan pembanding di modul Uji Akurasi (BAB III Testing) — tiap baris
     *    di sini bisa dipasangkan dengan satu penilaian manual dari pihak keuangan.
     */
    public function up(): void
    {
        if (Schema::hasTable('rekomendasi_kesehatan')) {
            return;
        }

        Schema::create('rekomendasi_kesehatan', function (Blueprint $table) {
            $table->id();
            $table->enum('periode_tipe', ['harian', 'mingguan', 'bulanan', 'tahunan']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedTinyInteger('skor'); // 0-100, dari Generative AI
            $table->enum('label', ['sehat', 'perlu_perhatian', 'kurang_sehat']); // dari Generative AI
            $table->text('ringkasan'); // narasi bahasa awam, untuk pengguna non-akuntan
            $table->text('saran'); // rekomendasi tindakan, bahasa awam
            $table->text('raw_response')->nullable(); // simpan mentah balasan Gemini untuk audit/debug
            $table->string('dibuat_oleh')->nullable(); // username web yang memicu generate
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasi_kesehatan');
    }
};
