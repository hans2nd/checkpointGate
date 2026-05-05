<div class="slide-1-content" id="slide-1-content">

    {{-- Activity Summary --}}
    <div id="activity-card">
        <div class="inner">
            <p style="font-size:1em;font-weight:700;color:#1f2937;margin-bottom:0.5em">Activity Summary</p>
            <p style="font-size:0.75em;color:#9ca3af;margin-bottom:1em">Periode: {{ now()->format('d/m/Y') }}</p>
            <table>
                <thead>
                    <tr style="border-bottom:1px solid #f3f4f6">
                        <th class="lbl" style="text-align:left;width:120px">Activity</th>
                        <th class="lbl" style="text-align:center">Parking</th>
                        <th class="lbl" style="text-align:center">Doc In</th>
                        <th class="lbl" style="text-align:center">Loading/Unloading</th>
                        <th class="lbl" style="text-align:center">Finish</th>
                        <th class="lbl" style="text-align:center;color:#f59e0b;font-weight:700">Total</th>
                    </tr>
                </thead>
                <tbody id="activity-summary-body">
                    @foreach ($activitySummary as $row)
                        <tr style="border-top:1px solid #f9fafb">
                            <td class="lbl" style="font-weight:600;color:#1f2937">{{ $row['label'] }}</td>
                            <td style="text-align:center"><span class="stat-num {{ $row['parking'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['parking'] }}</span></td>
                            <td style="text-align:center"><span class="stat-num {{ $row['receiving'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['receiving'] }}</span></td>
                            <td style="text-align:center"><span class="stat-num {{ $row['on_process'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['on_process'] }}</span></td>
                            <td style="text-align:center"><span class="stat-num {{ $row['finish'] > 0 ? 'text-gray-800' : 'text-gray-300' }}">{{ $row['finish'] }}</span></td>
                            <td style="text-align:center"><span class="stat-num" style="font-weight:700;{{ $row['total'] > 0 ? 'color:#f59e0b' : 'color:#d1d5db' }}">{{ $row['total'] }}</span></td>
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
                        <th rowspan="3" style="text-align:left;font-weight:600;color:#6b7280;padding:2px 8px 2px 0;border-right:1px solid #e5e7eb;white-space:nowrap;vertical-align:bottom;min-width:80px;font-size:1.5em">Kendaraan</th>
                        <th colspan="4" style="text-align:center;font-weight:600;color:#15803d;background:#f0fdf4;border:1px solid #e5e7eb;padding:3px 4px">DRY</th>
                        <th colspan="4" style="text-align:center;font-weight:600;color:#1d4ed8;background:#eff6ff;border:1px solid #e5e7eb;padding:3px 4px">FROZEN</th>
                        <th colspan="4" style="text-align:center;font-weight:600;color:#b45309;background:#fffbeb;border:1px solid #e5e7eb;padding:3px 4px">CHILLED</th>
                    </tr>
                    <tr>
                        @foreach (['DRY', 'DRY', 'FROZEN', 'FROZEN', 'CHILLED', 'CHILLED'] as $gi => $g)
                            @php $sub = $gi % 2 === 0 ? 'ALT' : 'AUT'; @endphp
                            <th colspan="2" style="text-align:center;padding:2px 4px;border:1px solid #e5e7eb;white-space:nowrap;{{ $g === 'DRY' ? 'color:#16a34a;background:#f0fdf4' : ($g === 'FROZEN' ? 'color:#2563eb;background:#eff6ff' : 'color:#d97706;background:#fffbeb') }}">
                                {{ $sub }}
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach (['text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-green-600 bg-green-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-blue-600 bg-blue-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50', 'text-amber-600 bg-amber-50'] as $idx => $cls)
                            <th style="text-align:center;padding:2px 6px;border:1px solid #e5e7eb">{{ $idx % 2 === 0 ? 'Today' : 'L.Month' }}</th>
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
                            <td style="padding:3px 8px 3px 0;font-weight:500;color:#374151;border-right:1px solid #e5e7eb;white-space:nowrap;font-size:0.90em">{{ $row['jenis_kendaraan'] }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['DRY_ALT'] ?? null, $row['DRY_ALT_LMONTH'] ?? null) }}">{{ $row['DRY_ALT'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['DRY_ALT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['DRY_ALT_LMONTH'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['DRY_AUT'] ?? null, $row['DRY_AUT_LMONTH'] ?? null) }}">{{ $row['DRY_AUT'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['DRY_AUT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['DRY_AUT_LMONTH'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['FROZEN_ALT'] ?? null, $row['FROZEN_ALT_LMONTH'] ?? null) }}">{{ $row['FROZEN_ALT'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['FROZEN_ALT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['FROZEN_ALT_LMONTH'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['FROZEN_AUT'] ?? null, $row['FROZEN_AUT_LMONTH'] ?? null) }}">{{ $row['FROZEN_AUT'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['FROZEN_AUT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['FROZEN_AUT_LMONTH'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['CHILLED_ALT'] ?? null, $row['CHILLED_ALT_LMONTH'] ?? null) }}">{{ $row['CHILLED_ALT'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['CHILLED_ALT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['CHILLED_ALT_LMONTH'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ $gc($row['CHILLED_AUT'] ?? null, $row['CHILLED_AUT_LMONTH'] ?? null) }}">{{ $row['CHILLED_AUT'] ?? '—' }}</td>
                            <td class="mono-val" style="text-align:center;padding:3px 6px;border:1px solid #f3f4f6;{{ isset($row['CHILLED_AUT_LMONTH']) ? 'color:#6b7280' : 'color:#d1d5db' }}">{{ $row['CHILLED_AUT_LMONTH'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
