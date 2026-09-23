@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">Total {{ $logs->total() }} log entries</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if (Auth::user()->hasPermission('audit_log.purge') || Auth::user()->isAdmin())
                    <button type="button" onclick="document.getElementById('purgeModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Purge Data
                    </button>
                @endif

                @if (Auth::user()->hasPermission('audit_log.export') || Auth::user()->isAdmin())
                    <a href="{{ route('audit-logs.export', request()->query()) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                @endif
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="flex flex-col lg:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama, email, deskripsi, IP..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <select name="action"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Action</option>
                        @foreach ($actions as $a)
                            <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $a)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="module"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Module</option>
                        @foreach ($modules as $m)
                            <option value="{{ $m }}" {{ request('module') === $m ? 'selected' : '' }}>
                                {{ ucfirst($m) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="channel"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Channel</option>
                        <option value="web" {{ request('channel') === 'web' ? 'selected' : '' }}>Web</option>
                        <option value="api" {{ request('channel') === 'api' ? 'selected' : '' }}>API (Mobile)</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        placeholder="Dari Tanggal"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        placeholder="Sampai Tanggal"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-lg transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('audit-logs.index') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-lg transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Action</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Module</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Deskripsi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Channel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                IP</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                                    {{ $log->created_at?->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $log->user_name ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->user_email ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $actionColors = [
                                            'login' => 'bg-green-100 text-green-700',
                                            'login_failed' => 'bg-red-100 text-red-700',
                                            'logout' => 'bg-gray-100 text-gray-700',
                                            'create' => 'bg-blue-100 text-blue-700',
                                            'update' => 'bg-amber-100 text-amber-700',
                                            'delete' => 'bg-red-100 text-red-700',
                                            'bulk_delete' => 'bg-red-100 text-red-700',
                                            'import' => 'bg-purple-100 text-purple-700',
                                            'purge' => 'bg-red-100 text-red-700',
                                            'cancel' => 'bg-orange-100 text-orange-700',
                                            'request_cancel' => 'bg-orange-100 text-orange-700',
                                            'approve_cancel' => 'bg-green-100 text-green-700',
                                            'reject_cancel' => 'bg-red-100 text-red-700',
                                        ];
                                        $color = $actionColors[$log->action] ?? 'bg-indigo-100 text-indigo-700';
                                    @endphp
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                                        {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ ucfirst($log->module) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate" title="{{ $log->description }}">
                                    {{ Str::limit($log->description, 60) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $log->channel === 'api' ? 'bg-violet-100 text-violet-700' : 'bg-sky-100 text-sky-700' }}">
                                        {{ strtoupper($log->channel) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 font-mono">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($log->old_values || $log->new_values)
                                        <button type="button" onclick="showDetail({{ $log->id }})"
                                            class="text-indigo-500 hover:text-indigo-700 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-400">Belum ada data audit log.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($logs->hasPages())
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Menampilkan {{ $logs->firstItem() }} - {{ $logs->lastItem() }} dari {{ $logs->total() }}
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDetail()"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full mx-auto p-6 z-10 max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Detail Perubahan</h3>
                    <button onclick="closeDetail()"
                        class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-semibold text-red-600 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                            Data Sebelum (Old)
                        </h4>
                        <pre id="detailOld" class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-gray-700 overflow-x-auto whitespace-pre-wrap max-h-96">-</pre>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-green-600 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Data Sesudah (New)
                        </h4>
                        <pre id="detailNew" class="bg-green-50 border border-green-200 rounded-lg p-3 text-xs text-gray-700 overflow-x-auto whitespace-pre-wrap max-h-96">-</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Purge Modal --}}
    @if (Auth::user()->hasPermission('audit_log.purge') || Auth::user()->isAdmin())
        <div id="purgeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                    onclick="document.getElementById('purgeModal').classList.add('hidden')"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto p-6 z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Purge Audit Log</h3>
                        <button onclick="document.getElementById('purgeModal').classList.add('hidden')"
                            class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <p class="text-sm text-amber-700">
                                <strong>Perhatian:</strong> Data audit log yang dihapus tidak dapat dikembalikan. Pastikan Anda sudah mengexport data sebelum menghapus.
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('audit-logs.purge') }}" id="purgeForm">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="purge_date_from" required
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="purge_date_to" required
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                            <button type="button" onclick="confirmPurge()"
                                class="w-full px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl transition-colors">
                                Hapus Audit Log
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Store log data for detail modal --}}
    <script>
        const logData = @json($logs->getCollection()->mapWithKeys(function ($log) {
            return [$log->id => [
                'old_values' => $log->old_values,
                'new_values' => $log->new_values,
            ]];
        }));

        function showDetail(id) {
            const data = logData[id];
            if (!data) return;

            document.getElementById('detailOld').textContent = data.old_values
                ? JSON.stringify(data.old_values, null, 2)
                : '(kosong)';
            document.getElementById('detailNew').textContent = data.new_values
                ? JSON.stringify(data.new_values, null, 2)
                : '(kosong)';
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function confirmPurge() {
            Swal.fire({
                title: 'Konfirmasi Purge',
                text: 'Apakah Anda yakin ingin menghapus data audit log pada range tanggal yang dipilih? Aksi ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('purgeForm').submit();
                }
            });
        }
    </script>
@endsection
