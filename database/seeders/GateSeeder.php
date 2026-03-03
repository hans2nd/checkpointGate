<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gate;

class GateSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];

        // FROZEN INBOUND - L300, CDE, CDE-LONG, CDD, CDD-LONG (Gate 1-10)
        foreach (['L300', 'CDE', 'CDE-LONG', 'CDD', 'CDD-LONG'] as $kendaraan) {
            for ($g = 1; $g <= 10; $g++) {
                $data[] = ['nomor_gate' => $g, 'jenis_barang' => 'FROZEN', 'jenis_kendaraan' => $kendaraan, 'aktivitas' => 'INBOUND'];
            }
        }

        // FROZEN INBOUND - FUSO, TRONTON, CONT-20FT, CONT-40FT (Gate 11-16)
        foreach (['FUSO', 'TRONTON', 'CONT-20FT', 'CONT-40FT'] as $kendaraan) {
            for ($g = 11; $g <= 16; $g++) {
                $data[] = ['nomor_gate' => $g, 'jenis_barang' => 'FROZEN', 'jenis_kendaraan' => $kendaraan, 'aktivitas' => 'INBOUND'];
            }
        }

        // FROZEN OUTBOUND - L300, CDE, CDE-LONG, CDD, CDD-LONG (Gate 1-10)
        foreach (['L300', 'CDE', 'CDE-LONG', 'CDD', 'CDD-LONG'] as $kendaraan) {
            for ($g = 1; $g <= 10; $g++) {
                $data[] = ['nomor_gate' => $g, 'jenis_barang' => 'FROZEN', 'jenis_kendaraan' => $kendaraan, 'aktivitas' => 'OUTBOUND'];
            }
        }

        // FROZEN OUTBOUND - FUSO, TRONTON, CONT-20FT, CONT-40FT (Gate 11-16)
        foreach (['FUSO', 'TRONTON', 'CONT-20FT', 'CONT-40FT'] as $kendaraan) {
            for ($g = 11; $g <= 16; $g++) {
                $data[] = ['nomor_gate' => $g, 'jenis_barang' => 'FROZEN', 'jenis_kendaraan' => $kendaraan, 'aktivitas' => 'OUTBOUND'];
            }
        }

        // DRY INBOUND - FUSO, TRONTON, CONT-20FT, CONT-40FT (Gate 17-21)
        foreach (['FUSO', 'TRONTON', 'CONT-20FT', 'CONT-40FT'] as $kendaraan) {
            for ($g = 17; $g <= 21; $g++) {
                $data[] = ['nomor_gate' => $g, 'jenis_barang' => 'DRY', 'jenis_kendaraan' => $kendaraan, 'aktivitas' => 'INBOUND'];
            }
        }

        // DRY OUTBOUND - FUSO, TRONTON, CONT-20FT, CONT-40FT (Gate 17-21)
        foreach (['FUSO', 'TRONTON', 'CONT-20FT', 'CONT-40FT'] as $kendaraan) {
            for ($g = 17; $g <= 21; $g++) {
                $data[] = ['nomor_gate' => $g, 'jenis_barang' => 'DRY', 'jenis_kendaraan' => $kendaraan, 'aktivitas' => 'OUTBOUND'];
            }
        }

        // DRY INBOUND - L300, CDE, CDE-LONG, CDD, CDD-LONG (Gate 22-27)
        foreach (['L300', 'CDE', 'CDE-LONG', 'CDD', 'CDD-LONG'] as $kendaraan) {
            for ($g = 22; $g <= 27; $g++) {
                $data[] = ['nomor_gate' => $g, 'jenis_barang' => 'DRY', 'jenis_kendaraan' => $kendaraan, 'aktivitas' => 'INBOUND'];
            }
        }

        // DRY OUTBOUND - L300, CDE, CDE-LONG, CDD, CDD-LONG (Gate 22-27)
        foreach (['L300', 'CDE', 'CDE-LONG', 'CDD', 'CDD-LONG'] as $kendaraan) {
            for ($g = 22; $g <= 27; $g++) {
                $data[] = ['nomor_gate' => $g, 'jenis_barang' => 'DRY', 'jenis_kendaraan' => $kendaraan, 'aktivitas' => 'OUTBOUND'];
            }
        }

        foreach ($data as $row) {
            Gate::create($row);
        }
    }
}
