@extends('layouts.app')

@section('title', 'Data Checkpoint')

@section('content')
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">Total {{ $checkpoints->total() }} record</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if (Auth::user()->hasPermission('checkpoint.delete'))
                    <button type="button" id="bulkDeleteBtn" onclick="doBulkDelete()"
                        class="hidden items-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Terpilih (<span id="selectedCount">0</span>)
                    </button>
                @endif
                @if (Auth::user()->hasPermission('checkpoint.import'))
                    <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import Excel
                    </button>
                @endif
                <a href="{{ route('checkpoints.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
                @if (Auth::user()->hasPermission('checkpoint.create'))
                    <a href="{{ route('checkpoints.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Data
                    </a>
                @endif
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

        {{-- Column Visibility Toggle --}}
        <div class="flex justify-end">
            <div class="relative" id="colToggleWrap">
                <button type="button" onclick="document.getElementById('colDropdown').classList.toggle('hidden')"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-xs font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Atur Kolom
                    <span id="colCount"
                        class="bg-orange-100 text-orange-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full"></span>
                </button>
                <div id="colDropdown"
                    class="hidden absolute right-0 mt-1 w-56 bg-white border border-gray-200 rounded-xl shadow-lg z-50 py-2 max-h-80 overflow-y-auto">
                    <div class="px-3 py-1.5 border-b border-gray-100 flex justify-between items-center">
                        <span class="text-xs font-semibold text-gray-500">Tampilkan/Sembunyikan</span>
                        <button type="button" onclick="resetColumns()"
                            class="text-[10px] text-indigo-500 hover:text-indigo-700 font-semibold">Reset</button>
                    </div>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-tanggal" checked> Tanggal
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-nopol" checked> No Polisi
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-vendor" checked> Vendor
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-kendaraan" checked> Kendaraan
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-barang" checked> Barang
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-aktivitas" checked> Aktivitas
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-penerimaan" checked> Penerimaan Dok
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-start" checked> Start Loading
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-end" checked> End Loading
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-gate" checked> Gate
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-status" checked> Status
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-durasi" checked> Durasi
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-penyerahan" checked> Penyerahan Dok
                    </label>
                    <label
                        class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 cursor-pointer text-xs text-gray-700">
                        <input type="checkbox"
                            class="col-toggle rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-col="col-durasi-dok" checked> Durasi Dokumen
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            @if (Auth::user()->hasPermission('checkpoint.delete'))
                                <th class="px-3 py-3"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"
                                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500"></th>
                            @endif
                            <th data-col="col-tanggal"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Tanggal</th>
                            <th data-col="col-nopol"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                No Polisi</th>
                            <th data-col="col-vendor"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Vendor</th>
                            <th data-col="col-kendaraan"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Kendaraan</th>
                            <th data-col="col-barang"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Barang</th>
                            <th data-col="col-aktivitas"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Aktivitas</th>
                            <th data-col="col-penerimaan"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                Penerimaan<br><span class="text-[10px] font-normal normal-case text-gray-400">Dokumen
                                    IN</span></th>
                            <th data-col="col-start"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                Start<br><span class="text-[10px] font-normal normal-case text-gray-400">Loading</span>
                            </th>
                            <th data-col="col-end"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                End<br><span class="text-[10px] font-normal normal-case text-gray-400">Loading</span></th>
                            <th data-col="col-gate"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Gate</th>
                            <th data-col="col-status"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Status</th>
                            <th data-col="col-durasi"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Durasi</th>
                            <th data-col="col-penyerahan"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                Penyerahan<br><span class="text-[10px] font-normal normal-case text-gray-400">Dokumen
                                    OUT</span></th>
                            <th data-col="col-durasi-dok"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                Durasi<br><span class="text-[10px] font-normal normal-case text-gray-400">Dokumen</span>
                            </th>
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($checkpoints as $cp)
                            <tr class="hover:bg-gray-50/50 transition-colors" data-cp-id="{{ $cp->id }}"
                                data-cp-nopol="{{ $cp->no_polisi }}" data-cp-vendor="{{ $cp->vendor }}"
                                data-cp-driver="{{ $cp->driver ?? '-' }}" data-cp-tipe="{{ $cp->tipe }}"
                                data-cp-kendaraan="{{ $cp->jenis_kendaraan }}" data-cp-barang="{{ $cp->jenis_barang }}"
                                data-cp-aktivitas="{{ $cp->aktivitas }}" data-cp-gate="{{ $cp->gate ?? '-' }}"
                                data-cp-tanggal="{{ $cp->tanggal->format('d/m/Y') }}"
                                data-cp-penerimaan="{{ $cp->waktu_penerimaan_dokumen?->format('d/m/Y H:i:s') ?? '' }}">
                                @if (Auth::user()->hasPermission('checkpoint.delete'))
                                    <td class="px-3 py-2.5"><input type="checkbox" data-id="{{ $cp->id }}"
                                            class="row-checkbox rounded border-gray-300 text-orange-500 focus:ring-orange-500"
                                            onchange="updateSelectedCount()"></td>
                                @endif
                                <td data-col="col-tanggal" class="px-3 py-2.5 text-gray-700 whitespace-nowrap text-xs">
                                    {{ $cp->tanggal->format('d/m/Y') }}</td>
                                <td data-col="col-nopol"
                                    class="px-3 py-2.5 font-medium text-gray-900 whitespace-nowrap text-xs">
                                    {{ $cp->no_polisi }}</td>
                                <td data-col="col-vendor" class="px-3 py-2.5 text-gray-600 whitespace-nowrap text-xs">
                                    {{ $cp->vendor }}</td>
                                <td data-col="col-kendaraan" class="px-3 py-2.5 text-gray-600 whitespace-nowrap text-xs">
                                    {{ $cp->jenis_kendaraan }}
                                </td>
                                <td data-col="col-barang" class="px-3 py-2.5 whitespace-nowrap"><span
                                        class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $cp->jenis_barang === 'FROZEN' ? 'bg-cyan-50 text-cyan-700' : 'bg-orange-50 text-orange-700' }}">{{ $cp->jenis_barang }}</span>
                                </td>
                                <td data-col="col-aktivitas" class="px-3 py-2.5 whitespace-nowrap"><span
                                        class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $cp->aktivitas === 'INBOUND' ? 'bg-orange-50 text-orange-700' : 'bg-amber-50 text-amber-700' }}">{{ $cp->aktivitas }}</span>
                                </td>
                                <td data-col="col-penerimaan" class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if ($cp->waktu_penerimaan_dokumen)
                                        <span
                                            class="text-xs text-gray-600">{{ $cp->waktu_penerimaan_dokumen->format('H:i:s') }}</span>
                                    @elseif(Auth::user()->hasPermission('checkpoint.trigger'))
                                        <button type="button"
                                            onclick="openGateModal({{ $cp->id }}, '{{ $cp->no_polisi }}')"
                                            class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-[10px] font-semibold rounded-md transition-all shadow-sm">📥
                                            Terima</button>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                                <td data-col="col-start" class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if ($cp->waktu_start)
                                        <span class="text-xs text-gray-600">{{ $cp->waktu_start->format('H:i:s') }}</span>
                                    @elseif($cp->waktu_penerimaan_dokumen && $cp->gate && Auth::user()->hasPermission('checkpoint.trigger'))
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
                                <td data-col="col-end" class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if ($cp->waktu_end)
                                        <span class="text-xs text-gray-600">{{ $cp->waktu_end->format('H:i:s') }}</span>
                                    @elseif($cp->waktu_start && Auth::user()->hasPermission('checkpoint.trigger'))
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
                                <td data-col="col-gate" class="px-3 py-2.5 text-gray-600 text-center text-xs">
                                    {{ $cp->gate ?? '-' }}</td>

                                <td data-col="col-status" class="px-3 py-2.5 whitespace-nowrap">
                                    @if ($cp->status === 'FINISH')
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            FINISH
                                        </span>
                                    @elseif($cp->status === 'ON LOADING')
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                            ON LOADING
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-yellow-50 text-yellow-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                            {{ $cp->status }}
                                        </span>
                                    @endif
                                </td>
                                <td data-col="col-durasi"
                                    class="px-3 py-2.5 text-gray-500 font-mono text-xs whitespace-nowrap">
                                    {{ $cp->durasi ?? '-' }}</td>
                                <td data-col="col-penyerahan" class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if ($cp->waktu_penyerahan_dokumen)
                                        <span
                                            class="text-xs text-gray-600">{{ $cp->waktu_penyerahan_dokumen->format('H:i:s') }}</span>
                                    @elseif($cp->waktu_end && Auth::user()->hasPermission('checkpoint.trigger'))
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
                                <td data-col="col-durasi-dok" class="px-3 py-2.5 text-center whitespace-nowrap">
                                    @if ($cp->waktu_penerimaan_dokumen && $cp->waktu_penyerahan_dokumen)
                                        @php
                                            $diff = $cp->waktu_penerimaan_dokumen->diff($cp->waktu_penyerahan_dokumen);
                                            $hours = $diff->days * 24 + $diff->h;
                                            $durasiDokumen = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
                                        @endphp
                                        <span class="text-xs font-mono text-gray-600">{{ $durasiDokumen }}</span>
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
                                        @if (Auth::user()->hasPermission('checkpoint.edit'))
                                            <a href="{{ route('checkpoints.edit', $cp) }}"
                                                class="p-1 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded transition-all"
                                                title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endif
                                        @if (Auth::user()->hasPermission('checkpoint.delete'))
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
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="16" class="px-4 py-12 text-center">
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
                    <button onclick="closeGateModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <!-- Loading state -->
                <div id="gateModalLoading" class="py-8 text-center">
                    <svg class="animate-spin h-8 w-8 text-orange-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
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

                    <form id="gateForm" method="POST" action="" onsubmit="handleGateSubmit(event)">
                        @csrf
                        <input type="hidden" name="gate" id="selectedGateInput" value="">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div>
                                <p class="text-sm text-gray-600">Gate terpilih: <span id="selectedGateLabel"
                                        class="font-bold text-orange-600">-</span></p>
                                <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                    <input type="checkbox" id="printTicketCheck" checked
                                        class="rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                                    <span class="text-xs text-gray-600">🖨️ Print Ticket Gate setelah terima</span>
                                </label>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" onclick="closeGateModal()"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg">Batal</button>
                                <button type="submit" id="gateConfirmBtn" disabled
                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all">📥
                                    Terima & Tetapkan Gate</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ===== COLUMN VISIBILITY =====
        const STORAGE_KEY = 'checkpoint_col_prefs';
        const ALL_COLS = ['col-tanggal', 'col-nopol', 'col-vendor', 'col-kendaraan', 'col-barang', 'col-aktivitas',
            'col-penerimaan', 'col-start', 'col-end', 'col-gate', 'col-status', 'col-durasi', 'col-penyerahan',
            'col-durasi-dok'
        ];

        function getColPrefs() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                return saved ? JSON.parse(saved) : ALL_COLS.slice();
            } catch {
                return ALL_COLS.slice();
            }
        }

        function saveColPrefs(visible) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(visible));
        }

        function applyColumnVisibility() {
            const visible = getColPrefs();
            ALL_COLS.forEach(col => {
                const show = visible.includes(col);
                document.querySelectorAll(`[data-col="${col}"]`).forEach(el => {
                    el.style.display = show ? '' : 'none';
                });
            });
            // Sync checkboxes
            document.querySelectorAll('.col-toggle').forEach(cb => {
                cb.checked = visible.includes(cb.dataset.col);
            });
            // Update counter
            const hidden = ALL_COLS.length - visible.length;
            const counter = document.getElementById('colCount');
            counter.textContent = hidden > 0 ? (ALL_COLS.length - hidden) + '/' + ALL_COLS.length : ALL_COLS.length + '/' +
                ALL_COLS.length;
        }

        function resetColumns() {
            saveColPrefs(ALL_COLS.slice());
            applyColumnVisibility();
        }

        document.addEventListener('DOMContentLoaded', function() {
            applyColumnVisibility();
            document.querySelectorAll('.col-toggle').forEach(cb => {
                cb.addEventListener('change', function() {
                    const prefs = getColPrefs();
                    if (this.checked) {
                        if (!prefs.includes(this.dataset.col)) prefs.push(this.dataset.col);
                    } else {
                        const idx = prefs.indexOf(this.dataset.col);
                        if (idx > -1) prefs.splice(idx, 1);
                    }
                    saveColPrefs(prefs);
                    applyColumnVisibility();
                });
            });
            // Close dropdown on click outside
            document.addEventListener('click', function(e) {
                const wrap = document.getElementById('colToggleWrap');
                const dd = document.getElementById('colDropdown');
                if (wrap && !wrap.contains(e.target)) dd.classList.add('hidden');
            });
        });

        // ===== BULK SELECT =====
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

        // ===== GATE MODAL =====
        let selectedGateNumber = null;
        let currentCheckpointId = null;

        function openGateModal(checkpointId, noPolisi) {
            currentCheckpointId = checkpointId;
            const modal = document.getElementById('gateModal');
            const form = document.getElementById('gateForm');
            const subtitle = document.getElementById('gateModalSubtitle');
            const loading = document.getElementById('gateModalLoading');
            const content = document.getElementById('gateModalContent');

            form.action = '{{ url('checkpoints') }}/' + checkpointId + '/trigger-penerimaan';
            subtitle.textContent = 'Penerimaan dokumen: ' + noPolisi;

            selectedGateNumber = null;
            document.getElementById('selectedGateInput').value = '';
            document.getElementById('selectedGateLabel').textContent = '-';
            document.getElementById('gateConfirmBtn').disabled = true;
            loading.classList.remove('hidden');
            content.classList.add('hidden');
            modal.classList.remove('hidden');

            fetch('{{ route('gates.available') }}')
                .then(res => res.json())
                .then(gates => {
                    const frozenGrid = document.getElementById('gateGridFrozen');
                    const dryGrid = document.getElementById('gateGridDry');
                    frozenGrid.innerHTML = '';
                    dryGrid.innerHTML = '';

                    gates.forEach(gate => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        let label = '';

                        if (gate.nomor >= 1 && gate.nomor <= 16) {
                            label = 'F-' + gate.nomor;
                        } else if (gate.nomor >= 17 && gate.nomor <= 27) {
                            label = 'D-' + (gate.nomor - 16);
                        } else {
                            label = 'Gate-' + gate.nomor;
                        }

                        btn.textContent = label;
                        btn.dataset.gate = gate.nomor;

                        if (gate.available) {
                            btn.className =
                                'gate-btn py-3 px-4 text-sm font-bold rounded-xl border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:border-green-400 transition-all cursor-pointer';
                            btn.onclick = function() {
                                selectGate(gate.nomor);
                            };
                        } else {
                            btn.className =
                                'py-3 px-4 text-sm font-bold rounded-xl border border-red-200 bg-red-50 text-red-400 cursor-not-allowed opacity-60';
                            btn.disabled = true;
                            btn.title = 'Gate sedang digunakan';
                        }

                        if (gate.jenis_barang === 'FROZEN') frozenGrid.appendChild(btn);
                        else dryGrid.appendChild(btn);
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

            document.querySelectorAll('.gate-btn').forEach(btn => {
                if (parseInt(btn.dataset.gate) === gateNumber) {
                    btn.className =
                        'gate-btn py-3 px-4 text-sm font-bold rounded-xl border-2 border-orange-500 bg-orange-100 text-orange-700 ring-2 ring-orange-300 transition-all cursor-pointer';
                } else {
                    btn.className =
                        'gate-btn py-3 px-4 text-sm font-bold rounded-xl border border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:border-green-400 transition-all cursor-pointer';
                }
            });
        }

        function closeGateModal() {
            document.getElementById('gateModal').classList.add('hidden');
        }

        // ===== PRINT TICKET =====
        function handleGateSubmit(e) {
            if (document.getElementById('printTicketCheck').checked && currentCheckpointId) {
                // Get data from the table row
                const row = document.querySelector(`tr[data-cp-id="${currentCheckpointId}"]`);
                if (row) {
                    const now = new Date();
                    const pad = n => String(n).padStart(2, '0');
                    const waktuTerima = pad(now.getDate()) + '/' + pad(now.getMonth() + 1) + '/' + now.getFullYear() + ' ' +
                        pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());

                    printGateTicket({
                        noPolisi: row.dataset.cpNopol,
                        vendor: row.dataset.cpVendor,
                        driver: row.dataset.cpDriver,
                        tipe: row.dataset.cpTipe,
                        jenisKendaraan: row.dataset.cpKendaraan,
                        jenisBarang: row.dataset.cpBarang,
                        aktivitas: row.dataset.cpAktivitas,
                        gate: selectedGateNumber,
                        tanggal: row.dataset.cpTanggal,
                        waktuTerima: waktuTerima,
                        printedBy: '{{ Auth::user()->name }}',
                    });
                }
            }
            // Allow form to submit normally
        }

        function printGateTicket(data) {
            const printWindow = window.open('', '_blank', 'width=450,height=640');

            let gateLabel = '';

            if (data.gate >= 1 && data.gate <= 16) {
                gateLabel = 'F-' + data.gate;
            } else if (data.gate >= 17 && data.gate <= 27) {
                gateLabel = 'D-' + (data.gate - 16);
            } else {
                gateLabel = 'Gate-' + data.gate;
            }

            printWindow.document.write(`<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Ticket Gate - ${data.noPolisi}</title>
<style>
    @page { size: 105mm 148.5mm; margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; width: 105mm; min-height: 148.5mm; padding: 6mm; background: #fff; color: #1a1a1a; }
    .ticket { border: 2px solid #222; border-radius: 8px; padding: 5mm; height: calc(148.5mm - 12mm); display: flex; flex-direction: column; }
    .header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 4mm; margin-bottom: 4mm; }
    .header h1 { font-size: 14pt; font-weight: 900; letter-spacing: 1px; margin-bottom: 1mm; }
    .header h2 { font-size: 9pt; color: #666; font-weight: 500; }
    .gate-badge { background: #222; color: #fff; font-size: 22pt; font-weight: 900; padding: 3mm 6mm; border-radius: 6px; display: inline-block; margin: 3mm 0 2mm; letter-spacing: 2px; }
    .info-grid { flex: 1; }
    .info-row { display: flex; border-bottom: 1px solid #eee; padding: 1.8mm 0; }
    .info-label { width: 35mm; font-size: 7.5pt; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .info-value { flex: 1; font-size: 8.5pt; font-weight: 700; }
    .badge { display: inline-block; padding: 0.8mm 2.5mm; border-radius: 3px; font-size: 7pt; font-weight: 700; }
    .badge-frozen { background: #e0f2fe; color: #0369a1; }
    .badge-dry { background: #fff7ed; color: #c2410c; }
    .badge-inbound { background: #fef3c7; color: #92400e; }
    .badge-outbound { background: #dbeafe; color: #1e40af; }
    .footer { border-top: 2px dashed #ccc; padding-top: 3mm; margin-top: 3mm; text-align: center; }
    .footer p { font-size: 6.5pt; color: #999; }
    .footer .printed-by { font-size: 7pt; color: #555; font-weight: 600; margin-top: 1mm; }
    @media print { body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } }
</style>
</head>
<body>
<div class="ticket">
    <div class="header">
        <h1>CHECKPOINT GIIC</h1>
        <h2>Ticket Penerimaan Dokumen</h2>
        <div class="gate-badge">GATE ${gateLabel}</div>
        
        <div style="margin-top: 3mm;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${gateLabel}" 
                style="width:30mm; height:30mm;" />
        </div>
    </div>
    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">No. Polisi</span>
            <span class="info-value">${data.noPolisi}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Vendor</span>
            <span class="info-value">${data.vendor}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Driver</span>
            <span class="info-value">${data.driver}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tipe</span>
            <span class="info-value">${data.tipe}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jenis Kendaraan</span>
            <span class="info-value">${data.jenisKendaraan}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Storage</span>
            <span class="info-value"><span class="badge ${data.jenisBarang === 'FROZEN' ? 'badge-frozen' : 'badge-dry'}">${data.jenisBarang}</span></span>
        </div>
        <div class="info-row">
            <span class="info-label">Aktivitas</span>
            <span class="info-value"><span class="badge ${data.aktivitas === 'INBOUND' ? 'badge-inbound' : 'badge-outbound'}">${data.aktivitas}</span></span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal</span>
            <span class="info-value">${data.tanggal}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Waktu Terima</span>
            <span class="info-value">${data.waktuTerima}</span>
        </div>
    </div>
    <div class="footer">
        <div class="printed-by">Diterima oleh: ${data.printedBy}</div>
        <p>Dokumen ini sebagai bukti penerimaan dokumen dan kendaraan siap loading.</p>
        <p style="margin-top:1mm">Dicetak: ${data.waktuTerima}</p>
    </div>
</div>
<script>window.onload=function(){window.print();}<\/script>
</body>
</html>`);
            printWindow.document.close();
        }
    </script>
@endsection
