<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\Gate;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LiveMonitoring2Controller extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : Carbon::today();

        $isToday = $selectedDate->isToday();

        // Get all active checkpoints for selected date (grouped by gate)
        $activeCheckpoints = Checkpoint::whereDate('tanggal', $selectedDate)
            ->whereNotNull('gate')
            ->orderBy('gate')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('gate');

        // Build gate data (1-27)
        $gates = [];
        for ($i = 1; $i <= 27; $i++) {
            $jenisBarang = $i <= 16 ? 'FROZEN' : 'DRY';
            $checkpoint = isset($activeCheckpoints[$i]) ? $activeCheckpoints[$i]->first() : null;
            $gates[$i] = [
                'nomor' => $i,
                'jenis_barang' => $jenisBarang,
                'checkpoint' => $checkpoint,
            ];
        }

        // Activity summary: Loading = INBOUND, Unloading = OUTBOUND
        $activitySummary = [];
        foreach (['INBOUND', 'OUTBOUND'] as $aktivitas) {
            foreach (['FROZEN', 'DRY'] as $jenis) {
                $label = ($aktivitas === 'INBOUND' ? 'LOADING' : 'UNLOADING') . ' ' . $jenis;
                $onProcess = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('aktivitas', $aktivitas)
                    ->where('jenis_barang', $jenis)
                    ->where('status', 'ON LOADING')
                    ->count();
                $finish = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('aktivitas', $aktivitas)
                    ->where('jenis_barang', $jenis)
                    ->where('status', 'FINISH')
                    ->count();
                $activitySummary[] = [
                    'label' => $label,
                    'on_process' => $onProcess,
                    'finish' => $finish,
                ];
            }
        }

        // Average loading time per vehicle type × jenis_barang × aktivitas
        $vehicleTypes = Vehicle::select('jenis_kendaraan')
            ->distinct()
            ->orderBy('jenis_kendaraan')
            ->pluck('jenis_kendaraan')
            ->toArray();
        $avgTimes = [];

        foreach ($vehicleTypes as $vt) {
            $row = ['jenis_kendaraan' => $vt];
            foreach (['DRY', 'FROZEN'] as $jenis) {
                $altAvg = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'INBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_ALT"] = $this->calculateAverage($altAvg);

                $autAvg = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'OUTBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_AUT"] = $this->calculateAverage($autAvg);
            }
            $avgTimes[] = $row;
        }

        $periode = $selectedDate->format('d/m/Y');
        $tanggalValue = $selectedDate->format('Y-m-d');

        return view('livemonitoring2', compact('gates', 'activitySummary', 'avgTimes', 'periode', 'tanggalValue', 'isToday'));
    }

    /**
     * API endpoint for auto-refresh
     */
    public function data(Request $request)
    {
        $selectedDate = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : Carbon::today();

        $activeCheckpoints = Checkpoint::whereDate('tanggal', $selectedDate)
            ->whereNotNull('gate')
            ->orderBy('gate')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('gate');

        $gates = [];
        for ($i = 1; $i <= 27; $i++) {
            $jenisBarang = $i <= 16 ? 'FROZEN' : 'DRY';
            $checkpoint = isset($activeCheckpoints[$i]) ? $activeCheckpoints[$i]->first() : null;
            $gates[$i] = [
                'nomor' => $i,
                'jenis_barang' => $jenisBarang,
                'no_polisi' => $checkpoint ? $checkpoint->no_polisi : null,
                'vendor' => $checkpoint ? $checkpoint->vendor : null,
                'status' => $checkpoint ? $checkpoint->status : null,
                'durasi' => $checkpoint ? $checkpoint->durasi : null,
                'waktu_start' => $checkpoint && $checkpoint->waktu_start ? $checkpoint->waktu_start->toIso8601String() : null,
                'waktu_end' => $checkpoint && $checkpoint->waktu_end ? $checkpoint->waktu_end->toIso8601String() : null,
                'waktu_penerimaan' => $checkpoint && $checkpoint->waktu_penerimaan_dokumen ? $checkpoint->waktu_penerimaan_dokumen->toIso8601String() : null,
                'waktu_penyerahan' => $checkpoint && $checkpoint->waktu_penyerahan_dokumen ? $checkpoint->waktu_penyerahan_dokumen->toIso8601String() : null,
            ];
        }

        $activitySummary = [];
        foreach (['INBOUND', 'OUTBOUND'] as $aktivitas) {
            foreach (['FROZEN', 'DRY'] as $jenis) {
                $label = ($aktivitas === 'INBOUND' ? 'LOADING' : 'UNLOADING') . ' ' . $jenis;
                $onProcess = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('aktivitas', $aktivitas)
                    ->where('jenis_barang', $jenis)
                    ->where('status', 'ON LOADING')
                    ->count();
                $finish = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('aktivitas', $aktivitas)
                    ->where('jenis_barang', $jenis)
                    ->where('status', 'FINISH')
                    ->count();
                $activitySummary[] = compact('label', 'onProcess', 'finish');
            }
        }

        // Average loading time per vehicle type (dynamic from master)
        $vehicleTypes = Vehicle::select('jenis_kendaraan')
            ->distinct()
            ->orderBy('jenis_kendaraan')
            ->pluck('jenis_kendaraan')
            ->toArray();
        $avgTimes = [];

        foreach ($vehicleTypes as $vt) {
            $row = ['jenis_kendaraan' => $vt];
            foreach (['DRY', 'FROZEN'] as $jenis) {
                $altAvg = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'INBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_ALT"] = $this->calculateAverage($altAvg);

                $autAvg = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'OUTBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_AUT"] = $this->calculateAverage($autAvg);
            }
            $avgTimes[] = $row;
        }

        return response()->json(compact('gates', 'activitySummary', 'avgTimes'));
    }

    private function calculateAverage($durations)
    {
        if ($durations->isEmpty()) return null;

        $totalSeconds = 0;
        $count = 0;

        foreach ($durations as $d) {
            $parts = explode(':', $d);
            if (count($parts) === 3) {
                $totalSeconds += ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                $count++;
            }
        }

        if ($count === 0) return null;

        $avg = intval($totalSeconds / $count);
        $h = intdiv($avg, 3600);
        $m = intdiv($avg % 3600, 60);
        $s = $avg % 60;

        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }
}
