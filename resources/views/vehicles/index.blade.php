@extends('layouts.app')

@section('title', 'Master Kendaraan')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><p class="text-sm text-gray-500">Total {{ $vehicles->total() }} kendaraan</p></div>
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" id="bulkDeleteBtn" onclick="doBulkDelete()" class="hidden items-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Import Excel
            </button>
            <a href="{{ route('vehicles.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
            <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kendaraan
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <form method="GET" action="{{ route('vehicles.index') }}" class="flex flex-col lg:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no polisi, driver, vendor..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
            </div>
            <select name="vendor" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                <option value="">Semua Vendor</option>
                @foreach($vendorList as $v)
                    <option value="{{ $v }}" {{ request('vendor') == $v ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
            <select name="per_page" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                @foreach([10,15,25,50,100,1000] as $size)
                    <option value="{{ $size }}" {{ request('per_page', 15) == $size ? 'selected' : '' }}>{{ $size }} / hal</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">Filter</button>
                <a href="{{ route('vehicles.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-gray-300 text-orange-500 focus:ring-orange-500"></th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">No Polisi</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Driver</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Vendor</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Jenis Kendaraan</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vehicles as $index => $vehicle)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3"><input type="checkbox" data-id="{{ $vehicle->id }}" class="row-checkbox rounded border-gray-300 text-orange-500 focus:ring-orange-500" onchange="updateSelectedCount()"></td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $vehicles->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $vehicle->no_polisi }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $vehicle->driver }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $vehicle->vendor }}</td>
                        <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $vehicle->tipe === 'INTERNAL' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">{{ $vehicle->tipe }}</span></td>
                        <td class="px-4 py-3 text-gray-700 font-medium">{{ $vehicle->jenis_kendaraan }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('vehicles.edit', $vehicle) }}" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form id="deleteVehicle{{ $vehicle->id }}" method="POST" action="{{ route('vehicles.destroy', $vehicle) }}">@csrf @method('DELETE')</form>
                                <button type="button" onclick="confirmDelete(document.getElementById('deleteVehicle{{ $vehicle->id }}'))" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-gray-400"><p class="font-medium">Belum ada data kendaraan</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vehicles->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $vehicles->links() }}</div>
        @endif
    </div>
</div>

<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('importModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Import Data Kendaraan</h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-xs text-blue-700"><strong>Format kolom:</strong> No Polisi | Driver | Vendor | Tipe (INTERNAL/EKSTERNAL) | Jenis Kendaraan</p>
                <p class="text-xs text-blue-600 mt-1">Baris pertama = header (dilewati). Jika No Polisi sudah ada, data akan diupdate.</p>
                <a href="{{ route('vehicles.template') }}" class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-blue-700 hover:text-blue-900">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Template
                </a>
            </div>
            <form method="POST" action="{{ route('vehicles.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx)</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(){const s=document.getElementById('selectAll');document.querySelectorAll('.row-checkbox').forEach(c=>c.checked=s.checked);updateSelectedCount()}
function updateSelectedCount(){const c=document.querySelectorAll('.row-checkbox:checked').length;const b=document.getElementById('bulkDeleteBtn');document.getElementById('selectedCount').textContent=c;if(c>0){b.classList.remove('hidden');b.classList.add('inline-flex')}else{b.classList.add('hidden');b.classList.remove('inline-flex')}const t=document.querySelectorAll('.row-checkbox').length;const s=document.getElementById('selectAll');s.checked=t>0&&c===t;s.indeterminate=c>0&&c<t}
function doBulkDelete(){const ids=[...document.querySelectorAll('.row-checkbox:checked')].map(c=>c.dataset.id);if(ids.length===0)return;confirmBulkDelete('{{ route("vehicles.bulk-delete") }}',ids)}
</script>
@endsection
