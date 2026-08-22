@extends('layouts.app')

@section('title', __('Dashboard'))

@section('header-actions')
    <button onclick="toggleFullscreen()" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" title="Fullscreen (ESC untuk keluar)">
        <svg class="w-5 h-5 fs-icon-expand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
        <svg class="w-5 h-5 fs-icon-compress" style="display:none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/></svg>
    </button>
@endsection

@section('content')
<!-- Floating exit fullscreen button -->
<button onclick="toggleFullscreen()" class="fs-exit-btn hidden fixed top-3 right-3 z-50 items-center gap-1.5 px-3 py-1.5 bg-gray-900/80 hover:bg-gray-900 text-white text-xs font-medium rounded-full shadow-lg backdrop-blur transition-all">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/></svg>
    {{ __('Keluar Fullscreen (ESC)') }}
</button>

<style>
    .dash-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        padding: 20px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .dash-card:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.06), 0 4px 10px rgba(0,0,0,0.04);
        transform: translateY(-2px);
    }
    .dash-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        border-radius: 16px 16px 0 0;
    }
    .dash-card.card-blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .dash-card.card-emerald::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .dash-card.card-amber::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .dash-card.card-violet::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-icon svg {
        width: 24px;
        height: 24px;
    }

    .matrix-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .matrix-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .matrix-icon-wrapper svg { width: 22px; height: 22px; }
    .matrix-content h4 { font-size: 0.75em; color: #64748b; font-weight: 600; text-transform: uppercase; margin-bottom: 2px; }
    .matrix-content p { font-size: 1.1em; color: #0f172a; font-weight: 800; line-height: 1.2; }
    .matrix-content span { font-size: 0.75em; color: #94a3b8; font-weight: 500; }

    .status-pipeline {
        display: flex;
        gap: 0;
        background: #f8fafc;
        border-radius: 12px;
        padding: 4px;
        border: 1px solid #e2e8f0;
    }
    .pipeline-step {
        flex: 1;
        text-align: center;
        padding: 12px 8px;
        border-radius: 10px;
        position: relative;
        transition: all 0.2s ease;
    }
    .pipeline-step:hover {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .pipeline-step .step-count {
        font-size: 1.6em;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
    }
    .pipeline-step .step-label {
        font-size: 0.68em;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .pipeline-step::after {
        content: '›';
        position: absolute;
        right: -6px;
        top: 50%;
        transform: translateY(-50%);
        color: #cbd5e1;
        font-size: 1.3em;
        font-weight: 300;
        z-index: 2;
    }
    .pipeline-step:last-child::after {
        display: none;
    }

    .gauge-ring {
        position: relative;
        width: 100px;
        height: 100px;
    }
    .gauge-ring svg {
        transform: rotate(-90deg);
    }
    .gauge-ring .gauge-text {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .chart-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        position: relative;
    }
    .chart-card h3 {
        font-size: 0.85em;
        font-weight: 700;
        color: #374151;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .chart-card h3 .chart-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    
    .interactive-chart {
        cursor: pointer;
    }
    
    #active-filters-container {
        display: none;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 20px;
        align-items: center;
        gap: 12px;
    }
    .filter-tag {
        display: inline-flex;
        align-items: center;
        background: #fff;
        border: 1px solid #3b82f6;
        color: #1d4ed8;
        font-size: 0.75em;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        gap: 6px;
    }
    .filter-tag button {
        color: #93c5fd;
        transition: color 0.2s;
    }
    .filter-tag button:hover {
        color: #1e3a8a;
    }
</style>

<div class="space-y-6">
    
    {{-- Active Filters UI --}}
    <div id="active-filters-container" class="flex flex-wrap shadow-sm">
        <span class="text-xs font-bold text-sky-800 uppercase tracking-wider flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            {{ __('Difilter Berdasarkan') }}:
        </span>
        <div id="filter-tags" class="flex gap-2"></div>
        <button id="clear-all-filters" class="ml-auto text-xs font-bold text-sky-600 hover:text-sky-800 underline">
            {{ __('Reset Filter') }}
        </button>
    </div>

    {{-- KPI Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
        {{-- Total Record --}}
        <div class="dash-card card-blue">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Total Record') }}</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['total_all']) }}</p>
                    <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ __('Hari ini') }}: <span class="font-bold text-blue-600">{{ $stats['total_today'] }}</span>
                    </p>
                </div>
                <div class="card-icon bg-blue-50">
                    <svg class="text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>

        {{-- Inbound --}}
        <div class="dash-card card-emerald">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Inbound</p>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($stats['inbound']) }}</p>
                    <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        {{ __('Hari ini') }}
                    </p>
                </div>
                <div class="card-icon bg-emerald-50">
                    <svg class="text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </div>
            </div>
        </div>

        {{-- Outbound --}}
        <div class="dash-card card-amber">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Outbound</p>
                    <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($stats['outbound']) }}</p>
                    <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        {{ __('Hari ini') }}
                    </p>
                </div>
                <div class="card-icon bg-amber-50">
                    <svg class="text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
            </div>
        </div>

        {{-- Selesai --}}
        <div class="dash-card card-violet">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Selesai') }}</p>
                    <p class="text-3xl font-extrabold text-violet-600 mt-1">{{ number_format($stats['finish']) }}</p>
                    <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Masih proses') }}: <span class="font-bold text-violet-600">{{ $stats['start'] }}</span>
                    </p>
                </div>
                <div class="card-icon bg-violet-50">
                    <svg class="text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Highlight Matrices (New) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Top Goods Type --}}
        <div class="matrix-card">
            <div class="matrix-icon-wrapper bg-sky-100 text-sky-600">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="matrix-content">
                <h4>{{ __('Goods Terbanyak') }}</h4>
                <p>{{ $topGoodsName }}</p>
                @if($topGoodsPct > 0) <span>{{ $topGoodsPct }}% {{ __('dari total keseluruhan') }}</span> @endif
            </div>
        </div>
        
        {{-- Top Vehicle Type --}}
        <div class="matrix-card">
            <div class="matrix-icon-wrapper bg-indigo-100 text-indigo-600">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div class="matrix-content">
                <h4>{{ __('Kendaraan Terbanyak') }}</h4>
                <p>{{ $topVehicleName }}</p>
                @if($topVehicleCount > 0) <span>{{ $topVehicleCount }} {{ __('unit terdata') }}</span> @endif
            </div>
        </div>

        {{-- Longest Loading Time --}}
        <div class="matrix-card">
            <div class="matrix-icon-wrapper bg-rose-100 text-rose-600">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="matrix-content">
                <h4>{{ __('Loading Terlama (Hari Ini)') }}</h4>
                <p>{{ $longestLoadingDur }}</p>
                @if($longestLoadingPolisi != '-') <span>{{ __('Nopol') }}: {{ $longestLoadingPolisi }}</span> @endif
            </div>
        </div>

        {{-- Most Used Gate --}}
        <div class="matrix-card">
            <div class="matrix-icon-wrapper bg-teal-100 text-teal-600">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            </div>
            <div class="matrix-content">
                <h4>{{ __('Gate Terbanyak (Hari Ini)') }}</h4>
                <p>{{ $mostUsedGateName ? 'Gate ' . $mostUsedGateName : '-' }}</p>
                @if($mostUsedGateCount > 0) <span>{{ __('Digunakan') }} {{ $mostUsedGateCount }} {{ __('kali') }}</span> @endif
            </div>
        </div>
    </div>

    {{-- Status Pipeline + Gate Utilization --}}
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-5">
        {{-- Status Pipeline (3/4 width) --}}
        <div class="xl:col-span-3 dash-card" style="padding:20px">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-700">{{ __('Status Pipeline') }}</h3>
                    <p class="text-xs text-gray-400">{{ __('Alur proses kendaraan saat ini') }}</p>
                </div>
                <span class="text-xs font-medium text-gray-400 bg-gray-50 px-3 py-1 rounded-full">Live</span>
            </div>
            <div class="status-pipeline">
                @php
                    $pipelineSteps = [
                        ['key' => 'parking', 'label' => 'Parking', 'color' => '#6b7280', 'bg' => '#f3f4f6'],
                        ['key' => 'doc_in', 'label' => 'Doc In', 'color' => '#2563eb', 'bg' => '#dbeafe'],
                        ['key' => 'assign_gate', 'label' => 'Waiting Gate', 'color' => '#4f46e5', 'bg' => '#e0e7ff'],
                        ['key' => 'on_loading', 'label' => 'Loading', 'color' => '#9333ea', 'bg' => '#f3e8ff'],
                        ['key' => 'finish', 'label' => 'Finish', 'color' => '#e11d48', 'bg' => '#fff1f2'],
                        ['key' => 'doc_out', 'label' => 'Doc Out', 'color' => '#0d9488', 'bg' => '#f0fdfa'],
                        ['key' => 'completed', 'label' => 'Completed', 'color' => '#059669', 'bg' => '#ecfdf5'],
                        ['key' => 'cancel', 'label' => 'Cancel', 'color' => '#dc2626', 'bg' => '#fef2f2'],
                    ];
                @endphp
                @foreach ($pipelineSteps as $step)
                    <div class="pipeline-step" style="{{ $statusBreakdown[$step['key']] > 0 ? 'background:' . $step['bg'] : '' }}">
                        <div class="step-count" style="color: {{ $statusBreakdown[$step['key']] > 0 ? $step['color'] : '#d1d5db' }}">
                            {{ $statusBreakdown[$step['key']] }}
                        </div>
                        <div class="step-label" style="color: {{ $statusBreakdown[$step['key']] > 0 ? $step['color'] : '#9ca3af' }}">
                            {{ $step['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Gate Utilization + Avg Time (1/4 width) --}}
        <div class="xl:col-span-1 dash-card" style="padding:20px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap: 16px;">
            <div class="text-center">
                <h3 class="text-sm font-bold text-gray-700 mb-1">{{ __('Gate Utilization') }}</h3>
                <p class="text-xs text-gray-400">{{ $occupiedGates }}/{{ $totalGates }} {{ __('terpakai') }}</p>
            </div>
            <div class="gauge-ring">
                @php
                    $radius = 42;
                    $circumference = 2 * pi() * $radius;
                    $offset = $circumference - ($gateUtil / 100) * $circumference;
                    $gaugeColor = $gateUtil > 80 ? '#ef4444' : ($gateUtil > 50 ? '#f59e0b' : '#10b981');
                @endphp
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="#f1f5f9" stroke-width="8"/>
                    <circle cx="50" cy="50" r="{{ $radius }}" fill="none" stroke="{{ $gaugeColor }}" stroke-width="8"
                            stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}"
                            stroke-linecap="round"
                            style="transition: stroke-dashoffset 1s ease"/>
                </svg>
                <div class="gauge-text">
                    <span class="text-2xl font-extrabold" style="color: {{ $gaugeColor }}">{{ $gateUtil }}%</span>
                </div>
            </div>
            @if ($avgLoadingTime)
                <div class="text-center pt-2 border-t border-gray-100 w-full">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ __('Avg. Loading') }}</p>
                    <p class="text-lg font-extrabold text-gray-800 font-mono mt-1">{{ $avgLoadingTime }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Bar Chart: Daily Activity --}}
        <div class="chart-card">
            <h3>
                <span class="chart-dot" style="background:linear-gradient(135deg,#10b981,#3b82f6)"></span>
                {{ __('Aktivitas Harian') }}
                <span class="text-xs font-normal text-gray-400 ml-auto">{{ __('7 hari terakhir') }}</span>
            </h3>
            <div id="dailyChart" class="h-72"></div>
        </div>

        {{-- Status Flow Chart --}}
        <div class="chart-card">
            <h3>
                <span class="chart-dot" style="background:linear-gradient(135deg,#8b5cf6,#ec4899)"></span>
                {{ __('Status Flow') }}
                <span class="text-xs font-normal text-gray-400 ml-auto">{{ __('Distribusi saat ini') }}</span>
            </h3>
            <div id="statusFlowChart" class="h-72"></div>
        </div>
    </div>

    {{-- Second Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Donut Chart: Goods Type --}}
        <div class="chart-card interactive-chart" title="Klik potongan untuk filter">
            <h3>
                <span class="chart-dot" style="background:linear-gradient(135deg,#3b82f6,#f97316)"></span>
                {{ __('Jenis Barang') }}
                <span class="text-[10px] font-normal text-gray-400 ml-auto bg-gray-100 px-2 py-0.5 rounded">{{ __('Klik = Filter') }}</span>
            </h3>
            <div id="goodsChart" class="h-64"></div>
        </div>

        {{-- Vehicle Type Chart --}}
        <div class="chart-card interactive-chart" title="Klik bar untuk filter">
            <h3>
                <span class="chart-dot" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa)"></span>
                {{ __('Jenis Kendaraan') }}
                <span class="text-[10px] font-normal text-gray-400 ml-auto bg-gray-100 px-2 py-0.5 rounded">{{ __('Klik = Filter') }}</span>
            </h3>
            <div id="vehicleChart" class="h-64"></div>
        </div>

        {{-- Vendor Chart --}}
        <div class="chart-card interactive-chart" title="Klik potongan untuk filter">
            <h3>
                <span class="chart-dot" style="background:linear-gradient(135deg,#10b981,#f59e0b)"></span>
                {{ __('Vendor') }}
                <span class="text-[10px] font-normal text-gray-400 ml-auto bg-gray-100 px-2 py-0.5 rounded">{{ __('Klik = Filter') }}</span>
            </h3>
            <div id="vendorChart" class="h-64"></div>
        </div>
    </div>
</div>

{{-- ApexCharts Scripts with Interactivity --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartFont = 'Inter, ui-sans-serif, system-ui, sans-serif';
    
    // Global chart instances
    let dailyChart, statusFlowChart, goodsChart, vehicleChart, vendorChart;
    
    // Active filters state
    let filters = {
        goods: null,
        vehicle: null,
        vendor: null
    };

    const filterContainer = document.getElementById('active-filters-container');
    const filterTagsDiv = document.getElementById('filter-tags');
    
    // Function to render active filter tags UI
    function updateFiltersUI() {
        filterTagsDiv.innerHTML = '';
        let hasFilters = false;
        
        const filterNames = { goods: 'Barang', vehicle: 'Kendaraan', vendor: 'Vendor' };
        
        for (const [key, value] of Object.entries(filters)) {
            if (value !== null) {
                hasFilters = true;
                const tag = document.createElement('div');
                tag.className = 'filter-tag';
                tag.innerHTML = `
                    <span><b>${filterNames[key]}:</b> ${value}</span>
                    <button onclick="removeFilter('${key}')" title="Hapus filter ini">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                `;
                filterTagsDiv.appendChild(tag);
            }
        }
        
        filterContainer.style.display = hasFilters ? 'flex' : 'none';
    }

    // Global expose for onclick
    window.removeFilter = function(key) {
        filters[key] = null;
        updateFiltersUI();
        fetchDataAndRender();
    };
    
    document.getElementById('clear-all-filters').addEventListener('click', () => {
        filters = { goods: null, vehicle: null, vendor: null };
        updateFiltersUI();
        fetchDataAndRender();
    });

    // Helper to toggle a filter when chart is clicked
    function handleChartClick(filterKey, clickedValue) {
        if (filters[filterKey] === clickedValue) {
            // Un-toggle if clicking the same thing
            filters[filterKey] = null;
        } else {
            filters[filterKey] = clickedValue;
        }
        updateFiltersUI();
        fetchDataAndRender();
    }

    // Function to fetch data and render/update charts
    function fetchDataAndRender() {
        const queryParams = new URLSearchParams();
        if (filters.goods) queryParams.append('goods', filters.goods);
        if (filters.vehicle) queryParams.append('vehicle', filters.vehicle);
        if (filters.vendor) queryParams.append('vendor', filters.vendor);

        const url = '{{ route("chart.data") }}' + (queryParams.toString() ? '?' + queryParams.toString() : '');
        
        fetch(url)
            .then(res => res.json())
            .then(data => {
                
                // Initialize charts if not exist, otherwise update them
                
                // --- Daily Activity Bar Chart ---
                if (!dailyChart) {
                    dailyChart = new ApexCharts(document.querySelector("#dailyChart"), {
                        chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: chartFont, animations: { enabled: true } },
                        series: [ { name: 'Inbound', data: data.daily.inbound }, { name: 'Outbound', data: data.daily.outbound } ],
                        xaxis: { categories: data.daily.categories, labels: { style: { fontSize: '11px', colors: '#9ca3af' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                        yaxis: { labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
                        colors: ['#10b981', '#f59e0b'],
                        plotOptions: { bar: { borderRadius: 8, columnWidth: '50%' } },
                        dataLabels: { enabled: false },
                        grid: { borderColor: '#f1f5f9', strokeDashArray: 4, padding: { left: 4, right: 4 } },
                        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px', fontWeight: 600, markers: { radius: 4 } },
                        tooltip: { theme: 'light', style: { fontSize: '12px' }, y: { formatter: val => val + ' kendaraan' } }
                    });
                    dailyChart.render();
                } else {
                    dailyChart.updateSeries([ { name: 'Inbound', data: data.daily.inbound }, { name: 'Outbound', data: data.daily.outbound } ]);
                    dailyChart.updateOptions({ xaxis: { categories: data.daily.categories } });
                }

                // --- Status Flow Chart ---
                if (!statusFlowChart) {
                    statusFlowChart = new ApexCharts(document.querySelector("#statusFlowChart"), {
                        chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: chartFont, animations: { enabled: true } },
                        series: [{ name: 'Jumlah', data: data.statusFlow.map(s => s.value) }],
                        xaxis: { categories: data.statusFlow.map(s => s.name), labels: { style: { fontSize: '11px', colors: '#6b7280', fontWeight: 600 } }, axisBorder: { show: false }, axisTicks: { show: false } },
                        yaxis: { labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
                        colors: ['#6b7280', '#3b82f6', '#6366f1', '#8b5cf6', '#ec4899', '#10b981'],
                        plotOptions: { bar: { borderRadius: 8, columnWidth: '55%', distributed: true } },
                        dataLabels: { enabled: true, style: { fontSize: '13px', fontWeight: 800, colors: ['#fff'] }, offsetY: -2 },
                        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                        legend: { show: false },
                        tooltip: { theme: 'light', style: { fontSize: '12px' }, y: { formatter: val => val + ' kendaraan' } }
                    });
                    statusFlowChart.render();
                } else {
                    statusFlowChart.updateSeries([{ name: 'Jumlah', data: data.statusFlow.map(s => s.value) }]);
                }

                // --- Goods Donut Chart ---
                if (!goodsChart) {
                    goodsChart = new ApexCharts(document.querySelector("#goodsChart"), {
                        chart: { 
                            type: 'donut', height: 260, fontFamily: chartFont, animations: { enabled: true },
                            events: {
                                dataPointSelection: function(event, chartContext, config) {
                                    const clickedValue = config.w.config.labels[config.dataPointIndex];
                                    handleChartClick('goods', clickedValue);
                                }
                            }
                        },
                        series: data.goods.values,
                        labels: data.goods.labels,
                        colors: ['#3b82f6', '#f97316', '#06b6d4', '#10b981', '#8b5cf6'],
                        plotOptions: { pie: { donut: { size: '68%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '13px', fontWeight: 700, color: '#6b7280' } } } } },
                        legend: { position: 'bottom', fontSize: '12px', fontWeight: 500, markers: { radius: 4 } },
                        dataLabels: { enabled: true, formatter: val => Math.round(val) + '%', style: { fontSize: '12px', fontWeight: 700 } },
                        stroke: { width: 3, colors: ['#fff'] },
                        tooltip: { style: { fontSize: '12px' } }
                    });
                    goodsChart.render();
                } else {
                    goodsChart.updateSeries(data.goods.values);
                    goodsChart.updateOptions({ labels: data.goods.labels });
                }

                // --- Vehicle Bar Chart ---
                if (!vehicleChart) {
                    vehicleChart = new ApexCharts(document.querySelector("#vehicleChart"), {
                        chart: { 
                            type: 'bar', height: 260, toolbar: { show: false }, fontFamily: chartFont, animations: { enabled: true },
                            events: {
                                dataPointSelection: function(event, chartContext, config) {
                                    const clickedValue = config.w.config.xaxis.categories[config.dataPointIndex];
                                    handleChartClick('vehicle', clickedValue);
                                }
                            }
                        },
                        series: [{ name: 'Jumlah', data: data.vehicles.values }],
                        xaxis: { categories: data.vehicles.labels, labels: { style: { fontSize: '10px', colors: '#6b7280' } }, axisBorder: { show: false }, axisTicks: { show: false } },
                        yaxis: { labels: { style: { fontSize: '11px', colors: '#9ca3af' } } },
                        colors: ['#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe', '#e879f9'],
                        plotOptions: { bar: { borderRadius: 6, columnWidth: '50%', distributed: true } },
                        dataLabels: { enabled: false },
                        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                        legend: { show: false },
                        tooltip: { theme: 'light', style: { fontSize: '12px' }, y: { formatter: val => val + ' unit' } }
                    });
                    vehicleChart.render();
                } else {
                    vehicleChart.updateSeries([{ name: 'Jumlah', data: data.vehicles.values }]);
                    vehicleChart.updateOptions({ xaxis: { categories: data.vehicles.labels } });
                }

                // --- Vendor Pie Chart ---
                if (!vendorChart) {
                    vendorChart = new ApexCharts(document.querySelector("#vendorChart"), {
                        chart: { 
                            type: 'pie', height: 260, fontFamily: chartFont, animations: { enabled: true },
                            events: {
                                dataPointSelection: function(event, chartContext, config) {
                                    const clickedValue = config.w.config.labels[config.dataPointIndex];
                                    handleChartClick('vendor', clickedValue);
                                }
                            }
                        },
                        series: data.vendors.values,
                        labels: data.vendors.labels,
                        colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'],
                        legend: { position: 'bottom', fontSize: '11px', fontWeight: 500, markers: { radius: 4 } },
                        dataLabels: { enabled: true, formatter: val => Math.round(val) + '%', style: { fontSize: '11px', fontWeight: 700 } },
                        stroke: { width: 2, colors: ['#fff'] },
                        tooltip: { style: { fontSize: '12px' } }
                    });
                    vendorChart.render();
                } else {
                    vendorChart.updateSeries(data.vendors.values);
                    vendorChart.updateOptions({ labels: data.vendors.labels });
                }
            });
    }

    // Initial load
    fetchDataAndRender();
});
</script>
@endsection
