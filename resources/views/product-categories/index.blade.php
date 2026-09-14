@extends('layouts.app')

@section('title', __('Master Category Product'))

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div><p class="text-sm text-gray-500">Total {{ $categories->total() }} {{ __('kategori produk') }}</p></div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('product-categories.export') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-green-500/30 transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Export Excel') }}
            </a>
            <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                {{ __('Import Excel') }}
            </button>
            <a href="{{ route('product-categories.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-orange-500/30 transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Tambah Kategori Produk') }}
            </a>
        </div>
    </div>

    <div class="checkpoint-filter-card mb-6">
        <form method="GET" action="{{ route('product-categories.index') }}" class="flex flex-col lg:flex-row gap-4 items-center">
            <div class="flex-1 w-full relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari kategori produk...') }}" class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all shadow-sm">
            </div>
            <div class="flex gap-2 w-full lg:w-auto">
                <button type="submit" class="flex-1 lg:flex-none px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl transition-all shadow-md hover:shadow-lg">Filter</button>
                <a href="{{ route('product-categories.index') }}" class="flex-1 lg:flex-none px-6 py-3 bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 text-sm font-bold rounded-xl transition-all text-center text-decoration-none shadow-sm hover:shadow-md">Reset</a>
            </div>
        </form>
    </div>

    <div class="checkpoint-table-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white;" class="text-left border-b border-orange-600 shadow-sm">
                        <th class="px-4 py-3 font-semibold text-white text-xs uppercase tracking-wider w-16">No</th>
                        <th class="px-4 py-3 font-semibold text-white text-xs uppercase tracking-wider">{{ __('Nama Kategori') }}</th>
                        <th class="px-4 py-3 font-semibold text-white text-xs uppercase tracking-wider">{{ __('Deskripsi') }}</th>
                        <th class="px-4 py-3 font-semibold text-white text-xs uppercase tracking-wider text-center w-32">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categories as $index => $cat)
                    <tr class="hover:bg-orange-50/30 transition-colors">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $cat->name }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $cat->description ?: '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('product-categories.edit', $cat) }}" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="{{ __('Edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form id="deletePC{{ $cat->id }}" method="POST" action="{{ route('product-categories.destroy', $cat) }}">@csrf @method('DELETE')</form>
                                <button type="button" onclick="confirmDelete(document.getElementById('deletePC{{ $cat->id }}'))" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="{{ __('Hapus') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-12 text-center text-gray-400"><p class="font-medium">{{ __('Belum ada data kategori produk') }}</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $categories->links() }}</div>
        @endif
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="closeImportModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">{{ __('Import Kategori Produk') }}</h3>
                <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('product-categories.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('Pilih File Excel') }} <span class="text-red-400">*</span></label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <p class="text-xs text-gray-500 mt-1">Format yang didukung: .xlsx, .xls, .csv</p>
                        <p class="text-xs text-blue-600 mt-2"><strong>Catatan:</strong> Kolom A (ID, diabaikan), Kolom B (Nama, wajib), Kolom C (Deskripsi, opsional). Jika nama sudah ada, baris tersebut akan diabaikan.</p>
                        <div class="mt-3">
                            <a href="{{ route('product-categories.template') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download Template Excel
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">{{ __('Batal') }}</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg">{{ __('Import') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }
    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }
</script>
@endsection
