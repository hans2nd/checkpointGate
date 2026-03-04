@extends('layouts.app')

@section('title', 'Data Checkpoint')

@section('content')
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">Total {{ $checkpoints->total() }} record</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" id="bulkDeleteBtn" onclick="doBulkDelete()"
                    class="hidden items-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Terpilih (<span id="selectedCount">0</span>)
                </button>
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import Excel
                </button>
                <a href="{{ route('checkpoints.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('checkpoints.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Data
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <form method="GET" action="{{ route('checkpoints.index') }}" class="flex flex-col lg:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari no polisi, vendor, driver..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                </div>
                <select name="aktivitas"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                    <option value="">Semua Aktivitas</option>
                    <option value="INBOUND" {{ request('aktivitas') == 'INBOUND' ? 'selected' : '' }}>Inbound</option>
                    <option value="OUTBOUND" {{ request('aktivitas') == 'OUTBOUND' ? 'selected' : '' }}>Outbound</option>
                </select>
                <select name="jenis_barang"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                    <option value="">Semua Barang</option>
                    <option value="FROZEN" {{ request('jenis_barang') == 'FROZEN' ? 'selected' : '' }}>Frozen</option>
                    <option value="DRY" {{ request('jenis_barang') == 'DRY' ? 'selected' : '' }}>Dry</option>
                </select>
                <select name="status"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                    <option value="">Semua Status</option>
                    <option value="ON LOADING" {{ request('status') == 'ON LOADING' ? 'selected' : '' }}>On Loading</option>
                    <option value="FINISH" {{ request('status') == 'FINISH' ? 'selected' : '' }}>Finish</option>
                </select>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                <select name="per_page" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                    @foreach ([10, 15, 25, 50, 100, 1000] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 15) == $size ? 'selected' : '' }}>
                            {{ $size }} / hal</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">Filter</button>
                    <a href="{{ route('checkpoints.index') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-3 py-3"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"
                                    class="rounded border-gray-300 text-orange-500 focus:ring-orange-500"></th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Tanggal</th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                No Polisi</th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Vendor</th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Kendaraan</th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Barang</th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Aktivitas</th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                Penerimaan<br><span class="text-[10px] font-normal normal-case text-gray-400">Dokumen
                                    IN</span></th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                Start<br><span class="text-[10px] font-normal normal-case text-gray-400">Loading</span>
                            </th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                End<br><span class="text-[10px] font-normal normal-case text-gray-400">Loading</span></th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Gate</th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Status</th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Durasi</th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                Penyerahan<br><span class="text-[10px] font-normal normal-case text-gray-400">Dokumen
                                    OUT</span></th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($checkpoints as $cp)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-2.5"><input type="checkbox" data-id="{{ $cp->id }}"
                                        class="row-checkbox rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                                        onchange="updateSelectedCount()"></td>
                                <td class="px-3 py-2.5 text-gray-700 whitespace-nowrap text-xs">
                                    {{ $cp->tanggal->format('d/m/Y') }}</td>
                                <td class="px-3 py-2.5 font-medium text-gray-900 whitespace-nowrap text-xs">
                                    {{ $cp->no_polisi }}</td>
                                <td class="px-3 py-2.5 text-gray-600 whitespace-nowrap text-xs">{{ $cp->vendor }}</td>
                                <td class="px-3 py-2.5 text-gray-600 whitespace-nowrap text-xs">{{ $cp->jenis_kendaraan }}
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap"><span
                                        class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $cp->jenis_barang === 'FROZEN' ? 'bg-cyan-50 text-cyan-700' : 'bg-orange-50 text-orange-700' }}">{{ $cp->jenis_barang }}</span>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap"><span
                                        class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $cp->aktivitas === 'INBOUND' ? 'bg-orange-50 text-orange-700' : 'bg-amber-50 text-amber-700' }}">{{ $cp->aktivitas }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if ($cp->waktu_penerimaan_dokumen)
                                        <span
                                            class="text-xs text-gray-600">{{ $cp->waktu_penerimaan_dokumen->format('H:i:s') }}</span>
                                    @else
                                        <button type="button"
                                            onclick="openGateModal({{ $cp->id }}, '{{ $cp->no_polisi }}')"
                                            class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-[10px] font-semibold rounded-md transition-all shadow-sm">📥
                                            Terima</button>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if ($cp->waktu_start)
                                        <span class="text-xs text-gray-600">{{ $cp->waktu_start->format('H:i:s') }}</span>
                                    @elseif($cp->waktu_penerimaan_dokumen && $cp->gate)
                                        <form method="POST" action="{{ route('checkpoints.trigger-start', $cp) }}"
                                            class="inline">@csrf
                                            <button type="submit"
                                                class="px-2 py-1 bg-orange-500 hover:bg-orange-600 text-white text-[10px] font-semibold rounded-md transition-all shadow-sm">▶
                                                Start</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if ($cp->waktu_end)
                                        <span class="text-xs text-gray-600">{{ $cp->waktu_end->format('H:i:s') }}</span>
                                    @elseif($cp->waktu_start)
                                        <form method="POST" action="{{ route('checkpoints.trigger-end', $cp) }}"
                                            class="inline">@csrf
                                            <button type="submit"
                                                class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-[10px] font-semibold rounded-md transition-all shadow-sm">⏹
                                                End</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-gray-600 text-center text-xs">{{ $cp->gate ?? '-' }}</td>

                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    @if($cp->status === 'FINISH')
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            FINISH
                                        </span>
                                    @elseif($cp->status === 'ON LOADING')
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                            ON LOADING
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-yellow-50 text-yellow-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                            {{ $cp->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-gray-500 font-mono text-xs whitespace-nowrap">
                                    {{ $cp->durasi ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if ($cp->waktu_penyerahan_dokumen)
                                        <span
                                            class="text-xs text-gray-600">{{ $cp->waktu_penyerahan_dokumen->format('H:i:s') }}</span>
                                    @elseif($cp->waktu_end)
                                        <form method="POST" action="{{ route('checkpoints.trigger-penyerahan', $cp) }}"
                                            class="inline">@csrf
                                            <button type="submit"
                                                class="px-2 py-1 bg-purple-500 hover:bg-purple-600 text-white text-[10px] font-semibold rounded-md transition-all shadow-sm">📤
                                                Serah</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('checkpoints.show', $cp) }}"
                                            class="p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-all"
                                            title="Detail">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('checkpoints.edit', $cp) }}"
                                            class="p-1 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded transition-all"
                                            title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form id="deleteForm{{ $cp->id }}" method="POST"
                                            action="{{ route('checkpoints.destroy', $cp) }}">@csrf @method('DELETE')
                                        </form>
                                        <button type="button"
                                            onclick="confirmDelete(document.getElementById('deleteForm{{ $cp->id }}'))"
                                            class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-all"
                                            title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-4 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="font-medium">Belum ada data</p>
                                        <p class="text-sm mt-1">Tambahkan data checkpoint pertama</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($checkpoints->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $checkpoints->links() }}</div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 mb-2">Keterangan Tombol Trigger:</p>
            <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5"><span
                        class="px-2 py-0.5 bg-blue-500 text-white rounded text-[10px] font-semibold">📥 Terima</span>
                    Penerimaan dokumen</span>
                <span class="flex items-center gap-1.5"><span
                        class="px-2 py-0.5 bg-purple-500 text-white rounded text-[10px] font-semibold">📤 Serah</span>
                    Penyerahan dokumen</span>
                <span class="flex items-center gap-1.5"><span
                        class="px-2 py-0.5 bg-orange-500 text-white rounded text-[10px] font-semibold">▶ Start</span> Mulai
                    loading</span>
                <span class="flex items-center gap-1.5"><span
                        class="px-2 py-0.5 bg-red-500 text-white rounded text-[10px] font-semibold">⏹ End</span> Selesai
                    loading</span>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50"
                onclick="document.getElementById('importModal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Import Data Checkpoint</h3>
                    <button onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-700"><strong>Format kolom:</strong> Tanggal | No Polisi | Vendor | Driver |
                        Tipe (INTERNAL/EKSTERNAL) | Jenis Kendaraan | Jenis Barang (FROZEN/DRY) | Aktivitas
                        (INBOUND/OUTBOUND) | Gate</p>
                    <p class="text-xs text-blue-600 mt-1">Baris pertama = header (dilewati).</p>
                    <a href="{{ route('checkpoints.template') }}"
                        class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-blue-700 hover:text-blue-900">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Template
                    </a>
                </div>
                <form method="POST" action="{{ route('checkpoints.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx)</label>
                        <input type="file" name="file" accept=".xlsx,.xls" required
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Gate Selection Modal -->
    <div id="gateModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" onclick="closeGateModal()"></div>
            <div class="relative bg-white rounded-2xl shadow-xl max-w-3xl w-full p-6 z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Pilih Gate</h3>
                        <p class="text-sm text-gray-500 mt-0.5" id="gateModalSubtitle"></p>
                    </div>
                    <button onclick="closeGateModal()"
                        class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <!-- Loading state -->
                <div id="gateModalLoading" class="py-8 text-center">
                    <svg class="animate-spin h-8 w-8 text-orange-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p class="text-sm text-gray-500">Memuat gate tersedia...</p>
                </div>

                <!-- Gate grid -->
                <div id="gateModalContent" class="hidden">
                    <p class="text-xs text-gray-500 mb-3">🟢 Gate tersedia &nbsp; 🔴 Gate sedang loading</p>
                    <div class="mb-4">
                        <p class="text-xs font-semibold text-blue-600 mb-1.5">FROZEN (Gate 1-16)</p>
                        <div class="grid grid-cols-3 gap-2.5" id="gateGridFrozen"></div>
                    </div>
                    <div class="mb-4">
                        <p class="text-xs font-semibold text-amber-600 mb-1.5">DRY (Gate 17-27)</p>
                        <div class="grid grid-cols-3 gap-2.5" id="gateGridDry"></div>
                    </div>

                    <form id="gateForm" method="POST" action="">
                        @csrf
                        <input type="hidden" name="gate" id="selectedGateInput" value="">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600">Gate terpilih: <span id="selectedGateLabel" class="font-bold text-orange-600">-</span></p>
                            <div class="flex gap-2">
                                <button type="button" onclick="closeGateModal()"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">Batal</button>
                                <button type="submit" id="gateConfirmBtn" disabled
                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all">📥 Terima & Tetapkan Gate</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSelectAll() {
            const s = document.getElementById('selectAll');
            document.querySelectorAll('.row-checkbox').forEach(c => c.checked = s.checked);
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
            const t = document.querySelectorAll('.row-checkbox').length;
            const s = document.getElementById('selectAll');
            s.checked = t > 0 && c === t;
            s.indeterminate = c > 0 && c < t
        }

        function doBulkDelete() {
            const ids = [...document.querySelectorAll('.row-checkbox:checked')].map(c => c.dataset.id);
            if (ids.length === 0) return;
            confirmBulkDelete('{{ route('checkpoints.bulk-delete') }}', ids)
        }

        // Gate Modal Logic
        let selectedGateNumber = null;

        function openGateModal(checkpointId, noPolisi) {
            const modal = document.getElementById('gateModal');
            const form = document.getElementById('gateForm');
            const subtitle = document.getElementById('gateModalSubtitle');
            const loading = document.getElementById('gateModalLoading');
            const content = document.getElementById('gateModalContent');

            // Set form action
            form.action = '{{ url("checkpoints") }}/' + checkpointId + '/trigger-penerimaan';
            subtitle.textContent = 'Penerimaan dokumen: ' + noPolisi;

            // Reset
            selectedGateNumber = null;
            document.getElementById('selectedGateInput').value = '';
            document.getElementById('selectedGateLabel').textContent = '-';
            document.getElementById('gateConfirmBtn').disabled = true;
            loading.classList.remove('hidden');
            content.classList.add('hidden');

            modal.classList.remove('hidden');

            // Fetch available gates
            fetch('{{ route("gates.available") }}')
                .then(res => res.json())
                .then(gates => {
                    const frozenGrid = document.getElementById('gateGridFrozen');
                    const dryGrid = document.getElementById('gateGridDry');
                    frozenGrid.innerHTML = '';
                    dryGrid.innerHTML = '';

                    gates.forEach(gate => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.textContent = 'Gate-' + gate.nomor;
                        btn.dataset.gate = gate.nomor;

                        if (gate.available) {
                            btn.className = 'gate-btn py-3 px-4 text-sm font-bold rounded-xl border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:border-green-400 transition-all cursor-pointer';
                            btn.onclick = function() { selectGate(gate.nomor); };
                        } else {
                            btn.className = 'py-3 px-4 text-sm font-bold rounded-xl border border-red-200 bg-red-50 text-red-400 cursor-not-allowed opacity-60';
                            btn.disabled = true;
                            btn.title = 'Gate sedang digunakan';
                        }

                        if (gate.jenis_barang === 'FROZEN') {
                            frozenGrid.appendChild(btn);
                        } else {
                            dryGrid.appendChild(btn);
                        }
                    });

                    loading.classList.add('hidden');
                    content.classList.remove('hidden');
                })
                .catch(err => {
                    console.error('Error loading gates:', err);
                    loading.innerHTML = '<p class="text-sm text-red-500">Gagal memuat data gate.</p>';
                });
        }

        function selectGate(gateNumber) {
            selectedGateNumber = gateNumber;
            document.getElementById('selectedGateInput').value = gateNumber;
            document.getElementById('selectedGateLabel').textContent = 'Gate ' + gateNumber;
            document.getElementById('gateConfirmBtn').disabled = false;

            // Highlight selected
            document.querySelectorAll('.gate-btn').forEach(btn => {
                if (parseInt(btn.dataset.gate) === gateNumber) {
                    btn.className = 'gate-btn py-3 px-4 text-sm font-bold rounded-xl border-2 border-orange-500 bg-orange-100 text-orange-700 ring-2 ring-orange-300 transition-all cursor-pointer';
                } else {
                    btn.className = 'gate-btn py-3 px-4 text-sm font-bold rounded-xl border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:border-green-400 transition-all cursor-pointer';
                }
            });
        }

        function closeGateModal() {
            document.getElementById('gateModal').classList.add('hidden');
        }
    </script>
@endsection
