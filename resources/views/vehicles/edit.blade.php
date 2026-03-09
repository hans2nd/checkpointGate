@extends('layouts.app')

@section('title', 'Edit Kendaraan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Master Kendaraan
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Edit Kendaraan — {{ $vehicle->no_polisi }}</h2>
        </div>

        <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" class="p-6">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label for="no_polisi" class="block text-sm font-medium text-gray-700 mb-1.5">No Polisi <span class="text-red-400">*</span></label>
                    <input type="text" id="no_polisi" name="no_polisi" value="{{ old('no_polisi', $vehicle->no_polisi) }}" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all uppercase">
                </div>

                <div>
                    <label for="driver" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Driver <span class="text-red-400">*</span></label>
                    <input type="text" id="driver" name="driver" value="{{ old('driver', $vehicle->driver) }}" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all uppercase">
                </div>

                <div>
                    <label for="vendor" class="block text-sm font-medium text-gray-700 mb-1.5">Vendor <span class="text-red-400">*</span></label>
                    <input type="text" id="vendor" name="vendor" value="{{ old('vendor', $vehicle->vendor) }}" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all uppercase">
                </div>

                <div>
                    <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1.5">Tipe <span class="text-red-400">*</span></label>
                    <select id="tipe" name="tipe" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                        <option value="EKSTERNAL" {{ old('tipe', $vehicle->tipe) == 'EKSTERNAL' ? 'selected' : '' }}>EKSTERNAL</option>
                        <option value="INTERNAL" {{ old('tipe', $vehicle->tipe) == 'INTERNAL' ? 'selected' : '' }}>INTERNAL</option>
                    </select>
                </div>

                <div>
                    <label for="jenis_kendaraan" class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Kendaraan <span class="text-red-400">*</span></label>
                    <select id="jenis_kendaraan" name="jenis_kendaraan" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white transition-all">
                        @foreach($jenisKendaraanList as $jk)
                            <option value="{{ $jk }}" {{ old('jenis_kendaraan', $vehicle->jenis_kendaraan) == $jk ? 'selected' : '' }}>{{ $jk }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                    Simpan Perubahan
                </button>
                <a href="{{ route('vehicles.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
