<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * Daftar jenis kendaraan yang valid (single source of truth).
     * Dipakai oleh form create/edit, validasi store/update, dan import Excel.
     */
    public const JENIS_KENDARAAN = [
        'L300',
        'CDE',
        'CDE-LONG',
        'CDD',
        'CDD-LONG',
        'FUSO',
        'TRONTON',
        'CONT-20FT',
        'CONT-40FT',
    ];

    protected $fillable = [
        'no_polisi',
        'driver',
        'vendor',
        'tipe',
        'jenis_kendaraan',
    ];
}
