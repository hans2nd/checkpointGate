<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gate extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_gate',
        'jenis_barang',
        'jenis_kendaraan',
        'aktivitas',
    ];

    protected $casts = [
        'nomor_gate' => 'integer',
    ];
}
