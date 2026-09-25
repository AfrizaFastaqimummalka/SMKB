<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'tanggal',
        'jumlah',
        'keterangan',
        'kategori',
        'input_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jumlah' => 'decimal:0',
        ];
    }

    public const KATEGORI = [
        'operasional',
        'cetak_produksi',
        'gaji',
        'listrik',
        'sewa_gudang',
        'lainnya',
    ];

    public function scopeTanggal($query, $dari, $sampai)
    {
        return $query->whereBetween('tanggal', [$dari, $sampai]);
    }
}
