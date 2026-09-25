<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';

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

    // Daftar kategori resmi yang ditawarkan di form web.
    public const KATEGORI = [
        'penjualan_buku',
        'modal',
        'lainnya',
    ];

    public function scopeTanggal($query, $dari, $sampai)
    {
        return $query->whereBetween('tanggal', [$dari, $sampai]);
    }
}
