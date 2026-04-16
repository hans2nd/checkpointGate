<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Checkpoint;

class CheckpointSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['tanggal'=>'2026-02-28','no_polisi'=>'B 1234 YY','vendor'=>'VITRANS','driver'=>'AKBAR','tipe'=>'EKSTERNAL','jenis_kendaraan'=>'CDD-LONG','waktu_penerimaan_dokumen'=>'2026-02-28 12:02:05','waktu_penyerahan_dokumen'=>'2026-02-28 12:15:34','jenis_barang'=>'DRY','aktivitas'=>'INBOUND','gate'=>27,'status'=>'FINISH','waktu_start'=>'2026-02-28 12:06:46','waktu_end'=>null,'durasi'=>'00:04:21'],
            ['tanggal'=>'2026-02-28','no_polisi'=>'B 1234 XX','vendor'=>'VITRANS','driver'=>'AKBAR','tipe'=>'EKSTERNAL','jenis_kendaraan'=>'CDE','waktu_penerimaan_dokumen'=>'2026-02-28 12:16:03','waktu_penyerahan_dokumen'=>'2026-02-28 12:21:07','jenis_barang'=>'FROZEN','aktivitas'=>'INBOUND','gate'=>1,'status'=>'FINISH','waktu_start'=>'2026-02-28 12:19:50','waktu_end'=>null,'durasi'=>'00:01:09'],
            ['tanggal'=>'2026-03-01','no_polisi'=>'B1234 XX','vendor'=>'BAHARI','driver'=>'BUDI','tipe'=>'EKSTERNAL','jenis_kendaraan'=>'FUSO','waktu_penerimaan_dokumen'=>'2026-03-01 16:05:31','waktu_penyerahan_dokumen'=>'2026-03-01 16:10:14','jenis_barang'=>'FROZEN','aktivitas'=>'INBOUND','gate'=>11,'status'=>'FINISH','waktu_start'=>'2026-03-01 16:08:39','waktu_end'=>null,'durasi'=>'00:01:26'],
            ['tanggal'=>'2026-03-01','no_polisi'=>'B 1234 XX','vendor'=>'BAHARI','driver'=>'BUDI','tipe'=>'EKSTERNAL','jenis_kendaraan'=>'CONT-20FT','waktu_penerimaan_dokumen'=>'2026-03-01 16:17:03','waktu_penyerahan_dokumen'=>'2026-03-01 16:22:13','jenis_barang'=>'FROZEN','aktivitas'=>'OUTBOUND','gate'=>11,'status'=>'FINISH','waktu_start'=>'2026-03-01 16:17:23','waktu_end'=>null,'durasi'=>'00:04:46'],
            ['tanggal'=>'2026-03-02','no_polisi'=>'B 1234 XX','vendor'=>'BAHARI','driver'=>'BUDI','tipe'=>'EKSTERNAL','jenis_kendaraan'=>'L300','waktu_penerimaan_dokumen'=>'2026-03-02 14:10:15','waktu_penyerahan_dokumen'=>'2026-03-02 14:11:31','jenis_barang'=>'FROZEN','aktivitas'=>'INBOUND','gate'=>1,'status'=>'FINISH','waktu_start'=>'2026-03-02 14:10:55','waktu_end'=>null,'durasi'=>'00:00:31'],
            ['tanggal'=>'2026-03-02','no_polisi'=>'B 1234 XX','vendor'=>'BAHARI','driver'=>'BUDI','tipe'=>'EKSTERNAL','jenis_kendaraan'=>'L300','waktu_penerimaan_dokumen'=>'2026-03-02 14:19:53','waktu_penyerahan_dokumen'=>'2026-03-02 14:22:34','jenis_barang'=>'FROZEN','aktivitas'=>'INBOUND','gate'=>1,'status'=>'FINISH','waktu_start'=>'2026-03-02 16:20:44','waktu_end'=>null,'durasi'=>'00:01:45'],
        ];

        foreach ($data as $row) {
            Checkpoint::create($row);
        }
    }
}
