<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 flex-shrink-0" id="monitoring-header">
    <div class="flex items-center gap-3">
        <span class="text-sm font-semibold text-gray-700">{{ now()->translatedFormat('l, d F Y') }}</span>
    </div>
    <div class="flex items-center gap-2">
        <span class="flex items-center gap-1.5 text-xs text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full">
            <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
            Live
        </span>
        <span id="last-update" class="text-xs text-gray-400"></span>
    </div>
</div>
