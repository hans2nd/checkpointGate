<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['no_polisi' => 'B 1234 XX', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'FUSO'],
            ['no_polisi' => 'B 1234 YY', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'TRONTON'],
            ['no_polisi' => 'B 1234 CC', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'CONT-20FT'],
            ['no_polisi' => 'B 1234 VV', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'CONT-40FT'],
            ['no_polisi' => 'B 1234 BB', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'FUSO'],
            ['no_polisi' => 'B 1234 AA', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'TRONTON'],
            ['no_polisi' => 'B 1234 DD', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'CONT-20FT'],
            ['no_polisi' => 'B 1234 EE', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'CONT-40FT'],
            ['no_polisi' => 'B 1234 NN', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'CONT-40FT'],
            ['no_polisi' => 'B 2345 XX', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'INTERNAL', 'jenis_kendaraan' => 'FUSO'],
            ['no_polisi' => 'B 2345 YY', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'TRONTON'],
            ['no_polisi' => 'B 2345 ZZ', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'CONT-20FT'],
            ['no_polisi' => 'B 2345 WW', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'TRONTON'],
            ['no_polisi' => 'B 2345 HH', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'INTERNAL', 'jenis_kendaraan' => 'L300'],
            ['no_polisi' => 'B 3456 XX', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'CDE-LONG'],
            ['no_polisi' => 'B 4567 XX', 'driver' => 'AKBAR', 'vendor' => 'VITRANS', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'CDD'],
            ['no_polisi' => 'B 1234 FF', 'driver' => 'BUDI', 'vendor' => 'BAHARI', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'FUSO'],
            ['no_polisi' => 'B 1234 GG', 'driver' => 'BUDI', 'vendor' => 'BAHARI', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'CONT-20FT'],
            ['no_polisi' => 'B 1234 HH', 'driver' => 'BUDI', 'vendor' => 'BAHARI', 'tipe' => 'EKSTERNAL', 'jenis_kendaraan' => 'L300'],
        ];

        foreach ($data as $row) {
            Vehicle::create($row);
        }
    }
}
