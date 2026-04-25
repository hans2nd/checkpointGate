<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use App\Models\Gate;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LiveMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $selectedDate = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : Carbon::today();

        $isToday = $selectedDate->isToday();
        $yesterday = $selectedDate->copy()->subDay();

        // Get all active checkpoints for selected date + overnight from yesterday
        $activeCheckpoints = Checkpoint::where(function ($q) use ($selectedDate) {
                $q->whereDate('tanggal', $selectedDate)
                  ->whereNotNull('gate')
                  ->where('status', '!=', 'CANCEL');
            })
            ->orWhere(function ($q) use ($yesterday, $selectedDate) {
                // Overnight: started yesterday, still ON LOADING or finished after midnight today
                $q->whereDate('tanggal', $yesterday)
                  ->whereNotNull('gate')
                  ->where('status', '!=', 'CANCEL')
                  ->where(function ($inner) use ($selectedDate) {
                      $inner->where('status', 'ON LOADING')
                            ->orWhere(function ($fin) use ($selectedDate) {
                                $fin->where('status', 'FINISH')
                                    ->whereNotNull('waktu_end')
                                    ->where('waktu_end', '>=', $selectedDate->copy()->startOfDay());
                            });
                  });
            })
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

        // Activity summary with total count + overnight vehicles
        $activitySummary = $this->buildActivitySummary($selectedDate, $yesterday);

        // Average loading time per vehicle type × jenis_barang × aktivitas
        $vehicleTypes = Vehicle::select('jenis_kendaraan')
            ->distinct()
            ->orderBy('jenis_kendaraan')
            ->pluck('jenis_kendaraan')
            ->toArray();
        $avgTimes = [];

        foreach ($vehicleTypes as $vt) {
            $row = ['jenis_kendaraan' => $vt];
            foreach (['DRY', 'FROZEN', 'CHILLED'] as $jenis) {
                $altAvg = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'INBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_ALT"] = $this->calculateAverage($altAvg);

                $altAvgLmonth = Checkpoint::whereBetween('tanggal', [$selectedDate->copy()->subMonth(), $selectedDate])
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'INBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_ALT_LMONTH"] = $this->calculateAverage($altAvgLmonth);

                $autAvg = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'OUTBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_AUT"] = $this->calculateAverage($autAvg);

                $autAvgLmonth = Checkpoint::whereBetween('tanggal', [$selectedDate->copy()->subMonth(), $selectedDate])
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'OUTBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_AUT_LMONTH"] = $this->calculateAverage($autAvgLmonth);
            }
            $avgTimes[] = $row;
        }

        $periode = $selectedDate->format('d/m/Y');
        $tanggalValue = $selectedDate->format('Y-m-d');

        return view('livemonitoring', compact('gates', 'activitySummary', 'avgTimes', 'periode', 'tanggalValue', 'isToday'));
    }

    /**
     * API endpoint for auto-refresh
     */
    public function data(Request $request)
    {
        $selectedDate = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : Carbon::today();

        $yesterday = $selectedDate->copy()->subDay();

        $activeCheckpoints = Checkpoint::where(function ($q) use ($selectedDate) {
                $q->whereDate('tanggal', $selectedDate)
                  ->whereNotNull('gate')
                  ->where('status', '!=', 'CANCEL');
            })
            ->orWhere(function ($q) use ($yesterday, $selectedDate) {
                $q->whereDate('tanggal', $yesterday)
                  ->whereNotNull('gate')
                  ->where('status', '!=', 'CANCEL')
                  ->where(function ($inner) use ($selectedDate) {
                      $inner->where('status', 'ON LOADING')
                            ->orWhere(function ($fin) use ($selectedDate) {
                                $fin->where('status', 'FINISH')
                                    ->whereNotNull('waktu_end')
                                    ->where('waktu_end', '>=', $selectedDate->copy()->startOfDay());
                            });
                  });
            })
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
                'aktivitas' => $checkpoint ? $checkpoint->aktivitas : null,
                'durasi' => $checkpoint ? $checkpoint->durasi : null,
                'waktu_start' => $checkpoint && $checkpoint->waktu_start ? $checkpoint->waktu_start->toIso8601String() : null,
                'waktu_end' => $checkpoint && $checkpoint->waktu_end ? $checkpoint->waktu_end->toIso8601String() : null,
                'waktu_penerimaan' => $checkpoint && $checkpoint->waktu_penerimaan_dokumen ? $checkpoint->waktu_penerimaan_dokumen->toIso8601String() : null,
                'waktu_penyerahan' => $checkpoint && $checkpoint->waktu_penyerahan_dokumen ? $checkpoint->waktu_penyerahan_dokumen->toIso8601String() : null,
            ];
        }

        $activitySummary = $this->buildActivitySummary($selectedDate, $yesterday);

        // Average loading time per vehicle type (dynamic from master)
        $vehicleTypes = Vehicle::select('jenis_kendaraan')
            ->distinct()
            ->orderBy('jenis_kendaraan')
            ->pluck('jenis_kendaraan')
            ->toArray();
        $avgTimes = [];

        foreach ($vehicleTypes as $vt) {
            $row = ['jenis_kendaraan' => $vt];
            foreach (['DRY', 'FROZEN', 'CHILLED'] as $jenis) {
                $altAvg = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'INBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_ALT"] = $this->calculateAverage($altAvg);

                $altAvgLmonth = Checkpoint::whereBetween('tanggal', [$selectedDate->copy()->subMonth(), $selectedDate])
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'INBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_ALT_LMONTH"] = $this->calculateAverage($altAvgLmonth);

                $autAvg = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'OUTBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_AUT"] = $this->calculateAverage($autAvg);

                $autAvgLmonth = Checkpoint::whereBetween('tanggal', [$selectedDate->copy()->subMonth(), $selectedDate])
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'OUTBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_AUT_LMONTH"] = $this->calculateAverage($autAvgLmonth);
            }
            $avgTimes[] = $row;
        }

        return response()->json(compact('gates', 'activitySummary', 'avgTimes'));
    }

    /**
     * Build activity summary with total count and overnight vehicle tracking.
     */
    private function buildActivitySummary(Carbon $selectedDate, Carbon $yesterday): array
    {
        $activitySummary = [];

        foreach (['INBOUND', 'OUTBOUND'] as $aktivitas) {
            foreach (['FROZEN', 'DRY', 'CHILLED'] as $jenis) {
                $label = ($aktivitas === 'INBOUND' ? 'IN' : 'OUT') . ' ' . $jenis;

                // Today's data
                $todayBase = Checkpoint::whereDate('tanggal', $selectedDate)
                    ->where('aktivitas', $aktivitas)
                    ->where('jenis_barang', $jenis)
                    ->where('status', '!=', 'CANCEL');

                // Overnight: still ON LOADING from yesterday
                $overnightOnLoading = Checkpoint::whereDate('tanggal', $yesterday)
                    ->where('aktivitas', $aktivitas)
                    ->where('jenis_barang', $jenis)
                    ->where('status', 'ON LOADING');

                // Overnight: finished after midnight today (started yesterday)
                $overnightFinished = Checkpoint::whereDate('tanggal', $yesterday)
                    ->where('aktivitas', $aktivitas)
                    ->where('jenis_barang', $jenis)
                    ->where('status', 'FINISH')
                    ->whereNotNull('waktu_end')
                    ->where('waktu_end', '>=', $selectedDate->copy()->startOfDay());

                $parking = (clone $todayBase)->whereNull('waktu_penerimaan_dokumen')->count();

                $receiving = (clone $todayBase)->where('status', 'START')
                    ->whereNotNull('waktu_penerimaan_dokumen')
                    ->whereNotNull('gate')
                    ->count();

                $onProcess = (clone $todayBase)->where('status', 'ON LOADING')->count()
                    + (clone $overnightOnLoading)->count();

                $finish = (clone $todayBase)->where('status', 'FINISH')->count()
                    + (clone $overnightFinished)->count();

                // Total = all non-cancelled vehicles for this activity today + overnight carry-overs
                $total = (clone $todayBase)->count()
                    + (clone $overnightOnLoading)->count()
                    + (clone $overnightFinished)->count();

                $activitySummary[] = [
                    'label' => $label,
                    'parking' => $parking,
                    'receiving' => $receiving,
                    'on_process' => $onProcess,
                    'finish' => $finish,
                    'total' => $total,
                ];
            }
        }

        return $activitySummary;
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
