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
    <!-- Floating exit fullscreen button -->
    <button onclick="toggleFullscreen()"
        class="fs-exit-btn hidden fixed top-3 right-3 z-50 items-center gap-1.5 px-3 py-1.5 bg-gray-900/80 hover:bg-gray-900 text-white text-xs font-medium rounded-full shadow-lg backdrop-blur transition-all">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" />
        </svg>
        Keluar Fullscreen (ESC)
    </button>

    <style>
        /* Carousel Styles */
        .carousel-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            border-radius: 1rem;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.8s ease-in-out;
            will-change: transform;
        }

        .carousel-slide {
            min-width: 100%;
            box-sizing: border-box;
        }

        .carousel-dots {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .carousel-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #d1d5db;
            /* gray-300 */
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .carousel-dot.active {
            background-color: #f97316;
            /* orange-500 */
            transform: scale(1.2);
        }
    </style>

    <div class="space-y-6" id="monitoring-container">
        {{-- Header with Date Picker --}}
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Live Gate Monitoring</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <label class="text-xs text-gray-500">Tanggal:</label>
                        <input type="date" id="tanggal-picker" value="{{ $tanggalValue }}"
                            class="px-2 py-0.5 border border-gray-200 rounded text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent cursor-pointer">
                        @if (!$isToday)
                            <a href="{{ route('livemonitoring2') }}"
                                class="text-[10px] px-2 py-0.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded font-medium transition-colors">
                                Kembali Hari Ini
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-end gap-1">
                @if ($isToday)
                    <span id="refresh-indicator"
                        class="flex items-center gap-1.5 text-sm text-orange-600 bg-orange-50 px-3 py-1.5 rounded-full font-bold shadow-sm">
                        <span class="w-2.5 h-2.5 bg-orange-500 rounded-full animate-pulse"></span>
                        LIVE REAL-TIME
                    </span>
                @else
                    <span
                        class="flex items-center gap-1.5 text-sm text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full font-bold shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        RIWAYAT DATA
                    </span>
                @endif
                <span id="last-update" class="text-[10px] text-gray-400 font-medium"></span>
            </div>
        </div>

        {{-- CAROUSEL GATE GRID: 9 per page --}}
        <div>
            <div class="carousel-container shadow-xl bg-white border border-gray-100 p-6 md:p-8">
                <div class="carousel-track" id="carousel-track">
                    @php
                        $chunks = array_chunk(range(1, 27), 9);
                    @endphp
                    @foreach ($chunks as $chunkIndex => $gateNumbers)
                        <div class="carousel-slide flex-shrink-0">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                                @foreach ($gateNumbers as $i)
                                    @php
                                        $g = $gates[$i];
                                        $cp = $g['checkpoint'];
                                        $isFrozen = $i <= 16;
                                        $borderColor = $isFrozen ? 'border-blue-200' : 'border-amber-200';
                                        $headerBg = $isFrozen
                                            ? 'bg-gradient-to-r from-blue-600 to-blue-400'
                                            : 'bg-gradient-to-r from-amber-500 to-orange-400';
                                        $typeLabel = $isFrozen ? 'FROZEN' : 'DRY';
                                        $displayGate = $isFrozen ? 'F-' . $i : 'D-' . ($i - 16);
                                    @endphp
                                    <div id="gate-{{ $i }}"
                                        class="rounded-2xl border-2 overflow-hidden transition-all duration-300 {{ $borderColor }} shadow-sm hover:shadow-md h-full flex flex-col">
                                        <div class="{{ $headerBg }} text-white text-center py-3 px-4 shadow-inner">
                                            <div class="flex justify-between items-center">
                                                <span
                                                    class="text-xs font-bold tracking-widest opacity-90 border border-white/30 rounded px-1.5 pb-0.5">{{ $typeLabel }}</span>
                                                <h3 class="text-2xl font-black tracking-tight">{{ $displayGate }}</h3>
                                                <span class="w-8"></span> {{-- Spacer for balance --}}
                                            </div>
                                        </div>
                                        <div class="gate-body p-5 flex-1 flex flex-col items-center justify-center transition-all duration-500 min-h-[160px]"
                                            data-waktu-start="{{ $cp && $cp->waktu_start ? $cp->waktu_start->toIso8601String() : '' }}"
                                            data-waktu-end="{{ $cp && $cp->waktu_end ? $cp->waktu_end->toIso8601String() : '' }}"
                                            data-waktu-penerimaan="{{ $cp && $cp->waktu_penerimaan_dokumen ? $cp->waktu_penerimaan_dokumen->toIso8601String() : '' }}"
                                            data-waktu-penyerahan="{{ $cp && $cp->waktu_penyerahan_dokumen ? $cp->waktu_penyerahan_dokumen->toIso8601String() : '' }}"
                                            data-status="{{ $cp ? $cp->status : '' }}">
                                            @if ($cp)
                                                <div class="text-center w-full space-y-2">
                                                    @if(strtoupper($cp->aktivitas) === 'INBOUND')
                                                        <div class="mb-2"><span class="px-3 py-1 rounded-full text-xs font-black tracking-widest bg-orange-100 text-orange-700 shadow-sm border border-orange-200">INBOUND</span></div>
                                                    @elseif(strtoupper($cp->aktivitas) === 'OUTBOUND')
                                                        <div class="mb-2"><span class="px-3 py-1 rounded-full text-xs font-black tracking-widest bg-indigo-100 text-indigo-700 shadow-sm border border-indigo-200">OUTBOUND</span></div>
                                                    @elseif($cp->aktivitas)
                                                        <div class="mb-2"><span class="px-3 py-1 rounded-full text-xs font-black tracking-widest bg-gray-100 text-gray-700 shadow-sm border border-gray-200">{{ strtoupper($cp->aktivitas) }}</span></div>
                                                    @endif
                                                    <p
                                                        class="text-2xl font-black text-gray-800 tracking-wider truncate px-2">
                                                        {{ $cp->no_polisi }}</p>
                                                    <p
                                                        class="text-sm font-semibold text-gray-500 truncate px-2 bg-white/50 inline-block rounded-md">
                                                        {{ $cp->vendor }}</p>

                                                    <div
                                                        class="pt-3 border-t border-gray-900/10 mt-3 w-full flex flex-col items-center gap-2">
                                                        @if ($cp->waktu_penyerahan_dokumen)
                                                            <span
                                                                class="gate-status text-xs px-4 py-1.5 rounded-full font-bold bg-purple-100 text-purple-700 shadow-sm border border-purple-200"><span
                                                                    class="text-sm">✅</span> COMPLETED</span>
                                                        @elseif($cp->status === 'FINISH')
                                                            <span
                                                                class="gate-status text-xs px-4 py-1.5 rounded-full font-bold bg-emerald-100 text-emerald-700 shadow-sm border border-emerald-200"><span
                                                                    class="text-sm">🏁</span> FINISH LOADING</span>
                                                            <span
                                                                class="text-sm font-black font-mono text-emerald-800 bg-white/60 px-3 py-1 rounded shadow-sm">{{ $cp->durasi ?? 'DONE' }}</span>
                                                        @elseif($cp->status === 'ON LOADING' && $cp->waktu_start)
                                                            <span
                                                                class="gate-status text-xs px-4 py-1.5 rounded-full font-bold animate-pulse bg-blue-100 text-blue-700 shadow-sm border border-blue-200"><span
                                                                    class="text-sm">⏳</span> ON LOADING</span>
                                                            <span
                                                                class="gate-timer text-2xl font-black font-mono text-blue-900 drop-shadow-sm"
                                                                data-start="{{ $cp->waktu_start->toIso8601String() }}">00:00:00</span>
                                                        @elseif($cp->waktu_penerimaan_dokumen)
                                                            <span
                                                                class="gate-status text-xs px-4 py-1.5 rounded-full font-bold bg-yellow-100 text-yellow-700 shadow-sm border border-yellow-200"><span
                                                                    class="text-sm">📋</span> ASSIGNED</span>
                                                            <p class="text-[10px] text-gray-500 mt-1 font-medium italic">
                                                                Menunggu mulai loading...</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex flex-col items-center justify-center opacity-40">
                                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1.5"
                                                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                                                        </path>
                                                    </svg>
                                                    <span
                                                        class="text-sm font-bold text-gray-400 uppercase tracking-widest">KOSONG</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Carousel Dots --}}
            <div class="carousel-dots" id="carousel-dots">
                @foreach ($chunks as $index => $chunk)
                    <div class="carousel-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></div>
                @endforeach
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
                                <th class="px-4 py-2.5 text-left font-semibold text-gray-600 text-xs uppercase">Aktivitas
                                </th>
                                <th class="px-4 py-2.5 text-center font-semibold text-gray-600 text-xs uppercase">On
                                    Process</th>
                                <th class="px-4 py-2.5 text-center font-semibold text-gray-600 text-xs uppercase">Finish
                                </th>
                            </tr>
                        </thead>
                        <tbody id="activity-summary-body" class="divide-y divide-gray-50">
                            @foreach ($activitySummary as $row)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-2.5 font-medium text-gray-700 text-xs">{{ $row['label'] }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span
                                            class="inline-flex min-w-[28px] items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold {{ $row['on_process'] > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-400' }}">
                                            {{ $row['on_process'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span
                                            class="inline-flex min-w-[28px] items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold {{ $row['finish'] > 0 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-400' }}">
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
                    <p class="text-xs text-gray-400">ALT = Avg Loading Time (Inbound) · AUT = Avg Unloading Time (Outbound)
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th rowspan="2"
                                    class="px-3 py-2 text-left font-semibold text-gray-600 text-xs uppercase border-r border-gray-200">
                                    Kendaraan</th>
                                <th colspan="2"
                                    class="px-3 py-1.5 text-center font-semibold text-xs uppercase bg-green-50 text-green-700 border-b border-green-200">
                                    DRY</th>
                                <th colspan="2"
                                    class="px-3 py-1.5 text-center font-semibold text-xs uppercase bg-blue-50 text-blue-700 border-b border-blue-200">
                                    FROZEN</th>
                            </tr>
                            <tr class="bg-gray-50">
                                <th
                                    class="px-3 py-1.5 text-center font-medium text-[10px] uppercase text-green-600 bg-green-50">
                                    ALT</th>
                                <th
                                    class="px-3 py-1.5 text-center font-medium text-[10px] uppercase text-green-600 bg-green-50 border-r border-gray-200">
                                    AUT</th>
                                <th
                                    class="px-3 py-1.5 text-center font-medium text-[10px] uppercase text-blue-600 bg-blue-50">
                                    ALT</th>
                                <th
                                    class="px-3 py-1.5 text-center font-medium text-[10px] uppercase text-blue-600 bg-blue-50">
                                    AUT</th>
                            </tr>
                        </thead>
                        <tbody id="avg-times-body" class="divide-y divide-gray-50">
                            @foreach ($avgTimes as $row)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-3 py-2 font-medium text-gray-700 text-xs border-r border-gray-100">
                                        {{ $row['jenis_kendaraan'] }}</td>
                                    <td
                                        class="px-3 py-2 text-center font-mono text-xs {{ $row['DRY_ALT'] ? 'text-green-700 bg-green-50/50' : 'text-gray-300' }}">
                                        {{ $row['DRY_ALT'] ?? '—' }}</td>
                                    <td
                                        class="px-3 py-2 text-center font-mono text-xs border-r border-gray-100 {{ $row['DRY_AUT'] ? 'text-green-700 bg-green-50/50' : 'text-gray-300' }}">
                                        {{ $row['DRY_AUT'] ?? '—' }}</td>
                                    <td
                                        class="px-3 py-2 text-center font-mono text-xs {{ $row['FROZEN_ALT'] ? 'text-blue-700 bg-blue-50/50' : 'text-gray-300' }}">
                                        {{ $row['FROZEN_ALT'] ?? '—' }}</td>
                                    <td
                                        class="px-3 py-2 text-center font-mono text-xs {{ $row['FROZEN_AUT'] ? 'text-blue-700 bg-blue-50/50' : 'text-gray-300' }}">
                                        {{ $row['FROZEN_AUT'] ?? '—' }}</td>
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
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-yellow-100 text-yellow-700">📋
                        ASSIGN</span> Dokumen diterima, menunggu loading
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-100 text-blue-700">⏳ ON
                        LOADING</span> Sedang proses loading
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-700">🏁 FINISH
                        LOADING</span> Loading selesai
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-purple-100 text-purple-700">✅
                        COMPLETED</span> Dokumen diserahkan
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
        // ===== CAROUSEL LOGIC =====
        const track = document.getElementById('carousel-track');
        const dots = document.querySelectorAll('.carousel-dot');
        let currentSlide = 0;
        const totalSlides = {{ count($chunks) }};
        const slideIntervalTime = 6000; // 6 seconds per slide
        let slideTimer;

        function goToSlide(index) {
            currentSlide = index;
            track.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';
            dots.forEach(d => d.classList.remove('active'));
            if (dots[currentSlide]) dots[currentSlide].classList.add('active');
            resetTimer();
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            goToSlide(currentSlide);
        }

        function resetTimer() {
            clearInterval(slideTimer);
            slideTimer = setInterval(nextSlide, slideIntervalTime);
        }

        // Initialize carousel clicks
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index);
            });
        });

        // Start auto slide
        resetTimer();

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
            if (minutes > 60) return {
                bg: 'bg-red-100',
                text: 'text-red-800',
                border: 'border-red-400',
                bodyBg: '#fee2e2'
            };
            if (minutes > 30) return {
                bg: 'bg-yellow-100',
                text: 'text-yellow-800',
                border: 'border-yellow-400',
                bodyBg: '#fef9c3'
            };
            return {
                bg: 'bg-emerald-100',
                text: 'text-emerald-800',
                border: 'border-emerald-400',
                bodyBg: '#ecfdf5'
            };
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
                return `<span class="gate-status text-xs px-4 py-1.5 rounded-full font-bold bg-purple-100 text-purple-700 shadow-sm border border-purple-200"><span class="text-sm">✅</span> COMPLETED</span>`;
            } else if (gate.status === 'FINISH') {
                let html =
                    `<span class="gate-status text-xs px-4 py-1.5 rounded-full font-bold bg-emerald-100 text-emerald-700 shadow-sm border border-emerald-200"><span class="text-sm">🏁</span> FINISH LOADING</span>`;
                html +=
                    `<span class="text-sm font-black font-mono text-emerald-800 bg-white/60 px-3 py-1 rounded shadow-sm mt-2">${gate.durasi || 'DONE'}</span>`;
                return html;
            } else if (gate.status === 'ON LOADING' && gate.waktu_start) {
                let html =
                    `<span class="gate-status text-xs px-4 py-1.5 rounded-full font-bold animate-pulse bg-blue-100 text-blue-700 shadow-sm border border-blue-200"><span class="text-sm">⏳</span> ON LOADING</span>`;
                const elapsed = formatDuration(gate.waktu_start, null);
                html +=
                    `<span class="gate-timer text-2xl font-black font-mono text-blue-900 drop-shadow-sm mt-2" data-start="${gate.waktu_start}">${elapsed}</span>`;
                return html;
            } else if (gate.waktu_penerimaan) {
                return `<span class="gate-status text-xs px-4 py-1.5 rounded-full font-bold bg-yellow-100 text-yellow-700 shadow-sm border border-yellow-200"><span class="text-sm">📋</span> ASSIGNED</span><p class="text-[10px] text-gray-500 mt-1 font-medium italic">Menunggu mulai loading...</p>`;
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

                let aktivitasHtml = '';
                if (gate.aktivitas) {
                    const actName = gate.aktivitas.toUpperCase();
                    if (actName === 'INBOUND') {
                        aktivitasHtml = '<div class="mb-2"><span class="px-3 py-1 rounded-full text-xs font-black tracking-widest bg-orange-100 text-orange-700 shadow-sm border border-orange-200">INBOUND</span></div>';
                    } else if (actName === 'OUTBOUND') {
                        aktivitasHtml = '<div class="mb-2"><span class="px-3 py-1 rounded-full text-xs font-black tracking-widest bg-indigo-100 text-indigo-700 shadow-sm border border-indigo-200">OUTBOUND</span></div>';
                    } else {
                        aktivitasHtml = `<div class="mb-2"><span class="px-3 py-1 rounded-full text-xs font-black tracking-widest bg-gray-100 text-gray-700 shadow-sm border border-gray-200">${gate.aktivitas}</span></div>`;
                    }
                }

                body.innerHTML = `
                <div class="text-center w-full space-y-2">
                    ${aktivitasHtml}
                    <p class="text-2xl font-black text-gray-800 tracking-wider truncate px-2">${gate.no_polisi}</p>
                    <p class="text-sm font-semibold text-gray-500 truncate px-2 bg-white/50 inline-block rounded-md">${gate.vendor || ''}</p>
                    <div class="pt-3 border-t border-gray-900/10 mt-3 w-full flex flex-col items-center gap-2">
                        ${statusHtml}
                    </div>
                </div>
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
                body.innerHTML = `<div class="flex flex-col items-center justify-center opacity-40">
                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">KOSONG</span>
            </div>`;
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
                window.location.href = '{{ route('livemonitoring2') }}?tanggal=' + date;
            }
        });

        // Auto-refresh every 5 seconds (only when viewing today)
        @if ($isToday)
            setInterval(function() {
                fetch('{{ route('livemonitoring2.data') }}?tanggal={{ $tanggalValue }}')
                    .then(res => res.json())
                    .then(data => {
                        // Update gates
                        for (let i = 1; i <= 27; i++) {
                            buildGateContent(data.gates[i]);
                        }
                        const now = new Date();
                        document.getElementById('last-update').textContent = 'Terakhir Sync: ' + now
                            .toLocaleTimeString('id-ID');
                    })
                    .catch(err => console.error('Refresh error:', err));
            }, 5000);
        @endif
    </script>
@endsection
