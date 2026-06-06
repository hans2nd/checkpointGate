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
                            @include('livemonitoring.gate-card', [
                                'id' => 'gate-' . $i,
                                'label' => 'F-' . $i,
                                'type' => 'FROZEN',
                                'color' => 'blue',
                                'cp' => $cp,
                            ])
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
                            @include('livemonitoring.gate-card', [
                                'id' => 'gate-' . $i,
                                'label' => $dLabel,
                                'type' => 'DRY',
                                'color' => 'amber',
                                'cp' => $cp,
                            ])
                        @endfor
                        {{-- pad --}}
                        <div class="gate-card border-transparent opacity-0 pointer-events-none">
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div class="dry-side-bottom gate-legend">
                        <p>Keterangan Status Gate:</p>
                        <div class="legend-items">
                            <span class="legend-item"><span class="gate-badge bg-yellow-100 text-yellow-700">📋
                                    ASSIGN</span> Waiting Loading</span>
                            <span class="legend-item"><span class="gate-badge bg-blue-100 text-blue-700">⏳ ON
                                    LOADING</span> Sedang loading</span>
                            <span class="legend-item"><span class="gate-badge bg-emerald-100 text-emerald-700">🏁
                                    FINISH</span> Loading selesai</span>
                            <span class="legend-item"><span class="gate-badge bg-purple-100 text-purple-700">✅
                                    DOC OUT</span> Dokumen selesai</span>
                        </div>
                    </div>
                </div>

            </div>{{-- end gate-unified-grid --}}
        </div>
    </div>
</div>
