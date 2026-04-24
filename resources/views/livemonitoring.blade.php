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
        * {
            box-sizing: border-box;
        }

        /* ===== MONITORING CONTAINER ===== */
        #monitoring-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 120px);
            gap: 8px;
        }

        /* ===== SLIDER ===== */
        .monitoring-slider {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            border-radius: 12px;
            overflow: hidden;
        }

        .slider-track-wrapper {
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        .slider-track {
            display: flex;
            height: 100%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
        }

        .slider-slide {
            min-width: 100%;
            height: 100%;
            flex-shrink: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            /* KUNCI SCALABLE FONT */
            container-type: size;
        }

        /* ===== SLIDE 1: SUMMARY ===== */
        .slide-1-content {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            gap: 1.5cqh;
            overflow: hidden;
            padding: 1cqh;
            --s1f: clamp(10px, 1.8cqh, 24px);
            font-size: var(--s1f);
        }

        #activity-card {
            flex: 0.6;
            display: flex;
            flex-direction: column;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
        }

        #activity-card .inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.5em;
        }

        #activity-card table {
            width: 100%;
            border-collapse: collapse;
            flex: 1;
            height: 100%;
        }

        #activity-card th {
            font-size: 0.85em;
            font-weight: 600;
            color: #6b7280;
            padding: 0.4em 0.8em;
        }

        #activity-card td {
            padding: 0.4em 0.8em;
            vertical-align: middle;
        }

        #activity-card .stat-num {
            font-size: 1.4em;
            font-weight: 700;
        }

        #activity-card .lbl {
            font-size: 0.9em;
        }

        #avg-card {
            flex: 1.4;
            display: flex;
            flex-direction: column;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
        }

        #avg-card .inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.5em;
            overflow: hidden;
        }

        #avg-card .card-title {
            font-size: 1.1em;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.8em;
            flex-shrink: 0;
        }

        #avg-card table {
            width: 100%;
            border-collapse: collapse;
            flex: 1;
            height: 100%;
        }

        #avg-card th,
        #avg-card td {
            font-size: 0.8em;
            vertical-align: middle;
        }

        #avg-card .mono-val {
            font-size: 0.95em;
            font-weight: 700;
            font-family: monospace;
        }

        /* ===== SLIDE 2: GATES ===== */
        .slide-2-content {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            --gts: clamp(12px, 2cqw, 24px);
        }

        #gate-section {
            flex: 1;
            min-height: 0;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        #gate-grid-body {
            flex: 1;
            min-height: 0;
            padding: 1cqh 1cqw;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        #gate-unified-grid {
            flex: 1;
            min-height: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1cqw;
        }

        .gate-side {
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        .frozen-side-wrapper {
            border-right: 1px solid #e5e7eb;
            padding-right: 1cqw;
        }

        .gate-side-title {
            text-align: center;
            font-weight: 700;
            margin-bottom: 0.5cqh;
            flex-shrink: 0;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-size: var(--gts);
        }

        #frozen-grid-inner {
            flex: 1;
            min-height: 0;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(4, 1fr);
            gap: 6px;
        }

        #dry-grid-inner {
            flex: 1;
            min-height: 0;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 6px;
        }

        /* ===== GATE CARD (SCALABLE & STRETCH FIX) ===== */
        .gate-card {
            border-radius: 6px;
            border-width: 1.5px;
            border-style: solid;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 0;
            container-type: size;
        }

        .gate-card-header {
            text-align: center;
            padding: 2px 3px;
            flex-shrink: 0;
            height: max(20px, 20cqh);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .gate-card-header .gate-number {
            font-size: clamp(10px, 14cqmin, 24px);
            font-weight: 800;
            line-height: 1;
            color: #fff;
            white-space: nowrap;
        }

        .gate-card-header .gate-type {
            font-size: clamp(7px, 8cqmin, 12px);
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            white-space: nowrap;
            line-height: 1;
        }

        .gate-body {
            flex: 1;
            min-height: 0;
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            padding: 3px;
            background: #fff;
            gap: 2px;
        }

        /* Kolom Kiri (Info) */
        .gate-body-inner {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            /* Supaya Aktivitas diam di atas */
            align-items: center;
            text-align: center;
            gap: 2px;
            padding: 2px 0;
            overflow: hidden;
        }

        /* Teks dibuat normal (bukan nowrap) agar full terlihat & membungkus (wrap) jika kepanjangan */
        .gate-aktivitas {
            font-size: clamp(10px, 12cqw, 18px);
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 4px;
            text-align: center;
            line-height: 1.1;
            width: fit-content;
        }

        .gate-plate {
            font-size: clamp(10px, 16cqw, 24px);
            font-weight: 800;
            color: #1f2937;
            line-height: 1.1;
            white-space: normal;
            /* Menghilangkan titik-titik (ellipsis) */
            word-wrap: break-word;
            width: 100%;
        }

        .gate-vendor-name {
            font-size: clamp(7px, 9cqw, 14px);
            color: #6b7280;
            line-height: 1.1;
            white-space: normal;
            /* Menghilangkan titik-titik (ellipsis) */
            word-wrap: break-word;
            width: 100%;
        }

        .gate-badge {
            font-size: clamp(7px, 10cqw, 13px);
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 9999px;
            white-space: normal;
            text-align: center;
            line-height: 1.1;
            margin-bottom: 2px;
        }

        .gate-timer-text {
            font-size: clamp(8px, 10cqw, 15px);
            font-weight: 700;
            font-family: monospace;
            padding: 1px 4px;
            border-radius: 3px;
            white-space: nowrap;
        }

        .gate-empty {
            color: #d1d5db;
            font-style: italic;
            font-size: clamp(8px, 12cqw, 16px);
            margin: auto 0;
        }

        /* Kolom Kanan (Waktu) */
        .gate-times {
            flex: 0 0 32%;
            /* Proporsi sedikit diturunkan krn format tumpuk */
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
            border-left: 1px solid #e5e7eb;
            padding-left: 2px;
            color: #6b7280;
            font-family: monospace;
            overflow: hidden;
        }

        .gate-times>div {
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Teks dan icon ke tengah (center alignment) */
            justify-content: center;
            text-align: center;
            font-size: clamp(7px, 8cqmin, 13px);
            /* Ukuran jam */
            line-height: 1;
        }

        .gate-times>div>span:first-child {
            font-size: clamp(9px, 10cqmin, 16px);
            /* Ukuran icon sedikit diperbesar */
            margin-bottom: 2px;
            /* Jarak antara icon dan waktu */
        }

        /* ===== DRY LEGEND ===== */
        .dry-side-bottom {
            flex-shrink: 0;
            margin-top: 6px;
        }

        .gate-legend {
            background: #fff;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            padding: 6px 10px;
        }

        .gate-legend p {
            font-size: clamp(10px, 1cqw, 14px);
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .legend-items {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: clamp(10px, 1cqw, 13px);
            color: #6b7280;
        }

        /* ===== SLIDER INDICATORS ===== */
        .slider-indicators {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 5px 0 2px;
            flex-shrink: 0;
        }

        .slider-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #d1d5db;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            padding: 0;
        }

        .slider-dot.active {
            background: linear-gradient(135deg, #f97316, #ea580c);
            transform: scale(1.3);
            box-shadow: 0 0 8px rgba(249, 115, 22, 0.4);
        }

        .slider-dot:hover:not(.active) {
            background: #9ca3af;
            transform: scale(1.1);
        }

        .slider-progress-wrap {
            width: 100px;
            height: 3px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin-left: 10px;
        }

        .slider-progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #f97316, #ea580c);
            border-radius: 3px;
            transition: width 0.2s linear;
        }

        .slider-label {
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.3s;
        }

        .slider-label.active {
            color: #f97316;
        }

        /* ===== FULLSCREEN ===== */
        body.fullscreen-mode #monitoring-container {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: #f9fafb;
            padding: 8px 12px;
            height: 100vh;
        }
    </style>

    {{-- Fullscreen exit btn --}}
    <button onclick="toggleFullscreen()"
        class="fs-exit-btn hidden fixed top-3 right-3 z-50 items-center gap-1.5 px-3 py-1.5 bg-gray-900/80 hover:bg-gray-900 text-white text-xs font-medium rounded-full shadow-lg backdrop-blur transition-all">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" />
        </svg>
        Keluar Fullscreen (ESC)
    </button>

    <div id="monitoring-container">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 flex-shrink-0"
            id="monitoring-header">
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
                    <span class="flex items-center gap-1.5 text-xs text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full">
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

        {{-- SLIDER --}}
        <div class="monitoring-slider" id="monitoring-slider">
            <div class="slider-track-wrapper" id="slider-track-wrapper">
                <div class="slider-track" id="slider-track">

                    {{-- ===== SLIDE 1: Summary ===== --}}
                    <div class="slider-slide" data-slide="0">
                        <div class="slide-1-content" id="slide-1-content">

                            {{-- Activity Summary --}}
                            <div id="activity-card">
                                <div class="inner">
                                    <p style="font-size:1em;font-weight:700;color:#1f2937;margin-bottom:0.5em">Activity
                                        Summary</p>
                                    <p style="font-size:0.75em;color:#9ca3af;margin-bottom:1em">Periode: {{ $periode }}
                                    </p>
                                    <table>
                                        <thead>
                                            <tr style="border-bottom:1px solid #f3f4f6">
                                                <th class="lbl" style="text-align:left;width:120px">Activity</th>
                                                <th class="lbl" style="text-align:center">Parking</th>
                                                <th class="lbl" style="text-align:center">Doc In</th>
                                                <th class="lbl" style="text-align:center">Loading/Unloading</th>
                                                <th class="lbl" style="text-align:center">Finish</th>
                                            </tr>
                                        </thead>
                                        <tbody id="activity-summary-body">
                                            @foreach ($activitySummary as $row)
                                                <tr style="border-top:1px solid #f9fafb">
                                                    <td class="lbl" style="font-weight:600;color:#1f2937">
                                                        {{ $row['label'] }}</td>
                                                    <td style="text-align:center"><span
                                                            class="stat-num {{ $row['parking'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['parking'] }}</span>
                                                    </td>
                                                    <td style="text-align:center"><span
                                                            class="stat-num {{ $row['receiving'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['receiving'] }}</span>
                                                    </td>
                                                    <td style="text-align:center"><span
                                                            class="stat-num {{ $row['on_process'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['on_process'] }}</span>
                                                    </td>
                                                    <td style="text-align:center"><span
                                                            class="stat-num {{ $row['finish'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['finish'] }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Average Loading Time --}}
                            <div id="avg-card">
                                <div class="inner">
                                    <p class="card-title">Average Loading Time</p>
                                    <table style="border-collapse:collapse">
                                        <thead>
                                            <tr>
                                                <th rowspan="3"
                                                    style="text-align:left;font-weight:600;color:#6b7280;padding:2px 8px 2px 0;border-right:1px solid #e5e7eb;white-space:nowrap;vertical-align:bottom;min-width:80px">
                                                    Kendaraan</th>
                                                <th colspan="4"
                                                    style="text-align:center;font-weight:600;color:#15803d;background:#f0fdf4;border:1px solid #e5e7eb;padding:3px 4px">
                                                    DRY</th>
                                                <th colspan="4"
                                                    style="text-align:center;font-weight:600;color:#1d4ed8;background:#eff6ff;border:1px solid #e5e7eb;padding:3px 4px">
                                                    FROZEN</th>
                                                <th colspan="4"
                                                    style="text-align:center;font-weight:600;color:#b45309;background:#fffbeb;border:1px solid #e5e7eb;padding:3px 4px">
                                                    CHILLED</th>
                                            </tr>
                                            <tr>
                                                @foreach (['DRY', 'DRY', 'FROZEN', 'FROZEN', 'CHILLED', 'CHILLED'] as $gi => $g)
                                                    @php $sub = $gi % 2 === 0 ? 'ALT' : 'AUT'; @endphp
                                                    <th colspan="2"
                                                        style="text-align:center;padding:2px 4px;border:1px solid #e5e7eb;white-space:nowrap;
                                                {{ $g === 'DRY' ? 'color:#16a34a;background:#f0fdf4' : ($g === 'FROZEN' ? 'color:#2563eb;background:#eff6ff' : 'color:#d97706;background:#fffbeb') }}">
                                                        {{ $sub }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach (['text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50'] as $idx => $cls)
                                                    <th style="text-align:center;padding:2px 6px;border:1px solid #e5e7eb">
                                                        {{ $idx % 2 === 0 ? 'Today' : 'L.Month' }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody id="avg-times-body">
                                            @foreach ($avgTimes as $row)
                                                @php
                                                    $gc = function ($t, $lm) {
                                                        if (!$t) {
                                                            return 'color:#d1d5db';
                                                        }
                                                        if (!$lm) {
                                                            return 'color:#374151';
                                                        }
                                                        if ($t > $lm) {
                                                            return 'color:#dc2626';
                                                        }
                                                        if ($t < $lm) {
                                                            return 'color:#16a34a';
                                                        }
                                                        return 'color:#374151';
                                                    };
                                                @endphp
                                                <tr style="border-top:1px solid #f9fafb">
                                                    <td
                                                        style="padding:3px 8px 3px 0;font-weight:500;color:#374151;border-right:1px solid #e5e7eb;white-space:nowrap">
                                                        {{ $row['jenis_kendaraan'] }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['DRY_ALT'] ?? null, $row['DRY_ALT_LMONTH'] ?? null) }}">
                                                        {{ $row['DRY_ALT'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['DRY_ALT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">
                                                        {{ $row['DRY_ALT_LMONTH'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['DRY_AUT'] ?? null, $row['DRY_AUT_LMONTH'] ?? null) }}">
                                                        {{ $row['DRY_AUT'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['DRY_AUT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">
                                                        {{ $row['DRY_AUT_LMONTH'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['FROZEN_ALT'] ?? null, $row['FROZEN_ALT_LMONTH'] ?? null) }}">
                                                        {{ $row['FROZEN_ALT'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['FROZEN_ALT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">
                                                        {{ $row['FROZEN_ALT_LMONTH'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['FROZEN_AUT'] ?? null, $row['FROZEN_AUT_LMONTH'] ?? null) }}">
                                                        {{ $row['FROZEN_AUT'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['FROZEN_AUT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">
                                                        {{ $row['FROZEN_AUT_LMONTH'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['CHILLED_ALT'] ?? null, $row['CHILLED_ALT_LMONTH'] ?? null) }}">
                                                        {{ $row['CHILLED_ALT'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['CHILLED_ALT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">
                                                        {{ $row['CHILLED_ALT_LMONTH'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['CHILLED_AUT'] ?? null, $row['CHILLED_AUT_LMONTH'] ?? null) }}">
                                                        {{ $row['CHILLED_AUT'] ?? '—' }}</td>
                                                    <td class="mono-val"
                                                        style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['CHILLED_AUT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">
                                                        {{ $row['CHILLED_AUT_LMONTH'] ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>{{-- end slide 1 --}}

                    {{-- ===== SLIDE 2: Gate Grid ===== --}}
                    <div class="slider-slide" data-slide="1">
                        <div class="slide-2-content">
                            <div id="gate-section">
                                <div id="gate-grid-body">
                                    <div id="gate-unified-grid">

                                        {{-- FROZEN --}}
                                        <div class="gate-side frozen-side-wrapper">
                                            <div class="gate-side-title text-blue-600">❄️ Frozen</div>
                                            <div id="frozen-grid-inner">
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
                                                                        class="gate-aktivitas {{ $cp->aktivitas === 'INBOUND' ? 'bg-orange-100 text-orange-700' : ($cp->aktivitas === 'OUTBOUND' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700') }}">
                                                                        {{ $cp->aktivitas }}
                                                                    </span>
                                                                    <div style="margin: auto 0; width: 100%;">
                                                                        <p class="gate-plate">{{ $cp->no_polisi }}</p>
                                                                        <p class="gate-vendor-name">{{ $cp->vendor }}
                                                                        </p>
                                                                    </div>
                                                                    @if ($cp->waktu_penyerahan_dokumen)
                                                                        <span
                                                                            class="gate-badge bg-purple-100 text-purple-700">✅
                                                                            COMPLETED</span>
                                                                    @elseif($cp->waktu_end || $cp->status === 'FINISH')
                                                                        <span
                                                                            class="static-timer gate-timer-text bg-emerald-100 text-emerald-700"
                                                                            data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}"
                                                                            data-end="{{ $cp->waktu_end?->toIso8601String() ?? '' }}">DONE</span>
                                                                        <span
                                                                            class="gate-badge bg-emerald-100 text-emerald-700">🏁
                                                                            FINISH</span>
                                                                    @elseif($cp->waktu_start || $cp->status === 'ON LOADING')
                                                                        <span
                                                                            class="gate-timer gate-timer-text bg-blue-100 text-blue-700"
                                                                            data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}">00:00:00</span>
                                                                        <span
                                                                            class="gate-badge bg-blue-100 text-blue-700 animate-pulse">⏳
                                                                            {{ $cp->aktivitas === 'INBOUND' ? 'UNLOADING' : 'LOADING' }}</span>
                                                                    @elseif($cp->waktu_penerimaan_dokumen)
                                                                        <span
                                                                            class="gate-badge bg-yellow-100 text-yellow-700">📋
                                                                            ASSIGN</span>
                                                                    @endif
                                                                @else
                                                                    <span class="gate-empty">kosong</span>
                                                                @endif
                                                            </div>

                                                            @if ($cp)
                                                                <div class="gate-times">
                                                                    <div
                                                                        style="color:{{ $cp->waktu_penerimaan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                                        <span>📋</span>
                                                                        <span>{{ $cp->waktu_penerimaan_dokumen?->format('H:i') ?? '--:--' }}</span>
                                                                    </div>
                                                                    <div
                                                                        style="color:{{ $cp->waktu_start ? '#4b5563' : '#d1d5db' }}">
                                                                        <span>⏳</span>
                                                                        <span>{{ $cp->waktu_start?->format('H:i') ?? '--:--' }}</span>
                                                                    </div>
                                                                    <div
                                                                        style="color:{{ $cp->waktu_end ? '#4b5563' : '#d1d5db' }}">
                                                                        <span>🏁</span>
                                                                        <span>{{ $cp->waktu_end?->format('H:i') ?? '--:--' }}</span>
                                                                    </div>
                                                                    <div
                                                                        style="color:{{ $cp->waktu_penyerahan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                                        <span>✅</span>
                                                                        <span>{{ $cp->waktu_penyerahan_dokumen?->format('H:i') ?? '--:--' }}</span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>

                                        {{-- DRY --}}
                                        <div class="gate-side">
                                            <div class="gate-side-title text-amber-600">🌡️ Dry</div>
                                            <div id="dry-grid-inner">
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
                                                    <div id="gate-{{ $i }}"
                                                        class="gate-card border-amber-300">
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
                                                                        class="gate-aktivitas {{ $cp->aktivitas === 'INBOUND' ? 'bg-orange-100 text-orange-700' : ($cp->aktivitas === 'OUTBOUND' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700') }}">
                                                                        {{ $cp->aktivitas }}
                                                                    </span>
                                                                    <div style="margin: auto 0; width: 100%;">
                                                                        <p class="gate-plate">{{ $cp->no_polisi }}</p>
                                                                        <p class="gate-vendor-name">{{ $cp->vendor }}
                                                                        </p>
                                                                    </div>
                                                                    @if ($cp->waktu_penyerahan_dokumen)
                                                                        <span
                                                                            class="gate-badge bg-purple-100 text-purple-700">✅
                                                                            COMPLETED</span>
                                                                    @elseif($cp->waktu_end || $cp->status === 'FINISH')
                                                                        <span
                                                                            class="static-timer gate-timer-text bg-emerald-100 text-emerald-700"
                                                                            data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}"
                                                                            data-end="{{ $cp->waktu_end?->toIso8601String() ?? '' }}">DONE</span>
                                                                        <span
                                                                            class="gate-badge bg-emerald-100 text-emerald-700">🏁
                                                                            FINISH</span>
                                                                    @elseif($cp->waktu_start || $cp->status === 'ON LOADING')
                                                                        <span
                                                                            class="gate-timer gate-timer-text bg-blue-100 text-blue-700"
                                                                            data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}">00:00:00</span>
                                                                        <span
                                                                            class="gate-badge bg-blue-100 text-blue-700 animate-pulse">⏳
                                                                            {{ $cp->aktivitas === 'INBOUND' ? 'UNLOADING' : 'LOADING' }}</span>
                                                                    @elseif($cp->waktu_penerimaan_dokumen)
                                                                        <span
                                                                            class="gate-badge bg-yellow-100 text-yellow-700">📋
                                                                            ASSIGN</span>
                                                                    @endif
                                                                @else
                                                                    <span class="gate-empty">kosong</span>
                                                                @endif
                                                            </div>

                                                            @if ($cp)
                                                                <div class="gate-times">
                                                                    <div
                                                                        style="color:{{ $cp->waktu_penerimaan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                                        <span>📋</span>
                                                                        <span>{{ $cp->waktu_penerimaan_dokumen?->format('H:i') ?? '--:--' }}</span>
                                                                    </div>
                                                                    <div
                                                                        style="color:{{ $cp->waktu_start ? '#4b5563' : '#d1d5db' }}">
                                                                        <span>⏳</span>
                                                                        <span>{{ $cp->waktu_start?->format('H:i') ?? '--:--' }}</span>
                                                                    </div>
                                                                    <div
                                                                        style="color:{{ $cp->waktu_end ? '#4b5563' : '#d1d5db' }}">
                                                                        <span>🏁</span>
                                                                        <span>{{ $cp->waktu_end?->format('H:i') ?? '--:--' }}</span>
                                                                    </div>
                                                                    <div
                                                                        style="color:{{ $cp->waktu_penyerahan_dokumen ? '#4b5563' : '#d1d5db' }}">
                                                                        <span>✅</span>
                                                                        <span>{{ $cp->waktu_penyerahan_dokumen?->format('H:i') ?? '--:--' }}</span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endfor
                                                {{-- pad --}}
                                                <div class="gate-card border-transparent opacity-0 pointer-events-none">
                                                </div>
                                            </div>

                                            {{-- Legend --}}
                                            <div class="dry-side-bottom gate-legend">
                                                <p>Keterangan Status Gate:</p>
                                                <div class="legend-items">
                                                    <span class="legend-item"><span
                                                            class="gate-badge bg-yellow-100 text-yellow-700">📋
                                                            ASSIGN</span> Dokumen diterima</span>
                                                    <span class="legend-item"><span
                                                            class="gate-badge bg-blue-100 text-blue-700">⏳ ON
                                                            LOADING</span> Sedang loading</span>
                                                    <span class="legend-item"><span
                                                            class="gate-badge bg-emerald-100 text-emerald-700">🏁
                                                            FINISH</span> Loading selesai</span>
                                                    <span class="legend-item"><span
                                                            class="gate-badge bg-purple-100 text-purple-700">✅
                                                            COMPLETED</span> Dok. diserahkan</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>{{-- end gate-unified-grid --}}
                                </div>
                            </div>
                        </div>
                    </div>{{-- end slide 2 --}}

                </div>{{-- slider-track --}}
            </div>{{-- slider-track-wrapper --}}

            {{-- Indicators --}}
            <div class="slider-indicators" id="slider-indicators">
                <span class="slider-label active" data-goto="0">📊 Summary</span>
                <button class="slider-dot active" data-goto="0"></button>
                <button class="slider-dot" data-goto="1"></button>
                <span class="slider-label" data-goto="1">🚪 Gates</span>
                <div class="slider-progress-wrap">
                    <div class="slider-progress-bar" id="slider-progress"></div>
                </div>
            </div>
        </div>{{-- monitoring-slider --}}

    </div>{{-- monitoring-container --}}

    <script>
        // =============================================
        // HELPERS
        // =============================================
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

        // =============================================
        // GATE COLORS & TIMERS
        // =============================================
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

        // =============================================
        // GATE CONTENT BUILDER (live refresh)
        // =============================================
        function getStatusHtml(gate) {
            if (gate.waktu_penyerahan)
                return '<span class="gate-badge bg-purple-100 text-purple-700">✅ COMPLETED</span>';
            if (gate.waktu_end) {
                const dur = gate.waktu_start ? formatDuration(gate.waktu_start, gate.waktu_end) : 'DONE';
                return `<span class="static-timer gate-timer-text bg-emerald-100 text-emerald-700" data-start="${gate.waktu_start||''}" data-end="${gate.waktu_end}">${dur}</span><span class="gate-badge bg-emerald-100 text-emerald-700">🏁 FINISH</span>`;
            }
            if (gate.waktu_start)
                return `<span class="gate-timer gate-timer-text bg-blue-100 text-blue-700" data-start="${gate.waktu_start}">${formatDuration(gate.waktu_start,null)}</span><span class="gate-badge animate-pulse bg-blue-100 text-blue-700">⏳ ${gate.aktivitas === 'INBOUND' ? 'UNLOADING' : 'LOADING'}</span>`;
            if (gate.waktu_penerimaan)
                return '<span class="gate-badge bg-yellow-100 text-yellow-700">📋 ASSIGN</span>';
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

            if (gate.waktu_penyerahan && ((new Date() - new Date(gate.waktu_penyerahan)) / 60000) >= 2) {
                body.innerHTML = '<div class="gate-body-inner"><span class="gate-empty">kosong</span></div>';
                body.style.backgroundColor = '';
                return;
            }

            const act = (gate.aktivitas || '').toUpperCase();
            let aktHtml = '';
            if (act === 'INBOUND') aktHtml = '<span class="gate-aktivitas bg-orange-100 text-orange-700">INBOUND</span>';
            else if (act === 'OUTBOUND') aktHtml =
                '<span class="gate-aktivitas bg-indigo-100 text-indigo-700">OUTBOUND</span>';
            else if (gate.aktivitas) aktHtml =
                `<span class="gate-aktivitas bg-gray-100 text-gray-700">${gate.aktivitas}</span>`;

            const timesHtml = `
        <div class="gate-times">
            <div style="color:${gate.waktu_penerimaan?'#4b5563':'#d1d5db'}">
                <span>📋</span>
                <span>${gate.waktu_penerimaan?formatTime(gate.waktu_penerimaan):'--:--'}</span>
            </div>
            <div style="color:${gate.waktu_start?'#4b5563':'#d1d5db'}">
                <span>⏳</span>
                <span>${gate.waktu_start?formatTime(gate.waktu_start):'--:--'}</span>
            </div>
            <div style="color:${gate.waktu_end?'#4b5563':'#d1d5db'}">
                <span>🏁</span>
                <span>${gate.waktu_end?formatTime(gate.waktu_end):'--:--'}</span>
            </div>
            <div style="color:${gate.waktu_penyerahan?'#4b5563':'#d1d5db'}">
                <span>✅</span>
                <span>${gate.waktu_penyerahan?formatTime(gate.waktu_penyerahan):'--:--'}</span>
            </div>
        </div>`;

            body.innerHTML = `
        <div class="gate-body-inner">
            ${aktHtml}
            <div style="margin: auto 0; width: 100%;">
                <p class="gate-plate">${gate.no_polisi}</p>
                <p class="gate-vendor-name">${gate.vendor||''}</p>
            </div>
            ${getStatusHtml(gate)}
        </div>
        ${timesHtml}`;

            if (gate.status === 'ON LOADING' && gate.waktu_start)
                body.style.backgroundColor = getDurationBg(getDurationMinutes(gate.waktu_start, null));
            else if (gate.status === 'FINISH' && gate.waktu_start && gate.waktu_end)
                body.style.backgroundColor = getDurationBg(getDurationMinutes(gate.waktu_start, gate.waktu_end));
            else
                body.style.backgroundColor = '';
        }

        // =============================================
        // FULLSCREEN
        // =============================================
        function toggleFullscreen() {
            const isFs = document.body.classList.toggle('fullscreen-mode');
            document.querySelector('.fs-icon-expand').style.display = isFs ? 'none' : '';
            document.querySelector('.fs-icon-compress').style.display = isFs ? '' : 'none';
            const exitBtn = document.querySelector('.fs-exit-btn');
            if (exitBtn) exitBtn.style.display = isFs ? 'flex' : 'none';
            const container = document.getElementById('monitoring-container');
            if (isFs) {
                container.style.height = '100vh';
            } else {
                container.style.height = 'calc(100vh - 120px)';
            }
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && document.body.classList.contains('fullscreen-mode')) toggleFullscreen();
        });

        // =============================================
        // SLIDER ENGINE
        // =============================================
        const SLIDE_INTERVAL = 10000;
        const PROGRESS_TICK = 100;
        let currentSlide = 0;
        const totalSlides = 2;
        let progressTimer = null;
        let progressValue = 0;
        let sliderPaused = false;

        const track = document.getElementById('slider-track');
        const dots = document.querySelectorAll('.slider-dot');
        const labels = document.querySelectorAll('.slider-label');
        const progressBar = document.getElementById('slider-progress');
        const sliderEl = document.getElementById('monitoring-slider');

        function goToSlide(index) {
            currentSlide = ((index % totalSlides) + totalSlides) % totalSlides;
            track.style.transform = `translateX(-${currentSlide * 100}%)`;
            document.querySelectorAll('.slider-slide').forEach((s, i) => s.classList.toggle('active', i === currentSlide));
            dots.forEach((d, i) => d.classList.toggle('active', i === currentSlide));
            labels.forEach(l => l.classList.toggle('active', parseInt(l.dataset.goto) === currentSlide));
            progressValue = 0;
            if (progressBar) progressBar.style.width = '0%';
        }

        function startSliderAutoplay() {
            if (progressTimer) clearInterval(progressTimer);
            progressValue = 0;
            progressTimer = setInterval(() => {
                if (sliderPaused) return;
                progressValue += PROGRESS_TICK;
                if (progressBar) progressBar.style.width = Math.min((progressValue / SLIDE_INTERVAL) * 100, 100) +
                    '%';
                if (progressValue >= SLIDE_INTERVAL) {
                    goToSlide(currentSlide + 1);
                    progressValue = 0;
                }
            }, PROGRESS_TICK);
        }

        dots.forEach(d => d.addEventListener('click', () => {
            goToSlide(parseInt(d.dataset.goto));
            startSliderAutoplay();
        }));
        labels.forEach(l => l.addEventListener('click', () => {
            goToSlide(parseInt(l.dataset.goto));
            startSliderAutoplay();
        }));
        sliderEl.addEventListener('mouseenter', () => sliderPaused = true);
        sliderEl.addEventListener('mouseleave', () => sliderPaused = false);
        document.addEventListener('keydown', e => {
            if (e.key === 'ArrowLeft') {
                goToSlide(currentSlide - 1);
                startSliderAutoplay();
            }
            if (e.key === 'ArrowRight') {
                goToSlide(currentSlide + 1);
                startSliderAutoplay();
            }
        });

        // =============================================
        // INIT
        // =============================================

        // Wait for DOM paint 
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                applyGateColors();
                startSliderAutoplay();
            });
        });

        setInterval(updateTimers, 1000);

        document.getElementById('tanggal-picker').addEventListener('change', function() {
            if (this.value) window.location.href = '{{ route('livemonitoring') }}?tanggal=' + this.value;
        });

        // =============================================
        // LIVE REFRESH (today only)
        // =============================================
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
                                        if (s) {
                                            s.textContent = v;
                                            s.className =
                                                `stat-num ${v>0?'text-gray-800':'text-gray-300'}`;
                                        }
                                    }
                                });
                            });
                        }

                        if (data.avgTimes) {
                            const avgBody = document.getElementById('avg-times-body');
                            if (avgBody) {
                                const gc = (t, lm) => !t ? 'color:#d1d5db' : !lm ? 'color:#374151' : t > lm ?
                                    'color:#dc2626' : t < lm ? 'color:#16a34a' : 'color:#374151';
                                avgBody.innerHTML = data.avgTimes.map(row => `
                        <tr style="border-top:1px solid #f9fafb">
                            <td style="padding:3px 8px 3px 0;font-weight:500;color:#374151;border-right:1px solid #e5e7eb;white-space:nowrap">${row.jenis_kendaraan}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.DRY_ALT,row.DRY_ALT_LMONTH)}">${row.DRY_ALT??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.DRY_ALT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.DRY_ALT_LMONTH??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.DRY_AUT,row.DRY_AUT_LMONTH)}">${row.DRY_AUT??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.DRY_AUT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.DRY_AUT_LMONTH??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.FROZEN_ALT,row.FROZEN_ALT_LMONTH)}">${row.FROZEN_ALT??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.FROZEN_ALT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.FROZEN_ALT_LMONTH??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.FROZEN_AUT,row.FROZEN_AUT_LMONTH)}">${row.FROZEN_AUT??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.FROZEN_AUT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.FROZEN_AUT_LMONTH??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.CHILLED_ALT,row.CHILLED_ALT_LMONTH)}">${row.CHILLED_ALT??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.CHILLED_ALT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.CHILLED_ALT_LMONTH??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${gc(row.CHILLED_AUT,row.CHILLED_AUT_LMONTH)}">${row.CHILLED_AUT??'—'}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;${row.CHILLED_AUT_LMONTH?'color:#6b7280':'color:#d1d5db'}">${row.CHILLED_AUT_LMONTH??'—'}</td>
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
