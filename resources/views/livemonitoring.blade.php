@extends('layouts.app')

@section('title', 'Live Monitoring')

@section('header-actions')
    <button onclick="toggleFullscreen()"
        class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
        title="Fullscreen (ESC untuk keluar)">
        <svg class="w-5 h-5 fs-icon-expand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
        </svg>
        <svg class="w-5 h-5 fs-icon-compress" style="display:none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" />
        </svg>
    </button>
@endsection

@section('content')

    <style>
        /* ===== GATE CARD ===== */
        .gate-card {
            border-radius: 8px;
            border-width: 2px;
            border-style: solid;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .gate-card-header {
            text-align: center;
            padding: 5px 6px 3px;
        }

        .gate-card-header .gate-number {
            font-size: 14px;
            font-weight: 800;
            line-height: 1.1;
            color: #fff;
            letter-spacing: -0.3px;
        }

        .gate-card-header .gate-type {
            font-size: 9px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.92);
            letter-spacing: 0.4px;
        }

        .gate-body {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            padding: 6px 5px;
            background: #fff;
            min-height: 60px;
        }

        .gate-body-inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            gap: 1px;
        }

        .gate-times {
            width: 52px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
            border-left: 1px solid #e5e7eb;
            padding-left: 5px;
            font-size: 8px;
            line-height: 1.3;
            color: #6b7280;
        }

        .gate-empty {
            color: #d1d5db;
            font-style: italic;
            font-size: 10px;
        }

        /* ===== GATE SECTION FULLSCREEN ===== */
        #gate-section.gate-fullscreen {
            position: fixed;
            inset: 0;
            z-index: 9999;
            border-radius: 0 !important;
            border: none !important;
            background: #f9fafb;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        #gate-section.gate-fullscreen #gate-grid-body {
            flex: 1;
            overflow-y: auto;
            padding: 12px 16px;
        }

        #gate-section.gate-fullscreen .gate-card-header .gate-number {
            font-size: 18px;
        }

        #gate-section.gate-fullscreen .gate-body {
            min-height: 80px;
        }

        #gate-section.gate-fullscreen #gate-unified-grid {
            gap: 24px;
        }

        /* ===== PAGE FULLSCREEN: KEEP NORMAL DATA, SCALE TO ONE VIEWPORT ===== */
        body.fullscreen-mode {
            overflow: hidden;
        }

        body.fullscreen-mode .fullscreen-main>main {
            overflow: hidden !important;
            padding: 6px !important;
        }

        body.fullscreen-mode #monitoring-container {
            transform: scale(var(--monitoring-fullscreen-scale, 1));
            transform-origin: top left;
            width: var(--monitoring-fullscreen-width, 100%);
            min-width: var(--monitoring-fullscreen-width, 100%);
            transition: transform 120ms ease;
        }
    </style>

    {{-- Global fullscreen exit button (page-level) --}}
    <button onclick="toggleFullscreen()"
        class="fs-exit-btn hidden fixed top-3 right-3 z-50 items-center gap-1.5 px-3 py-1.5 bg-gray-900/80 hover:bg-gray-900 text-white text-xs font-medium rounded-full shadow-lg backdrop-blur transition-all">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" />
        </svg>
        Keluar Fullscreen (ESC)
    </button>

    <div class="space-y-3" id="monitoring-container">

        {{-- Header with Date Picker --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2" id="monitoring-header">
            <div class="flex items-center gap-3">
                <label class="text-sm text-gray-500">Periode:</label>
                <input type="date" id="tanggal-picker" value="{{ $tanggalValue }}"
                    class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent cursor-pointer">
                @if (!$isToday)
                    <a href="{{ route('livemonitoring') }}"
                        class="text-xs px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-full font-medium transition-colors">
                        ↩ Hari Ini
                    </a>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if ($isToday)
                    <span id="refresh-indicator"
                        class="flex items-center gap-1.5 text-xs text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full">
                        <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                        Live
                    </span>
                @else
                    <span class="flex items-center gap-1.5 text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Riwayat
                    </span>
                @endif
                <span id="last-update" class="text-xs text-gray-400"></span>
            </div>
        </div>

        {{-- ===== Activity Summary (full width, stacked) ===== --}}
        <div class="border border-gray-200 rounded-lg overflow-hidden bg-white" id="activity-card">
            <div class="p-4">
                <p class="text-sm font-bold text-gray-800 mb-0.5">Activity Summary</p>
                <p class="text-[10px] text-gray-400 mb-3">Periode: {{ $periode }}</p>
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left font-semibold text-gray-600 py-1.5 pr-4 w-36">Activity</th>
                            <th class="text-center font-semibold text-gray-600 py-1.5 px-4">Parking</th>
                            <th class="text-center font-semibold text-gray-600 py-1.5 px-4">Doc In</th>
                            <th class="text-center font-semibold text-gray-600 py-1.5 px-4">Loading/Unloading</th>
                            <th class="text-center font-semibold text-gray-600 py-1.5 px-4">Finish</th>
                        </tr>
                    </thead>
                    <tbody id="activity-summary-body" class="divide-y divide-gray-50">
                        @foreach ($activitySummary as $row)
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-2 pr-4 font-semibold text-gray-800 text-xs">{{ $row['label'] }}</td>
                                <td class="py-2 px-4 text-center">
                                    <span
                                        class="font-semibold text-base {{ $row['parking'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['parking'] }}</span>
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <span
                                        class="font-semibold text-base {{ $row['receiving'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['receiving'] }}</span>
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <span
                                        class="font-semibold text-base {{ $row['on_process'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['on_process'] }}</span>
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <span
                                        class="font-semibold text-base {{ $row['finish'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['finish'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== Average Loading Time (full width, stacked) ===== --}}
        <div class="border border-gray-200 rounded-lg overflow-hidden bg-white" id="avg-card">
            <div class="p-4 overflow-x-auto">
                <p class="text-sm font-bold text-gray-800 mb-3">Average Loading Time</p>
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr>
                            <th rowspan="3"
                                class="text-left font-semibold text-gray-600 py-1 pr-4 align-bottom border-r border-gray-200 whitespace-nowrap w-28">
                                Kendaraan
                            </th>
                            <th colspan="4"
                                class="text-center font-semibold text-green-700 bg-green-50 py-1.5 border border-gray-200 tracking-wide">
                                DRY</th>
                            <th colspan="4"
                                class="text-center font-semibold text-blue-700 bg-blue-50 py-1.5 border border-gray-200 tracking-wide">
                                FROZEN</th>
                            <th colspan="4"
                                class="text-center font-semibold text-amber-700 bg-amber-50 py-1.5 border border-gray-200 tracking-wide">
                                CHILLED</th>
                        </tr>
                        <tr>
                            <th colspan="2"
                                class="text-center font-semibold text-green-600 bg-green-50 py-0.5 text-[10px] border border-gray-200">
                                ALT</th>
                            <th colspan="2"
                                class="text-center font-semibold text-green-600 bg-green-50 py-0.5 text-[10px] border border-gray-200">
                                AUT</th>
                            <th colspan="2"
                                class="text-center font-semibold text-blue-600 bg-blue-50 py-0.5 text-[10px] border border-gray-200">
                                ALT</th>
                            <th colspan="2"
                                class="text-center font-semibold text-blue-600 bg-blue-50 py-0.5 text-[10px] border border-gray-200">
                                AUT</th>
                            <th colspan="2"
                                class="text-center font-semibold text-amber-600 bg-amber-50 py-0.5 text-[10px] border border-gray-200">
                                ALT</th>
                            <th colspan="2"
                                class="text-center font-semibold text-amber-600 bg-amber-50 py-0.5 text-[10px] border border-gray-200">
                                AUT</th>
                        </tr>
                        <tr>
                            @foreach (['text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50'] as $idx => $cls)
                                <th
                                    class="text-center {{ $cls }} py-0.5 text-[10px] border border-gray-200 font-medium px-3">
                                    {{ $idx % 2 === 0 ? 'Today' : 'L.Month' }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="avg-times-body" class="divide-y divide-gray-50">
                        @foreach ($avgTimes as $row)
                            @php
                                $gc = function ($t, $lm) {
                                    if (!$t) {
                                        return 'text-gray-300';
                                    }
                                    if (!$lm) {
                                        return 'text-gray-700';
                                    }
                                    if ($t > $lm) {
                                        return 'text-red-600';
                                    }
                                    if ($t < $lm) {
                                        return 'text-green-600';
                                    }
                                    return 'text-gray-700';
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/50">
                                <td
                                    class="py-1.5 pr-4 font-medium text-gray-700 border-r border-gray-200 whitespace-nowrap">
                                    {{ $row['jenis_kendaraan'] }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ $gc($row['DRY_ALT'] ?? null, $row['DRY_ALT_LMONTH'] ?? null) }}">
                                    {{ $row['DRY_ALT'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ isset($row['DRY_ALT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                    {{ $row['DRY_ALT_LMONTH'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ $gc($row['DRY_AUT'] ?? null, $row['DRY_AUT_LMONTH'] ?? null) }}">
                                    {{ $row['DRY_AUT'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ isset($row['DRY_AUT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                    {{ $row['DRY_AUT_LMONTH'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ $gc($row['FROZEN_ALT'] ?? null, $row['FROZEN_ALT_LMONTH'] ?? null) }}">
                                    {{ $row['FROZEN_ALT'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ isset($row['FROZEN_ALT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                    {{ $row['FROZEN_ALT_LMONTH'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ $gc($row['FROZEN_AUT'] ?? null, $row['FROZEN_AUT_LMONTH'] ?? null) }}">
                                    {{ $row['FROZEN_AUT'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ isset($row['FROZEN_AUT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                    {{ $row['FROZEN_AUT_LMONTH'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ $gc($row['CHILLED_ALT'] ?? null, $row['CHILLED_ALT_LMONTH'] ?? null) }}">
                                    {{ $row['CHILLED_ALT'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ isset($row['CHILLED_ALT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                    {{ $row['CHILLED_ALT_LMONTH'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ $gc($row['CHILLED_AUT'] ?? null, $row['CHILLED_AUT_LMONTH'] ?? null) }}">
                                    {{ $row['CHILLED_AUT'] ?? '—' }}</td>
                                <td
                                    class="py-1.5 px-3 text-center font-mono {{ isset($row['CHILLED_AUT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                    {{ $row['CHILLED_AUT_LMONTH'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===== GATE SECTION: Frozen (left 4 cols) + Dry (right 4 cols) ===== --}}
        <div class="border border-gray-200 rounded-lg overflow-hidden bg-white" id="gate-section">

            {{-- Gate Section Header --}}
            <div class="px-4 py-2.5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    {{-- <p class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded bg-blue-500 inline-block"></span>
                        Frozen Gate
                        <span class="text-gray-400 font-normal">(F-1 – F-16)</span>
                    </p>
                    <span class="text-gray-300">|</span>
                    <p class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded bg-amber-500 inline-block"></span>
                        Dry Gate
                        <span class="text-gray-400 font-normal">(D-1 – D-11)</span>
                    </p> --}}
                </div>
                <button onclick="toggleGateFullscreen()" id="gate-fs-btn"
                    class="flex items-center gap-1.5 px-3 py-1 bg-gray-200 hover:bg-gray-400 text-gray-700 text-xs font-medium rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" id="gate-fs-icon-expand" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                    <svg class="w-3.5 h-3.5 hidden" id="gate-fs-icon-compress" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" />
                    </svg>
                    <span id="gate-fs-label">Fullscreen</span>
                </button>
            </div>

            {{-- Gate Grid Body --}}
            <div class="p-3" id="gate-grid-body">
                <div class="grid grid-cols-2 gap-3" id="gate-unified-grid">

                    {{-- ===== FROZEN SIDE (left, 4 cols) ===== --}}
                    <div class="border-r border-gray-200 pr-3">
                        <div class="text-center text-[14px] font-bold text-blue-600 mb-2 tracking-widest uppercase">
                            ❄️ Frozen
                        </div>
                        <div class="grid grid-cols-4 gap-1.5" id="frozen-grid-inner">
                            @for ($i = 1; $i <= 16; $i++)
                                @php
                                    $cp = $gates[$i]['checkpoint'];
                                    if (
                                        $cp &&
                                        $cp->waktu_penyerahan_dokumen &&
                                        now()->diffInMinutes($cp->waktu_penyerahan_dokumen) >= 2
                                    ) {
                                        $cp = null;
                                    }
                                @endphp
                                <div id="gate-{{ $i }}" class="gate-card border-blue-300">
                                    <div class="gate-card-header bg-blue-500">
                                        <div class="gate-number">F-{{ $i }}</div>
                                        <div class="gate-type">FROZEN</div>
                                    </div>
                                    <div class="gate-body"
                                        data-waktu-start="{{ $cp?->waktu_start?->toIso8601String() ?? '' }}"
                                        data-waktu-end="{{ $cp?->waktu_end?->toIso8601String() ?? '' }}"
                                        data-waktu-penerimaan="{{ $cp?->waktu_penerimaan_dokumen?->toIso8601String() ?? '' }}"
                                        data-waktu-penyerahan="{{ $cp?->waktu_penyerahan_dokumen?->toIso8601String() ?? '' }}"
                                        data-status="{{ $cp?->status ?? '' }}">
                                        <div class="gate-body-inner">
                                            @if ($cp)
                                                <span
                                                    class="text-[14px] font-bold px-1.5 py-0.5 rounded
                                                    {{ $cp->aktivitas === 'INBOUND' ? 'bg-orange-100 text-orange-700' : ($cp->aktivitas === 'OUTBOUND' ? 'bg-indigo-100 text-indigo-700' : '') }}">
                                                    {{ $cp->aktivitas }}
                                                </span>
                                                <p
                                                    class="text-[16px] font-bold text-gray-800 mt-0.5 leading-tight break-all">
                                                    {{ $cp->no_polisi }}</p>
                                                <p class="text-[12px] text-gray-800 leading-tight">{{ $cp->vendor }}</p>
                                                @if ($cp->waktu_penyerahan_dokumen)
                                                    <span
                                                        class="gate-status mt-0.5 text-[12px] px-1.5 py-0.5 rounded-full font-bold bg-purple-100 text-purple-700">✅
                                                        COMPLETED</span>
                                                @elseif($cp->waktu_end || $cp->status === 'FINISH')
                                                    <span
                                                        class="static-timer mt-0.5 text-[12px] px-1.5 py-0.5 rounded font-mono bg-emerald-100 text-emerald-700"
                                                        data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}"
                                                        data-end="{{ $cp->waktu_end?->toIso8601String() ?? '' }}">DONE</span>
                                                    <span
                                                        class="gate-status text-[12px] px-1.5 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-700">🏁
                                                        FINISH</span>
                                                @elseif($cp->waktu_start || $cp->status === 'ON LOADING')
                                                    <span
                                                        class="gate-timer mt-0.5 text-[12px] px-1.5 py-0.5 rounded font-mono bg-blue-100 text-blue-700"
                                                        data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}">00:00:00</span>
                                                    <span
                                                        class="gate-status text-[12px] px-1.5 py-0.5 rounded-full font-bold animate-pulse bg-blue-100 text-blue-700">⏳
                                                        LOADING</span>
                                                @elseif($cp->waktu_penerimaan_dokumen)
                                                    <span
                                                        class="gate-status mt-0.5 text-[12px] px-1.5 py-0.5 rounded-full font-bold bg-yellow-100 text-yellow-700">📋
                                                        ASSIGN</span>
                                                @endif
                                            @else
                                                <span class="gate-empty">kosong</span>
                                            @endif
                                        </div>
                                        @if ($cp)
                                            <div class="gate-times">
                                                <div class="font-mono text-[11px]"
                                                    style="color: {{ $cp->waktu_penerimaan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                    📋 {{ $cp->waktu_penerimaan_dokumen?->format('H:i') ?? '--:--' }}</div>
                                                <div class="font-mono text-[11px]"
                                                    style="color: {{ $cp->waktu_start ? '#4b5563' : '#d1d5db' }}">⏳
                                                    {{ $cp->waktu_start?->format('H:i') ?? '--:--' }}</div>
                                                <div class="font-mono text-[11px]"
                                                    style="color: {{ $cp->waktu_end ? '#4b5563' : '#d1d5db' }}">🏁
                                                    {{ $cp->waktu_end?->format('H:i') ?? '--:--' }}</div>
                                                <div class="font-mono text-[11px]"
                                                    style="color: {{ $cp->waktu_penyerahan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                    ✅ {{ $cp->waktu_penyerahan_dokumen?->format('H:i') ?? '--:--' }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- ===== DRY SIDE (right, 4 cols) ===== --}}
                    <div>
                        <div class="text-center text-[14px] font-bold text-amber-600 mb-2 tracking-widest uppercase">
                            🌡️ Dry
                        </div>
                        <div class="grid grid-cols-4 gap-1.5" id="dry-grid-inner">
                            @for ($i = 17; $i <= 27; $i++)
                                @php
                                    $cp = $gates[$i]['checkpoint'];
                                    if (
                                        $cp &&
                                        $cp->waktu_penyerahan_dokumen &&
                                        now()->diffInMinutes($cp->waktu_penyerahan_dokumen) >= 2
                                    ) {
                                        $cp = null;
                                    }
                                    $dLabel = 'D-' . ($i - 16);
                                @endphp
                                <div id="gate-{{ $i }}" class="gate-card border-amber-300">
                                    <div class="gate-card-header bg-amber-500">
                                        <div class="gate-number">{{ $dLabel }}</div>
                                        <div class="gate-type">DRY</div>
                                    </div>
                                    <div class="gate-body"
                                        data-waktu-start="{{ $cp?->waktu_start?->toIso8601String() ?? '' }}"
                                        data-waktu-end="{{ $cp?->waktu_end?->toIso8601String() ?? '' }}"
                                        data-waktu-penerimaan="{{ $cp?->waktu_penerimaan_dokumen?->toIso8601String() ?? '' }}"
                                        data-waktu-penyerahan="{{ $cp?->waktu_penyerahan_dokumen?->toIso8601String() ?? '' }}"
                                        data-status="{{ $cp?->status ?? '' }}">
                                        <div class="gate-body-inner">
                                            @if ($cp)
                                                <span
                                                    class="text-[14px] font-bold px-1.5 py-0.5 rounded
                                                    {{ $cp->aktivitas === 'INBOUND' ? 'bg-orange-100 text-orange-700' : ($cp->aktivitas === 'OUTBOUND' ? 'bg-indigo-100 text-indigo-700' : '') }}">
                                                    {{ $cp->aktivitas }}
                                                </span>
                                                <p
                                                    class="text-[16px] font-bold text-gray-800 mt-0.5 leading-tight break-all">
                                                    {{ $cp->no_polisi }}</p>
                                                <p class="text-[12px] text-gray-500 leading-tight">{{ $cp->vendor }}</p>
                                                @if ($cp->waktu_penyerahan_dokumen)
                                                    <span
                                                        class="gate-status mt-0.5 text-[12px] px-1.5 py-0.5 rounded-full font-bold bg-purple-100 text-purple-700">✅
                                                        COMPLETED</span>
                                                @elseif($cp->waktu_end || $cp->status === 'FINISH')
                                                    <span
                                                        class="static-timer mt-0.5 text-[12px] px-1.5 py-0.5 rounded font-mono bg-emerald-100 text-emerald-700"
                                                        data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}"
                                                        data-end="{{ $cp->waktu_end?->toIso8601String() ?? '' }}">DONE</span>
                                                    <span
                                                        class="gate-status text-[12px] px-1.5 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-700">🏁
                                                        FINISH</span>
                                                @elseif($cp->waktu_start || $cp->status === 'ON LOADING')
                                                    <span
                                                        class="gate-timer mt-0.5 text-[12px] px-1.5 py-0.5 rounded font-mono bg-blue-100 text-blue-700"
                                                        data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}">00:00:00</span>
                                                    <span
                                                        class="gate-status text-[12px] px-1.5 py-0.5 rounded-full font-bold animate-pulse bg-blue-100 text-blue-700">⏳
                                                        LOADING</span>
                                                @elseif($cp->waktu_penerimaan_dokumen)
                                                    <span
                                                        class="gate-status mt-0.5 text-[12px] px-1.5 py-0.5 rounded-full font-bold bg-yellow-100 text-yellow-700">📋
                                                        ASSIGN</span>
                                                @endif
                                            @else
                                                <span class="gate-empty">kosong</span>
                                            @endif
                                        </div>
                                        @if ($cp)
                                            <div class="gate-times">
                                                <div class="font-mono text-[11px]"
                                                    style="color: {{ $cp->waktu_penerimaan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                    📋 {{ $cp->waktu_penerimaan_dokumen?->format('H:i') ?? '--:--' }}</div>
                                                <div class="font-mono text-[11px]"
                                                    style="color: {{ $cp->waktu_start ? '#4b5563' : '#d1d5db' }}">⏳
                                                    {{ $cp->waktu_start?->format('H:i') ?? '--:--' }}</div>
                                                <div class="font-mono text-[11px]"
                                                    style="color: {{ $cp->waktu_end ? '#4b5563' : '#d1d5db' }}">🏁
                                                    {{ $cp->waktu_end?->format('H:i') ?? '--:--' }}</div>
                                                <div class="font-mono text-[11px]"
                                                    style="color: {{ $cp->waktu_penyerahan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                    ✅ {{ $cp->waktu_penyerahan_dokumen?->format('H:i') ?? '--:--' }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endfor
                            {{-- Pad odd number (11 gates → 1 empty slot) --}}
                            <div class="gate-card border-transparent opacity-0 pointer-events-none"></div>
                        </div>
                        <br>
                        <div class="bg-white rounded-lg border border-gray-200 p-3">
                            <p class="text-xs font-semibold text-gray-600 mb-2">Keterangan Status Gate:</p>
                            <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                                <span class="flex items-center gap-1.5">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-yellow-100 text-yellow-700">📋
                                        ASSIGN</span>
                                    Dokumen diterima, menunggu loading
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-700">⏳
                                        ON
                                        LOADING</span>
                                    Sedang proses loading
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-700">🏁
                                        FINISH
                                        LOADING</span>
                                    Loading selesai
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-purple-100 text-purple-700">✅
                                        COMPLETED</span>
                                    Dokumen diserahkan
                                </span>
                            </div>
                            <div class="flex flex-wrap gap-4 text-xs text-gray-500 mt-2 pt-2 border-t border-gray-100">
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-500"></span>
                                    FROZEN (Gate F-1
                                    – F-16)</span>
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-500"></span>
                                    DRY (Gate D-1 –
                                    D-11)</span>
                                <span class="flex items-center gap-1.5"><span
                                        class="w-3 h-3 rounded bg-emerald-400"></span> Loading &lt;
                                    30 menit</span>
                                <span class="flex items-center gap-1.5"><span
                                        class="w-3 h-3 rounded bg-yellow-400"></span> Loading 30–60
                                    menit</span>
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-500"></span>
                                    Loading &gt; 60
                                    menit</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        {{-- <div class="bg-white rounded-lg border border-gray-200 p-3">
            <p class="text-xs font-semibold text-gray-600 mb-2">Keterangan Status Gate:</p>
            <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-yellow-100 text-yellow-700">📋
                        ASSIGN</span>
                    Dokumen diterima, menunggu loading
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-700">⏳ ON
                        LOADING</span>
                    Sedang proses loading
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-700">🏁 FINISH
                        LOADING</span>
                    Loading selesai
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-purple-100 text-purple-700">✅
                        COMPLETED</span>
                    Dokumen diserahkan
                </span>
            </div>
            <div class="flex flex-wrap gap-4 text-xs text-gray-500 mt-2 pt-2 border-t border-gray-100">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-500"></span> FROZEN (Gate F-1
                    – F-16)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-500"></span> DRY (Gate D-1 –
                    D-11)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-400"></span> Loading &lt;
                    30 menit</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-yellow-400"></span> Loading 30–60
                    menit</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-500"></span> Loading &gt; 60
                    menit</span>
            </div>
        </div> --}}

    </div>

    <script>
        // ===== HELPERS =====
        function formatTime(iso) {
            if (!iso) return '--:--';
            const d = new Date(iso);
            return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
        }

        function getDurationMinutes(startISO, endISO) {
            return ((endISO ? new Date(endISO) : new Date()) - new Date(startISO)) / 60000;
        }

        function formatDuration(startISO, endISO) {
            const totalSec = Math.max(0, Math.floor(((endISO ? new Date(endISO) : new Date()) - new Date(startISO)) /
                1000));
            const h = Math.floor(totalSec / 3600);
            const m = Math.floor((totalSec % 3600) / 60);
            const s = totalSec % 60;
            return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'00')}:${String(s).padStart(2,'0')}`;
        }

        function getDurationBg(minutes) {
            if (minutes > 60) return '#FEE2E2';
            if (minutes > 30) return '#FEF9C3';
            return '#D1FAE5';
        }

        // ===== PAGE FULLSCREEN FIT =====
        function fitMonitoringFullscreen() {
            const container = document.getElementById('monitoring-container');
            const main = document.querySelector('.fullscreen-main > main');
            if (!container || !main) return;

            if (!document.body.classList.contains('fullscreen-mode') || gateIsFullscreen) {
                container.style.removeProperty('--monitoring-fullscreen-scale');
                container.style.removeProperty('--monitoring-fullscreen-width');
                container.style.transform = '';
                container.style.width = '';
                container.style.minWidth = '';
                return;
            }

            container.style.setProperty('--monitoring-fullscreen-scale', '1');
            container.style.setProperty('--monitoring-fullscreen-width', main.clientWidth + 'px');

            requestAnimationFrame(() => {
                const availableWidth = Math.max(1, main.clientWidth);
                const availableHeight = Math.max(1, main.clientHeight);
                const naturalWidth = Math.max(container.scrollWidth, availableWidth);
                const naturalHeight = Math.max(container.scrollHeight, availableHeight);
                const scale = Math.min(availableWidth / naturalWidth, availableHeight / naturalHeight, 1);

                container.style.setProperty('--monitoring-fullscreen-scale', scale.toFixed(4));
                container.style.setProperty('--monitoring-fullscreen-width', naturalWidth + 'px');
            });
        }

        const fullscreenClassObserver = new MutationObserver(fitMonitoringFullscreen);
        fullscreenClassObserver.observe(document.body, {
            attributes: true,
            attributeFilter: ['class']
        });
        window.addEventListener('resize', fitMonitoringFullscreen);
        window.addEventListener('load', fitMonitoringFullscreen);

        // ===== GATE COLOR INIT =====
        function applyGateColors() {
            document.querySelectorAll('.gate-body').forEach(body => {
                const ws = body.dataset.waktuStart,
                    we = body.dataset.waktuEnd,
                    st = body.dataset.status;
                if (!ws) {
                    body.style.backgroundColor = '';
                    return;
                }
                let mins;
                if (st === 'ON LOADING') mins = getDurationMinutes(ws, null);
                else if (st === 'FINISH' && we) mins = getDurationMinutes(ws, we);
                else {
                    body.style.backgroundColor = '';
                    return;
                }
                body.style.backgroundColor = getDurationBg(mins);
            });
            document.querySelectorAll('.static-timer').forEach(t => {
                if (t.dataset.start && t.dataset.end) t.textContent = formatDuration(t.dataset.start, t.dataset
                    .end);
            });
            document.querySelectorAll('.gate-timer').forEach(t => {
                if (t.dataset.start) t.textContent = formatDuration(t.dataset.start, null);
            });
        }

        function updateTimers() {
            document.querySelectorAll('.gate-timer').forEach(t => {
                if (!t.dataset.start) return;
                t.textContent = formatDuration(t.dataset.start, null);
                const body = t.closest('.gate-body');
                if (body) body.style.backgroundColor = getDurationBg(getDurationMinutes(t.dataset.start, null));
            });
        }

        // ===== GATE FULLSCREEN TOGGLE =====
        let gateIsFullscreen = false;

        function toggleGateFullscreen() {
            const section = document.getElementById('gate-section');
            gateIsFullscreen = !gateIsFullscreen;
            section.classList.toggle('gate-fullscreen', gateIsFullscreen);
            document.getElementById('gate-fs-icon-expand').classList.toggle('hidden', gateIsFullscreen);
            document.getElementById('gate-fs-icon-compress').classList.toggle('hidden', !gateIsFullscreen);
            document.getElementById('gate-fs-label').textContent = gateIsFullscreen ? 'Keluar' : 'Fullscreen';
            document.body.style.overflow = gateIsFullscreen ? 'hidden' : '';
            fitMonitoringFullscreen();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && gateIsFullscreen) toggleGateFullscreen();
        });

        // ===== GATE CONTENT BUILDER (for live refresh) =====
        function getStatusHtml(gate) {
            if (gate.waktu_penyerahan)
                return '<span class="gate-status mt-0.5 text-[12px] px-1.5 py-0.5 rounded-full font-bold bg-purple-100 text-purple-700">✅ COMPLETED</span>';
            if (gate.waktu_end) {
                const dur = gate.waktu_start ? formatDuration(gate.waktu_start, gate.waktu_end) : 'DONE';
                return `<span class="static-timer mt-0.5 text-[12px] px-1.5 py-0.5 rounded font-mono bg-emerald-100 text-emerald-700" data-start="${gate.waktu_start||''}" data-end="${gate.waktu_end}">${dur}</span><span class="gate-status text-[12px] px-1.5 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-700">🏁 FINISH</span>`;
            }
            if (gate.waktu_start) {
                return `<span class="gate-timer mt-0.5 text-[12px] px-1.5 py-0.5 rounded font-mono bg-blue-100 text-blue-700" data-start="${gate.waktu_start}">${formatDuration(gate.waktu_start, null)}</span><span class="gate-status text-[12px] px-1.5 py-0.5 rounded-full font-bold animate-pulse bg-blue-100 text-blue-700">⏳ LOADING</span>`;
            }
            if (gate.waktu_penerimaan)
                return '<span class="gate-status mt-0.5 text-[12px] px-1.5 py-0.5 rounded-full font-bold bg-yellow-100 text-yellow-700">📋 ASSIGN</span>';
            return '';
        }

        function buildGateContent(gate, gateIndex) {
            const id = (gate && gate.nomor) ? gate.nomor : gateIndex;
            const body = document.querySelector('#gate-' + id + ' .gate-body');
            if (!body) return;

            if (!gate || !gate.no_polisi) {
                body.innerHTML = '<div class="gate-body-inner"><span class="gate-empty">kosong</span></div>';
                body.style.backgroundColor = '';
                return;
            }

            body.dataset.status = gate.status || '';
            body.dataset.waktuStart = gate.waktu_start || '';
            body.dataset.waktuEnd = gate.waktu_end || '';
            body.dataset.waktuPenerimaan = gate.waktu_penerimaan || '';
            body.dataset.waktuPenyerahan = gate.waktu_penyerahan || '';

            if (gate.waktu_penyerahan && ((new Date() - new Date(gate.waktu_penyerahan)) / 60000) >= 2) {
                body.innerHTML = '<div class="gate-body-inner"><span class="gate-empty">kosong</span></div>';
                body.style.backgroundColor = '';
                return;
            }

            const act = (gate.aktivitas || '').toUpperCase();
            let aktHtml = '';
            if (act === 'INBOUND') aktHtml =
                '<span class="text-[16px] font-bold px-1.5 py-0.5 rounded bg-orange-100 text-orange-700">INBOUND</span>';
            else if (act === 'OUTBOUND') aktHtml =
                '<span class="text-[16px] font-bold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700">OUTBOUND</span>';
            else if (gate.aktivitas) aktHtml =
                `<span class="text-[16px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-700">${gate.aktivitas}</span>`;

            const timesHtml = `
                <div class="gate-times">
                    <div class="font-mono text-[11px]" style="color:${gate.waktu_penerimaan?'#4b5563':'#d1d5db'}">📋 ${gate.waktu_penerimaan ? formatTime(gate.waktu_penerimaan) : '--:--'}</div>
                    <div class="font-mono text-[11px]" style="color:${gate.waktu_start?'#4b5563':'#d1d5db'}">⏳ ${gate.waktu_start ? formatTime(gate.waktu_start) : '--:--'}</div>
                    <div class="font-mono text-[11px]" style="color:${gate.waktu_end?'#4b5563':'#d1d5db'}">🏁 ${gate.waktu_end ? formatTime(gate.waktu_end) : '--:--'}</div>
                    <div class="font-mono text-[11px]" style="color:${gate.waktu_penyerahan?'#4b5563':'#d1d5db'}">✅ ${gate.waktu_penyerahan ? formatTime(gate.waktu_penyerahan) : '--:--'}</div>
                </div>`;

            body.innerHTML = `
                <div class="gate-body-inner">
                    ${aktHtml}
                    <p style="font-size:16px;font-weight:700;color:#1f2937;margin-top:2px;line-height:1.2;word-break:break-all">${gate.no_polisi}</p>
                    <p style="font-size:12px;color:#6b7280;line-height:1.2">${gate.vendor || ''}</p>
                    ${getStatusHtml(gate)}
                </div>
                ${timesHtml}`;

            if (gate.status === 'ON LOADING' && gate.waktu_start) {
                body.style.backgroundColor = getDurationBg(getDurationMinutes(gate.waktu_start, null));
            } else if (gate.status === 'FINISH' && gate.waktu_start && gate.waktu_end) {
                body.style.backgroundColor = getDurationBg(getDurationMinutes(gate.waktu_start, gate.waktu_end));
            } else {
                body.style.backgroundColor = '';
            }
        }

        // ===== INIT =====
        applyGateColors();
        fitMonitoringFullscreen();
        setInterval(updateTimers, 1000);

        document.getElementById('tanggal-picker').addEventListener('change', function() {
            if (this.value) window.location.href = '{{ route('livemonitoring') }}?tanggal=' + this.value;
        });

        @if ($isToday)
            setInterval(function() {
                fetch('{{ route('livemonitoring.data') }}?tanggal={{ $tanggalValue }}')
                    .then(r => r.json())
                    .then(data => {
                        for (let i = 1; i <= 27; i++) buildGateContent(data.gates[i], i);

                        if (data.activitySummary) {
                            const rows = document.querySelectorAll('#activity-summary-body tr');
                            data.activitySummary.forEach((item, idx) => {
                                if (!rows[idx]) return;
                                const cells = rows[idx].querySelectorAll('td');
                                [item.parking ?? 0, item.receiving ?? 0, item.onProcess ?? item
                                    .on_process ?? 0, item.finish ?? 0
                                ].forEach((v, ci) => {
                                    if (cells[ci + 1]) {
                                        const s = cells[ci + 1].querySelector('span');
                                        s.textContent = v;
                                        s.className =
                                            `font-semibold text-base ${v > 0 ? 'text-gray-800' : 'text-gray-300'}`;
                                    }
                                });
                            });
                        }

                        if (data.avgTimes) {
                            const avgBody = document.getElementById('avg-times-body');
                            if (avgBody) {
                                const gc = (t, lm) => !t ? 'text-gray-300' : !lm ? 'text-gray-700' : t > lm ?
                                    'text-red-600' : t < lm ? 'text-green-600' : 'text-gray-700';
                                avgBody.innerHTML = data.avgTimes.map(row => `
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="py-1.5 pr-4 font-medium text-gray-700 border-r border-gray-200 whitespace-nowrap">${row.jenis_kendaraan}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${gc(row.DRY_ALT,row.DRY_ALT_LMONTH)}">${row.DRY_ALT??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${row.DRY_ALT_LMONTH?'text-gray-500':'text-gray-300'}">${row.DRY_ALT_LMONTH??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${gc(row.DRY_AUT,row.DRY_AUT_LMONTH)}">${row.DRY_AUT??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${row.DRY_AUT_LMONTH?'text-gray-500':'text-gray-300'}">${row.DRY_AUT_LMONTH??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${gc(row.FROZEN_ALT,row.FROZEN_ALT_LMONTH)}">${row.FROZEN_ALT??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${row.FROZEN_ALT_LMONTH?'text-gray-500':'text-gray-300'}">${row.FROZEN_ALT_LMONTH??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${gc(row.FROZEN_AUT,row.FROZEN_AUT_LMONTH)}">${row.FROZEN_AUT??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${row.FROZEN_AUT_LMONTH?'text-gray-500':'text-gray-300'}">${row.FROZEN_AUT_LMONTH??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${gc(row.CHILLED_ALT,row.CHILLED_ALT_LMONTH)}">${row.CHILLED_ALT??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${row.CHILLED_ALT_LMONTH?'text-gray-500':'text-gray-300'}">${row.CHILLED_ALT_LMONTH??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${gc(row.CHILLED_AUT,row.CHILLED_AUT_LMONTH)}">${row.CHILLED_AUT??'—'}</td>
                                        <td class="py-1.5 px-3 text-center font-mono ${row.CHILLED_AUT_LMONTH?'text-gray-500':'text-gray-300'}">${row.CHILLED_AUT_LMONTH??'—'}</td>
                                    </tr>`).join('');
                            }
                        }

                        document.getElementById('last-update').textContent = 'Terakhir: ' + new Date()
                            .toLocaleTimeString('id-ID');
                        fitMonitoringFullscreen();
                    })
                    .catch(err => console.error('Refresh error:', err));
            }, 5000);
        @endif
    </script>
@endsection
