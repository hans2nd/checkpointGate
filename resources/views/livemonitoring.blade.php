@extends('layouts.app')

@section('title', 'Live Monitoring')

@section('content')
<div class="space-y-5" id="monitoring-container">
    {{-- Header with Date Picker --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <label class="text-sm text-gray-500">Periode:</label>
            <input type="date" id="tanggal-picker" value="{{ $tanggalValue }}"
                   class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent cursor-pointer">
            @if(!$isToday)
                <a href="{{ route('livemonitoring') }}" class="text-xs px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-full font-medium transition-colors">
                    ↩ Hari Ini
                </a>
            @endif
        </div>
        <div class="flex items-center gap-2">
            @if($isToday)
                <span id="refresh-indicator" class="flex items-center gap-1.5 text-xs text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full">
                    <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                    Live
                </span>
            @else
                <span class="flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat
                </span>
            @endif
            <span id="last-update" class="text-xs text-gray-400"></span>
        </div>
    </div>

    {{-- GATE GRID: 4 columns, Gate 1-27 sequential --}}
    <div>
        <div class="grid grid-cols-4 gap-3">
            @for($i = 1; $i <= 27; $i++)
                @php
                    $g = $gates[$i];
                    $cp = $g['checkpoint'];
                    $isFrozen = $i <= 16;
                    $borderColor = $isFrozen ? 'border-blue-200' : 'border-amber-200';
                    $headerBg = $isFrozen ? 'bg-blue-500' : 'bg-amber-500';
                    $typeLabel = $isFrozen ? 'FROZEN' : 'DRY';
                    $displayGate = $isFrozen ? 'F-' . $i : 'D-' . ($i - 16);
                @endphp
                <div id="gate-{{ $i }}" class="rounded-xl border-2 overflow-hidden transition-all duration-300 {{ $borderColor }}">
                    <div class="{{ $headerBg }} text-white text-center py-1.5 px-2">
                        <p class="text-xs font-bold">{{ $displayGate }}</p>
                        <p class="text-[9px] font-medium opacity-80">{{ $typeLabel }}</p>
                    </div>
                    <div class="gate-body p-3 min-h-[80px] flex flex-col items-center justify-center transition-all duration-500"
                         data-waktu-start="{{ $cp && $cp->waktu_start ? $cp->waktu_start->toIso8601String() : '' }}"
                         data-waktu-end="{{ $cp && $cp->waktu_end ? $cp->waktu_end->toIso8601String() : '' }}"
                         data-waktu-penerimaan="{{ $cp && $cp->waktu_penerimaan_dokumen ? $cp->waktu_penerimaan_dokumen->toIso8601String() : '' }}"
                         data-waktu-penyerahan="{{ $cp && $cp->waktu_penyerahan_dokumen ? $cp->waktu_penyerahan_dokumen->toIso8601String() : '' }}"
                         data-status="{{ $cp ? $cp->status : '' }}">
                        @if($cp)
                            <p class="text-xs font-bold text-gray-800 truncate w-full text-center">{{ $cp->no_polisi }}</p>
                            <p class="text-[10px] text-gray-400 truncate w-full text-center">{{ $cp->vendor }}</p>
                            @if($cp->waktu_penyerahan_dokumen)
                                <span class="gate-status mt-1 text-[9px] px-2 py-0.5 rounded-full font-bold bg-purple-100 text-purple-700">✅ COMPLETED</span>
                            @elseif($cp->status === 'FINISH')
                                <span class="mt-1 text-[10px] px-2 py-0.5 rounded-full font-bold font-mono bg-emerald-100 text-emerald-700">{{ $cp->durasi ?? 'DONE' }}</span>
                                <span class="gate-status mt-0.5 text-[9px] px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-700">🏁 FINISH LOADING</span>
                            @elseif($cp->status === 'ON LOADING' && $cp->waktu_start)
                                <span class="gate-timer mt-1 text-[10px] px-2 py-0.5 rounded font-bold font-mono" data-start="{{ $cp->waktu_start->toIso8601String() }}">00:00:00</span>
                                <span class="gate-status mt-0.5 text-[9px] px-2 py-0.5 rounded-full font-bold animate-pulse bg-blue-100 text-blue-700">⏳ ON LOADING</span>
                            @elseif($cp->waktu_penerimaan_dokumen)
                                <span class="gate-status mt-1 text-[9px] px-2 py-0.5 rounded-full font-bold bg-yellow-100 text-yellow-700">📋 ASSIGN</span>
                            @endif
                        @else
                            <span class="text-[10px] text-gray-300 italic">kosong</span>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    {{-- Bottom Section: Activity Summary + Average Times --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Activity Summary --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Ringkasan Aktivitas</h3>
                <p class="text-xs text-gray-400">Periode: {{ $periode }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2.5 text-left font-semibold text-gray-600 text-xs uppercase">Aktivitas</th>
                            <th class="px-4 py-2.5 text-center font-semibold text-gray-600 text-xs uppercase">On Process</th>
                            <th class="px-4 py-2.5 text-center font-semibold text-gray-600 text-xs uppercase">Finish</th>
                        </tr>
                    </thead>
                    <tbody id="activity-summary-body" class="divide-y divide-gray-50">
                        @foreach($activitySummary as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-2.5 font-medium text-gray-700 text-xs">{{ $row['label'] }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="inline-flex min-w-[28px] items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold {{ $row['on_process'] > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $row['on_process'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="inline-flex min-w-[28px] items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold {{ $row['finish'] > 0 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $row['finish'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Average Loading Time --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Rata-rata Waktu Loading</h3>
                <p class="text-xs text-gray-400">ALT = Avg Loading Time (Inbound) · AUT = Avg Unloading Time (Outbound)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th rowspan="2" class="px-3 py-2 text-left font-semibold text-gray-600 text-xs uppercase border-r border-gray-200">Kendaraan</th>
                            <th colspan="2" class="px-3 py-1.5 text-center font-semibold text-xs uppercase bg-green-50 text-green-700 border-b border-green-200">DRY</th>
                            <th colspan="2" class="px-3 py-1.5 text-center font-semibold text-xs uppercase bg-blue-50 text-blue-700 border-b border-blue-200">FROZEN</th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-1.5 text-center font-medium text-[10px] uppercase text-green-600 bg-green-50">ALT</th>
                            <th class="px-3 py-1.5 text-center font-medium text-[10px] uppercase text-green-600 bg-green-50 border-r border-gray-200">AUT</th>
                            <th class="px-3 py-1.5 text-center font-medium text-[10px] uppercase text-blue-600 bg-blue-50">ALT</th>
                            <th class="px-3 py-1.5 text-center font-medium text-[10px] uppercase text-blue-600 bg-blue-50">AUT</th>
                        </tr>
                    </thead>
                    <tbody id="avg-times-body" class="divide-y divide-gray-50">
                        @foreach($avgTimes as $row)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-3 py-2 font-medium text-gray-700 text-xs border-r border-gray-100">{{ $row['jenis_kendaraan'] }}</td>
                            <td class="px-3 py-2 text-center font-mono text-xs {{ $row['DRY_ALT'] ? 'text-green-700 bg-green-50/50' : 'text-gray-300' }}">{{ $row['DRY_ALT'] ?? '—' }}</td>
                            <td class="px-3 py-2 text-center font-mono text-xs border-r border-gray-100 {{ $row['DRY_AUT'] ? 'text-green-700 bg-green-50/50' : 'text-gray-300' }}">{{ $row['DRY_AUT'] ?? '—' }}</td>
                            <td class="px-3 py-2 text-center font-mono text-xs {{ $row['FROZEN_ALT'] ? 'text-blue-700 bg-blue-50/50' : 'text-gray-300' }}">{{ $row['FROZEN_ALT'] ?? '—' }}</td>
                            <td class="px-3 py-2 text-center font-mono text-xs {{ $row['FROZEN_AUT'] ? 'text-blue-700 bg-blue-50/50' : 'text-gray-300' }}">{{ $row['FROZEN_AUT'] ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="bg-white rounded-xl border border-gray-100 p-3 shadow-sm">
        <p class="text-xs font-semibold text-gray-600 mb-2">Keterangan Status Gate:</p>
        <div class="flex flex-wrap gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1.5">
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-yellow-100 text-yellow-700">📋 ASSIGN</span> Dokumen diterima, menunggu loading
            </span>
            <span class="flex items-center gap-1.5">
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-700">⏳ ON LOADING</span> Sedang proses loading
            </span>
            <span class="flex items-center gap-1.5">
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-700">🏁 FINISH LOADING</span> Loading selesai
            </span>
            <span class="flex items-center gap-1.5">
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-purple-100 text-purple-700">✅ COMPLETED</span> Dokumen diserahkan
            </span>
        </div>
        <div class="flex flex-wrap gap-4 text-xs text-gray-500 mt-2 pt-2 border-t border-gray-100">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-blue-500"></span> FROZEN (Gate F-1 - F-16)
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-amber-500"></span> DRY (Gate D-1 - D-11)
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-emerald-400"></span> Loading &lt; 30 menit
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-yellow-400"></span> Loading 30-60 menit
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-red-500"></span> Loading &gt; 60 menit
            </span>
        </div>
    </div>
</div>

<script>
    // ===== DURATION HELPERS =====
    function getDurationMinutes(startISO, endISO) {
        const start = new Date(startISO);
        const end = endISO ? new Date(endISO) : new Date();
        return (end - start) / 60000;
    }

    function formatDuration(startISO, endISO) {
        const start = new Date(startISO);
        const end = endISO ? new Date(endISO) : new Date();
        let totalSec = Math.max(0, Math.floor((end - start) / 1000));
        const h = Math.floor(totalSec / 3600);
        const m = Math.floor((totalSec % 3600) / 60);
        const s = totalSec % 60;
        return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function getDurationColor(minutes) {
        if (minutes > 60) return { bg: 'bg-red-100', text: 'text-red-800', border: 'border-red-400', bodyBg: '#FEE2E2' };
        if (minutes > 30) return { bg: 'bg-yellow-100', text: 'text-yellow-800', border: 'border-yellow-400', bodyBg: '#FEF9C3' };
        return { bg: 'bg-emerald-100', text: 'text-emerald-800', border: 'border-emerald-400', bodyBg: '#D1FAE5' };
    }

    // ===== APPLY INITIAL COLORS ON PAGE LOAD =====
    function applyGateColors() {
        document.querySelectorAll('.gate-body').forEach(body => {
            const status = body.dataset.status;
            const waktuStart = body.dataset.waktuStart;
            const waktuEnd = body.dataset.waktuEnd;

            if (!status || !waktuStart) {
                body.style.backgroundColor = '';
                return;
            }

            let minutes;
            if (status === 'ON LOADING') {
                minutes = getDurationMinutes(waktuStart, null);
            } else if (status === 'FINISH' && waktuEnd) {
                minutes = getDurationMinutes(waktuStart, waktuEnd);
            } else {
                body.style.backgroundColor = '';
                return;
            }

            const color = getDurationColor(minutes);
            body.style.backgroundColor = color.bodyBg;
        });
    }

    // ===== UPDATE RUNNING TIMERS =====
    function updateTimers() {
        document.querySelectorAll('.gate-timer').forEach(timer => {
            const startISO = timer.dataset.start;
            if (startISO) {
                timer.textContent = formatDuration(startISO, null);
                const body = timer.closest('.gate-body');
                if (body) {
                    const minutes = getDurationMinutes(startISO, null);
                    const color = getDurationColor(minutes);
                    body.style.backgroundColor = color.bodyBg;
                }
            }
        });
    }

    // ===== DETERMINE GATE STATUS LABEL =====
    function getGateStatusHtml(gate) {
        if (gate.waktu_penyerahan) {
            return '<span class="gate-status mt-0.5 text-[9px] px-2 py-0.5 rounded-full font-bold bg-purple-100 text-purple-700">✅ COMPLETED</span>';
        } else if (gate.status === 'FINISH') {
            let html = `<span class="mt-1 text-[10px] px-2 py-0.5 rounded-full font-bold font-mono bg-emerald-100 text-emerald-700">${gate.durasi || 'DONE'}</span>`;
            html += '<span class="gate-status mt-0.5 text-[9px] px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-700">🏁 FINISH LOADING</span>';
            return html;
        } else if (gate.status === 'ON LOADING' && gate.waktu_start) {
            const elapsed = formatDuration(gate.waktu_start, null);
            let html = `<span class="gate-timer mt-1 text-[10px] px-2 py-0.5 rounded font-bold font-mono" data-start="${gate.waktu_start}">${elapsed}</span>`;
            html += '<span class="gate-status mt-0.5 text-[9px] px-2 py-0.5 rounded-full font-bold animate-pulse bg-blue-100 text-blue-700">⏳ ON LOADING</span>';
            return html;
        } else if (gate.waktu_penerimaan) {
            return '<span class="gate-status mt-1 text-[9px] px-2 py-0.5 rounded-full font-bold bg-yellow-100 text-yellow-700">📋 ASSIGN</span>';
        }
        return '';
    }

    // ===== GATE CONTENT BUILDER (for auto-refresh) =====
    function buildGateContent(gate) {
        const body = document.querySelector('#gate-' + gate.nomor + ' .gate-body');
        if (!body) return;

        // Update data attributes
        body.dataset.status = gate.status || '';
        body.dataset.waktuStart = gate.waktu_start || '';
        body.dataset.waktuEnd = gate.waktu_end || '';
        body.dataset.waktuPenerimaan = gate.waktu_penerimaan || '';
        body.dataset.waktuPenyerahan = gate.waktu_penyerahan || '';

        if (gate.no_polisi) {
            const statusHtml = getGateStatusHtml(gate);
            let minutes = 0;

            body.innerHTML = `
                <p class="text-xs font-bold text-gray-800 truncate w-full text-center">${gate.no_polisi}</p>
                <p class="text-[10px] text-gray-400 truncate w-full text-center">${gate.vendor || ''}</p>
                ${statusHtml}
            `;

            // Apply color based on loading status
            if (gate.status === 'ON LOADING' && gate.waktu_start) {
                minutes = getDurationMinutes(gate.waktu_start, null);
                const color = getDurationColor(minutes);
                body.style.backgroundColor = color.bodyBg;
            } else if (gate.status === 'FINISH' && gate.waktu_start && gate.waktu_end) {
                minutes = getDurationMinutes(gate.waktu_start, gate.waktu_end);
                const color = getDurationColor(minutes);
                body.style.backgroundColor = color.bodyBg;
            } else {
                body.style.backgroundColor = '';
            }
        } else {
            body.innerHTML = '<span class="text-[10px] text-gray-300 italic">kosong</span>';
            body.style.backgroundColor = '';
        }
    }

    // ===== INIT =====
    applyGateColors();

    // Update timers every second
    setInterval(updateTimers, 1000);

    // Date picker navigation
    document.getElementById('tanggal-picker').addEventListener('change', function() {
        const date = this.value;
        if (date) {
            window.location.href = '{{ route("livemonitoring") }}?tanggal=' + date;
        }
    });

    // Auto-refresh every 5 seconds (only when viewing today)
    @if($isToday)
    setInterval(function() {
        fetch('{{ route("livemonitoring.data") }}?tanggal={{ $tanggalValue }}')
            .then(res => res.json())
            .then(data => {
                // Update gates
                for (let i = 1; i <= 27; i++) {
                    buildGateContent(data.gates[i]);
                }

                // Update activity summary table
                if (data.activitySummary) {
                    const rows = document.querySelectorAll('#activity-summary-body tr');
                    data.activitySummary.forEach((item, idx) => {
                        if (rows[idx]) {
                            const cells = rows[idx].querySelectorAll('td');
                            const op = item.onProcess ?? item.on_process ?? 0;
                            const fin = item.finish ?? 0;
                            if (cells[1]) {
                                const span = cells[1].querySelector('span');
                                span.textContent = op;
                                span.className = `inline-flex min-w-[28px] items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold ${op > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-400'}`;
                            }
                            if (cells[2]) {
                                const span = cells[2].querySelector('span');
                                span.textContent = fin;
                                span.className = `inline-flex min-w-[28px] items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold ${fin > 0 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-400'}`;
                            }
                        }
                    });
                }

                // Update average loading time table (dynamic from master kendaraan)
                if (data.avgTimes) {
                    const avgBody = document.getElementById('avg-times-body');
                    if (avgBody) {
                        let html = '';
                        data.avgTimes.forEach(row => {
                            const dryAlt = row.DRY_ALT ?? null;
                            const dryAut = row.DRY_AUT ?? null;
                            const frozenAlt = row.FROZEN_ALT ?? null;
                            const frozenAut = row.FROZEN_AUT ?? null;
                            html += `<tr class="hover:bg-gray-50/50">
                                <td class="px-3 py-2 font-medium text-gray-700 text-xs border-r border-gray-100">${row.jenis_kendaraan}</td>
                                <td class="px-3 py-2 text-center font-mono text-xs ${dryAlt ? 'text-green-700 bg-green-50/50' : 'text-gray-300'}">${dryAlt ?? '—'}</td>
                                <td class="px-3 py-2 text-center font-mono text-xs border-r border-gray-100 ${dryAut ? 'text-green-700 bg-green-50/50' : 'text-gray-300'}">${dryAut ?? '—'}</td>
                                <td class="px-3 py-2 text-center font-mono text-xs ${frozenAlt ? 'text-blue-700 bg-blue-50/50' : 'text-gray-300'}">${frozenAlt ?? '—'}</td>
                                <td class="px-3 py-2 text-center font-mono text-xs ${frozenAut ? 'text-blue-700 bg-blue-50/50' : 'text-gray-300'}">${frozenAut ?? '—'}</td>
                            </tr>`;
                        });
                        avgBody.innerHTML = html;
                    }
                }

                const now = new Date();
                document.getElementById('last-update').textContent = 'Terakhir: ' + now.toLocaleTimeString('id-ID');
            })
            .catch(err => console.error('Refresh error:', err));
    }, 5000);
    @endif
</script>
@endsection
