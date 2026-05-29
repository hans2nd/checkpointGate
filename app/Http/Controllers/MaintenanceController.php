<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MaintenanceController extends Controller
{
    /**
     * Tampilkan halaman informatif "Sedang Maintenance" untuk user biasa
     */
    public function page()
    {
        // Jika sedang tidak maintenance, redirect kembali ke login atau dashboard
        if (!Cache::get('app_maintenance', false)) {
            return redirect()->route('login');
        }

        return view('maintenance.page');
    }

    /**
     * Tampilkan halaman pengaturan toggle (hanya untuk Admin)
     */
    public function index()
    {
        $isMaintenance = Cache::get('app_maintenance', false);
        return view('maintenance.index', compact('isMaintenance'));
    }

    /**
     * Toggle status maintenance (On/Off)
     */
    public function toggle(Request $request)
    {
        $status = $request->input('status') === '1'; // true or false
        Cache::forever('app_maintenance', $status);

        $msg = $status ? 'Maintenance Mode diaktifkan. Semua user kecuali Admin akan diblokir.' : 'Maintenance Mode dimatikan. Aplikasi berjalan normal.';
        
        return back()->with('success', $msg);
    }
}
