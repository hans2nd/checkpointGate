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
        /* ===== AUTO-SCROLL SLIDER ===== */
        .gate-slider-container {
            position: relative;
        }

        .gate-slider-wrapper {
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
            border-radius: 8px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .gate-slider-wrapper::-webkit-scrollbar {
            width: 6px;
        }

        .gate-slider-wrapper::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .gate-slider-wrapper::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .gate-slider-wrapper::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .gate-slider-track {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* Gate card — auto height, content drives size */
        .gate-card {
            border-radius: 10px;
            border-width: 2px;
            border-style: solid;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            height: 270px;
        }

        .gate-card-header {
            text-align: center;
            padding: 8px 10px 6px;
        }

        .gate-card-header .gate-number {
            font-size: 36px;
            font-weight: 800;
            line-height: 1.1;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .gate-card-header .gate-type {
            font-size: 14px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.92);
            letter-spacing: 0.5px;
        }

        .gate-body {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            padding: 10px 8px;
            background: #fff;
            min-height: 80px;
        }

        .gate-body-inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            gap: 2px;
        }

        .gate-times {
            width: 100px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
            border-left: 1px solid #e5e7eb;
            padding-left: 10px;
            font-size: 13px;
            line-height: 1.3;
            color: #6b7280;
        }

        .gate-empty {
            color: #d1d5db;
            font-style: italic;
            font-size: 16px;
        }

        /* Progress bar at bottom */
        .slider-progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            border-radius: 0 2px 2px 0;
            transition: width 0.15s linear;
            z-index: 10;
        }

        /* Hover pause hint */
        .slider-pause-hint {
            position: absolute;
            top: 6px;
            right: 14px;
            background: rgba(0, 0, 0, 0.45);
            color: #fff;
            font-size: 9px;
            padding: 2px 7px;
            border-radius: 999px;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 20;
            pointer-events: none;
            backdrop-filter: blur(3px);
        }

        .gate-slider-container:hover .slider-pause-hint {
            opacity: 1;
        }
    </style>

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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
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

        {{-- ===== TOP: Activity Summary + Average Loading Time (flat, side-by-side) ===== --}}
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="flex flex-col 2xl:flex-row divide-y 2xl:divide-y-0 2xl:divide-x divide-gray-200">

                {{-- Activity Summary --}}
                <div class="flex-none 2xl:w-[480px] p-4">
                    <p class="text-xs font-bold text-gray-700 mb-0.5">Activity Summary</p>
                    <p class="text-[10px] text-gray-400 mb-2">Periode: {{ $periode }}</p>
                    <table class="w-full text-xs">
                        <thead>
                            <tr>
                                <th
                                    class="text-left font-semibold text-[14px] text-gray-600 py-1 pr-3 border-b border-gray-200">
                                    Activity</th>
                                <th
                                    class="text-center font-semibold text-[14px] text-gray-600 py-1 px-2 border-b border-gray-200">
                                    Parking</th>
                                <th
                                    class="text-center font-semibold text-[14px] text-gray-600 py-1 px-2 border-b border-gray-200">
                                    Doc
                                    In</th>
                                <th
                                    class="text-center font-semibold text-[14px] text-gray-600 py-1 px-2 border-b border-gray-200">
                                    Loading/Unloading</th>
                                <th
                                    class="text-center font-semibold text-[14px] text-gray-600 py-1 px-2 border-b border-gray-200">
                                    Finish</th>
                            </tr>
                        </thead>
                        <tbody id="activity-summary-body" class="divide-y divide-gray-50">
                            @foreach ($activitySummary as $row)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="py-1 pr-3 font-medium text-gray-700">{{ $row['label'] }}</td>
                                    <td class="py-1 px-2 text-center">
                                        <span
                                            class="font-semibold {{ $row['parking'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['parking'] }}</span>
                                    </td>
                                    <td class="py-1 px-2 text-center">
                                        <span
                                            class="font-semibold {{ $row['receiving'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['receiving'] }}</span>
                                    </td>
                                    <td class="py-1 px-2 text-center">
                                        <span
                                            class="font-semibold {{ $row['on_process'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['on_process'] }}</span>
                                    </td>
                                    <td class="py-1 px-2 text-center">
                                        <span
                                            class="font-semibold {{ $row['finish'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['finish'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Average Loading Time --}}
                <div class="flex-1 p-4 overflow-x-auto w-full min-w-0">
                    <p class="text-xs font-bold text-gray-700 mb-2">Average Loading Time</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border-collapse min-w-[700px]">
                            <thead>
                                <tr>
                                    <th rowspan="3"
                                        class="text-left  font-semibold text-[14px] text-gray-600 py-1 pr-3 align-center border-r border-gray-200 whitespace-nowrap">
                                        Vehicle</th>
                                    <th colspan="4"
                                        class="text-center font-semibold text-green-700 bg-green-50 py-1 border border-gray-200">
                                        DRY</th>
                                    <th colspan="4"
                                        class="text-center font-semibold text-blue-700 bg-blue-50 py-1 border border-gray-200">
                                        FROZEN</th>
                                    <th colspan="4"
                                        class="text-center font-semibold text-amber-700 bg-amber-50 py-1 border border-gray-200">
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
                                            class="text-center {{ $cls }} py-0.5 text-[10px] border border-gray-200 font-medium px-2">
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
                                            class="py-1 pr-3 font-medium text-gray-700 border-r border-gray-200 whitespace-nowrap">
                                            {{ $row['jenis_kendaraan'] }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ $gc($row['DRY_ALT'] ?? null, $row['DRY_ALT_LMONTH'] ?? null) }}">
                                            {{ $row['DRY_ALT'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ isset($row['DRY_ALT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                            {{ $row['DRY_ALT_LMONTH'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ $gc($row['DRY_AUT'] ?? null, $row['DRY_AUT_LMONTH'] ?? null) }}">
                                            {{ $row['DRY_AUT'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ isset($row['DRY_AUT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                            {{ $row['DRY_AUT_LMONTH'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ $gc($row['FROZEN_ALT'] ?? null, $row['FROZEN_ALT_LMONTH'] ?? null) }}">
                                            {{ $row['FROZEN_ALT'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ isset($row['FROZEN_ALT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                            {{ $row['FROZEN_ALT_LMONTH'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ $gc($row['FROZEN_AUT'] ?? null, $row['FROZEN_AUT_LMONTH'] ?? null) }}">
                                            {{ $row['FROZEN_AUT'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ isset($row['FROZEN_AUT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                            {{ $row['FROZEN_AUT_LMONTH'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ $gc($row['CHILLED_ALT'] ?? null, $row['CHILLED_ALT_LMONTH'] ?? null) }}">
                                            {{ $row['CHILLED_ALT'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ isset($row['CHILLED_ALT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                            {{ $row['CHILLED_ALT_LMONTH'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ $gc($row['CHILLED_AUT'] ?? null, $row['CHILLED_AUT_LMONTH'] ?? null) }}">
                                            {{ $row['CHILLED_AUT'] ?? '—' }}</td>
                                        <td
                                            class="py-1 px-2 text-center font-mono {{ isset($row['CHILLED_AUT_LMONTH']) ? 'text-gray-500' : 'text-gray-300' }}">
                                            {{ $row['CHILLED_AUT_LMONTH'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        {{-- ===== GATE SECTION: Frozen (left) + Dry (right) with auto-scroll slider ===== --}}
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="flex divide-x divide-gray-200">

                {{-- FROZEN GATE (Gate 1–16) --}}
                <div class="flex-1 min-w-0">
                    <div class="px-4 py-2 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <p class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded bg-blue-500 inline-block"></span>
                            Frozen Gate
                            <span class="text-gray-400 font-normal">(F-1 – F-16)</span>
                        </p>
                        <span class="text-[10px] text-blue-500 font-medium">
                            <span class="animate-pulse inline-block w-1.5 h-1.5 rounded-full bg-blue-400 mr-1"></span>auto
                            scroll
                        </span>
                    </div>
                    <div class="p-3 gate-slider-container">
                        <div class="slider-pause-hint">⏸ Paused / Manual Scroll</div>
                        <div class="gate-slider-wrapper" id="frozen-wrapper" style="height:540px;">
                            <div class="gate-slider-track" id="frozen-track">
                                @php
                                    $frozenPairs = array_chunk(range(1, 16), 2);
                                @endphp
                                @foreach ($frozenPairs as $pair)
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach ($pair as $i)
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
                                            <div id="gate-{{ $i }}" class="gate-card border-blue-200">
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
                                                                class="text-[14px] font-bold px-3 py-1 rounded
                                                                {{ $cp->aktivitas === 'INBOUND' ? 'bg-orange-100 text-orange-700' : ($cp->aktivitas === 'OUTBOUND' ? 'bg-indigo-100 text-indigo-700' : '') }}">
                                                                {{ $cp->aktivitas }}
                                                            </span>
                                                            <p
                                                                class="text-[24px] font-bold text-gray-800 mt-2 leading-tight break-all">
                                                                {{ $cp->no_polisi }}</p>
                                                            <p class="text-[14px] text-gray-500 mt-1 leading-tight">
                                                                {{ $cp->vendor }}</p>
                                                            @if ($cp->waktu_penyerahan_dokumen)
                                                                <span
                                                                    class="gate-status mt-2 text-[12px] px-3 py-1 rounded-full font-bold bg-purple-100 text-purple-700">✅
                                                                    COMPLETED</span>
                                                            @elseif($cp->waktu_end || $cp->status === 'FINISH')
                                                                <span
                                                                    class="static-timer mt-2 text-[12px] px-3 py-1 rounded font-mono bg-emerald-100 text-emerald-700"
                                                                    data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}"
                                                                    data-end="{{ $cp->waktu_end?->toIso8601String() ?? '' }}">DONE</span>
                                                                <span
                                                                    class="gate-status mt-1 text-[12px] px-3 py-1 rounded-full font-bold bg-emerald-100 text-emerald-700">🏁
                                                                    FINISH</span>
                                                            @elseif($cp->waktu_start || $cp->status === 'ON LOADING')
                                                                <span
                                                                    class="gate-timer mt-2 text-[12px] px-3 py-1 rounded font-mono bg-blue-100 text-blue-700"
                                                                    data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}">00:00:00</span>
                                                                <span
                                                                    class="gate-status mt-1 text-[12px] px-3 py-1 rounded-full font-bold animate-pulse bg-blue-100 text-blue-700">⏳
                                                                    LOADING</span>
                                                            @elseif($cp->waktu_penerimaan_dokumen)
                                                                <span
                                                                    class="gate-status mt-2 text-[12px] px-3 py-1 rounded-full font-bold bg-yellow-100 text-yellow-700">📋
                                                                    ASSIGN</span>
                                                            @endif
                                                        @else
                                                            <span class="gate-empty">kosong</span>
                                                        @endif
                                                    </div>
                                                    @if ($cp)
                                                        <div class="gate-times">
                                                            <div
                                                                style="color: {{ $cp->waktu_penerimaan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                                📋
                                                                {{ $cp->waktu_penerimaan_dokumen?->format('H:i') ?? '--:--' }}
                                                            </div>
                                                            <div
                                                                style="color: {{ $cp->waktu_start ? '#4b5563' : '#d1d5db' }}">
                                                                ⏳ {{ $cp->waktu_start?->format('H:i') ?? '--:--' }}</div>
                                                            <div
                                                                style="color: {{ $cp->waktu_end ? '#4b5563' : '#d1d5db' }}">
                                                                🏁 {{ $cp->waktu_end?->format('H:i') ?? '--:--' }}</div>
                                                            <div
                                                                style="color: {{ $cp->waktu_penyerahan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                                ✅
                                                                {{ $cp->waktu_penyerahan_dokumen?->format('H:i') ?? '--:--' }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="slider-progress-bar" id="frozen-progress"
                            style="width:0%;background:linear-gradient(90deg,#3b82f6,#06b6d4)"></div>
                    </div>
                </div>

                {{-- DRY GATE (Gate 17–27) --}}
                <div class="flex-1 min-w-0">
                    <div class="px-4 py-2 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <p class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded bg-amber-500 inline-block"></span>
                            Dry Gate
                            <span class="text-gray-400 font-normal">(D-1 – D-11)</span>
                        </p>
                        <span class="text-[10px] text-amber-500 font-medium">
                            <span class="animate-pulse inline-block w-1.5 h-1.5 rounded-full bg-amber-400 mr-1"></span>auto
                            scroll
                        </span>
                    </div>
                    <div class="p-3 gate-slider-container">
                        <div class="slider-pause-hint">⏸ Paused / Manual Scroll</div>
                        <div class="gate-slider-wrapper" id="dry-wrapper" style="height:540px;">
                            <div class="gate-slider-track" id="dry-track">
                                @php
                                    $dryPairs = array_chunk(range(17, 27), 2);
                                @endphp
                                @foreach ($dryPairs as $pair)
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach ($pair as $i)
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
                                            <div id="gate-{{ $i }}" class="gate-card border-amber-200">
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
                                                                class="text-[14px] font-bold px-3 py-1 rounded
                                                                {{ $cp->aktivitas === 'INBOUND' ? 'bg-orange-100 text-orange-700' : ($cp->aktivitas === 'OUTBOUND' ? 'bg-indigo-100 text-indigo-700' : '') }}">
                                                                {{ $cp->aktivitas }}
                                                            </span>
                                                            <p
                                                                class="text-[24px] font-bold text-gray-800 mt-2 leading-tight break-all">
                                                                {{ $cp->no_polisi }}</p>
                                                            <p class="text-[14px] text-gray-500 mt-1 leading-tight">
                                                                {{ $cp->vendor }}</p>
                                                            @if ($cp->waktu_penyerahan_dokumen)
                                                                <span
                                                                    class="gate-status mt-2 text-[12px] px-3 py-1 rounded-full font-bold bg-purple-100 text-purple-700">✅
                                                                    COMPLETED</span>
                                                            @elseif($cp->waktu_end || $cp->status === 'FINISH')
                                                                <span
                                                                    class="static-timer mt-2 text-[12px] px-3 py-1 rounded font-mono bg-emerald-100 text-emerald-700"
                                                                    data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}"
                                                                    data-end="{{ $cp->waktu_end?->toIso8601String() ?? '' }}">DONE</span>
                                                                <span
                                                                    class="gate-status mt-1 text-[12px] px-3 py-1 rounded-full font-bold bg-emerald-100 text-emerald-700">🏁
                                                                    FINISH</span>
                                                            @elseif($cp->waktu_start || $cp->status === 'ON LOADING')
                                                                <span
                                                                    class="gate-timer mt-2 text-[12px] px-3 py-1 rounded font-mono bg-blue-100 text-blue-700"
                                                                    data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}">00:00:00</span>
                                                                <span
                                                                    class="gate-status mt-1 text-[12px] px-3 py-1 rounded-full font-bold animate-pulse bg-blue-100 text-blue-700">⏳
                                                                    LOADING</span>
                                                            @elseif($cp->waktu_penerimaan_dokumen)
                                                                <span
                                                                    class="gate-status mt-2 text-[12px] px-3 py-1 rounded-full font-bold bg-yellow-100 text-yellow-700">📋
                                                                    ASSIGN</span>
                                                            @endif
                                                        @else
                                                            <span class="gate-empty">kosong</span>
                                                        @endif
                                                    </div>
                                                    @if ($cp)
                                                        <div class="gate-times">
                                                            <div
                                                                style="color: {{ $cp->waktu_penerimaan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                                📋
                                                                {{ $cp->waktu_penerimaan_dokumen?->format('H:i') ?? '--:--' }}
                                                            </div>
                                                            <div
                                                                style="color: {{ $cp->waktu_start ? '#4b5563' : '#d1d5db' }}">
                                                                ⏳ {{ $cp->waktu_start?->format('H:i') ?? '--:--' }}</div>
                                                            <div
                                                                style="color: {{ $cp->waktu_end ? '#4b5563' : '#d1d5db' }}">
                                                                🏁 {{ $cp->waktu_end?->format('H:i') ?? '--:--' }}</div>
                                                            <div
                                                                style="color: {{ $cp->waktu_penyerahan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                                ✅
                                                                {{ $cp->waktu_penyerahan_dokumen?->format('H:i') ?? '--:--' }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        {{-- pad odd pair --}}
                                        @if (count($pair) === 1)
                                            <div class="gate-card border-transparent opacity-0 pointer-events-none"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="slider-progress-bar" id="dry-progress"
                            style="width:0%;background:linear-gradient(90deg,#f59e0b,#fb923c)"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Legend --}}
        <div class="bg-white rounded-lg border border-gray-200 p-3">
            <p class="text-xs font-semibold text-gray-600 mb-2">Keterangan Status Gate:</p>
            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                <span class="flex items-center gap-1.5"><span
                        class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-yellow-100 text-yellow-700">📋
                        ASSIGN</span> Dokumen diterima, menunggu loading</span>
                <span class="flex items-center gap-1.5"><span
                        class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700">⏳ ON
                        LOADING</span>
                    Sedang proses loading</span>
                <span class="flex items-center gap-1.5"><span
                        class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">🏁 FINISH
                        LOADING</span> Loading selesai</span>
                <span class="flex items-center gap-1.5"><span
                        class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 text-purple-700">✅
                        COMPLETED</span> Dokumen diserahkan</span>
            </div>
            <div class="flex flex-wrap gap-4 text-sm text-gray-500 mt-2 pt-2 border-t border-gray-100">
                <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-blue-500"></span> FROZEN (Gate F-1
                    – F-16)</span>
                <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-amber-500"></span> DRY (Gate D-1 –
                    D-11)</span>
                <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-emerald-400"></span> Loading &lt;
                    30 menit</span>
                <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-yellow-400"></span> Loading 30–60
                    menit</span>
                <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-red-500"></span> Loading &gt; 60
                    menit</span>
            </div>
        </div>

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
            return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        }

        function getDurationBg(minutes) {
            if (minutes > 60) return '#FEE2E2';
            if (minutes > 30) return '#FEF9C3';
            return '#D1FAE5';
        }

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

        // ===== AUTO-SCROLL SLIDER ENGINE =====
        function createSlider(wrapperId, trackId, progressId, pxPerFrame) {
            const wrapper = document.getElementById(wrapperId);
            const track = document.getElementById(trackId);
            const prog = document.getElementById(progressId);
            if (!wrapper || !track) return;

            const viewH = wrapper.offsetHeight;
            let offset = 0;
            let paused = false;
            let raf;

            function getMaxScroll() {
                return Math.max(0, track.scrollHeight - viewH);
            }

            function applyTransform() {
                wrapper.scrollTop = offset;
                const max = getMaxScroll();
                if (prog) prog.style.width = max > 0 ? ((offset / max) * 100) + '%' : '0%';
            }

            function smoothScrollTo(target, durationMs, onDone) {
                const from = offset;
                const startTime = performance.now();

                function step(now) {
                    const t = Math.min((now - startTime) / durationMs, 1);
                    const ease = 1 - Math.pow(1 - t, 3);
                    offset = from + (target - from) * ease;
                    applyTransform();
                    if (t < 1) requestAnimationFrame(step);
                    else {
                        offset = target;
                        if (onDone) onDone();
                    }
                }
                requestAnimationFrame(step);
            }

            function tick() {
                if (!paused) {
                    const max = getMaxScroll();
                    if (max <= 0) {
                        // Nothing to scroll — idle
                    } else if (offset >= max) {
                        // Reached bottom: pause 2s then ease back to top
                        paused = true;
                        setTimeout(() => {
                            smoothScrollTo(0, 900, () => {
                                paused = false;
                                raf = requestAnimationFrame(tick);
                            });
                        }, 2000);
                        return;
                    } else {
                        offset = Math.min(offset + pxPerFrame, max);
                        applyTransform();
                    }
                }
                raf = requestAnimationFrame(tick);
            }

            const container = wrapper.closest('.gate-slider-container') || wrapper;

            container.addEventListener('mouseenter', () => {
                paused = true;
            });
            container.addEventListener('mouseleave', () => {
                paused = false;
                offset = wrapper.scrollTop; // sync offset back after manual scroll
            });

            wrapper.addEventListener('scroll', () => {
                if (paused) {
                    offset = wrapper.scrollTop; // Manual scroll updates offset
                    const max = getMaxScroll();
                    if (prog) prog.style.width = max > 0 ? ((offset / max) * 100) + '%' : '0%';
                }
            });

            // Delay start so layout is settled
            setTimeout(() => {
                raf = requestAnimationFrame(tick);
            }, 600);
        }

        // ===== GATE CONTENT BUILDER =====
        function getStatusHtml(gate) {
            if (gate.waktu_penyerahan)
                return '<span class="gate-status mt-2 text-[12px] px-3 py-1 rounded-full font-bold bg-purple-100 text-purple-700">✅ COMPLETED</span>';
            if (gate.waktu_end) {
                const dur = gate.waktu_start ? formatDuration(gate.waktu_start, gate.waktu_end) : 'DONE';
                return `<span class="static-timer mt-2 text-[12px] px-3 py-1 rounded font-mono bg-emerald-100 text-emerald-700" data-start="${gate.waktu_start||''}" data-end="${gate.waktu_end}">${dur}</span><span class="gate-status mt-1 text-[12px] px-3 py-1 rounded-full font-bold bg-emerald-100 text-emerald-700">🏁 FINISH</span>`;
            }
            if (gate.waktu_start) {
                return `<span class="gate-timer mt-2 text-[12px] px-3 py-1 rounded font-mono bg-blue-100 text-blue-700" data-start="${gate.waktu_start}">${formatDuration(gate.waktu_start, null)}</span><span class="gate-status mt-1 text-[12px] px-3 py-1 rounded-full font-bold animate-pulse bg-blue-100 text-blue-700">⏳ LOADING</span>`;
            }
            if (gate.waktu_penerimaan)
                return '<span class="gate-status mt-2 text-[12px] px-3 py-1 rounded-full font-bold bg-yellow-100 text-yellow-700">📋 ASSIGN</span>';
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
                '<span class="text-[14px] font-bold px-3 py-1 rounded bg-orange-100 text-orange-700">INBOUND</span>';
            else if (act === 'OUTBOUND') aktHtml =
                '<span class="text-[14px] font-bold px-3 py-1 rounded bg-indigo-100 text-indigo-700">OUTBOUND</span>';
            else if (gate.aktivitas) aktHtml =
                `<span class="text-[14px] font-bold px-3 py-1 rounded bg-gray-100 text-gray-700">${gate.aktivitas}</span>`;

            const timesHtml = `
                <div class="gate-times">
                    <div style="color:${gate.waktu_penerimaan?'#4b5563':'#d1d5db'}">📋 ${gate.waktu_penerimaan ? formatTime(gate.waktu_penerimaan) : '--:--'}</div>
                    <div style="color:${gate.waktu_start?'#4b5563':'#d1d5db'}">⏳ ${gate.waktu_start ? formatTime(gate.waktu_start) : '--:--'}</div>
                    <div style="color:${gate.waktu_end?'#4b5563':'#d1d5db'}">🏁 ${gate.waktu_end ? formatTime(gate.waktu_end) : '--:--'}</div>
                    <div style="color:${gate.waktu_penyerahan?'#4b5563':'#d1d5db'}">✅ ${gate.waktu_penyerahan ? formatTime(gate.waktu_penyerahan) : '--:--'}</div>
                </div>`;

            body.innerHTML = `
                <div class="gate-body-inner">
                    ${aktHtml}
                    <p style="font-size:24px;font-weight:700;color:#1f2937;margin-top:8px;line-height:1.2;word-break:break-all">${gate.no_polisi}</p>
                    <p style="font-size:14px;color:#6b7280;line-height:1.2;margin-top:4px">${gate.vendor || ''}</p>
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
        setInterval(updateTimers, 1000);

        // Start sliders (px per animation frame ~60fps)
        createSlider('frozen-wrapper', 'frozen-track', 'frozen-progress', 0.15);
        createSlider('dry-wrapper', 'dry-track', 'dry-progress', 0.18);

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
                                            `font-semibold ${v > 0 ? 'text-gray-800' : 'text-gray-300'}`;
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
                                        <td class="py-1 pr-3 font-medium text-gray-700 border-r border-gray-200 whitespace-nowrap">${row.jenis_kendaraan}</td>
                                        <td class="py-1 px-2 text-center font-mono ${gc(row.DRY_ALT,row.DRY_ALT_LMONTH)}">${row.DRY_ALT??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${row.DRY_ALT_LMONTH?'text-gray-500':'text-gray-300'}">${row.DRY_ALT_LMONTH??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${gc(row.DRY_AUT,row.DRY_AUT_LMONTH)}">${row.DRY_AUT??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${row.DRY_AUT_LMONTH?'text-gray-500':'text-gray-300'}">${row.DRY_AUT_LMONTH??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${gc(row.FROZEN_ALT,row.FROZEN_ALT_LMONTH)}">${row.FROZEN_ALT??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${row.FROZEN_ALT_LMONTH?'text-gray-500':'text-gray-300'}">${row.FROZEN_ALT_LMONTH??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${gc(row.FROZEN_AUT,row.FROZEN_AUT_LMONTH)}">${row.FROZEN_AUT??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${row.FROZEN_AUT_LMONTH?'text-gray-500':'text-gray-300'}">${row.FROZEN_AUT_LMONTH??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${gc(row.CHILLED_ALT,row.CHILLED_ALT_LMONTH)}">${row.CHILLED_ALT??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${row.CHILLED_ALT_LMONTH?'text-gray-500':'text-gray-300'}">${row.CHILLED_ALT_LMONTH??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${gc(row.CHILLED_AUT,row.CHILLED_AUT_LMONTH)}">${row.CHILLED_AUT??'—'}</td>
                                        <td class="py-1 px-2 text-center font-mono ${row.CHILLED_AUT_LMONTH?'text-gray-500':'text-gray-300'}">${row.CHILLED_AUT_LMONTH??'—'}</td>
                                    </tr>`).join('');
                            }
                        }

                        document.getElementById('last-update').textContent = 'Terakhir: ' + new Date()
                            .toLocaleTimeString('id-ID');
                    })
                    .catch(err => console.error('Refresh error:', err));
            }, 5000);
        @endif
    </script>
@endsection
