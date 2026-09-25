<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Instrumen uji akurasi (BAB III Testing): untuk satu rekomendasi_kesehatan yang
     * dihasilkan AI, pihak keuangan (manusia) menilai secara manual periode yang sama
     * memakai skor & label yang sama formatnya. Perbandingan AI vs manual di sini yang
     * jadi dasar hitung persentase akurasi.
     *
     * Relasi 1-1 (unique per rekomendasi_kesehatan_id) — satu rekomendasi AI cuma boleh
     * punya satu penilaian manual pembanding.
     */
    public function up(): void
    {
        if (Schema::hasTable('penilaian_manual')) {
            return;
        }

        Schema::create('penilaian_manual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekomendasi_kesehatan_id')->unique()
                ->constrained('rekomendasi_kesehatan')->cascadeOnDelete();
            $table->unsignedTinyInteger('skor_manual'); // 0-100, dinilai manusia
            $table->enum('label_manual', ['sehat', 'perlu_perhatian', 'kurang_sehat']);
            $table->text('catatan')->nullable(); // alasan/pertimbangan penilai, opsional
            $table->string('dinilai_oleh')->nullable(); // nama pihak keuangan yang menilai
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_manual');
    }
};
