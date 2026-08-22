@extends('layouts.app')

@section('title', __('Master Vehicle Type'))

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><p class="text-sm text-gray-500">Total {{ $vehicleTypes->total() }} {{ __('jenis kendaraan') }}</p></div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('vehicle-types.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-orange-500/30 transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Tambah Jenis Kendaraan') }}
            </a>
        </div>
    </div>

    <div class="checkpoint-filter-card mb-6">
        <form method="GET" action="{{ route('vehicle-types.index') }}" class="flex flex-col lg:flex-row gap-4 items-center">
            <div class="flex-1 w-full relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari jenis kendaraan...') }}" class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all shadow-sm">
            </div>
            <div class="flex gap-2 w-full lg:w-auto">
                <button type="submit" class="flex-1 lg:flex-none px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl transition-all shadow-md hover:shadow-lg">Filter</button>
                <a href="{{ route('vehicle-types.index') }}" class="flex-1 lg:flex-none px-6 py-3 bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 text-sm font-bold rounded-xl transition-all text-center text-decoration-none shadow-sm hover:shadow-md">Reset</a>
            </div>
        </form>
    </div>

    <div class="checkpoint-table-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white;" class="text-left border-b border-orange-600 shadow-sm">
                        <th class="px-4 py-3 font-semibold text-white text-xs uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 font-semibold text-white text-xs uppercase tracking-wider">{{ __('Nama Jenis Kendaraan') }}</th>
                        <th class="px-4 py-3 font-semibold text-white text-xs uppercase tracking-wider">{{ __('Dibuat') }}</th>
                        <th class="px-4 py-3 font-semibold text-white text-xs uppercase tracking-wider text-center w-32">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($vehicleTypes as $index => $type)
                    <tr class="hover:bg-orange-50/30 transition-colors">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ ($vehicleTypes->currentPage() - 1) * $vehicleTypes->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $type->name }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $type->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('vehicle-types.edit', $type) }}" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('Edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form id="deleteVT{{ $type->id }}" method="POST" action="{{ route('vehicle-types.destroy', $type) }}">@csrf @method('DELETE')</form>
                                <button type="button" onclick="confirmDelete(document.getElementById('deleteVT{{ $type->id }}'))" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="{{ __('Hapus') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-12 text-center text-gray-400"><p class="font-medium">{{ __('Belum ada data jenis kendaraan') }}</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vehicleTypes->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $vehicleTypes->links() }}</div>
        @endif
    </div>
</div>
@endsection
