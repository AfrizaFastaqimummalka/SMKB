<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RekomendasiKesehatan extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi_kesehatan';

    protected $fillable = [
        'periode_tipe',
        'tanggal_mulai',
        'tanggal_selesai',
        'skor',
        'label',
        'ringkasan',
        'saran',
        'raw_response',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'skor' => 'integer',
        ];
    }

    // Dipakai di badge/label tampilan supaya konsisten di semua view.
    public const LABEL_TEKS = [
        'sehat' => 'Sehat',
        'perlu_perhatian' => 'Perlu Perhatian',
        'kurang_sehat' => 'Kurang Sehat',
    ];

    public function penilaianManual(): HasOne
    {
        return $this->hasOne(PenilaianManual::class);
    }
}
