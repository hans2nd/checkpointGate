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

    @include('livemonitoring.styles')

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
        @include('livemonitoring.header')

        {{-- SLIDER --}}
        <div class="monitoring-slider" id="monitoring-slider">
            <div class="slider-track-wrapper" id="slider-track-wrapper">
                <div class="slider-track" id="slider-track">

                    {{-- ===== SLIDE 1: Summary ===== --}}
                    <div class="slider-slide" data-slide="0">
                        @include('livemonitoring.summary-slide')
                    </div>{{-- end slide 1 --}}

                    {{-- ===== SLIDE 2: Gate Grid ===== --}}
                    <div class="slider-slide" data-slide="1">
                        @include('livemonitoring.gates-slide')
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

    @include('livemonitoring.scripts')

@endsection
