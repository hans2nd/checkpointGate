@extends('layouts.app')

@section('title', 'Master Employee')

@section('content')
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">Total {{ $employees->total() }} employee</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" id="bulkDeleteBtn" onclick="doBulkDelete()"
                    class="hidden items-center gap-2 px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Terpilih (<span id="selectedCount">0</span>)
                </button>
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Excel
                </button>
                <a href="{{ route('employees.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('employees.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-orange-500/30 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Tambah Employee
                </a>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="checkpoint-filter-card mb-6">
            <form method="GET" action="{{ route('employees.index') }}" class="flex flex-col lg:flex-row gap-4 items-center">
                <div class="flex-1 w-full relative">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari Employee ID atau nama..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all shadow-sm">
                </div>
                <select name="role"
                    class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white transition-all shadow-sm w-full lg:w-48">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name }}</option>
                    @endforeach
                </select>
                <select name="status"
                    class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white transition-all shadow-sm w-full lg:w-48">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <div class="flex gap-2 w-full lg:w-auto">
                    <button type="submit"
                        class="flex-1 lg:flex-none px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl transition-all shadow-md hover:shadow-lg">Filter</button>
                    <a href="{{ route('employees.index') }}"
                        class="flex-1 lg:flex-none px-6 py-3 bg-white hover:bg-gray-50 border border-gray-200 text-gray-600 text-sm font-bold rounded-xl transition-all text-center text-decoration-none shadow-sm hover:shadow-md">Reset</a>
                </div>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="checkpoint-table-card">
            <div class="flex items-center justify-between mb-4 mt-2 px-4">
                <h2 class="text-lg font-bold text-gray-800">Daftar Employee</h2>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium">Tampilkan:</label>
                    <form method="GET" action="{{ route('employees.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="role" value="{{ request('role') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <select name="per_page" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white font-medium shadow-sm">
                            @foreach([10,15,25,50,100] as $size)
                                <option value="{{ $size }}" {{ request('per_page', 15) == $size ? 'selected' : '' }}>{{ $size }} data</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white;" class="text-left border-b border-orange-600 shadow-sm">
                            <th class="px-3 py-3"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"
                                    class="rounded border-white/30 bg-white/20 text-orange-600 focus:ring-white"></th>
                            <th class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">No</th>
                            <th class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">Employee ID</th>
                            <th class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">Nama</th>
                            <th class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">Role</th>
                            <th class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">Status</th>
                            <th class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap">Dibuat</th>
                            <th class="px-3 py-3 font-semibold text-white text-xs uppercase tracking-wider whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($employees as $index => $emp)
                            <tr class="hover:bg-orange-50/30 transition-colors">
                                <td class="px-3 py-2.5"><input type="checkbox" data-id="{{ $emp->id }}"
                                        class="row-checkbox rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                                        onchange="updateSelectedCount()"></td>
                                <td class="px-3 py-2.5 text-gray-400 text-xs">{{ format_row_number($employees, $index) }}</td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-xs font-mono font-semibold">
                                        {{ $emp->employee_id }}
                                    </span>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($emp->name, 0, 2)) }}
                                        </div>
                                        <p class="font-medium text-gray-900 text-xs">{{ $emp->name }}</p>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    @if ($emp->role)
                                        @php
                                            $roleColors = [
                                                'admin' => 'bg-red-50 text-red-700 border-red-200',
                                                'administrator' => 'bg-red-50 text-red-700 border-red-200',
                                                'operator' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'viewer' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            ];
                                            $color = $roleColors[$emp->role->name] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[10px] font-semibold {{ $color }}">
                                            {{ $emp->role->display_name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if ($emp->is_active)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-gray-500 text-xs whitespace-nowrap">
                                    {{ $emp->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('employees.edit', $emp) }}"
                                            class="p-1 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded transition-all"
                                            title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('employees.toggle-status', $emp) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="p-1 rounded transition-all {{ $emp->is_active ? 'text-gray-400 hover:text-orange-600 hover:bg-orange-50' : 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50' }}"
                                                title="{{ $emp->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                @if ($emp->is_active)
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                        <form id="deleteForm{{ $emp->id }}" method="POST"
                                            action="{{ route('employees.destroy', $emp) }}">@csrf @method('DELETE')
                                        </form>
                                        <button type="button"
                                            onclick="confirmDelete(document.getElementById('deleteForm{{ $emp->id }}'))"
                                            class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-all"
                                            title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="font-medium">Belum ada data employee</p>
                                        <p class="text-sm mt-1">Tambahkan employee pertama</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($employees->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $employees->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Import Modal --}}
    <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50"
                onclick="document.getElementById('importModal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Import Data Employee</h3>
                    <button onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="mb-4 p-3 bg-emerald-50 rounded-lg">
                    <p class="text-xs text-emerald-700"><strong>Format kolom:</strong> Employee ID | Nama | Role
                        (admin/operator/viewer)</p>
                    <p class="text-xs text-emerald-600 mt-1">Baris pertama = header (dilewati).</p>
                    <a href="{{ route('employees.template') }}"
                        class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template
                    </a>
                </div>
                <form method="POST" action="{{ route('employees.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx)</label>
                        <input type="file" name="file" accept=".xlsx,.xls" required
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button"
                            onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSelectAll() {
            const s = document.getElementById('selectAll');
            document.querySelectorAll('.row-checkbox:not(:disabled)').forEach(c => c.checked = s.checked);
            updateSelectedCount()
        }

        function updateSelectedCount() {
            const c = document.querySelectorAll('.row-checkbox:checked').length;
            const b = document.getElementById('bulkDeleteBtn');
            document.getElementById('selectedCount').textContent = c;
            if (c > 0) {
                b.classList.remove('hidden');
                b.classList.add('inline-flex')
            } else {
                b.classList.add('hidden');
                b.classList.remove('inline-flex')
            }
            const t = document.querySelectorAll('.row-checkbox:not(:disabled)').length;
            const s = document.getElementById('selectAll');
            s.checked = t > 0 && c === t;
            s.indeterminate = c > 0 && c < t
        }

        function doBulkDelete() {
            const ids = [...document.querySelectorAll('.row-checkbox:checked')].map(c => c.dataset.id);
            if (ids.length === 0) return;
            confirmBulkDelete('{{ route('employees.bulk-delete') }}', ids)
        }
    </script>
@endsection
