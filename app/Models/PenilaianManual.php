<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianManual extends Model
{
    use HasFactory;

    protected $table = 'penilaian_manual';

    protected $fillable = [
        'rekomendasi_kesehatan_id',
        'skor_manual',
        'label_manual',
        'catatan',
        'dinilai_oleh',
    ];

    protected function casts(): array
    {
        return [
            'skor_manual' => 'integer',
        ];
    }

    public function rekomendasiKesehatan(): BelongsTo
    {
        return $this->belongsTo(RekomendasiKesehatan::class);
    }
}
