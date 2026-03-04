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

    {{-- GATE GRID --}}
    {{-- Row 1: Gate 1-9 (FROZEN) --}}
    <div>
        <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-9 gap-2">
            @for($i = 1; $i <= 9; $i++)
                @php $g = $gates[$i]; $cp = $g['checkpoint']; @endphp
                <div id="gate-{{ $i }}" class="rounded-lg border-2 overflow-hidden transition-all duration-300 border-blue-200">
                    <div class="bg-blue-500 text-white text-center py-1.5 px-1">
                        <p class="text-xs font-bold">GATE {{ $i }}</p>
                        <p class="text-[9px] font-medium opacity-80">FROZEN</p>
                    </div>
                    <div class="gate-body p-2 min-h-[60px] flex flex-col items-center justify-center transition-all duration-500"
                         data-waktu-start="{{ $cp && $cp->waktu_start ? $cp->waktu_start->toIso8601String() : '' }}"
                         data-waktu-end="{{ $cp && $cp->waktu_end ? $cp->waktu_end->toIso8601String() : '' }}"
                         data-status="{{ $cp ? $cp->status : '' }}">
                        @if($cp)
                            <p class="text-[10px] font-bold text-gray-800 truncate w-full text-center">{{ $cp->no_polisi }}</p>
                            <p class="text-[9px] text-gray-400 truncate w-full text-center">{{ $cp->vendor }}</p>
                            @if($cp->status === 'START' && $cp->waktu_start)
                                <span class="gate-timer mt-0.5 text-[9px] px-1.5 py-0.5 rounded font-bold font-mono" data-start="{{ $cp->waktu_start->toIso8601String() }}">00:00:00</span>
                                <span class="mt-0.5 text-[7px] px-1 py-0.5 rounded font-bold animate-pulse bg-white/60">⏳ LOADING</span>
                            @elseif($cp->status === 'FINISH')
                                <span class="mt-0.5 text-[9px] px-1.5 py-0.5 rounded font-bold font-mono">{{ $cp->durasi ?? 'DONE' }}</span>
                            @endif
                        @else
                            <span class="text-[10px] text-gray-300 italic">kosong</span>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    {{-- Row 2: Gate 10-18 (FROZEN 10-16, DRY 17-18) --}}
    <div>
        <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-9 gap-2">
            @for($i = 10; $i <= 18; $i++)
                @php
                    $g = $gates[$i];
                    $cp = $g['checkpoint'];
                    $isFrozen = $i <= 16;
                    $headerBg = $isFrozen ? 'bg-blue-500' : 'bg-amber-500';
                @endphp
                <div id="gate-{{ $i }}" class="rounded-lg border-2 overflow-hidden transition-all duration-300 {{ $isFrozen ? 'border-blue-200' : 'border-amber-200' }}">
                    <div class="{{ $headerBg }} text-white text-center py-1.5 px-1">
                        <p class="text-xs font-bold">GATE {{ $i }}</p>
                        <p class="text-[9px] font-medium opacity-80">{{ $isFrozen ? 'FROZEN' : 'DRY' }}</p>
                    </div>
                    <div class="gate-body p-2 min-h-[60px] flex flex-col items-center justify-center transition-all duration-500"
                         data-waktu-start="{{ $cp && $cp->waktu_start ? $cp->waktu_start->toIso8601String() : '' }}"
                         data-waktu-end="{{ $cp && $cp->waktu_end ? $cp->waktu_end->toIso8601String() : '' }}"
                         data-status="{{ $cp ? $cp->status : '' }}">
                        @if($cp)
                            <p class="text-[10px] font-bold text-gray-800 truncate w-full text-center">{{ $cp->no_polisi }}</p>
                            <p class="text-[9px] text-gray-400 truncate w-full text-center">{{ $cp->vendor }}</p>
                            @if($cp->status === 'START' && $cp->waktu_start)
                                <span class="gate-timer mt-0.5 text-[9px] px-1.5 py-0.5 rounded font-bold font-mono" data-start="{{ $cp->waktu_start->toIso8601String() }}">00:00:00</span>
                                <span class="mt-0.5 text-[7px] px-1 py-0.5 rounded font-bold animate-pulse bg-white/60">⏳ LOADING</span>
                            @elseif($cp->status === 'FINISH')
                                <span class="mt-0.5 text-[9px] px-1.5 py-0.5 rounded font-bold font-mono">{{ $cp->durasi ?? 'DONE' }}</span>
                            @endif
                        @else
                            <span class="text-[10px] text-gray-300 italic">kosong</span>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    {{-- Row 3: Gate 19-27 (DRY) --}}
    <div>
        <div class="grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-9 gap-2">
            @for($i = 19; $i <= 27; $i++)
                @php $g = $gates[$i]; $cp = $g['checkpoint']; @endphp
                <div id="gate-{{ $i }}" class="rounded-lg border-2 overflow-hidden transition-all duration-300 border-green-200">
                    <div class="bg-green-600 text-white text-center py-1.5 px-1">
                        <p class="text-xs font-bold">GATE {{ $i }}</p>
                        <p class="text-[9px] font-medium opacity-80">DRY</p>
                    </div>
                    <div class="gate-body p-2 min-h-[60px] flex flex-col items-center justify-center transition-all duration-500"
                         data-waktu-start="{{ $cp && $cp->waktu_start ? $cp->waktu_start->toIso8601String() : '' }}"
                         data-waktu-end="{{ $cp && $cp->waktu_end ? $cp->waktu_end->toIso8601String() : '' }}"
                         data-status="{{ $cp ? $cp->status : '' }}">
                        @if($cp)
                            <p class="text-[10px] font-bold text-gray-800 truncate w-full text-center">{{ $cp->no_polisi }}</p>
                            <p class="text-[9px] text-gray-400 truncate w-full text-center">{{ $cp->vendor }}</p>
                            @if($cp->status === 'START' && $cp->waktu_start)
                                <span class="gate-timer mt-0.5 text-[9px] px-1.5 py-0.5 rounded font-bold font-mono" data-start="{{ $cp->waktu_start->toIso8601String() }}">00:00:00</span>
                                <span class="mt-0.5 text-[7px] px-1 py-0.5 rounded font-bold animate-pulse bg-white/60">⏳ LOADING</span>
                            @elseif($cp->status === 'FINISH')
                                <span class="mt-0.5 text-[9px] px-1.5 py-0.5 rounded font-bold font-mono">{{ $cp->durasi ?? 'DONE' }}</span>
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
                    <tbody class="divide-y divide-gray-50">
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
                    <tbody class="divide-y divide-gray-50">
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
        <div class="flex flex-wrap gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-blue-500"></span> FROZEN (Gate 1-16)
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-green-600"></span> DRY (Gate 17-27)
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
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-gray-200"></span> Kosong
            </span>
        </div>
    </div>
</div>

<script>
    // ===== DURATION HELPERS =====
    function getDurationMinutes(startISO, endISO) {
        const start = new Date(startISO);
        const end = endISO ? new Date(endISO) : new Date();
        return (end - start) / 60000; // milliseconds to minutes
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
            if (status === 'START') {
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
                // Also update color of parent gate-body
                const body = timer.closest('.gate-body');
                if (body) {
                    const minutes = getDurationMinutes(startISO, null);
                    const color = getDurationColor(minutes);
                    body.style.backgroundColor = color.bodyBg;
                }
            }
        });
    }

    // ===== GATE CONTENT BUILDER (for auto-refresh) =====
    function buildGateContent(gate) {
        const body = document.querySelector('#gate-' + gate.nomor + ' .gate-body');
        if (!body) return;

        // Update data attributes
        body.dataset.status = gate.status || '';
        body.dataset.waktuStart = gate.waktu_start || '';
        body.dataset.waktuEnd = gate.waktu_end || '';

        if (gate.no_polisi) {
            let statusHtml = '';
            let minutes = 0;

            if (gate.status === 'START' && gate.waktu_start) {
                minutes = getDurationMinutes(gate.waktu_start, null);
                const elapsed = formatDuration(gate.waktu_start, null);
                statusHtml = `<span class="gate-timer mt-0.5 text-[9px] px-1.5 py-0.5 rounded font-bold font-mono" data-start="${gate.waktu_start}">${elapsed}</span>`;
                statusHtml += `<span class="mt-0.5 text-[7px] px-1 py-0.5 rounded font-bold animate-pulse bg-white/60">⏳ LOADING</span>`;
            } else if (gate.status === 'FINISH') {
                if (gate.waktu_start && gate.waktu_end) {
                    minutes = getDurationMinutes(gate.waktu_start, gate.waktu_end);
                }
                statusHtml = `<span class="mt-0.5 text-[9px] px-1.5 py-0.5 rounded font-bold font-mono">${gate.durasi || 'DONE'}</span>`;
            }

            body.innerHTML = `
                <p class="text-[10px] font-bold text-gray-800 truncate w-full text-center">${gate.no_polisi}</p>
                <p class="text-[9px] text-gray-400 truncate w-full text-center">${gate.vendor || ''}</p>
                ${statusHtml}
            `;

            // Apply color
            if (gate.waktu_start && (gate.status === 'START' || (gate.status === 'FINISH' && gate.waktu_end))) {
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

    // Auto-refresh every 15 seconds (only when viewing today)
    @if($isToday)
    setInterval(function() {
        fetch('{{ route("livemonitoring.data") }}?tanggal={{ $tanggalValue }}')
            .then(res => res.json())
            .then(data => {
                for (let i = 1; i <= 27; i++) {
                    buildGateContent(data.gates[i]);
                }
                const now = new Date();
                document.getElementById('last-update').textContent = 'Terakhir: ' + now.toLocaleTimeString('id-ID');
            })
            .catch(err => console.error('Refresh error:', err));
    }, 15000);
    @endif
</script>
@endsection
