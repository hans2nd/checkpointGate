<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 flex-shrink-0" id="monitoring-header">
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
