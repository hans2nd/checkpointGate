<?php

namespace App\Http\Controllers;

use App\Models\Checkpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'total_today' => Checkpoint::whereDate('tanggal', $today)->count(),
            'total_all' => Checkpoint::count(),
            'inbound' => Checkpoint::whereDate('tanggal', $today)->where('aktivitas', 'INBOUND')->count(),
            'outbound' => Checkpoint::whereDate('tanggal', $today)->where('aktivitas', 'OUTBOUND')->count(),
            'finish' => Checkpoint::where('status', 'FINISH')->count(),
            'start' => Checkpoint::whereIn('status', ['PARKING', 'DOC IN', 'WAITING', 'READY', 'ON LOADING'])->count(),
        ];

        // Status breakdown for today
        $statusBreakdown = [
            'parking' => Checkpoint::where('status', 'PARKING')->count(),
            'doc_in' => Checkpoint::where('status', 'DOC IN')->count(),
            'assign_gate' => Checkpoint::whereIn('status', ['ASSIGN GATE', 'WAITING', 'READY'])->count(),
            'on_loading' => Checkpoint::where('status', 'ON LOADING')->count(),
            'finish' => Checkpoint::where('status', 'FINISH')->whereNull('waktu_penyerahan_dokumen')->count(),
            'doc_out' => Checkpoint::whereNotNull('waktu_penyerahan_dokumen')->where('status', '!=', 'COMPLETED')->whereDate('waktu_penyerahan_dokumen', $today)->count(),
            'completed' => Checkpoint::where('status', 'COMPLETED')->whereDate('waktu_keluar', $today)->count(),
            'cancel' => Checkpoint::where('status', 'CANCEL')->whereDate('updated_at', $today)->count(),
        ];

        // Gate utilization
        $totalGates = 27;
        $occupiedGates = Checkpoint::whereNotNull('gate')
            ->where('status', '!=', 'CANCEL')
            ->whereNull('waktu_end')
            ->distinct('gate')
            ->count('gate');
        $gateUtil = $totalGates > 0 ? round(($occupiedGates / $totalGates) * 100) : 0;

        // Average loading time today
        $avgDurations = Checkpoint::whereDate('tanggal', $today)
            ->where('status', 'FINISH')
            ->whereNotNull('durasi')
            ->pluck('durasi');

        $avgLoadingTime = null;
        if ($avgDurations->isNotEmpty()) {
            $totalSec = 0;
            $cnt = 0;
            foreach ($avgDurations as $d) {
                $parts = explode(':', $d);
                if (count($parts) === 3) {
                    $totalSec += ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                    $cnt++;
                }
            }
            if ($cnt > 0) {
                $avg = intval($totalSec / $cnt);
                $avgLoadingTime = sprintf('%02d:%02d:%02d', intdiv($avg, 3600), intdiv($avg % 3600, 60), $avg % 60);
            }
        }

        // --- NEW METRICS FOR HIGHLIGHT CARDS ---

        // 1. Top Goods Type (All time)
        $topGoods = Checkpoint::select('jenis_barang', DB::raw('count(*) as total'))
            ->whereNotNull('jenis_barang')
            ->where('jenis_barang', '!=', '')
            ->groupBy('jenis_barang')
            ->orderBy('total', 'desc')
            ->first();
        
        $topGoodsName = $topGoods ? $topGoods->jenis_barang : '-';
        $topGoodsPct = 0;
        if ($topGoods && $stats['total_all'] > 0) {
            $topGoodsPct = round(($topGoods->total / $stats['total_all']) * 100);
        }

        // 2. Top Vehicle Type (All time)
        $topVehicle = Checkpoint::select('jenis_kendaraan', DB::raw('count(*) as total'))
            ->whereNotNull('jenis_kendaraan')
            ->where('jenis_kendaraan', '!=', '')
            ->groupBy('jenis_kendaraan')
            ->orderBy('total', 'desc')
            ->first();
            
        $topVehicleName = $topVehicle ? $topVehicle->jenis_kendaraan : '-';
        $topVehicleCount = $topVehicle ? number_format($topVehicle->total) : '0';

        // 3. Longest Loading Time Today
        $longestLoading = Checkpoint::whereDate('tanggal', $today)
            ->where('status', 'FINISH')
            ->whereNotNull('durasi')
            ->orderByRaw("TIME_TO_SEC(durasi) DESC")
            ->first();
            
        $longestLoadingDur = $longestLoading ? $longestLoading->durasi : '-';
        $longestLoadingPolisi = $longestLoading ? $longestLoading->no_polisi : '-';

        // 4. Most Used Gate Today
        $mostUsedGate = Checkpoint::select('gate', DB::raw('count(*) as total'))
            ->whereDate('tanggal', $today)
            ->whereNotNull('gate')
            ->where('gate', '!=', '')
            ->groupBy('gate')
            ->orderBy('total', 'desc')
            ->first();
            
        $mostUsedGateName = $mostUsedGate ? $mostUsedGate->gate : '-';
        $mostUsedGateCount = $mostUsedGate ? number_format($mostUsedGate->total) : '0';

        return view('dashboard', compact(
            'stats', 'statusBreakdown', 'gateUtil', 'occupiedGates', 'totalGates', 'avgLoadingTime',
            'topGoodsName', 'topGoodsPct', 'topVehicleName', 'topVehicleCount', 
            'longestLoadingDur', 'longestLoadingPolisi', 'mostUsedGateName', 'mostUsedGateCount'
        ));
    }

    public function chartData(Request $request)
    {
        // Filters
        $fGoods = $request->input('goods');
        $fVehicle = $request->input('vehicle');
        $fVendor = $request->input('vendor');

        // Helper function to apply filters
        $applyFilters = function($query) use ($fGoods, $fVehicle, $fVendor) {
            if ($fGoods) {
                $query->where('jenis_barang', $fGoods);
            }
            if ($fVehicle) {
                $query->where('jenis_kendaraan', $fVehicle);
            }
            if ($fVendor) {
                $query->where('vendor', $fVendor);
            }
            return $query;
        };

        // Activity per day (last 7 days)
        $dailyDataQuery = Checkpoint::select(
                DB::raw("DATE(tanggal) as date"),
                DB::raw("SUM(CASE WHEN aktivitas = 'INBOUND' THEN 1 ELSE 0 END) as inbound"),
                DB::raw("SUM(CASE WHEN aktivitas = 'OUTBOUND' THEN 1 ELSE 0 END) as outbound")
            )
            ->groupBy(DB::raw("DATE(tanggal)"))
            ->orderBy('date', 'asc')
            ->limit(7);
        $dailyData = $applyFilters($dailyDataQuery)->get();

        // Goods type breakdown
        $goodsDataQuery = Checkpoint::select(
                'jenis_barang',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('jenis_barang')
            ->where('jenis_barang', '!=', '')
            ->groupBy('jenis_barang')
            ->orderBy('total', 'desc');
        // Do not filter Goods chart by its own selected Goods, to allow clearing by clicking others
        $goodsData = $goodsDataQuery->when($fVehicle, fn($q) => $q->where('jenis_kendaraan', $fVehicle))
                                    ->when($fVendor, fn($q) => $q->where('vendor', $fVendor))
                                    ->get();

        // Vehicle type breakdown
        $vehicleDataQuery = Checkpoint::select(
                'jenis_kendaraan',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('jenis_kendaraan')
            ->where('jenis_kendaraan', '!=', '')
            ->groupBy('jenis_kendaraan')
            ->orderBy('total', 'desc');
        // Do not filter Vehicle chart by its own selected Vehicle
        $vehicleData = $vehicleDataQuery->when($fGoods, fn($q) => $q->where('jenis_barang', $fGoods))
                                        ->when($fVendor, fn($q) => $q->where('vendor', $fVendor))
                                        ->get();

        // Vendor breakdown
        $vendorDataQuery = Checkpoint::select(
                'vendor',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('vendor')
            ->where('vendor', '!=', '')
            ->groupBy('vendor')
            ->orderBy('total', 'desc')
            ->limit(10); // Limit to top 10 for readability in pie chart
        // Do not filter Vendor chart by its own selected Vendor
        $vendorData = $vendorDataQuery->when($fGoods, fn($q) => $q->where('jenis_barang', $fGoods))
                                      ->when($fVehicle, fn($q) => $q->where('jenis_kendaraan', $fVehicle))
                                      ->get();

        // Status flow data for today
        $today = Carbon::today();
        
        $sfParking = $applyFilters(Checkpoint::where('status', 'PARKING'))->count();
        $sfDocIn = $applyFilters(Checkpoint::where('status', 'DOC IN'))->count();
        $sfWaiting = $applyFilters(Checkpoint::whereIn('status', ['ASSIGN GATE', 'WAITING', 'READY']))->count();
        $sfLoading = $applyFilters(Checkpoint::where('status', 'ON LOADING'))->count();
        $sfFinish = $applyFilters(Checkpoint::where('status', 'FINISH'))->count();
        $sfCompleted = $applyFilters(Checkpoint::where('status', 'COMPLETED')->whereDate('waktu_keluar', $today))->count();

        $statusFlow = [
            ['name' => 'Parking', 'value' => $sfParking],
            ['name' => 'Doc In', 'value' => $sfDocIn],
            ['name' => 'Waiting', 'value' => $sfWaiting],
            ['name' => 'Loading', 'value' => $sfLoading],
            ['name' => 'Finish', 'value' => $sfFinish],
            ['name' => 'Completed', 'value' => $sfCompleted],
        ];

        return response()->json([
            'daily' => [
                'categories' => $dailyData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M')),
                'inbound' => $dailyData->pluck('inbound'),
                'outbound' => $dailyData->pluck('outbound'),
            ],
            'goods' => [
                'labels' => $goodsData->pluck('jenis_barang'),
                'values' => $goodsData->pluck('total'),
            ],
            'vehicles' => [
                'labels' => $vehicleData->pluck('jenis_kendaraan'),
                'values' => $vehicleData->pluck('total'),
            ],
            'vendors' => [
                'labels' => $vendorData->pluck('vendor'),
                'values' => $vendorData->pluck('total'),
            ],
            'statusFlow' => $statusFlow,
        ]);
    }
}
