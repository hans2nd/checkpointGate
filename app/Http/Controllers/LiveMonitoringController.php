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
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();

        // Live monitoring: show all statuses EXCEPT COMPLETED and CANCEL
        // COMPLETED means waktu_penyerahan_dokumen is not null
        $activeCheckpoints = Checkpoint::where(function ($q) use ($today) {
                // $q->whereDate('tanggal', $today)
                  $q->whereNotNull('gate')
                  ->whereNull('waktu_penyerahan_dokumen')
                  ->where('status', '!=', 'CANCEL');
            })
            ->orWhere(function ($q) use ($yesterday) {
                // Overnight: any active checkpoint from yesterday that is not yet completed/cancelled
                // $q->whereDate('tanggal', $yesterday)
                  $q->whereNotNull('gate')
                  ->whereNull('waktu_penyerahan_dokumen')
                  ->where('status', '!=', 'CANCEL');
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

        // Activity summary (still shows all statuses for counting purposes)
        $activitySummary = $this->buildActivitySummary($today, $yesterday);

        // Average loading time per vehicle type × jenis_barang × aktivitas
        $vehicleTypes = \App\Models\VehicleType::orderBy('name')
            ->pluck('name')
            ->toArray();
        $avgTimes = [];

        foreach ($vehicleTypes as $vt) {
            $row = ['jenis_kendaraan' => $vt];
            foreach (['DRY', 'FROZEN', 'CHILLED'] as $jenis) {
                $altAvg = Checkpoint::whereDate('tanggal', $today)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'INBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_ALT"] = $this->calculateAverage($altAvg);

                $altAvgLmonth = Checkpoint::whereBetween('tanggal', [$today->copy()->subMonth(), $today])
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'INBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_ALT_LMONTH"] = $this->calculateAverage($altAvgLmonth);

                $autAvg = Checkpoint::whereDate('tanggal', $today)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'OUTBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_AUT"] = $this->calculateAverage($autAvg);

                $autAvgLmonth = Checkpoint::whereBetween('tanggal', [$today->copy()->subMonth(), $today])
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

        return view('livemonitoring', compact('gates', 'activitySummary', 'avgTimes'));
    }

    /**
     * API endpoint for auto-refresh
     */
    public function data(Request $request)
    {
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();

        // Only show non-completed checkpoints (not COMPLETED, not CANCEL)
        $activeCheckpoints = Checkpoint::where(function ($q) use ($today) {
                // $q->whereDate('tanggal', $today)
                  $q->whereNotNull('gate')
                  ->whereNull('waktu_penyerahan_dokumen')
                  ->where('status', '!=', 'CANCEL');
            })
            ->orWhere(function ($q) use ($yesterday) {
                // $q->whereDate('tanggal', $yesterday)
                  $q->whereNotNull('gate')
                  ->whereNull('waktu_penyerahan_dokumen')
                  ->where('status', '!=', 'CANCEL');
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

        $activitySummary = $this->buildActivitySummary($today, $yesterday);

        // Average loading time per vehicle type (dynamic from master)
        $vehicleTypes = \App\Models\VehicleType::orderBy('name')
            ->pluck('name')
            ->toArray();
        $avgTimes = [];

        foreach ($vehicleTypes as $vt) {
            $row = ['jenis_kendaraan' => $vt];
            foreach (['DRY', 'FROZEN', 'CHILLED'] as $jenis) {
                $altAvg = Checkpoint::whereDate('tanggal', $today)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'INBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_ALT"] = $this->calculateAverage($altAvg);

                $altAvgLmonth = Checkpoint::whereBetween('tanggal', [$today->copy()->subMonth(), $today])
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'INBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_ALT_LMONTH"] = $this->calculateAverage($altAvgLmonth);

                $autAvg = Checkpoint::whereDate('tanggal', $today)
                    ->where('jenis_kendaraan', $vt)
                    ->where('jenis_barang', $jenis)
                    ->where('aktivitas', 'OUTBOUND')
                    ->where('status', 'FINISH')
                    ->whereNotNull('durasi')
                    ->pluck('durasi');

                $row["{$jenis}_AUT"] = $this->calculateAverage($autAvg);

                $autAvgLmonth = Checkpoint::whereBetween('tanggal', [$today->copy()->subMonth(), $today])
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
    private function buildActivitySummary(Carbon $today, Carbon $yesterday): array
    {
        $activitySummary = [];

        // Row khusus untuk kendaraan dengan status PARKING saja
        $parkingBase = Checkpoint::where('status', 'PARKING');

        $activitySummary[] = [
            'label' => 'PARKING',
            'parking' => (clone $parkingBase)->count(),
            'doc_in' => 0,
            'waiting_gate' => 0,
            'on_process' => 0,
            'finish' => 0,
            'doc_out' => 0,
            'completed' => 0,
        ];

        foreach (['INBOUND', 'OUTBOUND'] as $aktivitas) {
            foreach (['FROZEN', 'DRY', 'CHILLED'] as $jenis) {
                $label = ($aktivitas === 'INBOUND' ? 'IN' : 'OUT') . ' ' . $jenis;

                // Base untuk kendaraan yang sudah melewati PARKING (minimal DOC IN)
                $activeBase = Checkpoint::where('aktivitas', $aktivitas)
                    ->where('jenis_barang', $jenis)
                    ->where('status', '!=', 'CANCEL')
                    ->where('status', '!=', 'PARKING');

                $parking = 0; // Sudah dihitung di row khusus PARKING
                $docIn = (clone $activeBase)->where('status', 'DOC IN')->count();
                $waitingGate = (clone $activeBase)->whereIn('status', ['ASSIGN GATE', 'WAITING', 'READY'])->count();
                $onProcess = (clone $activeBase)->where('status', 'ON LOADING')->count();

                // 'finish' berarti status FINISH dan dokumen BELUM diserahkan
                $finish = (clone $activeBase)
                    ->where('status', 'FINISH')
                    ->whereNull('waktu_penyerahan_dokumen')
                    ->whereDate('updated_at', $today)
                    ->count();
                
                // 'doc_out' berarti dokumen SUDAH diserahkan tapi BELUM completed oleh security
                $docOut = (clone $activeBase)
                    ->whereNotNull('waktu_penyerahan_dokumen')
                    ->where('status', '!=', 'COMPLETED')
                    ->whereDate('waktu_penyerahan_dokumen', $today)
                    ->count();

                // 'completed' berarti status COMPLETED
                $completed = (clone $activeBase)
                    ->where('status', 'COMPLETED')
                    ->whereDate('waktu_keluar', $today)
                    ->count();

                $activitySummary[] = [
                    'label' => $label,
                    'parking' => $parking,
                    'doc_in' => $docIn,
                    'waiting_gate' => $waitingGate,
                    'on_process' => $onProcess,
                    'finish' => $finish,
                    'doc_out' => $docOut,
                    'completed' => $completed,
                ];
            }
        }

        $activitySummary[] = [
            'label' => 'TOTAL',
            'parking' => array_sum(array_column($activitySummary, 'parking')),
            'doc_in' => array_sum(array_column($activitySummary, 'doc_in')),
            'waiting_gate' => array_sum(array_column($activitySummary, 'waiting_gate')),
            'on_process' => array_sum(array_column($activitySummary, 'on_process')),
            'finish' => array_sum(array_column($activitySummary, 'finish')),
            'doc_out' => array_sum(array_column($activitySummary, 'doc_out')),
            'completed' => array_sum(array_column($activitySummary, 'completed')),
        ];

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
