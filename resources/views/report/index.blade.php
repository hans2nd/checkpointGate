@extends('layouts.app')

@section('title', 'Report Checkpoint')

@section('content')
    <div class="space-y-4">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-800">📊 Report Checkpoint</h2>
                <p class="text-sm text-gray-500 mt-0.5">Laporan data checkpoint berdasarkan periode dan filter</p>
            </div>
            @if (Auth::user()->hasPermission('report.export'))
                <a href="{{ route('report.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </a>
            @endif
        </div>

        {{-- Filter Form --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <form method="GET" action="{{ route('report.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Aktivitas</label>
                    <select name="aktivitas"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Semua</option>
                        <option value="INBOUND" {{ $aktivitas === 'INBOUND' ? 'selected' : '' }}>INBOUND</option>
                        <option value="OUTBOUND" {{ $aktivitas === 'OUTBOUND' ? 'selected' : '' }}>OUTBOUND</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Barang</label>
                    <select name="jenis_barang"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Semua</option>
                        <option value="FROZEN" {{ $jenisBarang === 'FROZEN' ? 'selected' : '' }}>FROZEN</option>
                        <option value="DRY" {{ $jenisBarang === 'DRY' ? 'selected' : '' }}>DRY</option>
                        <option value="CHILLED" {{ $jenisBarang === 'CHILLED' ? 'selected' : '' }}>CHILLED</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Semua</option>
                        <option value="START" {{ $status === 'START' ? 'selected' : '' }}>START</option>
                        <option value="ON LOADING" {{ $status === 'ON LOADING' ? 'selected' : '' }}>ON LOADING</option>
                        <option value="FINISH" {{ $status === 'FINISH' ? 'selected' : '' }}>FINISH</option>
                        <option value="CANCEL" {{ $status === 'CANCEL' ? 'selected' : '' }}>CANCEL</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-all">
                        Filter
                    </button>
                    <a href="{{ route('report.index') }}"
                        class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-all"
                        title="Reset">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Kendaraan</p>
                <p class="text-2xl font-bold text-orange-500 mt-1">{{ $totalKendaraan }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Finish</p>
                <p class="text-2xl font-bold text-emerald-500 mt-1">{{ $totalFinish }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">On Loading</p>
                <p class="text-2xl font-bold text-blue-500 mt-1">{{ $totalOnLoading }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Cancel</p>
                <p class="text-2xl font-bold text-red-500 mt-1">{{ $totalCancel }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg Durasi Loading</p>
                <p class="text-2xl font-bold text-purple-500 mt-1 font-mono">{{ $avgDurasi ?? '-' }}</p>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">No</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">No Polisi</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">Vendor</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">Kendaraan</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">Barang</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">Aktivitas</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">Penerimaan</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">Start</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">End</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">Gate</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">Durasi</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap text-center">Penyerahan</th>
                            <th class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">Durasi Dok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($checkpoints as $index => $cp)
                            @php
                                $gateLabel = '-';
                                if ($cp->gate) {
                                    if ($cp->gate >= 1 && $cp->gate <= 16) {
                                        $gateLabel = 'F-' . $cp->gate;
                                    } elseif ($cp->gate >= 17 && $cp->gate <= 27) {
                                        $gateLabel = 'D-' . ($cp->gate - 16);
                                    } else {
                                        $gateLabel = 'Gate-' . $cp->gate;
                                    }
                                }
                                $durasiDok = '';
                                if ($cp->waktu_penerimaan_dokumen && $cp->waktu_penyerahan_dokumen) {
                                    $diff = $cp->waktu_penerimaan_dokumen->diff($cp->waktu_penyerahan_dokumen);
                                    $hours = $diff->days * 24 + $diff->h;
                                    $durasiDok = sprintf('%02d:%02d:%02d', $hours, $diff->i, $diff->s);
                                }
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 py-2.5 text-gray-500 text-xs">{{ ($checkpoints->currentPage() - 1) * $checkpoints->perPage() + $index + 1 }}</td>
                                <td class="px-3 py-2.5 text-gray-700 text-xs whitespace-nowrap">{{ $cp->tanggal->format('d/m/Y') }}</td>
                                <td class="px-3 py-2.5 font-medium text-gray-900 text-xs whitespace-nowrap">{{ $cp->no_polisi }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $cp->vendor }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $cp->jenis_kendaraan }}</td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $cp->jenis_barang === 'FROZEN' ? 'bg-cyan-50 text-cyan-700' : 'bg-orange-50 text-orange-700' }}">{{ $cp->jenis_barang }}</span>
                                </td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $cp->aktivitas === 'INBOUND' ? 'bg-orange-50 text-orange-700' : 'bg-amber-50 text-amber-700' }}">{{ $cp->aktivitas }}</span>
                                </td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">{{ $cp->waktu_penerimaan_dokumen ? $cp->waktu_penerimaan_dokumen->format('H:i:s') : '—' }}</td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">{{ $cp->waktu_start ? $cp->waktu_start->format('H:i:s') : '—' }}</td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">{{ $cp->waktu_end ? $cp->waktu_end->format('H:i:s') : '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-600 text-center text-xs">{{ $gateLabel }}</td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    @if ($cp->status === 'CANCEL')
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-red-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> CANCEL
                                        </span>
                                    @elseif ($cp->status === 'FINISH')
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> FINISH
                                        </span>
                                    @elseif ($cp->status === 'ON LOADING')
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> ON LOADING
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-yellow-50 text-yellow-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> {{ $cp->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-gray-500 font-mono text-xs whitespace-nowrap">{{ $cp->durasi ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-center text-xs text-gray-600 whitespace-nowrap">{{ $cp->waktu_penyerahan_dokumen ? $cp->waktu_penyerahan_dokumen->format('H:i:s') : '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-500 font-mono text-xs whitespace-nowrap">{{ $durasiDok ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="px-4 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="font-medium">Tidak ada data</p>
                                        <p class="text-sm mt-1">Ubah filter untuk melihat data checkpoint</p>
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
    </div>
@endsection
