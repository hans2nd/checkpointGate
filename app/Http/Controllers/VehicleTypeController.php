<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleType::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $vehicleTypes = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();

        return view('vehicle-types.index', compact('vehicleTypes'));
    }

    public function create()
    {
        return view('vehicle-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:vehicle_types,name',
        ], [
            'name.required' => 'Nama jenis kendaraan wajib diisi.',
            'name.unique' => 'Jenis kendaraan ini sudah ada.',
        ]);

        $validated['name'] = strtoupper(trim($validated['name']));

        VehicleType::create($validated);

        return redirect()->route('vehicle-types.index')
            ->with('success', __('Jenis kendaraan berhasil ditambahkan.'));
    }

    public function edit(VehicleType $vehicleType)
    {
        return view('vehicle-types.edit', compact('vehicleType'));
    }

    public function update(Request $request, VehicleType $vehicleType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:vehicle_types,name,' . $vehicleType->id,
        ], [
            'name.required' => 'Nama jenis kendaraan wajib diisi.',
            'name.unique' => 'Jenis kendaraan ini sudah ada.',
        ]);

        $validated['name'] = strtoupper(trim($validated['name']));

        $vehicleType->update($validated);

        return redirect()->route('vehicle-types.index')
            ->with('success', __('Jenis kendaraan berhasil diperbarui.'));
    }

    public function destroy(VehicleType $vehicleType)
    {
        $vehicleType->delete();

        return redirect()->route('vehicle-types.index')
            ->with('success', __('Jenis kendaraan berhasil dihapus.'));
    }

    /**
     * API: Return all vehicle types as JSON (for dropdowns/autocomplete)
     */
    public function apiList()
    {
        $types = VehicleType::orderBy('name')->pluck('name');
        return response()->json($types);
    }
}
