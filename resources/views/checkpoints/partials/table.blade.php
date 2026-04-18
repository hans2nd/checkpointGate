        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            @if (Auth::user()->hasPermission('checkpoint.delete'))
                                <th class="px-3 py-3"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"
                                        class="rounded border-gray-300 text-orange-500 focus:ring-orange-500"></th>
                            @endif
                            <th
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                No</th>
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
                                End<br><span class="text-[10px] font-normal normal-case text-gray-400">Loading</span>
                            </th>
                            <th data-col="col-gate"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Gate</th>
                            <th data-col="col-status"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Status</th>
                            <th data-col="col-durasi"
                                class="px-3 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider whitespace-nowrap">
                                Durasi<br><span class="text-[10px] font-normal normal-case text-gray-400">Loading</span>
                            </th>
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
                            @endphp

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
                                <td class="px-3 py-2.5 text-gray-500 whitespace-nowrap text-xs">
                                    {{ format_row_number($checkpoints, $index) }}</td>
                                <td data-col="col-tanggal" class="px-3 py-2.5 text-gray-700 whitespace-nowrap text-xs">
                                    {{ $cp->tanggal->format('d/m/Y') }}</td>
                                <td data-col="col-nopol"
                                    class="px-3 py-2.5 font-medium text-gray-900 whitespace-nowrap text-xs">
                                    {{ $cp->no_polisi }}</td>
                                <td data-col="col-vendor" class="px-3 py-2.5 text-gray-600 whitespace-nowrap text-xs">
                                    {{ $cp->vendor }}</td>
                                <td data-col="col-kendaraan"
                                    class="px-3 py-2.5 text-gray-600 whitespace-nowrap text-xs">
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
                                        <span
                                            class="text-xs text-gray-600">{{ $cp->waktu_start->format('H:i:s') }}</span>
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
                                        <span
                                            class="text-xs text-gray-600">{{ $cp->waktu_end->format('H:i:s') }}</span>
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
                                    {{ $gateLabel ?? '-' }}</td>

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
                                        <form method="POST"
                                            action="{{ route('checkpoints.trigger-penyerahan', $cp) }}"
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
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endif
                                        @if (Auth::user()->hasPermission('checkpoint.delete'))
                                            <form id="deleteForm{{ $cp->id }}" method="POST"
                                                action="{{ route('checkpoints.destroy', $cp) }}">@csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button"
                                                onclick="confirmDelete(document.getElementById('deleteForm{{ $cp->id }}'))"
                                                class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-all"
                                                title="Hapus">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="17" class="px-4 py-12 text-center">
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
