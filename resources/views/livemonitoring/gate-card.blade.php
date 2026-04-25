<div id="{{ $id }}" class="gate-card border-{{ $color }}-300">
    <div class="gate-card-header bg-{{ $color }}-500">
        <div class="gate-number">{{ $label }}</div>
        <div class="gate-type">{{ $type }}</div>
    </div>
    <div class="gate-body"
        data-waktu-start="{{ $cp?->waktu_start?->toIso8601String() ?? '' }}"
        data-waktu-end="{{ $cp?->waktu_end?->toIso8601String() ?? '' }}"
        data-waktu-penerimaan="{{ $cp?->waktu_penerimaan_dokumen?->toIso8601String() ?? '' }}"
        data-waktu-penyerahan="{{ $cp?->waktu_penyerahan_dokumen?->toIso8601String() ?? '' }}"
        data-status="{{ $cp?->status ?? '' }}">

        <div class="gate-body-inner">
            @if ($cp)
                <span class="gate-aktivitas {{ $cp->aktivitas === 'INBOUND' ? 'bg-orange-100 text-orange-700' : ($cp->aktivitas === 'OUTBOUND' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700') }}">
                    {{ $cp->aktivitas }}
                </span>
                <div style="margin: auto 0; width: 100%;">
                    <p class="gate-plate">{{ $cp->no_polisi }}</p>
                    <p class="gate-vendor-name">{{ $cp->vendor }}</p>
                </div>
                @if ($cp->waktu_penyerahan_dokumen)
                    <span class="gate-badge bg-purple-100 text-purple-700">✅ COMPLETED</span>
                @elseif($cp->waktu_end || $cp->status === 'FINISH')
                    <span class="static-timer gate-timer-text bg-emerald-100 text-emerald-700"
                        data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}"
                        data-end="{{ $cp->waktu_end?->toIso8601String() ?? '' }}">DONE</span>
                    <span class="gate-badge bg-emerald-100 text-emerald-700">🏁 FINISH</span>
                @elseif($cp->waktu_start || $cp->status === 'ON LOADING')
                    <span class="gate-timer gate-timer-text bg-blue-100 text-blue-700"
                        data-start="{{ $cp->waktu_start?->toIso8601String() ?? '' }}">00:00:00</span>
                    <span class="gate-badge bg-blue-100 text-blue-700 animate-pulse">⏳ {{ $cp->aktivitas === 'INBOUND' ? 'UNLOADING' : 'LOADING' }}</span>
                @elseif($cp->waktu_penerimaan_dokumen)
                    <span class="gate-badge bg-yellow-100 text-yellow-700">📋 ASSIGN</span>
                @endif
            @else
                <span class="gate-empty">kosong</span>
            @endif
        </div>

        @if ($cp)
            <div class="gate-times">
                <div style="color:{{ $cp->waktu_penerimaan_dokumen ? '#4b5563' : '#d1d5db' }}">
                    <span>📋</span>
                    <span>{{ $cp->waktu_penerimaan_dokumen?->format('H:i') ?? '--:--' }}</span>
                </div>
                <div style="color:{{ $cp->waktu_start ? '#4b5563' : '#d1d5db' }}">
                    <span>⏳</span>
                    <span>{{ $cp->waktu_start?->format('H:i') ?? '--:--' }}</span>
                </div>
                <div style="color:{{ $cp->waktu_end ? '#4b5563' : '#d1d5db' }}">
                    <span>🏁</span>
                    <span>{{ $cp->waktu_end?->format('H:i') ?? '--:--' }}</span>
                </div>
                <div style="color:{{ $cp->waktu_penyerahan_dokumen ? '#4b5563' : '#d1d5db' }}">
                    <span>✅</span>
                    <span>{{ $cp->waktu_penyerahan_dokumen?->format('H:i') ?? '--:--' }}</span>
                </div>
            </div>
        @endif
    </div>
</div>
