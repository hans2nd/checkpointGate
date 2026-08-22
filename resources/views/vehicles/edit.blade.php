@extends('layouts.app')

@section('title', 'Edit Kendaraan')

@section('content')
<style>
    .premium-form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .form-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 24px 32px;
        color: white;
        position: relative;
    }
    .form-header::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: radial-gradient(circle at top right, rgba(249, 115, 22, 0.15) 0%, transparent 60%);
        pointer-events: none;
    }
    .form-group label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
        display: block;
    }
    .premium-input {
        width: 100%;
        padding: 10px 14px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9rem;
        color: #1e293b;
        transition: all 0.2s ease;
    }
    .premium-input:focus {
        background-color: #fff;
        border-color: #f97316;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
        outline: none;
    }
</style>

<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-orange-600 transition-colors bg-white px-4 py-2 rounded-lg border border-gray-100 shadow-sm w-max">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Master Kendaraan
        </a>
    </div>

    <div class="premium-form-card">
        <div class="form-header flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-white">Edit Kendaraan</h2>
                <p class="text-slate-300 mt-1 text-sm font-medium">Record ID #{{ $vehicle->id }} &bull; {{ $vehicle->no_polisi }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" class="p-8">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group md:col-span-2">
                    <label for="no_polisi">No Polisi <span class="text-orange-500">*</span></label>
                    <input type="text" id="no_polisi" name="no_polisi" value="{{ old('no_polisi', $vehicle->no_polisi) }}" required
                           class="premium-input uppercase">
                </div>

                <div class="form-group">
                    <label for="driver">Nama Driver <span class="text-orange-500">*</span></label>
                    <input type="text" id="driver" name="driver" value="{{ old('driver', $vehicle->driver) }}" required
                           class="premium-input uppercase">
                </div>

                <div class="form-group">
                    <label for="vendor">Vendor <span class="text-orange-500">*</span></label>
                    <input type="text" id="vendor" name="vendor" value="{{ old('vendor', $vehicle->vendor) }}" required
                           class="premium-input uppercase">
                </div>

                <div class="form-group">
                    <label for="tipe">Tipe <span class="text-orange-500">*</span></label>
                    <select id="tipe" name="tipe" required class="premium-input bg-white">
                        <option value="EKSTERNAL" {{ old('tipe', $vehicle->tipe) == 'EKSTERNAL' ? 'selected' : '' }}>EKSTERNAL</option>
                        <option value="INTERNAL" {{ old('tipe', $vehicle->tipe) == 'INTERNAL' ? 'selected' : '' }}>INTERNAL</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jenis_kendaraan">Jenis Kendaraan <span class="text-orange-500">*</span></label>
                    <select id="jenis_kendaraan" name="jenis_kendaraan" required class="premium-input bg-white">
                        @foreach($jenisKendaraanList as $jk)
                            <option value="{{ $jk }}" {{ old('jenis_kendaraan', $vehicle->jenis_kendaraan) == $jk ? 'selected' : '' }}>{{ $jk }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-orange-500/30 transition-all hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                    Simpan Perubahan
                </button>
                <a href="{{ route('vehicles.index') }}" class="px-8 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-bold rounded-xl transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
