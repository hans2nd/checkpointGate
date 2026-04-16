<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'no_polisi',
        'vendor',
        'driver',
        'tipe',
        'jenis_kendaraan',
        'waktu_penerimaan_dokumen',
        'waktu_penyerahan_dokumen',
        'jenis_barang',
        'aktivitas',
        'gate',
        'status',
        'waktu_start',
        'waktu_end',
        'durasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_penerimaan_dokumen' => 'datetime',
        'waktu_penyerahan_dokumen' => 'datetime',
        'waktu_start' => 'datetime',
        'waktu_end' => 'datetime',
        'gate' => 'string',
    ];
}
