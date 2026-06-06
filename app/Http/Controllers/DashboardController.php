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

        $recent = Checkpoint::orderBy('created_at', 'desc')->limit(5)->get();

        return view('dashboard', compact('stats', 'recent'));
    }

    public function chartData()
    {
        // Activity per day (last 7 days)
        $dailyData = Checkpoint::select(
                DB::raw("DATE(tanggal) as date"),
                DB::raw("SUM(CASE WHEN aktivitas = 'INBOUND' THEN 1 ELSE 0 END) as inbound"),
                DB::raw("SUM(CASE WHEN aktivitas = 'OUTBOUND' THEN 1 ELSE 0 END) as outbound")
            )
            ->groupBy(DB::raw("DATE(tanggal)"))
            ->orderBy('date', 'asc')
            ->limit(7)
            ->get();

        // Goods type breakdown
        $goodsData = Checkpoint::select(
                'jenis_barang',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('jenis_barang')
            ->get();

        // Vehicle type breakdown
        $vehicleData = Checkpoint::select(
                'jenis_kendaraan',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('jenis_kendaraan')
            ->orderBy('total', 'desc')
            ->get();

        // Vendor breakdown
        $vendorData = Checkpoint::select(
                'vendor',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('vendor')
            ->orderBy('total', 'desc')
            ->get();

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
        ]);
    }
}
